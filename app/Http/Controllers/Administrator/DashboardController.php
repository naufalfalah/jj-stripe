<?php

namespace App\Http\Controllers\Administrator;

use App\Constants\TaskConstant;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdsInvoice;
use App\Models\ChatMessage;
use App\Models\ClientTimelog;
use App\Models\FormActivity;
use App\Models\FormAssign;
use App\Models\FormData;
use App\Models\FormGroup;
use App\Models\FormRequest;
use App\Models\FormSubtask;
use App\Models\LeadClient;
use App\Models\MessageTemplate;
use App\Models\User;
use App\Models\SubAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\Project;
use App\Models\PushNotificationTemplate;
use App\Models\Transections;
use App\Models\UserDeviceToken;
use App\Services\CloudTalkService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role_name == 'admin' || $user->role_name == 'super_admin') {
            $data = [
                'breadcrumb' => 'Dashboard',
                'title' => 'Dashboard',
                'sub_account' => SubAccount::get()
            ];
            return view('admin.dashboard')->with($data);
        } elseif (
            $user->role_name == 'Wordpress Team' ||
            $user->role_name == 'Ads Designer Team' ||
            $user->role_name == 'Social Media Team' ||
            $user->role_name == 'Manager' ||
            str_contains($user->role_name, 'manager') ||
            str_contains($user->role_name, 'Manager')
        ) {
            return view('admin.task_screen.index', [
                'breadcrumb_main' => $user->role_name,
                'breadcrumb' => $user->role_name,
                'title' => $user->role_name,
                'users' => User::whereNotNull('google_access_token')->latest()->get(['id', 'client_name', 'email']),
                'groups' => FormGroup::all(),
                'priorities' => TaskConstant::getTaskPriorities(),
                'projects' => Project::all(),
                'clients' => User::all(),
                'admins' => Admin::where('team', $user->team)->get(),
                'yesterday' => Carbon::now()->subDay(2),
                'default_avatar' => asset('front/assets/images/avatars/avatar-13.png'),
                'messages' => [],
            ]);
        } elseif ($user->role_name == 'ISA Team') {
            $now = Carbon::now();
            $currentMonth = $now->month;
            $currentYear = $now->year;
            $services = new CloudTalkService();
            if ($request->ajax()) {
                return $this->callHistoryDatatable($request);
            }
            $client_groups = User::latest()->get();
            $data = [
                'breadcrumb_main' => 'Dashboard',
                'breadcrumb' => 'Dashboard',
                'title' => 'Dashboard',
                'total_clients' => User::count(),
                'new_clients' => User::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
                'total_request' => FormRequest::count(),
                'new_request' => FormRequest::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
                'total_leads' => LeadClient::count(),
                'new_leads' => LeadClient::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
                'total_msg_tmp' => MessageTemplate::count(),
                'new_msg_tmp' => MessageTemplate::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
                'card_call' => $services->getGroupStisticCard(),
                'tags' => $services->getTags(),
                'clients' => $client_groups,
            ];
            return view('admin.dashboard-isa')->with($data);
        }
    }

    // No longer used
    public function save_sub_account(Request $request)
    {
        $rules = [
            'sub_account_url' => 'required|url|unique:sub_accounts,sub_account_url',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $sub_account_save = new SubAccount;

        if (isset($request->sub_account_name) && !empty($request->sub_account_name)) {

            $sub_account_save->sub_account_name = $request->sub_account_name;

        } else {

            $parsed_url = parse_url($request->sub_account_url);
            $host_parts = explode('.', $parsed_url['host']);
            if (count($host_parts) >= 2) {
                $sub_account_name = count($host_parts) > 2 ? $host_parts[count($host_parts) - 2] : $host_parts[0];
            } else {
                $sub_account_name = $parsed_url['host'];
            }

            $sub_account_save->sub_account_name = $sub_account_name;
        }

        $sub_account_save->sub_account_url = $request->sub_account_url;
        $sub_account_save->save();

        return response()->json([
            'success' => 'Sub Account URL Add Successfully',
            'reload' => true,
        ]);
    }

    public function update_profile(Request $request)
    {
        $rules = [
            'name' => 'string|max:50',
            'email' => 'email',
        ];

        if ($request->hasFile('product_image')) {
            $rules['product_image'] = 'image|mimes:jpeg,png,jpg';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $user = Admin::find($request->id);
        $user->name = $request->name;
        if ($request->hasFile('profile_image')) {
            $profile_img = uploadSingleFile($request->file('profile_image'), 'uploads/profile_images/', 'png,jpeg,jpg');
            if (is_array($profile_img)) {
                return response()->json($profile_img);
            }
            if (file_exists($user->image)) {
                @unlink($user->image);
            }
            $user->image = $profile_img;
        }

        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'success' => 'Profile Updated Successfully',
            'reload' => true,
        ]);
    }

    public function edit_profile()
    {
        $data = [
            'breadcrumb_main' => 'User Profile',
            'breadcrumb' => 'User Profile',
            'title' => 'Profile',
            'edit' => Auth('admin')->user(),
        ];

        return view('admin.profile')->with($data);
    }

    public function save_device_token(Request $request)
    {

        $rules = [
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        UserDeviceToken::firstOrCreate(
            [
                'user_id' => auth('admin')->user()->id,
                'user_type' => 'admin',
                'device_token' => $request->device_token
            ]
        );

        return response()->json(true);
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', auth('admin')->id())->where('user_type', 'admin')->latest()->limit(100);
        $unread = Notification::where('user_id', auth('admin')->id())->where('user_type', 'admin')->where('is_read', 0)->count();
        return response()->json([
            'count' => $unread,
            'view_data' => view('components.include.admin_notification_list', ['notifications' => $notifications->get()])->render()
        ]);
    }

    public function update_notifications()
    {
        Notification::where('user_id', auth('admin')->id())->where('user_type', 'admin')->update(['is_read' => 1]);
        return response()->json(true);
    }

    public function update_sub_account_status($id, $status)
    {
        $sub = SubAccount::findOrFail($id);

        $sub->status = $status;
        $sub->save();

        return response()->json([
            'status' => $sub->status,
            'success' => 'The status has been updated successfully.',
            'reload' => true
        ]);
    }

    // No longer used
    public function subAccountShow(Request $request)
    {
        $data = [
            'breadcrumb' => 'Sub Account',
            'title' => 'Sub_Account',
        ];
        return view('admin.sub_account')->with($data);
    }

    public function view_progress($id, $subAccountId)
    {
        // dd(hashids_decode($id), hashids_decode($subAccountId));
        $data = [
            'breadcrumb_main' => 'Ads Requests',
            'breadcrumb' => 'All Ads Requests',
            'title' => 'All Ads Requests',
        ];

        return view('admin.running_ads.view_progress', compact('id', 'subAccountId'));
    }

    public function view_progress_datatable(Request $request, $id, $subAccountId)
    {
        $query = LeadClient::where('client_id', $subAccountId)->where('ads_id', $id)->get();

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('mobile_number', function ($data) {
                    return $data->mobile_number;
                })
                ->addColumn('user_type', function ($data) {
                    return $data->user_type;
                })
                // ->addColumn('action', function ($data) {
                //     return view('admin.client_management.include.ads_action_td', ['data' => $data]);
                // })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title', 'type'], request()->input('search'));
                        });
                    }
                })

                ->make(true);
        }
    }

    public function clientTimelog(Request $request)
    {
        if ($request->ajax()) {
            $timelogs = ClientTimelog::with(['admin', 'client'])->get();
            return DataTables::of($timelogs)
                ->addColumn('admin_name', function ($row) {
                    return $row->admin->name . ' ' . $row->admin->email;
                })
                ->addColumn('client_name', function ($row) {
                    return $row->client->client_name;
                })->make(true);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show_task(Request $request): JsonResponse
    {
        $taskId = $request->input('task_id');

        $formRequest = FormRequest::with('form_data', 'assigns', 'assigns.admin', 'subtasks', 'subtasks.admin', 'activities', 'activities.admin', 'activities.target')->find($request->input('task_id'));

        $formData = FormData::where('form_request_id', $formRequest->id)
            ->where(function ($query) {
                $query->where('f_key', 'upload_refrence')
                    ->orWhere('f_key', 'user_data_file');
            })->get();

        $formRequest['files'] = $formData;

        $userId = auth('admin')->user()->id;
        $type = Admin::class;
        $from = [
            ['form_request_id', $taskId],
            ['user_id', $userId],
            ['user_type', $type],
        ];

        $to = [
            ['form_request_id', $taskId],
            ['to_user_id', (int)$userId],
            ['to_user_type', $type]
        ];

        $messages = ChatMessage::with('user')->where($from);
        $first = $messages;
        if ($first->exists()) {
            ChatMessage::where($from)->update(['read_at' => now()]);
        }
        $messages->where($from)->orWhere($to)->orderBy('created_at', 'asc');
        $messageDatas = $messages->get();
        $groupedMessages = $messageDatas->groupBy(function ($item) {
            // Format the created_at field based on your requirements
            return $item->created_at->isToday()
                ? 'Today'
                : $item->created_at->format('d F Y');
        });
        $formattedMessages = [];
        $count = ChatMessage::where([
            ['form_request_id', $taskId],
        ])->whereNull('read_at')->count();
        $chats = ChatMessage::with('user')->where('form_request_id', $taskId)->get();
        $chatData = [];

        foreach ($chats as $chat) {
            $formattedCreatedAt = $chat->created_at->isToday()
                ? 'Today'
                : $chat->created_at->format('d F Y');
            $dateTime = Carbon::parse($chat->created_at);
            $timeFormatted = $dateTime->format('h:i A');
            $chatData[] = [
                'id' => $chat->user->id,
                'name' => $chat->user->full_name,
                'email' => $chat->user->email,
                'from_id' => $chat->from_user_id,
                'from_type' => $chat->from_user_type,
                'to_user_id' => $chat->to_user_id,
                'to_user_type' => $chat->to_user_type,
                'message' => $chat->message != '' || $chat->message != null ? $chat->message : '',
                'image' => $chat->attachments != null ? json_decode($chat->attachments) : null,
                'created_at' => $chat->created_at,
                'time' => $timeFormatted,
                'formatted_created_at' => $formattedCreatedAt,
                'type_name' => $chat->user->table_name,
                'count' => $count
            ];
        }

        foreach ($groupedMessages as $key => $group) {
            $formattedMessages[$key] = $chatData;
        }

        return response()->json([
            'data' => $formRequest,
            'messages' => $formattedMessages,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store_task(Request $request)
    {
        $request->merge([
            'to_team' => auth('admin')->user()->team,
        ]);
        FormRequest::create($request->all());
        
        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update_task(Request $request): JsonResponse
    {
        $formRequest = FormRequest::find($request->task_id);
        if ($request->order) {
            if ($formRequest->form_group_id != $request->form_group_id) {
                $formRequest->form_group_id = $request->form_group_id;

                $pushNotificationTemplate = PushNotificationTemplate::where('code', 'TM01')->first();
                if ($pushNotificationTemplate) {
                    $adminIds = $formRequest->assigns()->pluck('admin_id')->toArray();
                    send_push_notification_to_admin($pushNotificationTemplate->title, $pushNotificationTemplate->body, $adminIds, 'dashboard');
                }
            }
            $formRequest->order = $request->order;
        }

        $formActivity = new FormActivity();
        $formActivity = $formActivity->where('form_request_id', $request->task_id);
        $formActivity = $formActivity->where('admin_id', auth('admin')->user()->id);
        $formActivity = $formActivity->where('created_at', '>=', Carbon::now()->subMinutes(5));


        if ($request->client_id) {
            $client = User::find($request->client_id);
            $oldClient = $formRequest->clients->client_name;
            $newClient = $client->client_name;
            $formRequest->client_id = (int) $request->client_id;

            // Update Activity
            FormActivity::create([
                'form_request_id' => $request->task_id,
                'admin_id' => auth('admin')->user()->id,
                'description' => "changed client from $oldClient to $newClient",
                'field' => 'client',
                'target_id' => 0,
            ]);
        }
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            $oldProject = $formRequest->project->name;
            $newProject = $project->name;
            $formRequest->project_id = (int) $request->project_id;

            // Update Activity
            FormActivity::create([
                'form_request_id' => $request->task_id,
                'admin_id' => auth('admin')->user()->id,
                'description' => "changed project from $oldProject to $newProject",
                'field' => 'project',
                'target_id' => 0,
            ]);
        }
        if ($request->form_group_id) {
            $group = FormGroup::find($request->form_group_id);
            $oldGroup = $formRequest->formGroup->name;
            $newGroup = $group->name;
            $formRequest->form_group_id = (int) $request->form_group_id;

            // Update Activity
            $formActivity = $formActivity->where('field', 'priority')->first();
            FormActivity::create([
                'form_request_id' => $request->task_id,
                'admin_id' => auth('admin')->user()->id,
                'description' => "changed status from $oldGroup to $newGroup",
                'field' => 'group',
                'target_id' => 0,
            ]);

            // Push Notification & Notification
            $pushNotificationTempalte = PushNotificationTemplate::where('code', 'TM01')->first();
            if ($pushNotificationTempalte) {
                $adminIds = $formRequest->assigns()->pluck('admin_id')->toArray();
                send_push_notification_to_admin($pushNotificationTempalte->title, $pushNotificationTempalte->body, $adminIds, 'dashboard');
            }
        }
        if ($request->priority) {
            $oldPriority = $this->getPriorityLabel($formRequest->priority);
            $newPriority = $this->getPriorityLabel($request->priority);
            $formRequest->priority = (int) $request->priority;

            // Update Activity
            $formActivity = $formActivity->where('field', 'priority')->first();
            FormActivity::create([
                'form_request_id' => $request->task_id,
                'admin_id' => auth('admin')->user()->id,
                'description' => "changed priority from $oldPriority to $newPriority",
                'field' => 'priority',
                'target_id' => 0,
            ]);
        }
        if ($request->description) {
            $formRequest->description = $request->description;

            // Update Activity
            $formActivity = $formActivity->where('field', 'description')->first();
            if ($formActivity) {
                $formActivity->description = 'changed description';
                $formActivity->save();
            } else {
                FormActivity::create([
                    'form_request_id' => $request->task_id,
                    'admin_id' => auth('admin')->user()->id,
                    'description' => 'changed description',
                    'field' => 'description',
                    'target_id' => 0,
                ]);
            }
        }
        if ($request->assign) {
            $assignData = [
                'form_request_id' => $request->task_id,
                'admin_id' => $request->assign,
            ];

            $formAssign = FormAssign::where($assignData)->first();

            // Update Activity
            if ($formAssign) {
                $formAssign->delete();
                Notification::create([
                    'user_id' => $request->assign,
                    'title' => 'Task Unassigned',
                    'body' => "You unassigned form $formRequest->title",
                    'user_type' => 'admin',
                    'lead_url' => route('admin.home'),
                ]);

                FormActivity::create([
                    'form_request_id' => $request->task_id,
                    'admin_id' => auth('admin')->user()->id,
                    'description' => 'unassign ',
                    'field' => 'assign',
                    'target_id' => $request->assign,
                ]);
            } else {
                // Check available assign
                $availableFormAssign = FormAssign::where('form_request_id', $request->task_id)->first();
                if ($availableFormAssign) {
                    $availableFormAssign->delete();
                    FormActivity::create([
                        'form_request_id' => $request->task_id,
                        'admin_id' => auth('admin')->user()->id,
                        'description' => 'unassign ',
                        'field' => 'assign',
                        'target_id' => $availableFormAssign->admin_id,
                    ]);
                }
                // Create new assign
                FormAssign::create($assignData);
                Notification::create([
                    'user_id' => $request->assign,
                    'title' => 'Task Assigned',
                    'body' => "You assigned to $formRequest->title",
                    'user_type' => 'admin',
                    'lead_url' => route('admin.home'),
                ]);

                FormActivity::create([
                    'form_request_id' => $request->task_id,
                    'admin_id' => auth('admin')->user()->id,
                    'description' => 'assign ',
                    'field' => 'assign',
                    'target_id' => $request->assign,
                ]);
            }
        }
        if ($request->due_date) {
            if ($request->due_date == 'empty') {
                $formRequest->due_date = null;
            } else {
                $formRequest->due_date = $request->due_date;
            }

            // Update Activity
            $formActivity = $formActivity->where('field', 'due_date')->first();
            if ($formActivity) {
                $formActivity->description = "changed due date to $request->due_date";
                $formActivity->save();
            } else {
                FormActivity::create([
                    'form_request_id' => $request->task_id,
                    'admin_id' => auth('admin')->user()->id,
                    'description' => "changed due date to $request->due_date",
                    'field' => 'due_date',
                    'target_id' => 0,
                ]);
            }
        }

        $formRequest->save();
        return response()->json([
            'data' => $request->all(),
            'message' => 'Form request updated successfully'
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store_subtask(Request $request): JsonResponse
    {
        $formSubtask = new FormSubtask();
        $formSubtask->form_request_id = (int) $request->task_id;
        $formSubtask->priority = 3;
        $formSubtask->done = 0;
        $formSubtask->save();

        // Update Activity
        FormActivity::create([
            'form_request_id' => (int) $request->task_id,
            'admin_id' => auth('admin')->user()->id,
            'description' => 'created subtask',
            'field' => 'subtask',
            'target_id' => $formSubtask->id,
        ]);

        return response()->json([
            'data' => $formSubtask,
            'message' => 'Form request subtask created successfully'
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show_subtask(Request $request): JsonResponse
    {
        $subtaskId = $request->input('subtask_id');
        $formSubtask = FormSubtask::with('admin')->find($subtaskId);

        return response()->json([
            'data' => $formSubtask,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update_subtask(Request $request): JsonResponse
    {
        $formSubtask = FormSubtask::find((int) $request->subtask_id);

        if ($request->done || $request->done == 0) {
            $formSubtask->done = (int) $request->done;
        }
        if ($request->title) {
            $formSubtask->title = $request->title;
        }
        if ($request->admin_id) {
            if ($formSubtask->admin_id == (int) $request->admin_id) {
                Notification::create([
                    'user_id' => $formSubtask->admin_id,
                    'title' => 'Subtask Unassigned',
                    'body' => "You unassigned from $formSubtask->title",
                    'user_type' => 'admin',
                    'lead_url' => route('admin.home'),
                ]);
                $formSubtask->admin_id = null;
            } else {
                if ($formSubtask->admin_id) {
                    Notification::create([
                        'user_id' => $formSubtask->admin_id,
                        'title' => 'Subtask Unassigned',
                        'body' => "You unassigned from $formSubtask->title",
                        'user_type' => 'admin',
                        'lead_url' => route('admin.home'),
                    ]);
                }
                $formSubtask->admin_id = (int) $request->admin_id;
                Notification::create([
                    'user_id' => $formSubtask->admin_id,
                    'title' => 'Subtask Assigned',
                    'body' => "You assigned to $formSubtask->title",
                    'user_type' => 'admin',
                    'lead_url' => route('admin.home'),
                ]);
            }
        }
        if ($request->priority) {
            $formSubtask->priority = (int) $request->priority;
        }
        if ($request->due_date) {
            if ($request->due_date == 'empty') {
                $formSubtask->due_date = null;
            } else {
                $formSubtask->due_date = $request->due_date;
            }
        }

        $formSubtask->save();
        return response()->json([
            'data' => $request->all(),
            'message' => 'Form subtask updated successfully'
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function destroy_subtask(Request $request): JsonResponse
    {
        $formSubtask = FormSubtask::find((int) $request->subtask_id);

        // Update Activity
        FormActivity::create([
            'form_request_id' => (int) $formSubtask->form_request_id,
            'admin_id' => auth('admin')->user()->id,
            'description' => 'deleted subtask',
            'field' => 'subtask',
            'target_id' => $formSubtask->id,
        ]);

        $formSubtask->delete();
        return response()->json([
            'data' => $request->all(),
            'message' => 'Form subtask deleted successfully'
        ]);
    }

    /**
     * @param string $priority
     *
     * @return string $label
     */
    public function getPriorityLabel($priority): string
    {
        if ($priority == '1') {
            $label = 'urgent';
        } elseif ($priority == '2') {
            $label = 'high';
        } else {
            $label = 'normal';
        }

        return $label;
    }

    public function store_project(Request $request)
    {
        Project::create($request->all());

        return redirect()->back();
    }
}
