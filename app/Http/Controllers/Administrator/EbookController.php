<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\EBook;
use App\Models\EbookActivity;
use Carbon\Carbon;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EBook::latest();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name ?? '';
                })
                ->addColumn('description', function ($data) {
                    return $data->description ?? '';
                })
                ->addColumn('updated_at', function ($data) {
                    return $data->updated_at ?? '';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="' . route('admin.ebook.edit', $data->hashid) . '" 
                                class="text-warning" data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="javascript:void(0)" class="text-danger" 
                                onclick="ajaxRequest(this)" 
                                data-url="' . route('admin.ebook.delete', $data->hashid) . '" 
                                data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                            <a href="javascript:void(0)" class="text-success copy-button fs-5" 
                                data-clipboard-text="' . route('ebook_file_view', ['web', $data->hashid]) . '" 
                                data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                title="Copy File Url">
                                <i class="fadeIn animated bx bx-copy"></i>
                            </a>
                            <a href="' . route('admin.ebook.details', $data->hashid) . '" 
                                class="text-primary fs-5" data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" title="View File Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </div>
                    ';
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike('customer_name', request()->input('search'));
                        });
                    }
                })
                ->make(true);
        }
        $data = [

            'breadcrumb' => 'All Customer',
            'title' => 'All Customer',
        ];
        return view('admin.ebook.index')->with($data);
    }

    public function add_ebook()
    {
        $data = [
            'breadcrumb_main' => 'Add EBook',
            'breadcrumb' => 'Add EBook',
            'title' => 'Add EBook',
        ];
        return view('admin.ebook.add')->with($data);
    }



    public function save_ebook(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg|max:2048';
        }

        // dd($request->file('image')->getMimeType());

        if ($request->hasFile('pdf')) {
            $rules['pdf'] = 'mimes:pdf,csv,docs,docx,xls,xlsx|max:2048';
        }

        // dd($request->file('pdf')->getMimeType());

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $ebook = new EBook();
        if (!empty($request->id)) {
            $ebook = EBook::find($request->id);

            if (!$ebook) {
                return response()->json(['errors' => ['id' => 'EBook not found.']]);
            }

            $msg = [
                'success' => 'EBook updated successfully',
                'redirect' => route('admin.ebook.all'),
            ];
        } else {
            $msg = [
                'success' => 'EBook added successfully',
                'redirect' => route('admin.ebook.all'),
            ];
        }

        $ebook->name = $request->name;
        $ebook->description = $request->description;

        if ($request->hasFile('image')) {
            $imagePath = uploadSingleFile($request->file('image'), 'uploads/profile_images/');
            if (is_array($imagePath)) {
                return response()->json($imagePath);
            }

            if (!empty($ebook->image) && file_exists(public_path($ebook->image))) {
                @unlink(public_path($ebook->image));
            }

            $ebook->image = $imagePath;
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = uploadSingleFile($request->file('pdf'), 'uploads/ebooks/');
            if (is_array($pdfPath)) {
                return response()->json($pdfPath);
            }

            if (!empty($ebook->pdf) && file_exists(public_path($ebook->pdf))) {
                @unlink(public_path($ebook->pdf));
            }

            $ebook->pdf = $pdfPath;
        }

        $ebook->save();

        return response()->json($msg);
    }



    public function edit($id)
    {
        $data = [
            'breadcrumb_main' => 'Edit Ebook',
            'breadcrumb' => 'Edit Ebook',
            'title' => 'Edit Ebook',
            'edit' => EBook::hashidFind($id),
        ];

        return view('admin.ebook.add', $data);
    }


    public function delete($id)
    {
        $ebook = EBook::hashidFind($id)->delete();
        return response()->json([
            'success' => 'EBook deleted successfully',
            'reload' => true,
        ]);
    }


    public function file_view($slug, $id)
    {
        $ebook = EBook::hashidFind($id);
        $localIp = gethostbyname(gethostname());

        $existingActivity = EbookActivity::where('ebook_id', $ebook->id)
            ->where('ip_address', $localIp)
            ->first();

        if ($existingActivity) {
            $lastOpenTime = $existingActivity->last_open;
            if (now()->diffInMinutes($lastOpenTime) >= 5) {
                $existingActivity->last_open = now();
                $existingActivity->total_views += 1;
                $existingActivity->save();
            }
        } else {
            $newActivity = new EbookActivity();
            $newActivity->ebook_id = $ebook->id;
            $newActivity->date_time = now();
            $newActivity->last_open = now();
            $newActivity->total_views = 1;
            $newActivity->activity_route = $slug;
            $newActivity->ip_address = $localIp;
            $newActivity->save();
        }

        $data = [
            'title' => 'EBook Preview',
            'data' => $ebook,
        ];

        return view('admin.ebook.file_preview', $data);
    }



    public function details($id)
    {

        $auth_id = auth('admin')->id();
        $seven_days_ago = Carbon::now()->subDays(7);
        $ebook = EBook::hashidfind($id);

        $total_views = intval(EbookActivity::where('ebook_id', hashids_decode($id))
        ->sum('total_views'));
        
        $get_file_activity = EbookActivity::where('ebook_id', hashids_decode($id))->latest()->get();

        $get_file_details = EbookActivity::with('ebook')->where('ebook_id', hashids_decode($id))->first();
        // dd( $get_file_details->ebook->name );
        $viewed_in_last_7_days = EbookActivity::where('ebook_id', hashids_decode($id))->where('last_open', '>=', $seven_days_ago)->get();

        $viewed_in_last_7_days_total = intval(EbookActivity::where('ebook_id', hashids_decode($id))
        ->where('last_open', '>=', $seven_days_ago)
        ->sum('total_views'));

        $view_multiple_time = LeadActivity::with('lead')->where('file_id', hashids_decode($id))->get();

        $currentDateTime = date('Y-m-d H:i:s');
        $oneWeekAgoDateTime = date('Y-m-d H:i:s', strtotime('-7 days'));
       
        $data = [
            'breadcrumb_main' => 'EBook File Details',
            'breadcrumb' => 'EBook File Details',
            'title' => 'EBook File Details',
            'client_file' => $ebook,
            'get_file_activity' => $get_file_activity,
            'viewed_last_7_days' => $viewed_in_last_7_days,
            'total_views' => $total_views,
            'viewed_in_last_7_days_total' => $viewed_in_last_7_days_total,
            'get_file_details' => $get_file_details
        ];
        return view('admin.ebook.file_detail', $data);
    }

}
