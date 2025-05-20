<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\MessageTemplate;
use App\Models\TempActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {

        $auth_id = auth('web')->id();
        $activity = 'Message Template';
        $table = 'MessageTemplate';
        ActivityLogHelper::save_activity_with_check($auth_id, $activity, $table);

        if ($request->ajax()) {
            return DataTables::of(MessageTemplate::query()->with('message_activity')->where('client_id', $auth_id)->latest())
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    return view('client.message_template.include.name_td', ['data' => $data]);
                })
                ->addColumn('description', function ($data) {
                    return Str::limit($data->description ?? '-', 50); // Replace 50 with your desired character limit
                })
                ->addColumn('sent', function ($data) {
                    $count = $data->message_activity->count();

                    return $count > 0 ? $count.' times' : '-';
                })
                ->addColumn('last_sent', function ($data) {
                    return $data->message_activity->count() > 0 ? $data->message_activity[0]->created_at->format('M d - h:i A') : '-';
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
            'breadcrumb_main' => 'Message Template',
            'breadcrumb' => 'Message Template',
            'title' => 'Message Template',
            'template_count' => MessageTemplate::where('client_id', $auth_id)->count(),
        ];

        return view('client.message_template.index', $data);
    }

    public function save(Request $request)
    {

        $auth_id = auth('web')->id();

        if ($request->type != 'note') {
            $rules = [
                'title' => 'required',
                'template_message' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $message_template = new MessageTemplate;
            if ($request->id && !empty($request->id)) {
                $message_template = $message_template->findOrfail($request->id);
                ActivityLogHelper::save_activity($auth_id, 'Message Template Updated', 'MessageTemplate');

                if (isset($request->reopen) && !empty($request->reopen)) {
                    $msg = [
                        'success' => 'Message Template Updated Successfully',
                        'redirect' => route('user.message-template.temp_details', ['id' => hashids_encode($request->id), 'send_message' => 'send_message']),
                    ];
                } else {
                    $msg = [
                        'success' => 'Message Template Updated Successfully',
                        'reload' => true,
                    ];
                }

                if ($request->type == 'note') {
                    ActivityLogHelper::save_activity($auth_id, 'Private Note message Template save', 'MessageTemplate');

                    $message_template->private_note = $request->note;
                    $message_template->save();
                    DB::commit();

                    return response()->json([
                        'success' => 'Private Note Updated',
                        'reload' => true,
                    ]);
                }
            } else {
                ActivityLogHelper::save_activity($auth_id, 'Message Template Save', 'MessageTemplate');

                $msg = [
                    'success' => 'Message Template Added Successfully',
                    'reload' => true,
                ];
            }

            $message_template->client_id = auth('web')->id();
            $message_template->title = $request->title;
            $message_template->description = $request->template_message;
            $message_template->save();

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

    public function msg_tmp_details($id, $send_message = null)
    {
        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Message Template Details', 'MessageTemplate');
        }

        $message_template = MessageTemplate::with('message_activity')->hashidFind($id);

        $data = [
            'breadcrumb' => '<span title="'.@$message_template->title.'">'.Str::limit(@$message_template->title, 25).'</span>',
            'title' => 'Template Detail',
            'data' => $message_template,
            'clients' => LeadClient::where('client_id', auth('web')->user()->id)->get(['id', 'name', 'email', 'mobile_number']),
            'send_message' => $send_message,
        ];

        return view('client.message_template.temp_detail', $data);
    }

    public function delete($id)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Message Template Delete', 'MessageTemplate');

        $msg_temp = MessageTemplate::hashidFind($id);
        $msg_temp->delete();

        return response()->json([
            'success' => 'Message Template Deleted Successfully',
            'redirect' => route('user.message-template.all'),
        ]);
    }

    public function copy_temp(Request $request)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Message Template Copy', 'MessageTemplate');

        DB::beginTransaction();
        try {
            $copy_message_template = new MessageTemplate;

            $copy_message_template->client_id = auth('web')->id();
            $copy_message_template->title = 'Copy of '.$request->copy_data['title'];
            $copy_message_template->description = $request->copy_data['description'];
            if (isset($request->copy_data['private_note']) && !empty($request->copy_data['private_note'])) {
                $copy_message_template->private_note = $request->copy_data['private_note'];
            }
            $copy_message_template->save();

            DB::commit();

            return response()->json([
                'success' => 'Message Template Copied Successfully',
                'redirect' => route('user.message-template.temp_details', $copy_message_template->hashid),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error lead: '.$e->getMessage()], 500);
        }
    }

    public function Send(Request $request)
    {

        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Message Template Send', 'MessageTemplate');

        // $rules = [
        //     'client' => 'required',
        // ];

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return ['errors' => $validator->errors()];
        // }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $message_activity = new TempActivity;

            $message_activity->template_id = $request->temp_id;
            $message_activity->client_id = $request->lead_id;
            $message_activity->template_type = 'message';
            $message_activity->activity_route = 'web';
            $message_activity->save();

            $message_lead_activity = new LeadActivity;

            $message_lead_activity->lead_client_id = $request->lead_id;
            $message_lead_activity->title = $request->title;
            $message_lead_activity->description = $request->template_message;
            $message_lead_activity->date_time = now()->format('Y-m-d h:i');
            $message_lead_activity->type = 'message';
            $message_lead_activity->message_template_id = $request->temp_id;
            $message_lead_activity->activity_route = 'web';
            $message_lead_activity->user_type = 'user';
            $message_lead_activity->added_by_id = auth('web')->user()->id;
            $message_lead_activity->save();

            $msg = [
                'success' => 'Message Activity Save Successfully',
                'redirect' => route('user.message-template.temp_details', hashids_encode($request->temp_id)),
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
}
