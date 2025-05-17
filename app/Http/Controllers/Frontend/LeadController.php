<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Imports\LeadClientImport;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\Group;
use App\Models\Admin;
use App\Models\LeadAssign;
use App\Models\LeadGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Traits\GoogleTrait;
use Carbon\Carbon;
use App\Helpers\ActivityLogHelper;
use App\Models\JunkLead;
use App\Models\LeadSource;
use App\Services\WhatsappService;
use App\Models\LeadActivityAttachments;
use Maatwebsite\Excel\Concerns\ToArray;

class LeadController extends Controller
{
    use GoogleTrait;

    public function index(Request $request)
    {
        $auth_id = auth('web')->id();

        $todayDate = now()->format('Y-m-d');
        $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');

        $seven_days_ago = Carbon::now()->subDays(7);

        if ($request->ajax()) {

            // dd($request->dateRange);

            if ($request->type == 'all_leads') {


                ActivityLogHelper::save_activity($auth_id, 'Leads Management', 'LeadClient');


                $query = LeadClient::query()->with('ads', 'activity', 'lead_groups.group', 'assign', 'lead_source')
                    ->where('client_id', $auth_id);

                if (isset($request->leadSource) && !empty($request->leadSource) && $request->leadSource !== 'all') {
                    $query = $query->where('lead_type', $request->leadSource);
                }

                if ($request->dateRange) {
                    $dates = explode('-', $request->dateRange);

                    if (count($dates) === 2) {
                        $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                        $query->whereBetween('created_at', [$startDate, $endDate]);
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
                        return view('client.lead_management.include.select_box', ['data' => $data]);
                    })
                    ->addColumn('lead_type', function ($data) {
                        if ($data->lead_type == 'ppc') {
                            return '<span class="badge bg-primary">' . strtoupper($data->lead_type) . ' (' . $data->ads->adds_title . ')</span>';
                        } if ($data->lead_type == 'manual') {
                            return '<span class="badge bg-primary">' . ucfirst($data->lead_type) . '</span>';
                        } else {
                            return '<span class="badge bg-primary">' . ucfirst($data->lead_type) .' ('.$data->lead_source->name.')</span>';
                        }
                    })
                    ->addColumn('name', function ($data) {
                        return view('client.lead_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('email', function ($data) {
                        return $data->email ?? '';
                    })
                    ->addColumn('mobile_number', function ($data) {
                        return $data->mobile_number ?? '';
                    })
                    ->addColumn('details', function ($data) {
                        return view('client.lead_management.include.detail_td', ['data' => $data]);
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
                                $search_query->whereLike(['name', 'email', 'mobile_number'], request()->input('search'));
                            });
                        }
                    })
                    ->orderColumn('DT_RowIndex', function ($q, $o) {
                        $q->orderBy('id', $o);
                    })
                    ->rawColumns(['lead_type'])
                    ->make(true);
            } elseif ($request->type == 'uncontacted_leads') {

                ActivityLogHelper::save_activity($auth_id, 'Uncontacted Leads', 'LeadClient');

                $query = LeadClient::query()->with('activity') ->where('client_id', $auth_id)->where('status', 'uncontacted');
                if (isset($request->leadSource) && !empty($request->leadSource) && $request->leadSource !== 'all') {
                    $query = $query->where('lead_type', $request->leadSource);
                }
                if ($request->dateRange) {
                    $dates = explode('-', $request->dateRange);

                    if (count($dates) === 2) {
                        $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                }
                return DataTables::of($query->latest())
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {

                        return view('client.lead_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('source', function ($data) {
                        return $data->lead_source->name ?? '-';
                    })
                    ->addColumn('details', function ($data) {
                        return view('client.lead_management.include.detail_td', ['data' => $data]);
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

                ActivityLogHelper::save_activity($auth_id, 'Recently Viewed Content', 'LeadClient');
                $query = LeadClient::where('client_id', $auth_id)
                ->whereHas('activity', function ($query) use ($seven_days_ago) {
                    $query->where('last_open', '>=', $seven_days_ago);
                });
                if (isset($request->leadSource) && !empty($request->leadSource) && $request->leadSource !== 'all') {
                    $query = $query->where('lead_type', $request->leadSource);
                }
                if ($request->dateRange) {
                    $dates = explode('-', $request->dateRange);

                    if (count($dates) === 2) {
                        $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                        $query->with(['activity' => function ($query) use ($startDate, $endDate) {
                            $query->whereBetween('last_open', [$startDate, $endDate])->orderBy('id', 'desc');
                        }]);
                    }
                } else {
                    $query->with(['activity' => function ($query) use ($seven_days_ago) {
                        $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                    }]);
                }

                return DataTables::of($query->latest())
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {
                        return view('client.lead_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('details', function ($data) {
                        return view('client.lead_management.include.detail_td', ['data' => $data]);
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

        $client_groups = Group::where('client_id', auth('web')->user()->id)->latest()->get();

        $data = [
            'breadcrumb_main' => 'Lead Management',
            'breadcrumb' => 'Leads Management',
            'title' => 'All Leads',
            'due_today' => $due_today->count(),
            'up_coming' => $up_coming->count(),
            'over_due' => $over_due->count(),
            'due_someday' => $up_coming->count(),
            'client_groups' => $client_groups,
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

        return view('client.lead_management.index', $data);
    }

    public function client_details($id)
    {
        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Lead Client Details', 'LeadClient');
        }

        $client = LeadClient::with('activity', 'lead_data', 'lead_groups.group', 'lead_source', 'activity.attachments')->hashidFind($id);

        if (!isset($client->name) && empty($client->name)) {
            abort(404, 'Client not found');
        }

        $client_groups = Group::where('client_id', auth('web')->user()->id)->latest()->get();

        $phoneNumber = config('services.2chat.phone_number');
        $toPhoneNumber = $client->mobile_number;

        $data = [
            'breadcrumb' => $client->name,
            'title' => 'Client Details',
            'data' => $client,
            'client_groups' => $client_groups,
            'phone_number' => $phoneNumber,
            'to_phone_number' => $toPhoneNumber,
        ];
        return view('client.lead_management.client_details', $data);
    }

    public function assign_lead_to_isa(Request $request)
    {

        $rules = [
            'lead_ids' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $leadIds = $request->lead_ids;

        $existingLeadIds = LeadAssign::whereIn('lead_id', $leadIds)->pluck('lead_id')->toArray();

        $newLeadIds = array_diff($leadIds, $existingLeadIds);

        // $admin = Admin::where('role_name', 'ISA Team')->first();
        $admin = Admin::first();

        if ($admin) {
            foreach ($newLeadIds as $lead_id) {
                LeadAssign::create([
                    'lead_id' => $lead_id,
                    'assign_to' => $admin->id
                ]);
            }

            $msg = [
                'success' => 'Lead Assigned To ISA Successfully',
                'reload' => true,
            ];
        } else {

            $msg = [
                'error' => 'ISA Team Member Not Available',
            ];
        }

        return response()->json($msg);
    }

    public function save(Request $request)
    {

        $auth_id = auth('web')->id();

        if ($request->type != 'note') {
            $rules = [
                'client_name' => [
                    'required',
                    'regex:/^[a-zA-Z0-9\s]+$/'
                ],

                'email' => 'required|email',
                'mobile_number' => [
                    'required',
                    'regex:/^[89][0-9]{7}$/'
                ],
            ];

            $messages = [
                'mobile_number.regex' => 'Please enter a valid 8-digit Singapore phone number starting with 8 or 9',
                'client_name.regex' => 'Please enter the valid characters',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }

        }

        // Start a database transaction
        DB::beginTransaction();

        try {

            //   code for junk lead
            $existingLead = LeadClient::where('email', $request->email)
                ->orWhere('mobile_number', $request->mobile_number)
                ->first();

            if ($existingLead && $existingLead->is_admin_spam == '1') {

                return response()->json([
                    'error' => 'The lead was flagged as spam.',
                ]);
            }
            // code for junk lead



            $client_lead = new LeadClient();

            if ($request->id && !empty($request->id)) {
                $client_lead = $client_lead->findOrfail($request->id);
                ActivityLogHelper::save_activity($auth_id, 'Update Lead Client', 'LeadClient');
                // dd("yes updated");
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
                                    $fail('The ' . $attribute . ' field cannot contain only spaces.');
                                }
                            }
                        ],
                    ];

                    $validator = Validator::make($request->all(), $rules, );

                    if ($validator->fails()) {
                        return ['errors' => $validator->errors()];
                    }

                    ActivityLogHelper::save_activity($auth_id, 'Lead Client Note Added', 'LeadClient');

                    $client_lead->note = $request->note;
                    $client_lead->save();

                    DB::commit();

                    return response()->json([
                        'success' => 'Note Info Updated',
                        'reload' => true,
                    ]);
                }
            } else {
                $client_lead->user_type = 'user';
                $client_lead->added_by_id = auth('web')->id();
                ActivityLogHelper::save_activity($auth_id, 'Lead Client Save', 'LeadClient');

                $msg = [
                    'success' => 'Lead Added Successfully',
                    'reload' => true,
                ];
            }


            $client_lead->client_id = auth('web')->id();
            $client_lead->name = $request->client_name;
            $client_lead->email = $request->email;
            $client_lead->mobile_number = $request->mobile_number;
            $client_lead->admin_status = $request->lead_status;
            if ($request->lead_source == 'manual') {
                $client_lead->lead_type = $request->lead_source;
                $client_lead->source_type_id = null;
            } else {
                $client_lead->lead_type = 'webhook';
                $client_lead->source_type_id = $request->lead_source;
            }
            $client_lead->save();

            // Save lead data into Google Spreadsheet
            $sheet_name = 'Manual';
            $spreadsheet_id = auth('web')->user()->spreadsheet_id;
            if (isset($spreadsheet_id) && !empty($spreadsheet_id)) {
                $this->saveToGoogleSheet($request->client_name, $request->email, $request->mobile_number, $request->data, $sheet_name, $spreadsheet_id);
            }

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
                        'user_type' => 'user',
                        'added_by_id' => auth('web')->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                LeadData::insert($lead_data);
            }

            send_push_notification('New Lead', 'New Lead Added', $client_lead->client_id, $client_lead->id);
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

    public function group_save(Request $request)
    {
        $client_id = auth('web')->id();
        // ActivityLogHelper::save_activity($client_id,'Group Save', 'Group');

        if (isset($request->edit_group_id) && !empty($request->edit_group_id)) {
            $rules = [
                'group_name' => 'required|unique:groups,group_title,' . $request->edit_group_id . ',id,client_id,' . $client_id . ',deleted_at,NULL',
            ];
        } else {
            $rules = [
                'group_name' => 'required|unique:groups,group_title,NULL,id,client_id,' . $client_id . ',deleted_at,NULL',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {

            $client_group = new Group();

            if (isset($request->edit_group_id) && !empty($request->edit_group_id)) {

                ActivityLogHelper::save_activity($client_id, 'Group Update', 'Group');

                $client_group = $client_group->findorfail($request->edit_group_id);
                $client_group->background_color = $request->edit_group_colour;
                $msg = [
                    'success' => 'Group Updated Successfully',
                    'reload' => true,
                ];
            } else {
                ActivityLogHelper::save_activity($client_id, 'Group Save', 'Group');

                $client_group->background_color = $request->group_colour;
                $client_group->added_by_id = auth('web')->id();
                $msg = [
                    'success' => 'Group Added Successfully',
                    'reload' => true,
                ];
            }

            $client_group->client_id = $client_id;
            $client_group->group_title = $request->group_name;
            $client_group->save();

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

    public function group_lead_save(Request $request)
    {

        // dd($request);
        $auth_id = auth('web')->id();


        ActivityLogHelper::save_activity($auth_id, 'Lead Group Save', 'LeadGroup');

        if ($request->ajax()) {
            $lead_id = hashids_decode($request->group_lead_id);

            // Start a database transaction
            DB::beginTransaction();
            try {
                if (!empty($request->groups)) {
                    $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->get();
                    foreach ($delete_lead_groups as $lead_group) {
                        $lead_group->delete_by_type = 'user';
                        $lead_group->delete_by_id = auth('web')->id();
                        $lead_group->save();
                        $lead_group->delete();
                    }
                    foreach ($request->groups as $lead_group) {
                        $client_lead_group = new LeadGroup();
                        $client_lead_group->group_id = $lead_group;
                        $client_lead_group->lead_id = $lead_id;
                        $client_lead_group->added_by_id = auth('web')->id();
                        $client_lead_group->save();
                    }
                } else {
                    $delete_lead_groups = LeadGroup::where('lead_id', $lead_id)->get();
                    foreach ($delete_lead_groups as $lead_group) {
                        $lead_group->delete_by_type = 'user';
                        $lead_group->delete_by_id = auth('web')->id();
                        $lead_group->save();
                        $lead_group->delete();
                    }
                }
                // Commit the transaction
                DB::commit();
            } catch (\Exception $e) {
                // Something went wrong, rollback the transaction
                DB::rollback();

                // Log or handle the exception as needed
                return response()->json(['error' => 'Error lead: ' . $e->getMessage()], 500);
            }
        }
    }

    public function delete_group($id)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Delete Group', 'Group');

        $group = Group::findOrfail($id);
        $group->delete_by_type = 'user';
        $group->delete_by_id = auth('web')->id();
        $group->save();
        $lead_group = LeadGroup::where('group_id', $id)->get();
        foreach ($lead_group as $lead_group) {
            $lead_group->delete_by_type = 'user';
            $lead_group->delete_by_id = auth('web')->id();
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
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Lead Client File Import', 'LeadClientImport');

        $rules = [
            'upload_file' => [
                'required',
                'max:25600',
                function ($attribute, $value, $fail) use ($request) {
                    $file = $request->file('upload_file');
                    $allowedMimes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
                    $allowedExtensions = ['xlsx', 'xls', 'csv'];


                    if (!in_array($file->getMimeType(), $allowedMimes)) {
                        return $fail('The upload file must be a file of type: xlsx, xls, csv.');
                    }

                    if (!in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                        return $fail('The upload file must have a valid extension: xlsx, xls, csv.');
                    }


                    $importedData = Excel::toArray(new LeadClientImport, $file);
                    // dd($importedData);

                    if (empty(array_filter($importedData[0], fn ($row) => array_filter($row)))) {
                        return $fail('The file is empty.');
                    }


                    if ($importedData[0][0][0] == null) {
                        return $fail('The header of the file is empty.');
                    }

                    $requiredHeaders = [
                        0 => 'Name',
                        1 => 'Email',
                        2 => 'Mobile Number'
                    ];

                    $headers = $importedData[0][0];
                    foreach ($requiredHeaders as $key => $header) {
                        if (!isset($headers[$key]) || $headers[$key] !== $header) {
                            return $fail("Error: Missing or incorrect header: $header at position $key");
                        }
                    }

                    for ($i = 1; $i < count($importedData[0]); $i++) {
                        $row = $importedData[0][$i];
                        
                        // Validate Name
                        if (empty($row[0]) || !preg_match('/^[a-zA-Z0-9\s]+$/', $row[0])) {
                            return $fail("Error: Invalid Name in row $i");
                        }
                        
                        // Validate Email (basic validation)
                        if (empty($row[1]) || !filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                            return $fail("Error: Invalid Email in row $i");
                        }
                        
                        // Validate Mobile Number (basic numeric check)
                        if (empty($row[2]) || !is_numeric($row[2])) {
                            return $fail("Error: Invalid Mobile Number in row $i");
                        }
                    }
                


                    
                    if (count($importedData[0]) === 1) {
                        return $fail('The uploaded file does not contain any data after the header.');
                    }

                    $array = $importedData;
                    foreach ($array as $key => $subArray) {
                        // Loop through the sub-arrays starting from index 1 (to skip the first row)
                        foreach ($subArray as $subKey => $subSubArray) {
                            // Skip the first row (index 0) where headers are located
                            if ($subKey === 0) {
                                continue; // Skip the first row
                            }
                    
                            // Check if all values in the sub-array are either null, empty strings, or spaces
                            if (count(array_filter($subSubArray, function ($value) {
                                return !is_null($value) && trim($value) !== ''; // Filter out non-null, non-blank values
                            })) === 0) {
                                // If all are null or blank, unset the sub-array
                                unset($array[$key][$subKey]);
                            }
                        }
                    
                        // If the inner array becomes empty after unsetting, remove it as well
                        if (count($array[$key]) === 1) { // If only the header row remains (count of 1), consider it empty
                            unset($array[$key]);
                        }
                    }
                    
                    // After cleaning up, check if the whole array is empty
                    if (empty($array)) {
                        return $fail('The uploaded file does not contain any data after the header.');
                    }

                },
            ],
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        Excel::import(new LeadClientImport, $request->file('upload_file'));

        return response()->json([
            'success' => 'File Uploaded Successfully',
            'reload' => true,
        ]);
    }










    public function delete($id, $is_reload = null)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Lead Client Delete', 'LeadClient');

        $client = LeadClient::with('activity', 'lead_data', 'lead_groups', 'assign')->hashidFind($id);
        $client->delete_by_type = 'user';
        $client->delete_by_id = auth('web')->id();
        $client->save();
        $activity_ids = $client->activity()->pluck('id');
        if (!empty($activity_ids)) {
            LeadActivity::whereIn('id', $activity_ids)->update([
                'delete_by_type' => 'user',
                'delete_by_id' => auth('web')->id()
            ]);
        }

        $lead_data_ids = $client->lead_data()->pluck('id');
        if (!empty($lead_data_ids)) {
            LeadData::whereIn('id', $lead_data_ids)->update([
                'delete_by_type' => 'user',
                'delete_by_id' => auth('web')->id()
            ]);
        }

        $lead_group_ids = $client->lead_groups()->pluck('id');
        if (!empty($lead_group_ids)) {
            LeadGroup::whereIn('lead_id', $lead_group_ids)->update([
                'delete_by_type' => 'user',
                'delete_by_id' => auth('web')->id()
            ]);
        }

        $client->activity()->delete();
        $client->lead_data()->delete();
        $client->lead_groups()->delete();
        $client->assign()->delete();
        $client->delete();

        if ($is_reload == 1) {
            return response()->json([
                'success' => 'Client Deleted Successfully',
                'reload' => true
            ]);
        } else {
            return response()->json([
                'success' => 'Client Deleted Successfully',
                'redirect' => route('user.leads-management.all'),
            ]);
        }
    }


    public function activity_save(Request $request)
    {
        // dd($request->all());
        $auth_id = auth('web')->id();

        if ($request->activity_type == 'attachment') {
            if (!isset($request->old_file_id) && $request->old_file_id == null) {
                if (empty($request->attachments)) {
                    return response()->json([
                        'errors' => [
                            'attachmentss' => ['The attachmentss field is required.']
                        ]
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
                                'attachmentss' => ['The attachmentss field is required.']
                            ]
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

        $activity = new LeadActivity();
        if ($request->id && !empty($request->id)) {
            $activity = $activity->findOrfail($request->id);
            ActivityLogHelper::save_activity($auth_id, 'Update Lead Activity', 'LeadActivity');
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
            $activity->added_by_id = auth('web')->id();
            ActivityLogHelper::save_activity($auth_id, 'Lead Activity Save', 'LeadActivity');
            $msg = [
                'success' => 'Activity Log Added',
                'reload' => true,
            ];
        }

        DB::beginTransaction();

        try {

            $client_lead = LeadClient::find($request->lead_client_id);

            $activity->lead_client_id = $request->lead_client_id;
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->date_time = date('Y-m-d H:i', strtotime($request->date_time));
            $activity->type = $request->activity_type;
            
            if ($client_lead->status == 'uncontacted') {
                $client_lead->status = 'contacted';
                $client_lead->save();
            }

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
                            $file = fileManagerUploadFile($value, 'uploads/' . $folder_name . '/');
                            $LeadActivityAttachments = new LeadActivityAttachments();
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
            return response()->json(['error' => 'Error activity: ' . $e->getMessage()], 500);
        }
    }

    public function activity_delete($id)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Lead Activity Delete', 'LeadActivity');

        $activity = LeadActivity::findOrfail($id);
        $activity->delete_by_type = 'user';
        $activity->delete_by_id = auth('web')->id();
        $activity->save();
        $activity->delete();

        return response()->json([
            'success' => 'Activity Log Deleted',
            'reload' => true,
        ]);
    }


    public function set_follow_up(Request $request)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Set Follow Up', 'LeadClient');

        $rules = [
            'follow_up_date_time' => 'required',
        ];

        $follow_up_date_time = date('Y-m-d H:i', strtotime($request->follow_up_date_time));

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $lead = LeadClient::findOrfail($request->id);
        $lead->follow_up_date_time = $follow_up_date_time;
        $lead->save();

        return response()->json([
            'success' => 'Follow up set sucessfully',
            'reload' => true,
        ]);
    }

    public function unset_follow_up($id)
    {
        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'UnSet Follow Up', 'LeadClient');

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
        $auth_id = auth('web')->id();


        $todayDate = now()->format('Y-m-d');
        if ($request->ajax() && !$request->is_html) {
            if ($request->type == 'due_today') {
                ActivityLogHelper::save_activity($auth_id, 'Follow Up Due Today', 'LeadClient');
                $follow_ups = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate)->latest();
            } elseif ($request->type == 'up_comming') {
                ActivityLogHelper::save_activity($auth_id, 'Follow Up Up Comming', 'LeadClient');
                $follow_ups = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->latest();
            } elseif ($request->type == 'over_due') {
                ActivityLogHelper::save_activity($auth_id, 'Follow Up Over Due', 'LeadClient');
                $follow_ups = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate)->latest();
            } elseif ($request->type == 'due_someday') {
                ActivityLogHelper::save_activity($auth_id, 'Follow Up Due Someday', 'LeadClient');
                $follow_ups = LeadClient::query()->where('client_id', $auth_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->latest();
            }

            return DataTables::of($follow_ups)
                ->addIndexColumn()
                ->addColumn('follow_ups', function ($data) {
                    return get_fulltime($data->follow_up_date_time, 'd-m-y h:i A');
                })
                ->addColumn('name', function ($data) {
                    return view('client.lead_management.include.name_td', ['data' => $data]);
                })
                ->addColumn('details', function ($data) {
                    return view('client.lead_management.include.detail_td', ['data' => $data]);
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
            'due_today' => LeadClient::where('client_id', auth('web')->id())->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', $todayDate)->count(),
            'up_coming' => LeadClient::where('client_id', auth('web')->id())->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
            'over_due' => LeadClient::where('client_id', auth('web')->id())->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '<', $todayDate)->count(),
            'due_someday' => LeadClient::where('client_id', auth('web')->id())->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>', $todayDate)->count(),
        ];

        return response()->json(['template' => view('client.lead_management.include.follow_ups', $data)->render(), 'type' => $request->type]);
    }

    public function update_status(Request $request)
    {

        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Update Lead Status', 'LeadClient');

        $lead = LeadClient::findOrfail($request->id);
        $lead->status = $request->status;
        $lead->save();
        if ($request->status == 'spam') {
            LeadAssign::where('lead_id', $request->id)->delete();
        }
        return response()->json([
            'success' => 'Status Updated',
            'reload' => true,
        ]);
    }

    public function ppc_leads()
    {

        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'PPC Leads', 'LeadClient');
        }


        $data = [
            'breadcrumb_main' => 'Leads',
            'breadcrumb' => 'PPC Leads',
            'title' => 'PPC Leads',
        ];
        return view('client.lead_management.ppc_leads')->with($data);
    }

    public function lead_status(Request $request)
    {


        $auth_id = auth('web')->id();

        switch ($request->admin_status) {
            case 'contacted':
                ActivityLogHelper::save_activity($auth_id, 'Lead status contacted', 'LeadClient');
                break;
            case 'appointment_set':
                ActivityLogHelper::save_activity($auth_id, 'Lead status appointment_set', 'LeadClient');
                break;
            case 'burst':
                ActivityLogHelper::save_activity($auth_id, 'Lead status burst', 'LeadClient');
                break;
            case 'follow_up':
                ActivityLogHelper::save_activity($auth_id, 'Lead status follow_up', 'LeadClient');
                break;
            case 'call_back':
                ActivityLogHelper::save_activity($auth_id, 'Lead status call_back', 'LeadClient');
                break;
            default:
                ActivityLogHelper::save_activity($auth_id, 'Lead status', 'LeadClient');
                break;
        }


        $lead_client = LeadClient::find($request->lead_id);
        $lead_client->admin_status = $request->admin_status;
        $lead_client->save();

        return response()->json([
            'success' => 'Status Changed Successfully',
        ]);
    }

    public function view_client_file_activity($id)
    {

        $auth_id = auth('web')->id();

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'View Client File Ctivity', 'LeadActivity');
        }


        $get_lead_activity = LeadActivity::with('client_file')->where('id', hashids_decode($id))->latest()->first();
        $get_client_lead = LeadClient::with('clients')->where('id', $get_lead_activity->lead_client_id)->latest()->first();

        $get_lead_activity->last_open = Carbon::now();
        $get_lead_activity->total_views += 1;
        $get_lead_activity->updated_at = now();
        $get_lead_activity->save();

        $data = [
            'title' => $get_lead_activity->title . ' for ' . $get_client_lead->name,
            'get_lead_activity' => $get_lead_activity,
            'get_client_lead' => $get_client_lead
        ];

        return view('client.lead_file_view', $data);
    }


    // function for add lead to spam
    public function add_lead_to_spam(Request $request)
    {
        // dd($request);

        $rules = [
            'lead_ids' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }


        $leadIds = $request->lead_ids;

        LeadClient::whereIn('id', $leadIds)->update(['status' => 'spam']);

        $msg = [
            'success' => 'Leads marked as spam successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }

    // function for add lead to spam


}
