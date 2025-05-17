<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientFolder;
use App\Models\LeadActivity;
use App\Models\ClientFiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Helpers\ActivityLogHelper;
use App\Models\LeadClient;
use App\Models\PageTemplate;
use App\Models\TempActivity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FileManagerController extends Controller
{
    
    public function files_view(Request $request)
    {
        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'File Manager', 'ClientFolder');
        }


        if ($request->ajax()) {
            return DataTables::of(PageTemplate::query()->latest())
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    return view('client.page_management.include.name_td', ['data' => $data]);
                })
                ->addColumn('description', function ($data) {
                    return Str::limit($data->description ?? '-', 50);
                })
                ->addColumn('sent', function ($data) {
                    $count = $data->page_activity->count();
                    return $count > 0 ? $count . ' times' : '-';
                })
                ->addColumn('last_sent', function ($data) {
                    return $data->page_activity->count() > 0 ? $data->page_activity[0]->created_at->format('M d - h:i A') : '-';
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['title'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
                ->make(true);
        }

        $data = [
            'breadcrumb_main' => 'File Manager',
            'breadcrumb' => 'File Manager',
            'title' => 'File Manager',
            'folder_id' => ClientFolder::where('client_id', $auth_id)->latest()->first(),
            'all_folders' => ClientFolder::with('client_files')->where('client_id', $auth_id)->latest()->get(),
            'pages_count' => PageTemplate::all()->count(),
        ];
        return view('client.file_manager.index', $data);
    }

    public function get_client_files(Request $request)
    {
        if ($request->ajax()) {
            $auth_id = auth('web')->id();
            if (isset($request->folder_id) && !empty($request->folder_id)) {
                $folder_name = ClientFolder::where('id', $request->folder_id)->latest()->first();
                $folder_files = ClientFiles::where('folder_id', $request->folder_id)->latest()->get();
                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    'body' => view('client.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
                ]);
            } elseif (isset($request->request_id_input) && !empty($request->request_id_input)) {
                $get_folder_name = ClientFolder::where('id', hashids_decode($request->request_id_input))->latest()->first();
                if ($get_folder_name->parent_folder_id == '') {
                    $get_folder_name = ClientFolder::where('client_id', $auth_id)->where('parent_folder_id', null)->latest()->first();
                    $folder_files = ClientFiles::where('folder_id', $get_folder_name->id)->latest()->get();
                    $folder_name = "$get_folder_name->folder_name";
                } else {
                    // $folder_files = ClientFiles::where('folder_id', $get_folder_name->id)->latest()->get();
                    $folder_files = ClientFiles::where('folder_id', $get_folder_name->id)->latest()->get();
                    $folder_name = $get_folder_name->folder_name;
                }

                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    'folder_id' => $get_folder_name->id,
                    'body' => view('client.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
                ]);
            } else {

                $get_folder_name = ClientFolder::where('client_id', $auth_id)->where('parent_folder_id', null)->latest()->first();
                $folder_files = ClientFiles::where('client_id', $auth_id)->latest()->get();
                // dd($folder_files);
                $folder_name = 'All Files';
                return response([
                    'status' => true,
                    'folder_name' => $folder_name,
                    // 'folder_id'  => $get_folder_name->id,
                    'body' => view('client.file_manager.include.table_body', ['folder_files' => $folder_files])->render(),
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

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'File Detail', 'ClientFiles');
        }


        $seven_days_ago = Carbon::now()->subDays(7);

        // $client_file = ClientFiles::hashidfind($id);
        $client_file = ClientFiles::with('file_activity')->hashidfind($id);

        $get_file_activity = LeadActivity::with('file_lead')->where('file_id', hashids_decode($id))->latest()->get();
        // dd(\DB::getQueryLog());
        $viewed_in_last_7_days = LeadActivity::with('file_lead')->where('file_id', hashids_decode($id))->where('last_open', '>=', $seven_days_ago)->get();

        $view_multiple_time = LeadActivity::with('file_lead')->where('file_id', hashids_decode($id))->get();

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
            'clients' => LeadClient::where('client_id', $auth_id)->latest()->get(),

        ];
        return view('client.file_manager.file_detail', $data);
    }

    public function update_file_name(Request $request)
    {
        $auth_id = auth('web')->id();


        ActivityLogHelper::save_activity($auth_id, 'Update File Name', 'ClientFiles');



        $rules = [
            'file_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $client_file = ClientFiles::find($request->id);

        if ($client_file) {

            $activity_file = LeadActivity::where('file_id', $request->id)->get();

            if ($activity_file) {
                foreach ($activity_file as $file) {
                    $file->title = $request->file_name;
                    $file->save();
                }
            }

            $client_file->file_name = $request->file_name;
            $client_file->save();

            return response()->json([
                'success' => 'File Name Updated Successfully',
                'reload' => true,
            ]);
        }
    }

    public function save_folder(Request $request)
    {
        $auth_id = auth('web')->id();
        // dd($auth_id);

        ActivityLogHelper::save_activity($auth_id, 'Add Folder', 'ClientFolder');


        $rules = [
            'folder_name' => 'required|string|max:50',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $get_parent_folder = ClientFolder::where('client_id', auth('web')->user()->id)->where('parent_folder_id', null)->latest()->first();

        $client_folder = new ClientFolder;
        $client_folder->client_id = auth('web')->user()->id;
        $client_folder->parent_folder_id = $get_parent_folder->id;
        $client_folder->folder_name = $request->folder_name;
        $client_folder->save();

        $folder_name = $get_parent_folder->folder_name;
        $folderPath = public_path('uploads/' . $folder_name . '/' . $request->folder_name);

        return response()->json([
            'success' => 'Folder Add Successfully',
            'redirect' => route('user.file_manager.view'),
        ]);
    }


    public function save_file(Request $request)
    {
        $rules = [
            'file_name' => 'required|file',
            // 'new_file_name' => 'required|max:25',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $client_file = $request->file('file_name')->getClientOriginalName();
        
        // dd($client_file);

        if (isset($request->new_file_name) && !empty($request->new_file_name)) {
            $filename = $request->new_file_name;
        } else {
            $filename = pathinfo($client_file, PATHINFO_FILENAME);
        }

        $user_id = auth('web')->user()->id;

        $check_file = ClientFiles::where('client_id', $user_id)->where('file_name', $filename)->count();
        if ($check_file > 0) {
            return response()->json([
                'errors' => [
                    'file' => ['This file is alerdy uploaded']
                ]
            ]);
        }
        
        ActivityLogHelper::save_activity($user_id, 'Upload File', 'ClientFiles');


        $client_file = new ClientFiles;
        $client_file->file_name = $filename;
        $client_file->client_id = $user_id;

        if (isset($request->main_folder_id) && !empty($request->main_folder_id)) {
            $find_client_folder = ClientFolder::find($request->main_folder_id);
            $client_file->main_folder_id = $request->main_folder_id;
        } else {
            if (is_numeric($request->folder_id)) {
                $find_client_folder = ClientFolder::find($request->folder_id);
                $client_file->folder_id = $request->folder_id;
            } else {
                $find_client_folder = ClientFolder::where('client_id', auth('web')->user()->id)->where('parent_folder_id', null)->latest()->first();

                if (empty($find_client_folder)) {

                    $user_data = auth('web')->user();
                    $new_folder_name = $user_data->client_name.'-'.$user_data->id;

                    $new_client_folder = new ClientFolder;
                    $new_client_folder->client_id = auth('web')->user()->id;
                    $new_client_folder->folder_name = $new_folder_name;
                    $new_client_folder->parent_folder_id = null;
                    $new_client_folder->save();

                    $client_file->main_folder_id = $new_client_folder->id;
                } else {

                    $client_file->main_folder_id = $find_client_folder->id;
                }
            }
        }

        if (empty($find_client_folder)) {
            $client_folder = $new_client_folder->folder_name;

            $folder_name = $new_client_folder->folder_name;
            if ($client_folder == $folder_name) {
                $file = fileManagerUploadFile($request->file('file_name'), 'uploads/' . $folder_name . '/', 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain');
            } else {
                $file = fileManagerUploadFile($request->file('file_name'), 'uploads/' . $folder_name . '/' . $client_folder . '/', 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain');
            }
            if (is_array($file)) {
                return response()->json($file);
            }
            if (file_exists($client_file->file_path)) {
                @unlink($client_file->file_path);
            }
            $client_file->file_path = $file;
            $client_file->save();

            return response()->json([
                'success' => 'File Uploaded Successfully',
                'redirect' => route('user.file_manager.view', ['id' => hashids_encode($client_file->folder_id)]),
            ]);


        } else {
            $client_folder = $find_client_folder->folder_name;

            $folder_name = $find_client_folder->folder_name;
            if ($client_folder == $folder_name) {
                $file = fileManagerUploadFile($request->file('file_name'), 'uploads/' . $folder_name . '/', 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain');
            } else {
                $file = fileManagerUploadFile($request->file('file_name'), 'uploads/' . $folder_name . '/' . $client_folder . '/', 'png,jpeg,jpg,svg,csv,doc,docx,xls,xlsx,pdf,webp,zip,mp3,mp4,text/plain');
            }
            if (is_array($file)) {
                return response()->json($file);
            }
            if (file_exists($client_file->file_path)) {
                @unlink($client_file->file_path);
            }
            $client_file->file_path = $file;
            $client_file->folder_id = $find_client_folder->id;
            $client_file->save();

            return response()->json([
                'success' => 'File Uploaded Successfully',
                'redirect' => route('user.file_manager.view', ['id' => hashids_encode($client_file->folder_id)]),
            ]);
        }


    }


    public function delete_file($id)
    {
        $auth_id = auth('web')->user()->id;
        ActivityLogHelper::save_activity($auth_id, 'Delete File', 'ClientFiles');

        $data = ClientFiles::find($id);
        if (file_exists($data->file_path)) {
            @unlink($data->file_path);
        }
        $data->delete();

        return response()->json([
            'success' => 'File Deleted Successfully',
            'redirect' => route('user.file_manager.view', ['id' => hashids_encode($data->folder_id)]),
        ]);
    }

    public function send_file(Request $request)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'File Send', 'FileTemplate');
        DB::beginTransaction();

        try {
            $page_activity = new TempActivity();

            $page_activity->template_id = $request->file_id;
            $page_activity->client_id = hashids_decode($request->lead_id);
            $page_activity->template_type = 'file';
            $page_activity->activity_route = 'web';
            $page_activity->save();


            $page_lead_activity = new LeadActivity();

            $page_lead_activity->lead_client_id = hashids_decode($request->lead_id);
            $page_lead_activity->title = $request->title;
            $page_lead_activity->date_time = now()->format('Y-m-d h:i');
            $page_lead_activity->type = 'file';
            $page_lead_activity->file_id = $request->file_id;
            $page_lead_activity->activity_url = $request->activity_url;
            $page_lead_activity->activity_route = 'web';
            $page_lead_activity->user_type = 'user';
            $page_lead_activity->added_by_id = auth('web')->user()->id;
            $page_lead_activity->save();

            $msg = [
                'success' => 'Page Activity Save Successfully',
                'redirect' => route('user.file_manager.file_detail', hashids_encode($request->file_id)),
            ];

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error lead: ' . $e->getMessage()], 500);
        }
    }

    public function client_file_view($id, $client_id)
    {
        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'file Preview', 'FileTemplate');
        }
        $temp_activity = TempActivity::where('template_id', hashids_decode($id))
                            ->where('template_type', 'message')
                            ->where('client_id', hashids_decode($client_id))
                            ->latest()
                            ->first();

        if ($temp_activity) {  // Check if $temp_activity is not null
            $temp_activity->last_open = Carbon::now();
            $temp_activity->total_views += 1;
            $temp_activity->updated_at = now();
            $temp_activity->save();
        }

        $temp_lead_activity = LeadActivity::where('file_id', hashids_decode($id))
                                ->where('lead_client_id', hashids_decode($client_id))
                                ->latest()
                                ->first();

        if ($temp_lead_activity) {
            $temp_lead_activity->last_open = Carbon::now();
            $temp_lead_activity->total_views += 1;
            $temp_lead_activity->updated_at = now();
            $temp_lead_activity->save();
        }

        $file = ClientFiles::with('user')->hashidFind($id);

        $data = [
            'title' => 'File Preview',
            'data' => $file,
        ];

        return view('client.file_manager.file_preview', $data);
    }

    public function file_view($id)
    {
        $file = ClientFiles::with('user')->hashidFind($id);

        $data = [
            'title' => 'File Preview',
            'data' => $file,
        ];

        return view('client.file_manager.file_preview', $data);
    }
}
