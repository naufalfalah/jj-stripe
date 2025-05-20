<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Imports\AdminLeadImport;
use App\Jobs\SendDiscordMessage;
use App\Models\Ads;
use App\Models\Group;
use App\Models\LeadActivity;
use App\Models\LeadActivityAttachments;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\LeadGroup;
use App\Models\LeadSource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ClientLeadManagmentController extends Controller
{
    private function send_discord_msg($url, $data)
    {
        $post_array = [
            'content' => $data,
            'embeds' => null,
            'attachments' => [],
        ];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_array),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Cookie: __dcfduid=8ec71370974011ed9aeb96cee56fe4d4; __sdcfduid=8ec71370974011ed9aeb96cee56fe4d49deabe12bc0fc3d686d23eaa0b49af957ffe68eadec722cff5170d5c750b00ea',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function client_leads(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $seven_days_ago = Carbon::now()->subDays(7);
        if (isset($request->client) && !empty($request->client)) {

            $auth_id = hashids_decode($request->client);

            $query = LeadClient::query()->with('activity', 'clients', 'lead_groups.group', 'assign')
                ->where('client_id', $auth_id);

            return response()->json($query);
            $todayDate = now()->format('Y-m-d');
            $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');
            $seven_days_ago = Carbon::now()->subDays(7);

            if ($request->ajax()) {
                $auth_id = hashids_decode($request->client);
                if ($request->type == 'all_leads') {

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    if (!empty($request->group_id)) {
                        $query = $query->whereHas('lead_groups', function ($q) use ($request) {
                            $q->where('group_id', $request->group_id);
                        });
                    }

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('check_boxes', function ($data) {
                            return view('admin.client_leads_management.include.select_box', ['data' => $data]);
                        })
                        ->addColumn('assign_status', function ($data) {
                            return view('admin.client_leads_management.include.assign_isa', ['data' => $data]);
                        })
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('client_name', function ($data) {
                            return $data->clients->client_name ?? '';
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('latest_activity', function ($data) {
                            if ($data->activity()->count() > 0) {
                                $last = ($data->activity()->count() - 1);

                                return $data->activity[$last]->created_at->diffForHumans();
                            } else {
                                return '-';
                            }
                        })
                        ->addColumn('date_added', function ($data) {
                            return $data->created_at->format('M-d-Y - h:i a');
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                } elseif ($request->type == 'uncontacted_leads') {

                    $query = LeadClient::query()->with('activity')
                        ->where('client_id', $auth_id)->where('status', 'uncontacted');

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('source', function ($data) {
                            return $data->lead_source->name ?? '';
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('date_added', function ($data) {
                            return $data->created_at->format('M-d-Y - h:i a');
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                } elseif ($request->type == 'recently_viewed_leads') {

                    $query = LeadClient::where('client_id', $auth_id)
                        ->whereHas('activity', function ($query) use ($seven_days_ago) {
                            $query->where('last_open', '>=', $seven_days_ago);
                        })->with(['activity' => function ($query) use ($seven_days_ago) {
                            $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                        }]);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('viewed_item', function ($data) {
                            $latestActivity = $data->activity->first();

                            return $latestActivity ? Str::limit($latestActivity->title, 25, '...') : 'No activity';
                        })
                        ->addColumn('last_viewed', function ($data) {

                            $latestActivity = $data->activity->first();

                            return $latestActivity ? $latestActivity->last_open->format('M-d-Y - h:i a') : 'No activity';
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                }
            }

            $due_today = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', now()->format('Y-m-d'));

            $up_coming = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', now()->format('Y-m-d'));

            $over_due = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', now()->format('Y-m-d'));

            $client_groups = Group::where('client_id', $auth_id)->latest()->get();

            $data = [
                'breadcrumb_main' => 'Lead Management',
                'breadcrumb' => 'Clients Leads Management',
                'title' => 'Clients Leads Management',
                'due_today' => $due_today->count(),
                'up_coming' => $up_coming->count(),
                'over_due' => $over_due->count(),
                'due_someday' => $up_coming->count(),
                'lead_source' => LeadSource::get(),
                'client_groups' => $client_groups,
                'clients' => User::whereNotNull('email_verified_at')->latest()->get(['id', 'client_name', 'email']),
                'leads_count' => LeadClient::where('client_id', $auth_id)->count(),
                'uncontacted_leads_count' => LeadClient::where('client_id', $auth_id)->where('status', 'uncontacted')->count(),
                'followup_leads_count' => LeadClient::where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->count(),
                'recently_viewed_count' => LeadClient::where('client_id', $auth_id)->whereHas('activity', function ($query) use ($seven_days_ago) {
                    $query->where('last_open', '>=', $seven_days_ago);
                })
                    ->with(['activity' => function ($query) use ($seven_days_ago) {
                        $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                    }])->count(),
            ];
        } else {

            $todayDate = now()->format('Y-m-d');
            $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');

            if ($request->ajax()) {

                if ($request->type == 'all_leads') {
                    // dd($request->type);
                    $query = LeadClient::query()->with('activity', 'lead_groups.group', 'clients', 'assign');

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    if (!empty($request->group_id)) {
                        $query = $query->whereHas('lead_groups', function ($q) use ($request) {
                            $q->where('group_id', $request->group_id);
                        });
                    }

                    $data = $query->latest()->get();

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('check_boxes', function ($data) {
                            return view('admin.client_leads_management.include.select_box', ['data' => $data]);
                        })
                        ->addColumn('assign_status', function ($data) {
                            return view('admin.client_leads_management.include.assign_isa', ['data' => $data]);
                        })
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('client_name', function ($data) {
                            return optional($data->clients)->client_name;
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('latest_activity', function ($data) {
                            if ($data->activity()->count() > 0) {
                                $last = ($data->activity()->count() - 1);

                                return $data->activity[$last]->created_at->diffForHumans();
                            } else {
                                return '-';
                            }
                        })
                        ->addColumn('date_added', function ($data) {
                            return $data->created_at->format('M-d-Y - h:i a');
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                } elseif ($request->type == 'uncontacted_leads') {

                    $query = LeadClient::query()->with('activity')->where('status', 'uncontacted');

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('source', function ($data) {
                            return $data->lead_source->name ?? '';
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('date_added', function ($data) {
                            return $data->created_at->format('M-d-Y - h:i a');
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                } elseif ($request->type == 'recently_viewed_leads') {

                    $query = LeadClient::whereHas('activity', function ($query) use ($seven_days_ago) {
                        $query->where('last_open', '>=', $seven_days_ago);
                    })->with(['activity' => function ($query) use ($seven_days_ago) {
                        $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                    }]);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $query = $query->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $query->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $query->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }

                    if (isset($request->isa_status) && !empty($request->isa_status)) {
                        if ($request->isa_status === 'assign') {
                            $query->whereHas('assign', function ($query) {});
                        }
                        if ($request->isa_status === 'unassign') {
                            $query->whereDoesntHave('assign', function ($query) {});
                        }
                    }

                    return DataTables::of($query->latest())
                        ->addIndexColumn()
                        ->addColumn('name', function ($data) {
                            return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                        })
                        ->addColumn('details', function ($data) {
                            return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        })
                        ->addColumn('viewed_item', function ($data) {
                            $latestActivity = $data->activity->first();

                            return $latestActivity ? Str::limit($latestActivity->title, 25, '...') : 'No activity';
                        })
                        ->addColumn('last_viewed', function ($data) {

                            $latestActivity = $data->activity->first();

                            return $latestActivity ? $latestActivity->last_open->format('M-d-Y - h:i a') : 'No activity';
                        })
                        ->filter(function ($query) {
                            if (request()->input('search')) {
                                $query->where(function ($search_query) {
                                    $search_query->whereLike(['name'], request()->input('search'));
                                });
                            }
                        })
                        ->orderColumn('DT_RowIndex', function ($q, $o) {
                            $q->orderBy('id', $o);
                        })
                        ->make(true);
                }
            }

            $due_today = LeadClient::query()->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', now()->format('Y-m-d'));

            $up_coming = LeadClient::query()->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', now()->format('Y-m-d'));

            $over_due = LeadClient::query()->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', now()->format('Y-m-d'));

            $client_groups = Group::latest()->get();
            $data = [
                'breadcrumb_main' => 'Lead Management',
                'breadcrumb' => 'Clients Leads Management',
                'title' => 'Clients Leads Management',
                'due_today' => $due_today->count(),
                'up_coming' => $up_coming->count(),
                'over_due' => $over_due->count(),
                'due_someday' => $up_coming->count(),
                'client_groups' => $client_groups,
                'lead_source' => LeadSource::get(),
                'clients' => User::whereNotNull('email_verified_at')->latest()->get(['id', 'client_name', 'email']),
                'uncontacted_leads_count' => LeadClient::where('status', 'uncontacted')->count(),
                'followup_leads_count' => LeadClient::whereNotNull('follow_up_date_time')->count(),
                'recently_viewed_count' => LeadClient::whereHas('activity', function ($query) use ($seven_days_ago) {
                    $query->where('last_open', '>=', $seven_days_ago);
                })
                    ->with(['activity' => function ($query) use ($seven_days_ago) {
                        $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                    }])->count(),
            ];
        }

        return view('admin.client_leads_management.index', $data);
    }

    public function client_details($id)
    {
        if (Auth::user('admin')->can('lead-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $client = LeadClient::with('activity', 'lead_data', 'lead_groups.group')->hashidFind($id);

        $client_groups = Group::where('client_id', $client->client_id)->latest()->get();

        $phoneNumber = config('services.2chat.phone_number');
        $toPhoneNumber = $client->mobile_number;

        $data = [
            'breadcrumb' => $client->name,
            'title' => 'Client Details',
            'data' => $client,
            'client_groups' => $client_groups,
            'client_id' => $client->client_id,
            'phone_number' => $phoneNumber,
            'to_phone_number' => $toPhoneNumber,
        ];

        return view('admin.client_leads_management.client_details', $data);
    }

    public function save(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true || Auth::user('admin')->can('lead-management-update') != true) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->type != 'note') {
            $rules = [
                'client_name' => 'required',
                'email' => 'required',
                'mobile_number' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $client_lead = new LeadClient;

            if ($request->id && !empty($request->id)) {
                $client_lead = $client_lead->findOrfail($request->id);
                $client_lead->client_id = $request->client_id;
                $msg = [
                    'success' => 'Lead Updated Successfully',
                    'reload' => true,
                ];

                if ($request->type == 'note') {
                    $rules = [
                        'note' => [
                            'required',
                            function ($attribute, $value, $fail) {
                                if (trim($value) === '') {
                                    $fail('The '.$attribute.' field cannot contain only spaces.');
                                }
                            },
                        ],
                    ];

                    $validator = Validator::make($request->all(), $rules);

                    if ($validator->fails()) {
                        return ['errors' => $validator->errors()];
                    }

                    $client_lead->note = $request->note;
                    $client_lead->save();

                    DB::commit();

                    return response()->json([
                        'success' => 'Note Info Updated',
                        'reload' => true,
                    ]);
                }
            } else {
                $client_lead->client_id = hashids_decode($request->client_id);
                $client_lead->user_type = 'admin';
                $client_lead->added_by_id = auth('admin')->id();
                $msg = [
                    'success' => 'Lead Added Successfully',
                    'reload' => true,
                ];
            }

            $client_lead->name = $request->client_name;
            $client_lead->email = $request->email;
            $client_lead->mobile_number = $request->mobile_number;
            $client_lead->save();

            $lead_data = [];
            if (!empty($request->data) && count($request->data) > 0) {
                if ($request->id && !empty($request->id)) {
                    $client_lead->lead_data()->delete();
                }
                foreach ($request->data as $k => $val) {
                    $lead_data[] = [
                        'lead_client_id' => $client_lead->id,
                        'key' => $val['key'],
                        'value' => $val['value'],
                        'user_type' => 'admin',
                        'added_by_id' => auth('admin')->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                LeadData::insert($lead_data);
            }

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

    public function group_save(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        $client_id = $request->client_id;
        if (isset($request->edit_group_id) && !empty($request->edit_group_id)) {
            $rules = [
                'group_name' => 'required|unique:groups,group_title,'.$request->edit_group_id.',id,client_id,'.$client_id,
            ];
        } else {
            $rules = [
                'group_name' => 'required|unique:groups,group_title,NULL,id,client_id,'.$client_id,
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            if (isset($request->edit_group_id) && !empty($request->edit_group_id)) {
                $client_group = Group::find($request->edit_group_id);
                $client_group->client_id = $client_id;
                $client_group->group_title = $request->group_name;
                $client_group->background_color = $request->edit_group_colour;
                $client_group->save();
                $msg = [
                    'success' => 'Group Updated Successfully',
                    'reload' => true,
                ];
            } else {
                $client_group = new Group;
                $client_group->client_id = $client_id;
                $client_group->group_title = $request->group_name;
                $client_group->background_color = $request->group_colour;
                $client_group->user_type = 'admin';
                $client_group->added_by_id = auth('admin')->id();
                $client_group->save();
                $msg = [
                    'success' => 'Group Added Successfully',
                    'reload' => true,
                ];
            }

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Group: '.$e->getMessage()], 500);
        }
    }

    public function group_lead_save(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->ajax()) {
            $lead_id = hashids_decode($request->group_lead_id);

            // Start a database transaction
            DB::beginTransaction();
            try {
                if (!empty($request->groups)) {
                    $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->get();
                    foreach ($delete_lead_groups as $lead_group) {
                        $lead_group->delete_by_type = 'admin';
                        $lead_group->delete_by_id = auth('admin')->id();
                        $lead_group->save();
                        $lead_group->delete();
                    }
                    // $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->delete();
                    foreach ($request->groups as $lead_group) {
                        $client_lead_group = new LeadGroup;
                        $client_lead_group->group_id = $lead_group;
                        $client_lead_group->lead_id = $lead_id;
                        $client_lead_group->user_type = 'admin';
                        $client_lead_group->added_by_id = auth('admin')->id();
                        $client_lead_group->save();
                    }
                } else {
                    $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->get();
                    foreach ($delete_lead_groups as $lead_group) {
                        $lead_group->delete_by_type = 'admin';
                        $lead_group->delete_by_id = auth('admin')->id();
                        $lead_group->save();
                        $lead_group->delete();
                    }
                    // $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->delete();
                }
                // Commit the transaction
                DB::commit();
            } catch (\Exception $e) {
                // Something went wrong, rollback the transaction
                DB::rollback();

                // Log or handle the exception as needed
                return response()->json(['error' => 'Error Group: '.$e->getMessage()], 500);
            }
        }
    }

    public function delete_group($id)
    {
        if (Auth::user('admin')->can('lead-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }
        $group = Group::findOrfail($id);
        $group->delete_by_type = 'admin';
        $group->delete_by_id = auth('admin')->id();
        $group->save();
        $lead_group = LeadGroup::where('group_id', $id)->get();
        foreach ($lead_group as $lead_group) {
            $lead_group->delete_by_type = 'admin';
            $lead_group->delete_by_id = auth('admin')->id();
            $lead_group->save();
            $lead_group->delete();
        }
        $group->delete();

        return response()->json([
            'success' => 'Group Log Deleted',
            'reload' => true,
        ]);
    }

    public function import_file(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'upload_file' => 'required|mimes:xlsx,xls|max:25600',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $client_id = hashids_decode($request->client_id);
        Excel::import(new AdminLeadImport($client_id), $request->file('upload_file'));

        return response()->json([
            'success' => 'File Uploaded Successfully',
            'reload' => true,
        ]);
    }

    public function delete($id, $is_reload = null)
    {
        if (Auth::user('admin')->can('lead-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }
        $client = LeadClient::with('activity', 'lead_data', 'lead_groups')->hashidFind($id);
        $client->delete_by_type = 'admin';
        $client->delete_by_id = auth('admin')->id();
        $client->save();
        $activity_ids = $client->activity()->pluck('id');
        if (!empty($activity_ids)) {
            LeadActivity::whereIn('id', $activity_ids)->update([
                'delete_by_type' => 'admin',
                'delete_by_id' => auth('admin')->id(),
            ]);
        }

        $lead_data_ids = $client->lead_data()->pluck('id');
        if (!empty($lead_data_ids)) {
            LeadData::whereIn('id', $lead_data_ids)->update([
                'delete_by_type' => 'admin',
                'delete_by_id' => auth('admin')->id(),
            ]);
        }

        $lead_group_ids = $client->lead_groups()->pluck('id');
        if (!empty($lead_group_ids)) {
            LeadGroup::whereIn('lead_id', $lead_group_ids)->update([
                'delete_by_type' => 'admin',
                'delete_by_id' => auth('admin')->id(),
            ]);
        }

        $client->activity()->delete();
        $client->lead_data()->delete();
        $client->delete();

        if ($is_reload == 1) {
            return response()->json([
                'success' => 'Client Deleted Successfully',
                'reload' => true,
            ]);
        } else {
            return response()->json([
                'success' => 'Client Deleted Successfully',
                'redirect' => route('admin.lead-management.all'),
            ]);
        }
    }

    public function activity_save(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->activity_type == 'attachment') {
            if (!isset($request->old_file_id) && $request->old_file_id == null) {
                if (empty($request->attachments)) {
                    return response()->json([
                        'errors' => [
                            'attachmentss' => ['The attachmentss field is required.'],
                        ],
                    ]);
                }
            }
            if (!empty($request->attachments)) {
                $filteredAttachments = array_filter($request->attachments, function ($item) {
                    // Check if the item is exactly the string "[object Object]"
                    return $item !== '[object Object]';
                });
                $filteredAttachments = array_values($filteredAttachments);
                if ($request->id && !empty($request->id)) {
                    if (!isset($request->old_file_id) && count($filteredAttachments) == 0) {
                        return response()->json([
                            'errors' => [
                                'attachmentss' => ['The attachmentss field is required.'],
                            ],
                        ]);
                    }
                }
            }
        }
        $rules = [
            'activity_type' => 'required',
            'title' => 'required',
            'date_time' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $activity = new LeadActivity;
        if ($request->id && !empty($request->id)) {
            $activity = $activity->findOrfail($request->id);
            $msg = [
                'success' => 'Activity Log Updated',
                'reload' => true,
            ];

            if ($request->activity_type != 'attachment' && empty($request->old_file_id)) {
                DB::table('lead_activity_attachments')
                    ->where('activity_id', $request->id)
                    ->delete();
            }
            if (!empty($request->old_file_id)) {
                DB::table('lead_activity_attachments')
                    ->where('activity_id', $request->id)
                    ->whereNotIn('id', $request->old_file_id)
                    ->delete();
                // unset($request->attachments[0]);
            }
        } else {
            $activity->user_type = 'admin';
            $activity->added_by_id = auth('admin')->id();
            $msg = [
                'success' => 'Activity Log Added',
                'reload' => true,
            ];
        }

        DB::beginTransaction();

        try {

            $activity->lead_client_id = $request->lead_client_id;
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->date_time = date('Y-m-d H:i', strtotime($request->date_time));
            $activity->type = $request->activity_type;
            $activity->save();

            // Commit the transaction
            DB::commit();
            // if($request->activity_type == 'attachment'){
            if (is_array($request->attachments) && count($request->attachments) > 0) {
                $folder_name = 'attachment';
                foreach ($request->attachments as $key => $value) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $existingAttachment = LeadActivityAttachments::where('activity_id', $activity->id)
                            ->where('file_name', $value->getClientOriginalName())
                            ->first();
                        if (!$existingAttachment) {
                            $file = fileManagerUploadFile($value, 'uploads/'.$folder_name.'/');
                            $LeadActivityAttachments = new LeadActivityAttachments;
                            $LeadActivityAttachments->file_name = $value->getClientOriginalName();
                            $LeadActivityAttachments->activity_id = $activity->id;
                            $LeadActivityAttachments->file_url = $file;
                            $LeadActivityAttachments->save();
                        }
                    }
                }
            }
            // }

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error activity: '.$e->getMessage()], 500);
        }
    }

    public function activity_delete($id)
    {
        if (Auth::user('admin')->can('lead-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }
        $activity = LeadActivity::findOrfail($id);
        $activity->delete_by_type = 'admin';
        $activity->delete_by_id = auth('admin')->id();
        $activity->save();
        $activity->delete();

        return response()->json([
            'success' => 'Activity Log Deleted',
            'reload' => true,
        ]);
    }

    public function set_follow_up(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'follow_up_date_time' => 'required|date_format:Y-m-d H:i',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $lead = LeadClient::findOrfail($request->id);
        $lead->follow_up_date_time = $request->follow_up_date_time;
        $lead->save();

        return response()->json([
            'success' => 'Follow Saved',
            'reload' => true,
        ]);
    }

    public function unset_follow_up($id)
    {
        if (Auth::user('admin')->can('lead-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }
        $lead = LeadClient::findOrfail($id);
        $lead->follow_up_date_time = null;
        $lead->save();

        return response()->json([
            'success' => 'Follow Removed',
            'reload' => true,
        ]);
    }

    public function get_follow_ups(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        if (isset($request->client) && !empty($request->client)) {
            $auth_id = hashids_decode($request->client);

            $todayDate = now()->format('Y-m-d');

            if ($request->ajax() && !$request->is_html) {

                if ($request->type == 'due_today') {

                    $follow_ups = LeadClient::query()->with('clients')->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'up_comming') {

                    $follow_ups = LeadClient::query()->with('clients')->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'over_due') {

                    $follow_ups = LeadClient::query()->with('clients')->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'due_someday') {

                    $follow_ups = LeadClient::query()->with('clients')->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                }

                return DataTables::of($follow_ups->latest())
                    ->addIndexColumn()
                    ->addColumn('client_name', function ($data) {
                        return $data->clients->client_name;
                    })
                    ->addColumn('follow_ups', function ($data) {
                        return get_fulltime($data->follow_up_date_time, 'D M Y - h:i a');
                    })
                    ->addColumn('name', function ($data) {
                        return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('details', function ($data) {
                        return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        // return Str::limit($data->note ?? '-', 50);
                    })
                    ->filter(function ($query) {
                        if (request()->input('search')) {
                            $query->where(function ($search_query) {
                                $search_query->whereLike(['name'], request()->input('search'));
                            });
                        }
                    })
                    ->orderColumn('DT_RowIndex', function ($q, $o) {
                        $q->orderBy('id', $o);
                    })
                    ->make(true);
            }

            $data = [
                'type' => $request->type,
                'due_today' => LeadClient::where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate)->count(),
                'up_coming' => LeadClient::where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
                'over_due' => LeadClient::where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate)->count(),
                'due_someday' => LeadClient::where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
            ];
        } else {
            $todayDate = now()->format('Y-m-d');
            if ($request->ajax() && !$request->is_html) {
                if ($request->type == 'due_today') {

                    $follow_ups = LeadClient::query()->with('clients')->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'up_comming') {

                    $follow_ups = LeadClient::query()->with('clients')->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'over_due') {

                    $follow_ups = LeadClient::query()->with('clients')->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                } elseif ($request->type == 'due_someday') {

                    $follow_ups = LeadClient::query()->with('clients')->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate);

                    if (isset($request->leadSource) && !empty($request->leadSource)) {
                        $follow_ups = $follow_ups->where('source_type_id', $request->leadSource);
                    }

                    if ($request->dateRange) {
                        $dates = explode('-', $request->dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                            $follow_ups->whereBetween('created_at', [$startDate, $endDate]);
                        }
                    }

                    if (isset($request->leadActivity) && !empty($request->leadActivity)) {
                        $follow_ups->whereHas('activity', function ($query) use ($request) {
                            $query->where('type', $request->leadActivity);
                        });
                    }
                }

                return DataTables::of($follow_ups->latest())
                    ->addIndexColumn()
                    ->addColumn('client_name', function ($data) {
                        return $data->clients->client_name;
                    })
                    ->addColumn('follow_ups', function ($data) {
                        return get_fulltime($data->follow_up_date_time, 'D M Y - h:i a');
                    })
                    ->addColumn('name', function ($data) {
                        return view('admin.client_leads_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('details', function ($data) {
                        return view('admin.client_leads_management.include.detail_td', ['data' => $data]);
                        // return Str::limit($data->note ?? '-', 50);
                    })
                    ->filter(function ($query) {
                        if (request()->input('search')) {
                            $query->where(function ($search_query) {
                                $search_query->whereLike(['name'], request()->input('search'));
                            });
                        }
                    })
                    ->orderColumn('DT_RowIndex', function ($q, $o) {
                        $q->orderBy('id', $o);
                    })
                    ->make(true);
            }
            $data = [
                'type' => $request->type,
                'due_today' => LeadClient::whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate)->count(),
                'up_coming' => LeadClient::whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
                'over_due' => LeadClient::whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate)->count(),
                'due_someday' => LeadClient::whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
            ];
        }

        return response()->json(['template' => view('admin.client_leads_management.include.follow_ups', $data)->render(), 'type' => $request->type]);
    }

    public function update_status(Request $request)
    {
        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        $lead = LeadClient::findOrfail($request->id);
        $lead->status = $request->status;
        $lead->save();

        return response()->json([
            'success' => 'Status Updated',
            'reload' => true,
        ]);
    }

    // function for add leads to spam
    public function add_lead_to_spam(Request $request)
    {

        if (Auth::user('admin')->can('lead-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'lead_ids' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $leadIds = $request->lead_ids;

        LeadClient::whereIn('id', $leadIds)->update([
            'status' => 'spam',
            'is_admin_spam' => '1',

        ]);

        $msg = [
            'success' => 'Leads marked as spam successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }

    // send_lead_to_discord
    public function send_lead_to_discord_old(Request $request)
    {
        $leadIds = $request->lead_ids;
        $sendType = $request->send_type;
        // dd($sendType.$leadIds);

        if ($sendType === 'immediate') {

            foreach ($leadIds as $leadId) {
                $lead = LeadClient::find($leadId);
                $leadMessage = "\n- Mobile Number: https://wa.me/+65{$lead->mobile_number}";
                $leadMessage .= "\n- Name: {$lead->name}";

                $url = $lead->discord_link;
                $this->send_discord_msg($url, $leadMessage);
            }

            return response()->json(['success' => 'Leads sent to Discord immediately.']);
        } elseif ($sendType === 'after_5_minutes') {

            foreach ($leadIds as $leadId) {
                SendDiscordMessageJob::dispatch($leadId)->delay(now()->addMinutes(5));
            }

            return response()->json(['success' => 'Leads will be sent to Discord after 5 minutes.']);
        }

        return response()->json(['error' => 'Invalid send type.']);
    }
    // send_lead_to_discord

    /**
     * Send Lead to Discord
     *
     * This function handles sending lead information to a Discord channel based on the provided send type.
     * It validates the input, fetches the required data, and sends messages via Discord.
     *
     * Author: Muhammad Wajahat
     * Date: Saturday, 4 January 2025
     */
    public function send_lead_to_discord(Request $request)
    {

        if (Auth::user('admin')->can('lead-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve the send type and lead IDs from the request
        $send_type = strtolower($request->send_type);
        $lead_ids = $request->lead_ids;

        // Validate the send type
        if (!in_array($send_type, ['immediate', 'with_delay'])) {
            return response()->json([
                'message' => 'Invalid Send Type',
            ], 400);
        }

        // Validate that lead IDs are provided as an array
        if (!is_array($lead_ids)) {
            return response()->json([
                'message' => 'Lead IDs must be an array',
            ], 400);
        }

        // Process the "immediate" send type
        if ($send_type === 'immediate') {
            $success = 0; // Counter for successful sends
            $failed = []; // Array to store failure reasons

            // Loop through each lead ID and attempt to send a message
            foreach ($lead_ids as $lead_id) {
                $lead_client = LeadClient::find($lead_id); // Fetch the lead client by ID

                if ($lead_client) {
                    $client_id = $lead_client->client_id;
                    $ads_record = Ads::where('client_id', $client_id)->latest()->first(); // Fetch the latest ad record for the client

                    if ($ads_record) {
                        $discord_link = $ads_record->discord_link; // Get the Discord link

                        if ($discord_link) {
                            // Construct the message for Discord
                            $msg = "\n- Mobile Number: https://wa.me/+65{$lead_client->mobile_number}";
                            $msg .= "\n- Name: {$lead_client->name}";

                            // Send the message to Discord
                            $this->send_discord_msg($discord_link, $msg);
                            $success++;
                        } else {
                            $failed[] = 'Discord link not found.'; // Add failure reason
                        }
                    } else {
                        $failed[] = 'Ads not found.'; // Add failure reason
                    }
                } else {
                    $failed[] = 'Lead client not found.'; // Add failure reason
                }
            }

            // Construct the response message
            $response_msg = '';
            if ($success > 0) {
                $response_msg .= "Total $success lead(s) sent successfully.<br>";
            }
            if (count($failed) > 0) {
                $response_msg .= 'Total '.count($failed).' lead(s) failed to send.<br>';
                $response_msg .= 'Failed Reason:<br>';

                foreach ($failed as $text) {
                    $response_msg .= $text.'<br>';
                }
            }

            // Return the final response
            return response()->json([
                'message' => $response_msg,
            ], 200);
        }

        // Handle the "with_delay"
        if ($send_type === 'with_delay') {
            $delay = 0;

            foreach ($lead_ids as $lead_id) {
                $lead_client = LeadClient::find($lead_id);

                if ($lead_client) {
                    $client_id = $lead_client->client_id;
                    $ads_record = Ads::where('client_id', $client_id)->latest()->first();

                    if ($ads_record && $ads_record->discord_link) {
                        $discord_link = $ads_record->discord_link;
                        $message = "\n- Mobile Number: https://wa.me/+65{$lead_client->mobile_number}";
                        $message .= "\n- Name: {$lead_client->name}";

                        // Dispatch job with delay
                        SendDiscordMessage::dispatch($discord_link, $message)
                            ->delay(now()->addMinutes($delay));

                        $delay += (int) $request->delay;
                    }
                }
            }

            return response()->json(['message' => 'Messages scheduled successfully'], 200);
        }
    }
}
