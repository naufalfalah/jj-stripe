<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientFolder;
use App\Models\ClientFiles;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientFilesManagement extends Controller
{
    public function files_view(Request $request)
    {
        if (Auth::user('admin')->can('file-manager-read') != true) {
            abort(403, 'Unauthorized action.');
        }
        if (isset($request->client) && !empty($request->client)) {
            $data = [
                'breadcrumb_main' => 'File Manager',
                'breadcrumb' => 'File Manager',
                'title' => 'File Manager',
                // 'folder_id' => ClientFolder::where('client_id', $auth_id)->latest()->first(),
                'all_folders' => ClientFolder::with('client_files')->where('client_id', hashids_decode($request->client))->latest()->get(),
                'clients' => User::whereNotNull('email_verified_at')->latest()->get(['id','client_name','email']),
            ];
        } else {
            $data = [
                'breadcrumb_main' => 'File Manager',
                'breadcrumb' => 'File Manager',
                'title' => 'File Manager',
                // 'folder_id' => ClientFolder::where('client_id', $auth_id)->latest()->first(),
                'all_folders' => ClientFolder::with('client_files')->latest()->get(),
                'clients' => User::whereNotNull('email_verified_at')->latest()->get(['id','client_name','email']),
            ];
        }

        // $auth_id = auth('web')->id();

        return view('admin.file_manager.index', $data);
    }

    public function get_client_files(Request $request)
    {

        if ($request->ajax()) {
            if (isset($request->folder_id) && !empty($request->folder_id)) {
                $folder_name = ClientFolder::where('id', $request->folder_id)->latest()->first();
                $folder_files = ClientFiles::where('folder_id', $request->folder_id)->orWhere('main_folder_id', $request->folder_id)->latest()->get();
                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    'body' => view('admin.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
                ]);
            } elseif (isset($request->request_id_input) && !empty($request->request_id_input)) {
                $get_folder_name = ClientFolder::latest()->first();
                $folder_files = ClientFiles::where('folder_id', hashids_decode($request->request_id_input))->orWhere('main_request_id_input', hashids_decode($request->request_id_input))->latest()->get();
                $folder_name = 'All Files';
                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    'folder_id' => $get_folder_name->id,
                    'body' => view('admin.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
                ]);
            } else {
                // $get_folder_name = ClientFolder::latest()->first();
                $folder_files = ClientFiles::groupBy('file_name')->latest()->get();
                $folder_name = 'All Files';
                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    // 'folder_id'  => $get_folder_name->id,
                    'body' => view('admin.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
                ]);
            }
        } else {
            return response([
                'status' => false,
                'message' => 'Item not found',
            ]);
        }
    }

    public function file_detail($id)
    {

        $auth_id = auth('web')->id();

        $seven_days_ago = Carbon::now()->subDays(7);

        $client_file = ClientFiles::hashidfind($id);

        $get_file_activity = LeadActivity::with('lead')->where('file_id', hashids_decode($id))->latest()->get();

        $viewed_in_last_7_days = LeadActivity::with('lead')->where('file_id', hashids_decode($id))->where('last_open', '>=', $seven_days_ago)->get();

        $view_multiple_time = LeadActivity::with('lead')->where('file_id', hashids_decode($id))->get();

        $currentDateTime = date('Y-m-d H:i:s');
        $oneWeekAgoDateTime = date('Y-m-d H:i:s', strtotime('-7 days'));
        $file_details = DB::select(DB::raw('
            SELECT
                COUNT(id) AS Total_Shared,
                SUM(total_views) AS opened,
                COUNT(CASE WHEN total_views = 0 THEN 1 ELSE NULL END) AS unopend,
                COUNT(CASE WHEN created_at BETWEEN :seven_days_ago AND :currentDateTime THEN 1 ELSE NULL END) AS viewed_in_last_7_days,
                COUNT(CASE WHEN total_views > 1 THEN 1 ELSE NULL END) AS viewed_multiple_times
            FROM `lead_activities`
            WHERE file_id = :file_id;'), [
            'seven_days_ago' => $seven_days_ago,
            'currentDateTime' => $currentDateTime,
            'file_id' => hashids_decode($id)
        ]);

        $data = [
            'breadcrumb_main' => 'File Manager',
            'breadcrumb' => 'File Detail',
            'title' => 'File Detail',
            'client_file' => $client_file,
            'get_file_activity' => $get_file_activity,
            'file_details' => $file_details[0],
            'viewed_last_7_days' => $viewed_in_last_7_days,
            // 'clients' => LeadClient::where('client_id', $auth_id)->get(['id', 'name', 'email', 'mobile_number']),
            'clients' => LeadClient::all(),

        ];
        return view('admin.file_manager.file_detail', $data);
    }

    public function delete_file($id)
    {
        if (Auth::user('admin')->can('file-manager-delete') != true) {
            abort(403, 'Unauthorized action.');
        }
        $data = ClientFiles::find($id);
        if (file_exists($data->file_path)) {
            @unlink($data->file_path);
        }
        $data->delete();

        return response()->json([
            'success' => 'File Deleted Successfully',
            'redirect' => route('admin.file_manager.view'),
        ]);
    }

    public function save_file(Request $request)
    {
        if (Auth::user('admin')->can('file-manager-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'file' => 'required|file',
        ];

        if ($request->send_to === 'single_multiple_users') {
            $rules['clients'] = 'required|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $client_file = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($client_file, PATHINFO_FILENAME);

        if ($request->send_to == 'single_multiple_users') {
            $users = User::whereIn('id', $request->clients)->get();
        } else {
            $users = User::get();
        }

        $main_file = fileManagerUploadFile($request->file('file'), 'uploads/clone/', 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain');

        if (is_array($main_file)) {
            return response()->json($main_file);
        }

        DB::beginTransaction();
        try {
            foreach ($users as $k => $val) {
                $client_main_folder = ClientFolder::where('client_id', $val->id)->whereNull('parent_folder_id')->latest()->first();
                if (!empty($client_main_folder)) {
                    $client_file = new ClientFiles;
                    $client_file->client_id = $val->id;
                    $client_file->main_folder_id = $client_main_folder->id;
                    $client_file->file_name = $filename;

                    $file_path = 'uploads/'.$client_main_folder->folder_name.'/';
                    $file_path = $file_path . date('Y') . '/';
                    if (!file_exists($file_path)) {
                        mkdir($file_path, 0755, true);
                    }
                    if (File::exists($main_file)) {
                        $fileName = basename($main_file);
                        $newPdfPath = $file_path . $fileName;
                        // Copy the file to the new destination
                        File::copy($main_file, public_path($newPdfPath));

                        $client_file->file_path = $newPdfPath;
                    }
                    $client_file->save();
                }

            }

            DB::commit();

            if (file_exists($main_file)) {
                @unlink($main_file);
            }

            return response()->json([
                'success' => 'File Uploaded Successfully',
                'reload' => true,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
