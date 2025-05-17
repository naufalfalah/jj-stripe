<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EmailTemplate;
use App\Models\LeadClient;
use App\Models\TempActivity;
use App\Models\LeadActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogHelper;
use App\Models\User;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user('admin')->can('email-template-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth('admin')->id();
        $activity = 'Email Template';
        $table = 'EmailTemplate';

        if ($request->ajax()) {
            if ($request->dropdown_value == 'all') {
                $query = EmailTemplate::query()
                    ->with('client')
                    ->latest();
            } else {
                $query = EmailTemplate::query()
                    ->with('client')
                    ->where(
                        'client_id',
                        hashids_decode($request->dropdown_value)
                    )
                    ->latest();
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function ($data) {
                    return $data->client->client_name;
                })
                ->addColumn('title', function ($data) {
                    return view('admin.email_template.include.name_td', [
                        'data' => $data,
                    ]);
                })
                ->addColumn('description', function ($data) {
                    return Str::limit($data->description ?? '-', 50); // Replace 50 with your desired character limit
                })
                ->addColumn('sent', function ($data) {
                    $count = $data->message_activity->count();
                    return $count > 0 ? $count . ' times' : '-';
                })
                ->addColumn('last_sent', function ($data) {
                    return $data->message_activity->count() > 0
                        ? $data->message_activity[0]->created_at->format(
                            'M d - h:i A'
                        )
                        : '-';
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(
                                ['title'],
                                request()->input('search')
                            );
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
                ->make(true);
        }
        $data = [
            'breadcrumb_main' => 'Email Template',
            'breadcrumb' => 'Email Template',
            'title' => 'Email Template',
            'template_count' => EmailTemplate::count(),
            'clients' => User::whereNotNull('email_verified_at')
                ->latest()
                ->get(['id', 'client_name', 'email']),
        ];

        return view('admin.email_template.index', $data);
    }

    public function save(Request $request)
    {
        if (
            Auth::user('admin')->can('email-template-write') != true ||
            Auth::user('admin')->can('email-template-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth('admin')->id();

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
            $email_template = new EmailTemplate();

            if ($request->id && !empty($request->id)) {
                $email_template = $email_template->findOrfail($request->id);
                // ActivityLogHelper::save_activity($auth_id,'Email Template Updated', 'EmailTemplate');
                if (isset($request->reopen) && !empty($request->reopen)) {
                    $msg = [
                        'success' => 'Email Template Updated Successfully',
                        'redirect' => route(
                            'admin.email-template.temp_details',
                            [
                                'id' => hashids_encode($request->id),
                                'send_email' => 'send_email',
                            ]
                        ),
                    ];
                } else {
                    $msg = [
                        'success' => 'Email Template Updated Successfully',
                        'reload' => true,
                    ];
                }

                if ($request->type == 'note') {
                    //  ActivityLogHelper::save_activity($auth_id,'Private Note save', 'EmailTemplate');

                    $email_template->private_note = $request->note;
                    $email_template->save();
                    DB::commit();

                    return response()->json([
                        'success' => 'Private Note Updated',
                        'reload' => true,
                    ]);
                }
            } else {
                //    ActivityLogHelper::save_activity($auth_id,'Email Template Save', 'EmailTemplate');
                $msg = [
                    'success' => 'Email Template Added Successfully',
                    'reload' => true,
                ];
            }

            $email_template->client_id = auth('admin')->id();
            $email_template->title = $request->title;
            $email_template->description = $request->template_message;
            $email_template->save();

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(
                ['error' => 'Error lead: ' . $e->getMessage()],
                500
            );
        }
    }

    public function email_tmp_details($id, $send_email = null)
    {
        if (Auth::user('admin')->can('email-template-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $email_template = EmailTemplate::with('message_activity')->hashidFind(
            $id
        );

        $auth_id = auth('admin')->id();
        if (!request()->ajax()) {
            // ActivityLogHelper::save_activity($auth_id,'Email Template Details', 'EmailTemplate');
        }

        $data = [
            'breadcrumb' =>
                '<span title="' .
                @$email_template->title .
                '">' .
                Str::limit(@$email_template->title, 25) .
                '</span>',
            'title' => 'Template Detail',
            'data' => @$email_template,
            'clients' => LeadClient::where(
                'client_id',
                auth('admin')->user()->id
            )->get(['id', 'name', 'email', 'mobile_number']),
            'send_email' => $send_email,
        ];
        return view('admin.email_template.temp_detail', $data);
    }

    public function delete($id)
    {
        if (Auth::user('admin')->can('email-template-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth('admin')->id();
        // ActivityLogHelper::save_activity($auth_id,'Email Template Delete', 'EmailTemplate');

        $email_template = EmailTemplate::hashidFind($id);
        $email_template->delete();

        return response()->json([
            'success' => 'Email Template Deleted Successfully',
            'redirect' => route('admin.email-template.all'),
        ]);
    }

    public function copy_temp(Request $request)
    {
        if (Auth::user('admin')->can('email-template-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth('admin')->id();
        // ActivityLogHelper::save_activity($auth_id,'Copy Email Template', 'EmailTemplate');

        DB::beginTransaction();
        try {
            $copy_email_template = new EmailTemplate();

            $copy_email_template->client_id = auth('admin')->id();
            $copy_email_template->title =
                'Copy of ' . $request->copy_data['title'];
            $copy_email_template->description =
                $request->copy_data['description'];
            if (
                isset($request->copy_data['private_note']) &&
                !empty($request->copy_data['private_note'])
            ) {
                $copy_email_template->private_note =
                    $request->copy_data['private_note'];
            }
            $copy_email_template->save();

            DB::commit();

            return response()->json([
                'success' => 'Email Template Copied Successfully',
                'redirect' => route(
                    'admin.email-template.temp_details',
                    $copy_email_template->hashid
                ),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(
                ['error' => 'Error lead: ' . $e->getMessage()],
                500
            );
        }
    }

    public function Send(Request $request)
    {
        if (Auth::user('admin')->can('email-template-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth('admin')->id();
        // ActivityLogHelper::save_activity($auth_id,'Email Template Send', 'EmailTemplate');

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
            $email_activity = new TempActivity();

            $email_activity->template_id = $request->temp_id;
            $email_activity->client_id = $request->lead_id;
            $email_activity->template_type = 'email';
            $email_activity->activity_route = 'admin';
            $email_activity->save();

            $email_lead_activity = new LeadActivity();

            $email_lead_activity->lead_client_id = $request->lead_id;
            $email_lead_activity->title = $request->title;
            $email_lead_activity->description = $request->template_message;
            $email_lead_activity->date_time = now()->format('Y-m-d h:i');
            $email_lead_activity->type = 'email';
            $email_lead_activity->email_template_id = $request->temp_id;
            $email_lead_activity->activity_route = 'web';
            $email_lead_activity->user_type = 'admin';
            $email_lead_activity->added_by_id = auth('admin')->user()->id;
            $email_lead_activity->save();

            $msg = [
                'success' => 'Email Activity Save Successfully',
                'redirect' => route(
                    'admin.email-template.temp_details',
                    hashids_encode($request->temp_id)
                ),
            ];

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(
                ['error' => 'Error Email Template: ' . $e->getMessage()],
                500
            );
        }
    }
}
