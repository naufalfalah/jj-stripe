<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agency;
use App\Models\Industry;
use App\Models\Transections;
use App\Models\WalletTopUp;
use App\Models\Ads;
use App\Models\ClientFolder;
use App\Models\GoogleAccount;
use App\Models\GoogleAd;
use App\Models\GoogleAdsConversionAction;
use App\Models\LeadClient;
use App\Models\Package;
use App\Models\UserSubAccount;
use App\Services\GoogleAdsService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Traits\GoogleTrait;
use Illuminate\Support\Facades\Log;

class ClientManagmentController extends Controller
{
    use GoogleTrait;

    public function index()
    {
        if (Auth::user('admin')->can('user-add') == true) {
            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'All Clients',
                'title' => 'All Clients',

                'all_users' => User::with('Agencices', 'Industries')
                    ->where(
                        'sub_account_id',
                        hashids_decode(session()->get('sub_account_id'))
                    )
                    ->latest()
                    ->get(),
                'sub_account_id' => session()->get('sub_account_id'),
            ];
            return view('admin.client_management.clients.all_clients')->with(
                $data
            );
        } else {
            return response()->json(
                [
                    'error' =>
                        'You do not have permission to access this resource.',
                ],
                403
            );
        }
    }

    public function all_clients(Request $request)
    {
        if (Auth::user('admin')->role_name === 'super_admin') {
            if ($request->ajax()) {
                if (isset($request->user_id)) {
                    $url = route(
                        'admin.sub_account.client-management.clone_client',
                        [
                            'sub_account_id' => session()->get('sub_account_id'),
                            'id' => $request->user_id,
                        ]
                    );
                    return response()->json(['redirect_url' => $url]);
                }
            }

            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'Import Clients',
                'title' => 'Import Clients',
                'agencies' => Agency::all(),
                'industries' => Industry::all(),
                'users' => User::where(
                    'sub_account_id',
                    '!=',
                    hashids_decode(session()->get('sub_account_id'))
                )
                    ->with('user_agency')
                    ->latest()
                    ->get(),
                'sub_account_id' => session()->get('sub_account_id'),
                'packages' => Package::all(),
            ];
            return view('admin.client_management.clients.import_clients', $data);
        }
        if (Auth::user('admin')->role_name === 'ISA Team') {
            $data = [
                'breadcrumb_main' => 'Clients',
                'breadcrumb' => 'All Clients',
                'title' => 'All Clients',
                'all_clients' => User::whereNotNull('email_verified_at')->latest()->get(),
            ];
            return view('admin.client_management.all_clients')->with($data);
        }
    }

    public function add()
    {
        if (Auth::user('admin')->can('user-add') == true) {
            $customers = [];
            if (Auth::user('admin')->google_access_token) {
                $googleAdsService = new GoogleAdsService();
                $getCustomers = $googleAdsService->getCustomers();
                foreach ($getCustomers['resourceNames'] as $customer) {
                    $customerId = removePrefix($customer);
                    $getCustomerClients = $googleAdsService->getCustomerClients(
                        $customerId
                    );

                    foreach (
                        $getCustomerClients['results'] as $customerClient
                    ) {
                        $customers[] = $customerClient;
                    }
                }
            }

            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'Add Client',
                'title' => 'Add Client',
                'agencies' => Agency::all(),
                'industries' => Industry::all(),
                'sub_account_id' => session()->get('sub_account_id'),
                'customers' => $customers,
                'packages' => Package::all(),
            ];
            return view('admin.client_management.clients.add_client')->with(
                $data
            );
        } else {
            return response()->json(
                [
                    'error' =>
                        'You do not have permission to access this resource.',
                ],
                403
            );
        }
    }

    public function view($id)
    {
        $user = User::hashidFind($id);
        $due_today = LeadClient::query()
            ->where('client_id', $user->id)
            ->whereNotNull('follow_up_date_time')
            ->whereDate('follow_up_date_time', now()->format('Y-m-d'));
        $up_coming = LeadClient::query()
            ->where('client_id', $user->id)
            ->whereNotNull('follow_up_date_time')
            ->whereDate('follow_up_date_time', '>', now()->format('Y-m-d'));
        $over_due = LeadClient::query()
            ->where('client_id', $user->id)
            ->whereNotNull('follow_up_date_time')
            ->whereDate('follow_up_date_time', '<', now()->format('Y-m-d'));
        $remaining_balance = DB::table('transections')
            ->select('client_id', DB::raw('COALESCE(SUM(CASE WHEN amount_out IS NULL THEN available_balance ELSE 0 END), 0) - COALESCE(SUM(amount_out), 0) AS remaining_balance'))
            ->where('client_id', $user->id)
            ->where('deleted_at', null)
            ->groupBy('client_id')
            ->get();
        $remaining_amount = $remaining_balance->isEmpty() ? 0 : $remaining_balance[0]->remaining_balance;

        $get_ppc_leads = LeadClient::where('client_id', $user->id)->where('lead_type', 'ppc')->count();

        $data = [
            'breadcrumb' => 'Client Details',
            'title' => 'Client Details',
            'folder_files' => ClientFolder::with('client_files')->where('client_id', $user->id)->latest()->get(),
            'all_folders' => ClientFolder::with('client_files')->where('client_id', $user->id)->latest()->get(),
            'client_id' => $id,
            'user_calender' => User::where('id', hashids_decode($id))->latest()->first(),
            'due_today' => $due_today->count(),
            'up_coming' => $up_coming->count(),
            'over_due' => $over_due->count(),
            'due_someday' => $up_coming->count(),
            'total_balance' => $remaining_amount,
            'total_ppc_leads' => $get_ppc_leads,
        ];
        if ($this->checkRefreshToken($user)) {
            $data['user_calender'] = null;
        }

        return view('admin.client_management.client_files', $data);
    }

    public function get_agency_address(Request $request)
    {
        if ($request->ajax()) {
            $agencyId = $request->agency_id;

            if ($agencyId) {
                $agency = Agency::find($agencyId);

                if ($agency && $agency->address) {
                    return response()->json([
                        'success' => true,
                        'address' => $agency->address,
                    ]);
                }
            }
        }

        return response()->json(['error' => 'Agency address not found'], 404);
    }

    public function save(Request $request)
    {
        if (
            !Auth::user()->can('user-add') &&
            !Auth::user()->can('user-update')
        ) {
            return response()->json(
                [
                    'error' => 'Unauthorized action.',
                ],
                403
            );
        }

        $userId = $request->id ? hashids_decode($request->id) : null;

        $rules = [
            'client_name' => 'required|string',
            'phone_number' => 'required|numeric',
            'email' =>
                'required|email|unique:users,email,' .
                ($userId ?? 'NULL') .
                ',id,deleted_at,NULL',
            'agency_id' => 'required|integer',
            'package' => 'required|string',
            'address' => 'required|string',
            'industry_id' => 'required|integer',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png',
            'password' => 'nullable|min:8',
            'confirm_password' => 'nullable|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if ($userId) {
            $user = User::findOrFail($userId);
            $message = 'User updated successfully';
        } else {
            $user = new User();
            $message = 'User created successfully';
        }

        $googleAccountId = (int) $request->input('google_account_id');

        $user->sub_account_id = hashids_decode(
            session()->get('sub_account_id')
        );

        $user->client_name = $request->client_name;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        $user->agency_id = $request->agency_id;
        $user->package = $request->package;
        $user->address = $request->address;
        $user->industry_id = $request->industry_id;
        $user->google_account_id = $googleAccountId;
        $user->customer_id = $request->customer_id;
        $user->email_verified_at = now();

        if (empty($userId)) {
            if ($request->hasFile('image')) {
                $profile_img = uploadSingleFile(
                    $request->file('image'),
                    'uploads/profile_images/',
                    'png,jpeg,jpg'
                );
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }
                $user->image = $profile_img;
            } else {
                if (!isset($request->client_image)) {
                    return response()->json(
                        ['error' => 'Profile image is required for new user'],
                        400
                    );
                }
            }
        } else {
            if ($request->hasFile('image')) {
                if (file_exists($user->image)) {
                    @unlink($user->image);
                }
                $profile_img = uploadSingleFile(
                    $request->file('image'),
                    'uploads/profile_images/',
                    'png,jpeg,jpg'
                );
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }
                $user->image = $profile_img;
            }
        }

        if (isset($request->client_image) && !$request->hasFile('image')) {
            $user->image = $request->client_image;
        }

        if (!Hash::needsRehash($request->password)) {
            $user->password = $request->password;
        } else {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $check_admin_account = Admin::where('user_type', 'admin')
            ->where('role_name', 'super_admin')
            ->whereNotNull('google_access_token')
            ->count();
        if ($check_admin_account > 0) {
            $create_client_sheet = $this->createNewSpreadsheet(
                $request->client_name,
                $user->id
            );
            $user->spreadsheet_id = $create_client_sheet;
            $user->save();
        }

        return response()->json([
            'success' => $message,
            'reload' => true,
        ]);
    }

    public function clone_client($sub_account_id, $id)
    {
        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Add Client',
            'title' => 'Add Client',
            'agencies' => Agency::all(),
            'industries' => Industry::all(),
            'clone_client' => User::hashidFind($id),
            'last_inserted_id' => User::latest()->value('id'),
            'users' => User::where(
                'sub_account_id',
                '!=',
                hashids_decode(session()->get('sub_account_id'))
            )
                ->with('user_agency')
                ->latest()
                ->get(),
            'sub_account_id' => session()->get('sub_account_id'),
            'packages' => Package::all(),
        ];

        return view('admin.client_management.clients.import_clients', $data);
    }

    public function edit($sub_account_id)
    {
        if (Auth::user('admin')->can('user-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $customers = [];
        if (Auth::user('admin')->google_access_token) {
            $googleAdsService = new GoogleAdsService();
            $getCustomers = $googleAdsService->getCustomers();
            foreach ($getCustomers['resourceNames'] as $customer) {
                $customerId = removePrefix($customer);
                $getCustomerClients = $googleAdsService->getCustomerClients(
                    $customerId
                );

                if (!isset($getCustomerClients['results'])) {
                    continue;
                }

                foreach ($getCustomerClients['results'] as $customerClient) {
                    $customers[] = $customerClient;
                }
            }
        }

        $subAccountId = hashids_decode($sub_account_id);
        $client = User::where('sub_account_id', $subAccountId)->first();
        $userSubAccounts = UserSubAccount::where(
            'sub_account_id',
            $subAccountId
        )->get();

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Client',
            'title' => 'Edit Client',
            'agencies' => Agency::all(),
            'industries' => Industry::all(),
            'edit' => $client,
            'sub_account_id' => $sub_account_id,
            'customers' => $customers,
            'google_accounts' => GoogleAccount::all(),
            'user_sub_accounts' => $userSubAccounts,
            'packages' => Package::all(),
        ];

        return view('admin.client_management.clients.add_client', $data);
    }

    public function delete($sub_account_id, $id)
    {
        $client = User::findOrFail(hashids_decode($id))->delete();
        return response()->json([
            'success' => 'Client deleted successfully',
            'remove_tr' => true,
        ]);
    }

    public function update_password(Request $request)
    {
        if (
            !Auth::user()->can('user-add') &&
            !Auth::user()->can('user-update')
        ) {
            return response()->json(
                [
                    'error' => 'Unauthorized action.',
                ],
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:12|confirmed',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::hashidFind($request->client_id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => 'User password has been updated',
            'reload' => true,
        ]);
    }

    public function top_Up(Request $request)
    {
        if (Auth::user('admin')->can('topup-add') == true) {
            $sub_account_ids = User::where(
                'sub_account_id',
                hashids_decode(session()->get('sub_account_id'))
            )->pluck('id');

            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'All Top Up',
                'title' => 'All TopUps',
                'all_users' => User::where(
                    'sub_account_id',
                    hashids_decode(session()->get('sub_account_id'))
                )->get(),
                'all_wallet_topup' => WalletTopUp::with('clients')
                    ->whereIn('client_id', $sub_account_ids)
                    ->latest()
                    ->get(),
                'sub_account_id' => session()->get('sub_account_id'),
            ];
            return view('admin.client_management.TopUp.all_topup')->with($data);
        } else {
            return response()->json(
                [
                    'error' =>
                        'You do not have permission to access this resource.',
                ],
                403
            );
        }
    }

    public function topup_save(Request $request)
    {
        if (
            !Auth::user()->can('topup-add') &&
            !Auth::user()->can('topup-update')
        ) {
            return response()->json(
                [
                    'error' => 'Unauthorized action.',
                ],
                403
            );
        }

        $rules = [
            'client_id' => 'required|numeric',
            'topup_amount' => 'required|numeric',
            'proof' => 'nullable|array',
            'proof.*' => 'image|mimes:jpeg,jpg,png',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $topup = empty($request->id)
            ? new WalletTopUp()
            : WalletTopUp::findOrFail(hashids_decode($request->id));

        $topup->client_id = $request->client_id;
        $topup->topup_amount = $request->topup_amount;
        $topup->status = 'approve';
        $topup->approved_by = Auth::id();
        $topup->approve_at = Carbon::now();
        $topup->added_by_id = Auth::user('admin')->id;
        $topup->added_by = Auth::user('admin')->user_type;

        if ($request->hasFile('proof')) {
            $deposit_slips = [];
            foreach ($request->file('proof') as $file) {
                $deposit_slip = uploadSingleFile(
                    $file,
                    'uploads/client/clients_topups/',
                    'png,jpeg,jpg'
                );
                if (is_array($deposit_slip)) {
                    return response()->json($deposit_slip);
                }
                $deposit_slips[] = $deposit_slip;
            }

            $topup->proof = implode(',', $deposit_slips);
        }

        $topup->save();

        $trans = Transections::where('client_id', $topup->client_id);
        $remaining_amount =
            $trans->sum('amount_in') - $trans->sum('amount_out');

        $transection = new Transections();
        $transection->client_id = $topup->client_id;
        $transection->amount_in = $topup->topup_amount;
        $transection->available_balance =
            $topup->topup_amount + $remaining_amount;
        $transection->topup_id = $topup->id;
        $transection->save();

        return response()->json([
            'success' => empty($request->id)
                ? 'Top Up created successfully'
                : 'Top Up updated successfully',
            'redirect' => route('admin.sub_account.client-management.top_up', [
                'sub_account_id' => session()->get('sub_account_id'),
            ]),
        ]);
    }

    public function topup_edit($sub_account_id, $id)
    {
        if (Auth::user('admin')->can('topup-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sub_account_id = User::where(
            'sub_account_id',
            hashids_decode(session()->get('sub_account_id'))
        )->pluck('id');

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Top Up',
            'title' => 'Edit Top Up',
            'edit' => WalletTopUp::hashidFind($id),
            'all_users' => User::where(
                'sub_account_id',
                hashids_decode(session()->get('sub_account_id'))
            )->get(),
            'all_wallet_topup' => WalletTopUp::with('clients')
                ->whereIn('client_id', $sub_account_id)
                ->latest()
                ->get(),
            'sub_account_id' => session()->get('sub_account_id'),
        ];

        return view('admin.client_management.TopUp.all_topup')->with($data);
    }

    public function topup_delete($id)
    {
        if (Auth::user('admin')->can('topup-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $delete = WalletTopUp::find($id);
        $delete->delete();
        return response()->json([
            'success' => 'Record Delete Successfully',
            'redirect' => route('admin.sub_account.client-management.Top_Up', [
                'sub_account_id' => session()->get('sub_account_id'),
            ]),
        ]);
    }

    public function all_ads($sub_account_id, Request $request)
    {
        $decoded_sub_account_id = hashids_decode($sub_account_id);
        $sub_account_ids = User::where(
            'sub_account_id',
            $decoded_sub_account_id
        )->pluck('id');

        if ($request->ajax()) {
            $ads = Ads::with('client')
                ->whereIn('client_id', $sub_account_ids)
                ->latest()
                ->get();
            return DataTables::of($ads)
                ->addIndexColumn()
                ->addColumn(
                    'client_name',
                    fn ($data) => $data->client->client_name ?? '-'
                )
                ->addColumn(
                    'adds_title',
                    fn ($data) => Str::limit($data->adds_title, 20, '...') ??
                        '---'
                )
                ->addColumn(
                    'type',
                    fn ($data) => Str::limit(
                        ads_type_text(explode(',', $data->type)),
                        30,
                        '...'
                    )
                )
                ->addColumn(
                    'status',
                    fn ($data) => view('admin.ads_management.include.status', [
                        'data' => $data,
                        'sub_account_id' => $sub_account_id,
                    ])
                )
                ->addColumn(
                    'action',
                    fn ($data) => view(
                        'admin.ads_management.include.action_td',
                        ['data' => $data, 'sub_account_id' => $sub_account_id]
                    )
                )
                ->filter(function ($query) {
                    if ($search = request('search')) {
                        $query->where(
                            fn ($search_query) => $search_query->whereLike(
                                ['adds_title', 'type'],
                                $search
                            )
                        );
                    }
                })
                ->make(true);
        }

        $all_add_requests = Ads::with('client')
            ->whereIn('client_id', $sub_account_ids)
            ->latest()
            ->get();
        $all_users = User::where(
            'sub_account_id',
            $decoded_sub_account_id
        )->get();

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'All Ads',
            'title' => 'All Ads',
            'all_add_requests' => $all_add_requests,
            'all_users' => $all_users,
            'sub_account_id' => $sub_account_id,
        ];

        return view('admin.client_management.ads.index', $data);
    }

    public function ads_create(Request $request)
    {
        $sub_account_id = hashids_decode(session('sub_account_id'));
        $sub_account_ids = User::where(
            'sub_account_id',
            $sub_account_id
        )->pluck('id');

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Create Ads Request',
            'title' => 'Create Ads Request',
            'all_add_requests' => Ads::with('client')
                ->whereIn('client_id', $sub_account_ids)
                ->latest()
                ->get(),
            'all_users' => User::where(
                'sub_account_id',
                $sub_account_id
            )->get(),
            'sub_account_id' => session('sub_account_id'),
        ];

        return view('admin.client_management.ads.create', $data);
    }

    public function ads_save($sub_account_id, Request $request)
    {
        $trans = Transections::where('client_id', $request->client_id);
        $remaining_amount =
            $trans->sum('amount_in') - $trans->sum('amount_out');

        if ($remaining_amount <= 50) {
            return response()->json(
                [
                    'error' =>
                        'You do not have enough balance to make a new ad request.',
                ],
                402
            );
        }

        $rules = [
            'descord_link' => 'required|url',
            'type' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Start a database transaction
        DB::beginTransaction();
        try {
            $ads_add = $request->id
                ? Ads::findOrFail(hashids_decode($request->id))
                : new Ads();
            $msg = $request->id
                ? 'Ads Updated Successfully'
                : 'Ads Added Successfully';

            $ads_add
                ->fill([
                    'client_id' => $request->client_id,
                    'adds_title' => $request->title ?? '',
                    'discord_link' => $request->descord_link,
                    'type' => implode(',', $request->type),
                    'status' => $request->status,
                ])
                ->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => $msg,
                'redirect' => route(
                    'admin.sub_account.client-management.all_ads',
                    ['sub_account_id' => $sub_account_id]
                ),
            ]);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(
                [
                    'error' => 'Error lead: ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    public function ads_edit($sub_account_id, $id)
    {
        if (Auth::user('admin')->can('user-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sub_account_id = hashids_decode(session()->get('sub_account_id'));
        $sub_account_ids = User::where(
            'sub_account_id',
            $sub_account_id
        )->pluck('id');

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Ads Request',
            'title' => 'Edit Ads Request',
            'all_add_requests' => Ads::with('client')
                ->whereIn('client_id', $sub_account_ids)
                ->latest()
                ->get(),
            'all_users' => User::where(
                'sub_account_id',
                $sub_account_id
            )->get(),
            'sub_account_id' => session()->get('sub_account_id'),
            'edit' => Ads::hashidFind($id),
        ];

        return view('admin.client_management.ads.create', $data);
    }

    public function ads_delete($sub_account_id, $id)
    {
        $ad = Ads::find(hashids_decode($id));

        if (!$ad) {
            return response()->json(
                [
                    'error' => 'Ad not found',
                ],
                404
            );
        }

        // Delete the ad
        $ad->delete();

        return response()->json([
            'success' => 'Ad deleted successfully',
            'remove_tr' => true,
        ]);
    }

    public function google_ads_campaign(Request $request)
    {
        if (Auth::user('admin')->can('campaigns-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();

        return view('admin.client_management.google_ads_campaign.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Campaign',
            'title' => 'Google Ads Campaign',
            'sub_account_id' => $sessionId,
            'client' => $client,
        ]);
    }

    public function google_ads_campaign_edit(Request $request)
    {
        if (
            Auth::user('admin')->can('campaigns-write') != true ||
            Auth::user('admin')->can('campaigns-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $campaignResourceName = $request->campaign_resource_name;
        $campaign = [];

        if (Auth::user('admin')->google_access_token) {
            $googleAdsService = new GoogleAdsService();
            $campaign = $googleAdsService->getCampaignByResourceName(
                $customerId,
                $campaignResourceName
            );
        }

        return view('admin.client_management.google_ads_campaign.edit', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Campaign',
            'title' => 'Google Ads Campaign',
            'sub_account_id' => $sessionId,
            'customer_id' => $customerId,
            'campaign_resource_name' => $campaignResourceName,
            'campaign' => $campaign,
        ]);
    }

    public function google_ads_campaign_update(Request $request)
    {
        if (Auth::user('admin')->can('campaigns-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (!Auth::user('admin')->google_access_token) {
                return redirect()
                    ->back()
                    ->with('error', 'Google Ads not connected yet.');
            }

            // Processing request body
            $customerId = $request->customer_id;
            $campaignResourceName = $request->campaign_resource_name;
            $requestBodyCampaign = [];
            $requestBodyCampaign['name'] = $request->campaign_name;
            $requestBodyCampaign['status'] = $request->campaign_status;
            $requestBodyCampaign['startDate'] = $request->campaign_start_date;
            $requestBodyCampaign['endDate'] = $request->campaign_end_date;

            $campaignBudgetResourceName =
                $request->campaign_budget_resource_name;
            $requestBodyCampaignBudget = [];
            $requestBodyCampaignBudget['name'] = $request->campaign_name;
            $requestBodyCampaignBudget['amountMicros'] =
                (int) $request->campaign_budget_amount * 1000000;

            // Update Google Ads campaign
            $googleAdsService = new GoogleAdsService();
            $googleAdsService->updateCampaign(
                $customerId,
                $campaignResourceName,
                $requestBodyCampaign
            );

            $googleAdsService->updateCampaignBudget(
                $customerId,
                $campaignBudgetResourceName,
                $requestBodyCampaignBudget
            );

            // Update Google Ads in internal DB
            $googleAd = GoogleAd::where(
                'campaign_resource_name',
                $campaignResourceName
            )->first();
            if ($googleAd) {
                $googleAd->campaign_name = $requestBodyCampaign['name'];
                $googleAd->campaign_start_date =
                    $requestBodyCampaign['startDate'];
                $googleAd->campaign_end_date = $requestBodyCampaign['endDate'];
                $googleAd->campaign_budget_amount =
                    $requestBodyCampaignBudget['amountMicros'];
                $googleAd->save();
            }

            return redirect()
                ->back()
                ->with('success', 'Google ads campaign updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update campaign: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function google_ads_ad_group(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-group-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();

        return view('admin.client_management.google_ads_ad_group.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'client' => $client,
        ]);
    }

    public function google_ads_ad_group_show(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-group-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $adGroupResourceName = $request->ad_group_resource_name;
        $adGroup = [];

        if (Auth::user('admin')->google_access_token) {
            $googleAdsService = new GoogleAdsService();
            $adGroup = $googleAdsService->getAdGroupByResourceName(
                $customerId,
                $adGroupResourceName
            );
        }

        return view('admin.client_management.google_ads_ad_group.show', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'customer_id' => $customerId,
            'ad_group_resource_name' => $adGroupResourceName,
            'ad_group' => $adGroup,
        ]);
    }

    public function google_ads_ad_group_ad(Request $request)
    {
        if (
            Auth::user('admin')->can('google-ad-group-write') != true ||
            Auth::user('admin')->can('google-ad-group-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();
        $ads = Ads::where('client_id', $client->id)->get();

        return view('admin.client_management.google_ads_ad_group_ad.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'adsRequests' => $ads,
        ]);
    }

    public function google_ads_create()
    {
        if (Auth::user('admin')->can('google-ad-group-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();
        $client_ids = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->pluck('id');

        return view('admin.client_management.google_ads.create', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Create Google Ads',
            'title' => 'Create Google Ads',
            'sub_account_id' => $sessionId,
            'ads_requests' => Ads::with('client')
                ->whereIn('client_id', $client_ids)
                ->latest()
                ->get(),
            'client' => $client,
        ]);
    }

    public function google_ads_store(Request $request)
    {
        if (
            Auth::user('admin')->can('google-ad-group-write') != true ||
            Auth::user('admin')->can('google-ad-group-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (!Auth::user('admin')->google_access_token) {
                return redirect()
                    ->back()
                    ->with('error', 'Google Ads not connected yet.');
            }

            // Validate request
            $requestBody = $request->validate([
                'ad_request_id' => 'required|exists:ads,id',

                'campaign_name' => 'required|string|max:255',
                'campaign_type' => 'required|in:SEARCH,PERFORMANCE_MAX',
                'campaign_budget_type' => 'required|in:LIFETIME,DAILY',
                'campaign_budget_amount' => 'required|numeric|min:1',
                'campaign_start_date' => 'nullable|date',
                'campaign_end_date' =>
                    'nullable|date|after_or_equal:campaign_start_date',

                'keywords' => 'required|string',

                'ad_name' => 'required|string|max:255',
                'ad_url' => 'required|string',
                'ad_headlines' => 'required|array',
                'ad_headlines.*' => 'nullable|distinct|string|max:30',
                'ad_descriptions' => 'required|array',
                'ad_descriptions.*' => 'nullable|distinct|string|max:90',
                'sitelinks' => 'array',
                'sitelinks.*.text' => 'nullable|string',
                'sitelinks.*.url' => 'nullable|string',
                'ad_callouts' => 'required|array',
                'ad_callouts.*' => 'nullable|distinct|string|max:25',
            ]);

            $requestAd = Ads::find($request->ad_request_id);

            // Find client
            $client = User::findOrFail($requestAd->client_id);

            // Check customer id
            if (is_null($client->customer_id)) {
                return redirect()
                    ->back()
                    ->with('error', 'The client does not have a customer ID.');
            }
            $customerId = $client->customer_id;
            $googleAccountId = $client->google_account_id;

            // Processing request body
            $today = Carbon::today();
            $startDate = $today->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
            if ($request->has('campaign_duration')) {
                $duration = $request->campaign_duration;

                if ($duration === 'week') {
                    $endDate = $today->addDays(7)->format('Y-m-d');
                } elseif ($duration === 'month') {
                    $endDate = $today->addDays(30)->format('Y-m-d');
                } elseif ($duration === 'custom') {
                    $startDate = $request->start_date;
                    $endDate = $request->end_date;
                }
            }
            $requestBody['start_date'] = $startDate;
            $requestBody['end_date'] = $endDate;
            $requestBody['campaign_budget_amount'] =
                (int) $request->campaign_budget_amount * 1000000; // Need to be multiplied by 1 million

            // Location
            if ($request->location == 'SINGAPORE') {
                $requestBody['locations'] = ['geoTargetConstants/2702'];
            } else {
                $requestBody['locations'] = $request->locations;
            }

            // Language
            $requestBody['language'] = 'languageConstants/1000';

            // Format keyword
            $keywords = array_filter(
                array_map('trim', explode("\r\n", $request->keywords)),
                'strlen'
            );
            $formattedKeywords = array_map(function ($keyword) {
                $match_type = 'BROAD';
                if (preg_match('/^\[.*\]$/', $keyword)) {
                    $match_type = 'EXACT';
                    $keyword = trim($keyword, '[]');
                } elseif (preg_match('/^".*"$/', $keyword)) {
                    $match_type = 'PHRASE';
                    $keyword = trim($keyword, '"');
                } else {
                    $keyword = trim($keyword);
                }
                return [
                    'text' => $keyword,
                    'match_type' => $match_type,
                ];
            }, $keywords);
            $requestBody['keywords'] = $formattedKeywords;

            $requestBody['ad_url_1'] = $request->ad_url_1;
            $requestBody['ad_url_2'] = $request->ad_url_2;

            $requestBody['sitelinks'] = $request->sitelinks ?? [];

            // Initiate google ads service
            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);

            $googleAdsService = new GoogleAdsService(
                $googleAccount->access_token
            );
            // Create campaign budget
            $campaignBudget = $googleAdsService->createCampaignBudget(
                $customerId,
                $requestBody
            );
            if (is_array($campaignBudget)) {
                if (isset($campaignBudget['errors'])) {
                    foreach ($campaignBudget['errors'] as $error) {
                        return redirect()
                            ->back()
                            ->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Campaign budget created"
            );

            // Create campaign
            $campaign = $googleAdsService->createCampaign(
                $customerId,
                $campaignBudget,
                $requestBody
            );
            if (is_array($campaign)) {
                if (isset($campaign['errors'])) {
                    foreach ($campaign['errors'] as $error) {
                        return redirect()
                            ->back()
                            ->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Campaign created"
            );

            // Add campaign criteria
            $googleAdsService->createCampaignCriteria(
                $customerId,
                $campaign,
                $requestBody
            );
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Campaign criteria created"
            );

            // Create ad group
            $adGroup = $googleAdsService->createAdGroup(
                $customerId,
                $campaign,
                $requestBody
            );
            if (is_array($adGroup)) {
                if (isset($adGroup['errors'])) {
                    foreach ($adGroup['errors'] as $error) {
                        return redirect()
                            ->back()
                            ->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Ad group created"
            );

            // Add keyword
            $keywords = $googleAdsService->addKeywordToAdGroup(
                $customerId,
                $adGroup,
                $requestBody
            );
            if (is_array($keywords)) {
                if (isset($keywords['errors'])) {
                    foreach ($keywords['errors'] as $error) {
                        return redirect()
                            ->back()
                            ->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Keyword created"
            );

            // Create ad
            $ad = $googleAdsService->createAd(
                $customerId,
                $adGroup,
                $requestBody
            );
            if (is_array($ad)) {
                if (isset($ad['errors'])) {
                    foreach ($ad['errors'] as $error) {
                        return redirect()
                            ->back()
                            ->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Ad created"
            );

            $hasNull = in_array(null, $requestBody['sitelinks'][1], true);
            if (count($requestBody['sitelinks']) && !$hasNull) {
                $sitelinks = $googleAdsService->createAsset(
                    $customerId,
                    $requestBody
                );
                if (is_array($sitelinks)) {
                    if (isset($sitelinks['errors'])) {
                        foreach ($sitelinks['errors'] as $error) {
                            return redirect()
                                ->back()
                                ->with('error', $error);
                        }
                    }
                }
            }
            Log::channel('google_ads')->info(
                "Google Ads - {$requestBody['campaign_name']}: Assets created"
            );

            GoogleAd::create([
                'client_id' => $requestAd->client_id,
                'ad_request_id' => $request->ad_request_id,
                'campaign_budget_resource_name' => $campaignBudget ?? '',

                'campaign_name' => $request->campaign_name,
                'campaign_type' => $request->campaign_type,
                'campaign_budget_type' => $request->campaign_budget_type,
                'campaign_budget_amount' =>
                    $requestBody['campaign_budget_amount'],
                'campaign_target_url' => $request->campaign_target_url ?? '',
                'campaign_start_date' => $startDate,
                'campaign_end_date' => $endDate,
                'campaign_resource_name' => $campaign ?? '',

                'locations' => json_encode($requestBody['locations']),
                'languages' => $requestBody['language'],

                'ad_group_name' => $request->campaign_name,
                'ad_group_bid_amount' => $requestBody['campaign_budget_amount'],
                'ad_group_resource_name' => $adGroup ?? '',

                'keywords' => json_encode($requestBody['keywords']),
                'keyword_match_types' => $request->keyword_match_types ?? '',

                'ad_name' => $request->ad_name,
                'ad_final_url' => $request->ad_url,
                'ad_headlines' => json_encode($request->ad_headlines),
                'ad_descriptions' => json_encode($request->ad_descriptions),
                'ad_sitelinks' => json_encode($requestBody['sitelinks']),
                'ad_resource_name' => $ad ?? '',

                'google_account_id' => $googleAccountId,
                'customer_id' => $customerId,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Google ads created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to Google ad: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function google_ads_sync(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-group-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAccountId = $client->google_account_id;

            $googleAccount = GoogleAccount::find($client->google_account_id);
            if (!$googleAccount) {
                return redirect()
                    ->back()
                    ->with('error', 'Google Ads not found.');
            }

            $customerId = $client->customer_id;
            $adGroupAdIds = $request->ad_id;
            $campaignResourceName = "customers/{$customerId}/adGroupAds/{$adGroupAdIds}";
            $checkAd = GoogleAd::where(
                'ad_resource_name',
                $campaignResourceName
            )->first();
            if ($checkAd) {
                return redirect()
                    ->back()
                    ->with('error', 'Google Ad already exist.');
            }

            $googleAdsService = new GoogleAdsService();
            $googleAd = $googleAdsService->getAdByResourceName(
                $customerId,
                $campaignResourceName
            );

            GoogleAd::create([
                'client_id' => $client->id,
                'ad_request_id' => $request->ad_request_id,
                'campaign_budget_resource_name' => null,

                'campaign_name' => $googleAd['campaign']['name'] ?? null,
                'campaign_type' =>
                    $googleAd['campaign']['advertisingChannelType'] ?? null,
                'campaign_budget_type' => null,
                'campaign_budget_amount' => null,
                'campaign_target_url' => null,
                'campaign_start_date' =>
                    $googleAd['campaign']['startDate'] ?? null,
                'campaign_end_date' => $googleAd['campaign']['endDate'] ?? null,
                'campaign_resource_name' =>
                    $googleAd['campaign']['resourceName'] ?? null,

                'locations' => null,
                'languages' => null,

                'ad_group_name' => $googleAd['adGroup']['name'] ?? null,
                'ad_group_bid_amount' =>
                    $adGroup['adGroup']['cpcBidMicros'] ?? null,
                'ad_group_resource_name' =>
                    $adGroup['adGroup']['resourceName'] ?? null,

                'keywords' => null,
                'keyword_match_types' => null,

                'ad_name' => 'Imported Ad',
                'ad_final_url' =>
                    $googleAd['adGroupAd']['ad']['finalUrls'][0] ?? null,
                'ad_headlines' => null,
                'ad_descriptions' => null,
                'ad_sitelinks' => null,
                'ad_resource_name' =>
                    $googleAd['adGroupAd']['resourceName'] ?? null,

                'google_account_id' => $googleAccountId,
                'customer_id' => $customerId,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Google ads synced successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to Google ad: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function google_ads_conversion_action(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-group-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();

        return view(
            'admin.client_management.google_ads_conversion_action.index',
            [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'Google Ads Conversion Action',
                'title' => 'Google Ads Conversion Action',
                'sub_account_id' => $sessionId,
                'clients' => User::where(
                    'sub_account_id',
                    hashids_decode($sessionId)
                )->get(),
                'client' => $client,
            ]
        );
    }

    public function google_ads_conversion_action_create()
    {
        if (Auth::user('admin')->can('google-ad-group-write') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();

        return view(
            'admin.client_management.google_ads_conversion_action.create',
            [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'Create Google Ads Conversion Action',
                'title' => 'Create Google Ads Conversion Action',
                'sub_account_id' => $sessionId,
                'clients' => User::where(
                    'sub_account_id',
                    hashids_decode($sessionId)
                )->get(),
                'client' => $client,
            ]
        );
    }

    public function google_ads_conversion_action_store(Request $request)
    {
        if (
            Auth::user('admin')->can('google-ad-group-write') != true ||
            Auth::user('admin')->can('google-ad-group-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (!Auth::user('admin')->google_access_token) {
                return redirect()
                    ->back()
                    ->with('error', 'Google Ads not connected yet.');
            }

            // Validate request
            $requestBody = $request->validate([
                'client_id' => 'required|exists:users,id',
                'name' => 'array',
                'name.*' => 'required|string|max:255',
                'type' => 'array',
                'type.*' => 'required|string',
                'category' => 'array',
                'category.*' => 'required|string',
                'website_url' => 'array',
                'website_url.*' => 'required|string',
                'counting_type' => 'array',
                'counting_type.*' => 'required|string',
                'click_through_days' => 'array',
                'click_through_days.*' => 'required|integer',
                'view_through_days' => 'array',
                'view_through_days.*' => 'required|integer',
            ]);

            // Find client
            $client = User::findOrFail($request->client_id);

            // Check customer id
            if (is_null($client->customer_id)) {
                return redirect()
                    ->back()
                    ->with('error', 'The client does not have a customer ID.');
            }
            $customerId = $client->customer_id;

            // Initiate google ads service
            $googleAdsService = new GoogleAdsService();
            // Create conversion action
            $createConversionAction = $googleAdsService->createConversionAction(
                $customerId,
                $requestBody
            );
            if (!isset($createConversionAction['results'][0]['resourceName'])) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Failed to create conversion action. No resource name returned.'
                    );
            }

            for ($i = 0; $i < count($requestBody['name']); $i++) {
                GoogleAdsConversionAction::create([
                    'client_id' => $requestBody['client_id'],
                    'name' => $requestBody['name'][$i],
                    'type' => $requestBody['type'][$i],
                    'category' => $requestBody['category'][$i],
                    'website_url' => $requestBody['website_url'][$i],
                    'counting_type' => $requestBody['counting_type'][$i],
                    'click_through_days' =>
                        $requestBody['click_through_days'][$i],
                    'view_through_days' =>
                        $requestBody['view_through_days'][$i],
                    'resource_name' =>
                        $createConversionAction['results'][$i]['resourceName'],
                    'customer_id' => $customerId,
                ]);
            }

            $parts = explode(
                '/',
                $createConversionAction['results'][0]['resourceName']
            );
            $conversionActionId = end($parts);

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Google ads conversion action created successfully.'
                )
                ->with('conversionActionId', $conversionActionId);
        } catch (\Exception $e) {
            Log::error(
                'Failed to create conversion action: ' . $e->getMessage()
            );
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function get_ads(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Ads::query()->where('client_id', hashids_decode($request->id))->latest())
                ->addIndexColumn()
                ->addColumn('adds_title', function ($data) {
                    return Str::limit($data->adds_title, 20, '...') ?? '-';
                })
                ->addColumn('type', function ($data) {
                    $types = explode(',', $data->type);
                    return Str::limit(ads_type_text($types), 30, '...');
                })
                ->addColumn('status', function ($data) {
                    return view('admin.client_management.include.ads_status', ['data' => $data]);
                })
                ->addColumn('spend_amount', function ($data) {
                    return number_format($data->spend_amount, 2);
                })
                ->addColumn('action', function ($data) {
                    return view('admin.client_management.include.ads_action_td', ['data' => $data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title','type'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    public function get_topups(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(WalletTopUp::query()->where('client_id', hashids_decode($request->id))->latest())
                ->addIndexColumn()
                ->addColumn('topup_amount', function ($data) {
                    return $data->topup_amount;
                })
                ->addColumn('status', function ($data) {
                    return view('admin.client_management.include.status', ['data' => $data]);
                })
                ->addColumn('slip', function ($data) {
                    return view('admin.client_management.include.slip', ['data' => $data]);
                })
                ->addColumn('action', function ($data) {
                    return view('admin.client_management.include.topup_status', ['data' => $data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','topup_amount'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    public function transactions(Request $request)
    {
        $auth_id = hashids_decode($request->id);

        $get_transection = Transections::with('get_top_up', 'get_ads')->where('client_id', $auth_id);
        if ($request->ajax()) {
            return DataTables::of(Transections::query()->with('get_top_up', 'get_ads')->where('client_id', $auth_id))
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return $data->created_at->format('M-d-Y');
                })
                ->addColumn('trans_type', function ($data) {
                    return view('admin.client_management.include.trans_type', ['data' => $data]);
                })
                ->addColumn('debit', function ($data) {
                    return view('admin.client_management.include.debit', ['data' => $data]);
                })
                ->addColumn('credit', function ($data) {
                    return view('admin.client_management.include.credit', ['data' => $data]);
                })
                ->addColumn('vat_charges', function ($data) {
                    return view('admin.client_management.include.vat_charges', ['data' => $data]);
                })
                ->addColumn('available_balance', function ($data) {
                    return view('admin.client_management.include.avail_amount', ['data' => $data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['available_balance','amount_in', 'amount_out'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    public function get_leads(Request $request)
    {
        $todayDate = now()->format('Y-m-d');
        $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');
        if ($request->ajax()) {
            if ($request->type == 'all_leads') {
                return DataTables::of(LeadClient::query()->with('activity')
                    ->where('client_id', hashids_decode($request->id))->latest())
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {
                        return view('admin.client_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('details', function ($data) {
                        return view('admin.client_management.include.detail_td', ['data' => $data]);
                        // return Str::limit($data->note ?? '-', 50); // Replace 50 with your desired character limit
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
            } elseif ($request->type == 'new_leads') {
                return DataTables::of(LeadClient::query()->with('activity')
                    ->where('client_id', hashids_decode($request->id))->where('status', 'new_lead')->latest())
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {
                        return view('admin.client_management.include.name_td', ['data' => $data]);
                    })
                    ->addColumn('source', function ($data) {
                        return '-';
                    })
                    ->addColumn('details', function ($data) {
                        return view('admin.client_management.include.detail_td', ['data' => $data]);
                        // return Str::limit($data->note ?? '-', 50); // Replace 50 with your desired character limit
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
            }
        }
    }
}
