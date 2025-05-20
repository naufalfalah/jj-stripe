<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\PageTemplate;
use App\Models\PageTemplateGallery;
use App\Models\PageWebsiteLink;
use App\Models\PageYoutubeLink;
use App\Models\TempActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function add_page($id)
    {

        $page_name = ucfirst(str_replace('_', ' ', $id));
        $data = [
            'breadcrumb' => 'Add '.$page_name,
            'title' => 'Add '.$page_name,
            'page_name' => $id,
        ];

        return view('client.page_management.add_page', $data);
    }

    public function page_view(Request $request)
    {

        $auth_id = auth('web')->id();
        $activity = 'Page Template';
        $table = 'PageTemplate';
        ActivityLogHelper::save_activity_with_check($auth_id, $activity, $table);

        if ($request->ajax()) {
            return DataTables::of(PageTemplate::with('page_activity')->latest())
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    return view('client.page_management.include.name_td', ['data' => $data]);
                })
                ->addColumn('description', function ($data) {
                    return Str::limit($data->description ?? '-', 50);
                })
                ->addColumn('sent', function ($data) {
                    $count = $data->page_activity->count();

                    return $count > 0 ? $count.' times' : '-';
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
            'breadcrumb_main' => 'Page Template',
            'breadcrumb' => 'Page Template',
            'title' => 'Page Template',
            'pages_count' => PageTemplate::all()->count(),
        ];

        return view('client.page_management.index', $data);
    }

    public function page_save(Request $request)
    {
        if ($request->type != 'note') {
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website_title.*' => 'string|nullable',
                'website_link.*' => 'url|nullable',
                'youtube_title.*' => 'string|nullable',
                'youtube_link.*' => 'url|nullable',
                'pages_images.*' => 'file|mimes:png,jpeg,jpg|nullable',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }
        }

        DB::beginTransaction();

        try {

            if ($request->id && !empty($request->id)) {

                if ($request->type == 'note') {

                    $pageNote = PageTemplate::find($request->id);

                    ActivityLogHelper::save_activity(auth('web')->id(), 'Private Note Page save', 'PageTemplate');

                    $pageNote->private_note = $request->note;
                    $pageNote->save();
                    DB::commit();

                    return response()->json([
                        'success' => 'Private Note Updated',
                        'reload' => true,
                    ]);
                }

            } else {

                if (isset($request->edit_id) && !empty($request->edit_id)) {
                    $pageTemplate = PageTemplate::find($request->edit_id);
                    $msg = [
                        'success' => 'Page Updated Successfully',
                        'reload' => true,
                    ];
                } else {
                    $pageTemplate = new PageTemplate;
                    $msg = [
                        'success' => 'Page Created Successfully',
                        'reload' => true,
                    ];
                }

                $pageTemplate->client_id = auth('web')->id();
                $pageTemplate->title = $request->title;
                $pageTemplate->description = $request->description ?? null;
                $pageTemplate->google_maps = $request->google_location ?? null;
                $pageTemplate->save();

                if (isset($request->website_title) && is_array($request->website_title)) {
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageWebsiteLink::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->website_title as $key => $title) {
                        if (!empty($title) && !empty($request->website_link[$key])) {
                            $pageWebsiteLink = new PageWebsiteLink;
                            $pageWebsiteLink->page_id = $pageTemplate->id;
                            $pageWebsiteLink->link_title = $title;
                            $pageWebsiteLink->website_link = $request->website_link[$key];
                            $pageWebsiteLink->save();
                        }
                    }
                }

                if (isset($request->youtube_title) && is_array($request->youtube_title)) {  // Corrected isset typo
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageYoutubeLink::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->youtube_title as $key => $title) {
                        if (!empty($title) && !empty($request->youtube_link[$key])) {
                            $pageYoutubeLink = new PageYoutubeLink;
                            $pageYoutubeLink->page_id = $pageTemplate->id;
                            $pageYoutubeLink->link_title = $title;
                            $pageYoutubeLink->youtube_link = $request->youtube_link[$key];
                            $pageYoutubeLink->save();
                        }
                    }
                }

                if ($request->hasFile('pages_images')) {
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageTemplateGallery::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->file('pages_images') as $key => $image) {

                        $originalName = $image->getClientOriginalName();

                        $imgPath = uploadSingleFile($image, 'uploads/client_files/page_images/');

                        if ($key === 0) {
                            $pageTemplate->cover_image = $imgPath;
                            $pageTemplate->save();
                        }

                        $pageTemplateGallery = new PageTemplateGallery;
                        $pageTemplateGallery->page_id = $pageTemplate->id;
                        $pageTemplateGallery->title = $originalName;
                        $pageTemplateGallery->images = $imgPath;
                        $pageTemplateGallery->save();
                    }
                }

                ActivityLogHelper::save_activity(auth('web')->id(), 'Page Creation', 'PageTemplate');
            }

            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Page Creation: '.$e->getMessage()], 500);
        }
    }

    public function page_details($id)
    {
        $auth_id = auth('web')->id();
        $seven_days_ago = Carbon::now()->subDays(7);

        $page_template = PageTemplate::with('page_activity')->hashidFind($id);

        $get_page_activity = TempActivity::with('lead_client')->where('template_id', $page_template->id)->get();

        $viewed_in_last_7_days = TempActivity::with('lead_client')->where('template_id', $page_template->id)->where('last_open', '>=', $seven_days_ago)->get();

        $view_multiple_time = TempActivity::with('lead_client')->where('template_id', $page_template->id)->get();

        $currentDateTime = date('Y-m-d H:i:s');
        $oneWeekAgoDateTime = date('Y-m-d H:i:s', strtotime('-7 days'));
        $page_details = DB::select(DB::raw('
            SELECT
                COUNT(id) AS Total_Shared,
                SUM(total_views) AS opened,
                COUNT(CASE WHEN total_views = 0 THEN 1 ELSE NULL END) AS unopend,
                SUM(CASE WHEN created_at BETWEEN :seven_days_ago AND :currentDateTime THEN total_views ELSE 0 END) AS viewed_in_last_7_days,
                COUNT(CASE WHEN total_views > 1 THEN 1 ELSE NULL END) AS viewed_multiple_times
            FROM `temp_activities`
            WHERE template_id = :template_id;'), [
            'seven_days_ago' => $seven_days_ago,
            'currentDateTime' => $currentDateTime,
            'template_id' => hashids_decode($id),
        ]);
        // dd($page_details);
        $data = [
            'breadcrumb' => $page_template->title,
            'title' => 'Page Detail',
            'data' => $page_template,
            'page_details' => $page_details[0],
            'clients' => LeadClient::where('client_id', auth('web')->user()->id)->get(['id', 'name', 'email', 'mobile_number']),
            'get_page_activity' => $get_page_activity,
            'viewed_last_7_days' => $viewed_in_last_7_days,
        ];

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Page Details', 'PageTemplate');
        }

        return view('client.page_management.page_detail', $data);
    }

    public function page_preview($id)
    {

        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Page Preview', 'PageTemplate');
        }

        $page = PageTemplate::with('galleries', 'page_website_links', 'page_youtube_links')->hashidFind($id);

        $data = [
            'title' => 'Page Preview',
            'data' => $page,
        ];

        return view('client.page_management.page_preview', $data);
    }

    public function delete_page($id)
    {

        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Delete Page', 'PageTemplate');
        }

        $page = PageTemplate::hashidFind($id);
        $pageWebsiteLink = PageWebsiteLink::where('page_id', $page->id)->delete();
        $PageYoutubeLink = PageYoutubeLink::where('page_id', $page->id)->delete();
        $PageTemplateGallery = PageTemplateGallery::where('page_id', $page->id)->delete();

        $page->delete();

        return response()->json([
            'success' => 'Page Deleted Successfully',
            'redirect' => route('user.file_manager.view'),
        ]);

    }

    public function Send(Request $request)
    {

        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Page Send', 'PageTemplate');

        $rules = [
            'client' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $page_activity = new TempActivity;

            $page_activity->template_id = $request->page_id;
            $page_activity->client_id = $request->lead_id;
            $page_activity->template_type = 'page';
            $page_activity->activity_route = 'web';
            $page_activity->save();

            $page_lead_activity = new LeadActivity;

            $page_lead_activity->lead_client_id = $request->lead_id;
            $page_lead_activity->title = $request->title;
            $page_lead_activity->date_time = now()->format('Y-m-d h:i');
            $page_lead_activity->type = 'message';
            $page_lead_activity->page_id = $request->page_id;
            $page_lead_activity->activity_url = $request->activity_url;
            $page_lead_activity->activity_route = 'web';
            $page_lead_activity->user_type = 'user';
            $page_lead_activity->added_by_id = auth('web')->user()->id;
            $page_lead_activity->save();

            $msg = [
                'success' => 'Page Activity Save Successfully',
                'redirect' => route('user.page.page_details', hashids_encode($request->page_id)),
            ];

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error lead: '.$e->getMessage()], 500);
        }
    }

    public function client_page_view($id, $client_id)
    {
        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Page Preview', 'PageTemplate');
        }

        $temp_activity = TempActivity::where('template_id', hashids_decode($id))->where('template_type', 'page')->where('client_id', hashids_decode($client_id))->latest()->first();

        $temp_activity->last_open = Carbon::now();
        $temp_activity->total_views += 1;
        $temp_activity->updated_at = now();
        $temp_activity->save();

        $temp_lead_activity = LeadActivity::where('page_id', hashids_decode($id))->where('lead_client_id', hashids_decode($client_id))->latest()->first();
        $temp_lead_activity->last_open = Carbon::now();
        $temp_lead_activity->total_views += 1;
        $temp_lead_activity->updated_at = now();
        $temp_lead_activity->save();

        $page = PageTemplate::with('galleries', 'page_website_links', 'page_youtube_links', 'user')->hashidFind($id);

        $data = [
            'title' => 'Page Preview',
            'data' => $page,
        ];

        return view('client.page_management.page_preview', $data);
    }

    public function edit_page($id)
    {

        $page = PageTemplate::with('galleries', 'page_website_links', 'page_youtube_links', 'user')->hashidFind($id);

        if (isset($page->description) && !empty($page->description)) {

            $data = [
                'breadcrumb' => 'Edit Event Page',
                'title' => 'Edit Event Page',
                'edit' => $page,
                'page_name' => 'event_page',
            ];

            return view('client.page_management.add_page', $data);
        } else {

            $data = [
                'breadcrumb' => 'Edit Image Gallery',
                'title' => 'Edit Image Gallery',
                'edit' => $page,
                'page_name' => 'image_gallery',
            ];

            return view('client.page_management.add_page', $data);
        }

    }
}
