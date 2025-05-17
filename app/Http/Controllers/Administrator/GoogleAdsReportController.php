<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleReport;
use App\Models\Admin;
use App\Models\GoogleAdsAccount;
use App\Traits\GoogleTrait;
use App\Models\CampaignNote;
use App\Models\GoogleAd;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

class GoogleAdsReportController extends Controller
{
    use GoogleTrait;

    public function update_act_expiry_date(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-report-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $google_ads_account = GoogleAdsAccount::where(
            'account_id',
            $request->account_id
        )->first();

        if ($google_ads_account) {
            $google_ads_account->account_expiry_date = $request->expiry_date;
            $google_ads_account->save();

            $message = 'Google Ads Account Expiry Date Updated Successfully';
        } else {
            $message = 'Google Ads Account Not Found';
        }

        return response()->json([
            'success' => $message,
            'reload' => true,
        ]);
    }

    public function google_ads_report()
    {
        if (Auth::user('admin')->can('google-ad-report-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();

        // save Customer in database if google account connected & google ads account table is empty
        $check_google_customer_exists = GoogleAdsAccount::count();
        if (
            !empty(auth('admin')->user()->provider_id) &&
            !empty(auth('admin')->user()->google_access_token) &&
            $check_google_customer_exists == 0
        ) {
            $this->save_customer_account_detail();
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();
        $get_google_report = GoogleReport::where(
            'client_id',
            $client->id
        )->first();

        $format_last_update = null;

        $dates = '';
        $clicks = '';
        $impressions = '';
        $conversations = '';
        $average_cpc = '';
        $cost = '';
        $conversation_rate = '';
        $cost_per_con = '';
        $ctr = '';

        $performance_dates = '';
        $costs = '';
        $cost_per_1000_imp = '';
        $cost_per_click = '';
        $reveneu_per_click = '';
        $total_value = '';

        if ($get_google_report) {
            $campaign = json_decode($get_google_report->campaign, true);
            $ads_group = json_decode($get_google_report->ads_group, true);
            $keywords = json_decode($get_google_report->keywords, true);
            $ads = json_decode($get_google_report->ads, true);
            $devices = json_decode(
                $get_google_report->performance_device,
                true
            );
            $summary_graph = json_decode(
                $get_google_report->summary_graph_data,
                true
            );
            $performance_graph = json_decode(
                $get_google_report->performance_graph_data,
                true
            );
            $format_last_update = Carbon::parse(
                $get_google_report->last_update
            );

            if (is_array($summary_graph) && !empty($summary_graph['dates'])) {
                foreach ($summary_graph['dates'] as $key => $summary_graph_v) {
                    $dates .= "'" . $summary_graph_v . "',";
                    $clicks .=
                        "'" . round($summary_graph['clicks'][$key]) . "',";
                    $impressions .=
                        "'" . round($summary_graph['impressions'][$key]) . "',";
                    $conversations .=
                        "'" . round($summary_graph['conversions'][$key]) . "',";
                    $average_cpc .=
                        "'" . round($summary_graph['average_cpc'][$key]) . "',";
                    $cost .= "'" . round($summary_graph['cost'][$key]) . "',";
                    $conversation_rate .=
                        "'" .
                        round(
                            $summary_graph['conversation_rate'][$key] * 100,
                            2
                        ) .
                        "',";
                    $cost_per_con .=
                        "'" .
                        round($summary_graph['cost_per_conversion'][$key]) .
                        "',";
                    $ctr .=
                        "'" .
                        round(
                            (round($summary_graph['clicks'][$key]) /
                                round($summary_graph['impressions'][$key])) *
                                100
                        ) .
                        "',";
                }
            }

            if (
                is_array($performance_graph) &&
                !empty($performance_graph['dates'])
            ) {
                foreach (
                    $performance_graph['dates'] as $key => $performance_graph_v
                ) {
                    $performance_dates .= "'" . $performance_graph_v . "',";
                    $costs .=
                        "'" . round($performance_graph['cost'][$key]) . "',";
                    $cost_per_1000_imp .=
                        "'" .
                        round(
                            $performance_graph['cost_per_1000_impressions'][
                                $key
                            ]
                        ) .
                        "',";
                    $cost_per_click .=
                        "'" .
                        round($performance_graph['cost_per_click'][$key]) .
                        "',";
                    $reveneu_per_click .=
                        "'" .
                        round($performance_graph['revenue_per_click'][$key]) .
                        "',";
                    $total_value .=
                        "'" .
                        round($performance_graph['total_value'][$key]) .
                        "',";
                }
            }
        }

        $campaign_notes = CampaignNote::where(
            'ads_report',
            'google_ads_report'
        )->get();
        $campaign_with_notes = [];

        if (isset($campaign['results']) && !empty($campaign['results'])) {
            foreach ($campaign['results'] as $item) {
                $campaigns = $item['campaign'];
                $metrics = $item['metrics'];
                $campaign_budget = $item['campaignBudget'];
                $campaign_ctr =
                    $metrics['impressions'] > 0
                        ? ($metrics['clicks'] / $metrics['impressions']) * 100
                        : 0;
                $campaign_cost =
                    $metrics['costMicros'] > 0
                        ? $metrics['costMicros'] / 1000000
                        : 0;
                $campaign_start_date = $campaigns['startDate'];
                $format_start_date = date(
                    'jS M Y',
                    strtotime($campaign_start_date)
                );
                $cost_per_conversation =
                    $metrics['conversions'] > 0
                        ? $campaign_cost / $metrics['conversions']
                        : 0;
                $cal_campaign_budget =
                    $campaign_budget['amountMicros'] > 0
                        ? $campaign_budget['amountMicros'] / 1000000
                        : 0;

                // calculate daily budget

                // $daily_budg_start_date = new DateTime($campaign_start_date);
                // $daily_budg_end_date = new DateTime($end_date);
                // $interval = $daily_budg_start_date->diff($daily_budg_end_date);
                // $total_days = $interval->days;
                // $daily_budget = ($total_days > 0) ? ($cost / $total_days * 100) : 0;

                $campaign_notes_for_campaign = $campaign_notes
                    ->where('campaign_name', $campaigns['name'])
                    ->pluck('note')
                    ->toArray();

                $campaign_with_notes[] = [
                    'campaign_id' => $campaigns['id'],
                    'name' => $campaigns['name'],
                    'date' => $format_start_date,
                    'total_leads' => $metrics['conversions'] ?? '0',
                    'cost_per_conversation' => $cost_per_conversation ?? '0',
                    'spend' => $campaign_cost ?? '0',
                    'campaign_budget' => $cal_campaign_budget ?? '0',
                    'campaign_notes' => $campaign_notes_for_campaign,
                ];
            }
        }

        if (
            isset($ads['results']) &&
            !empty($ads['results']) &&
            !empty($campaign_with_notes)
        ) {
            foreach ($ads['results'] as $ad) {
                $campaign_id = $ad['campaign']['id'];
                $ad_final_url =
                    $ad['adGroupAd']['ad']['finalUrls'][0] ??
                    'No Website URL Found';

                foreach ($campaign_with_notes as &$campaign_note) {
                    if ($campaign_note['campaign_id'] == $campaign_id) {
                        $campaign_note['final_url'] =
                            $ad_final_url ?? 'No Website URL Found';

                        break;
                    }
                }
            }
        }

        $data = [
            'breadcrumb_main' => 'Google Ads Report',
            'breadcrumb' => 'Google Ads Report',
            'title' => 'Google Ads Report',
            'campaign' => $campaign ?? '',
            'campaign_notes' => $campaign_notes,
            'ads_group' => $ads_group ?? '',
            'keywords' => $keywords ?? '',
            'ads' => $ads ?? '',
            'performance_device' => $devices ?? '',
            'get_customers' => GoogleAdsAccount::get(),
            'customer_account_id' => $get_google_report->act_id ?? '',
            'start_date' => $get_google_report->start_date ?? '',
            'end_date' => $get_google_report->end_date ?? '',
            'last_updated' => isset($format_last_update)
                ? $format_last_update->diffForHumans()
                : 'No Data Found',
            'last_updated_date' => isset($format_last_update)
                ? $format_last_update->format('M d, Y')
                : 'No Data Found',
            'get_facebook_ads_account' => GoogleAdsAccount::where(
                'account_id',
                $get_google_report->act_id ?? ''
            )->first(),
            'campaign_with_notes' => $campaign_with_notes,

            // summary graph data
            'summary_graph_dates' => $dates,
            'summary_graph_clicks' => $clicks,
            'summary_graph_impressions' => $impressions,
            'summary_graph_conversations' => $conversations,

            // performance graph data
            'performance_graph_dates' => $performance_dates,
            'performance_graph_costs' => $costs,
            'performance_graph_cost_per_1000_imp' => $cost_per_1000_imp,
            'performance_graph_cost_per_click' => $cost_per_click,
            'performance_graph_reveneu_per_click' => $reveneu_per_click,
            'performance_graph_total_value' => $total_value,

            // widget graph data
            'total_impressions' => array_sum(
                $summary_graph['impressions'] ?? [0]
            ),
            'total_clicks' => array_sum($summary_graph['clicks'] ?? [0]),
            'total_conversions' => array_sum(
                $summary_graph['conversions'] ?? [0]
            ),
            'total_cost' => array_sum($summary_graph['cost'] ?? [0]),

            'widget_graph_average_cpc' => $average_cpc,
            'widget_graph_cost' => $cost,
            'widget_graph_conversation_rate' => $conversation_rate,
            'widget_graph_cost_per_conversion' => $cost_per_con,
            'widget_graph_ctr' => $ctr,

            'client' => $client,
        ];

        return view('admin.google_report.index', $data);
    }

    public function save_google_report(Request $request)
    {
        if (
            Auth::user('admin')->can('google-ad-report-write') != true ||
            Auth::user('admin')->can('google-ad-report-update') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->daterange) {
            $dates = explode('-', $request->daterange);
            $startDate = trim($dates[0]);
            $endDate = trim($dates[1]);

            $startDateFormatted = date('Y-m-d', strtotime($startDate));
            $endDateFormatted = date('Y-m-d', strtotime($endDate));

            $acct_id = $request->date_act_id;
            $start_date = $startDateFormatted;
            $end_date = $endDateFormatted;
        } else {
            $acct_id = $request->google_account_id;
            $start_date =
                $request->act_start_date ?? Carbon::today()->toDateString();
            $end_date =
                $request->act_end_date ?? Carbon::today()->toDateString();
        }

        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();
        $acct_id = $client->customer_id;

        $devloper_token = config('services.google.developer_token');

        // google access token
        $get_client = $this->getAdminClient();
        $get_access_token = $get_client->getAccessToken();
        $access_token = $get_access_token['access_token'];

        if (!empty($access_token)) {
            $get_google_campaigns = $this->get_google_campaigns(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_google_ads_group = $this->get_google_ads_group(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_google_keywords = $this->get_google_keywords(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_google_ads = $this->get_google_ads(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_performance_devices = $this->get_performance_devices(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_summary_graph_data = $this->get_summary_graph_data(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );
            $get_performance_data = $this->get_performance_data_for_graph(
                $start_date,
                $end_date,
                $acct_id,
                $access_token,
                $devloper_token
            );

            GoogleReport::updateOrCreate(
                [
                    'act_id' => $acct_id,
                    'client_id' => $client->id,
                ],
                [
                    'campaign' => $get_google_campaigns,
                    'ads_group' => $get_google_ads_group,
                    'keywords' => $get_google_keywords,
                    'ads' => $get_google_ads,
                    'performance_device' => $get_performance_devices,
                    'summary_graph_data' => $get_summary_graph_data,
                    'performance_graph_data' => $get_performance_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'last_update' => now(),
                ]
            );

            return response()->json([
                'success' => 'Google Report Data Fetch Successfully',
                'reload' => true,
            ]);
        }
    }

    public function google_act_disconnect(Request $request)
    {
        if (Auth::user('admin')->can('google-ad-report-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $admin = Admin::where('id', auth('admin')->id())->first();
            $admin->provider_id = null;
            $admin->provider_name = null;
            $admin->google_access_token = null;
            $admin->save();
            GoogleAdsAccount::truncate();
            GoogleReport::truncate();
            CampaignNote::where('ads_report', 'google_ads_report')->delete();

            return response()->json([
                'success' => 'Google Account Disconnected Successfully',
            ]);
        }
    }

    public function campaign_note_save(Request $request)
    {
        $rules = [
            'note_date' => 'required',
            'campaign' => 'required',
            'notes' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $campaign_note = new CampaignNote();
        $campaign_note->note_date = $request->note_date;
        $campaign_note->campaign_name = $request->campaign;
        $campaign_note->note = $request->notes;
        $campaign_note->ads_report = 'google_ads_report';
        $campaign_note->save();

        $msg = [
            'success' => 'Campaign Notes Added Successfully',
            'redirect' => route(
                'admin.sub_account.google-ads-report.google_ads_report',
                ['sub_account_id' => session()->get('sub_account_id')]
            ),
        ];

        return response()->json($msg);
    }

    public function campaign_note_delete($id)
    {
        $campaign_note = CampaignNote::hashidFind($id);
        $campaign_note->delete();

        return response()->json([
            'success' => 'Campaign Note Deleted Successfully',
            'redirect' => route(
                'admin.sub_account.google-ads-report.google_ads_report',
                ['sub_account_id' => session()->get('sub_account_id')]
            ),
        ]);
    }

    public function download_pdf()
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where(
            'sub_account_id',
            hashids_decode($sessionId)
        )->first();
        $get_google_report = GoogleReport::where(
            'client_id',
            $client->id
        )->first();

        $format_last_update = null;

        $campaign = '';
        $ads_group = '';
        $keywords = '';
        $ads = '';
        $devices = '';

        $dates = '';
        $clicks = '';
        $impressions = '';
        $conversations = '';

        $performance_dates = '';
        $costs = '';
        $cost_per_1000_imp = '';
        $cost_per_click = '';
        $reveneu_per_click = '';
        $total_value = '';

        if ($get_google_report) {
            $campaign = json_decode($get_google_report->campaign, true);
            $ads_group = json_decode($get_google_report->ads_group, true);
            $keywords = json_decode($get_google_report->keywords, true);
            $ads = json_decode($get_google_report->ads, true);
            $devices = json_decode(
                $get_google_report->performance_device,
                true
            );
            $format_last_update = Carbon::parse(
                $get_google_report->last_update
            );
            $summary_graph = json_decode(
                $get_google_report->summary_graph_data,
                true
            );
            $performance_graph = json_decode(
                $get_google_report->performance_graph_data,
                true
            );

            if (is_array($summary_graph) && !empty($summary_graph['dates'])) {
                foreach ($summary_graph['dates'] as $key => $summary_graph_v) {
                    $dates .= "'" . $summary_graph_v . "',";
                    $clicks .=
                        "'" . round($summary_graph['clicks'][$key]) . "',";
                    $impressions .=
                        "'" . round($summary_graph['impressions'][$key]) . "',";
                    $conversations .=
                        "'" . round($summary_graph['conversions'][$key]) . "',";
                }
            }

            if (
                is_array($performance_graph) &&
                !empty($performance_graph['dates'])
            ) {
                foreach (
                    $performance_graph['dates'] as $key => $performance_graph_v
                ) {
                    $performance_dates .= "'" . $performance_graph_v . "',";
                    $costs .=
                        "'" . round($performance_graph['cost'][$key]) . "',";
                    $cost_per_1000_imp .=
                        "'" .
                        round(
                            $performance_graph['cost_per_1000_impressions'][
                                $key
                            ]
                        ) .
                        "',";
                    $cost_per_click .=
                        "'" .
                        round($performance_graph['cost_per_click'][$key]) .
                        "',";
                    $reveneu_per_click .=
                        "'" .
                        round($performance_graph['revenue_per_click'][$key]) .
                        "',";
                    $total_value .=
                        "'" .
                        round($performance_graph['total_value'][$key]) .
                        "',";
                }
            }

            $campaign_notes = CampaignNote::where(
                'ads_report',
                'google_ads_report'
            )->get();
            $campaign_with_notes = [];

            if (isset($campaign['results']) && !empty($campaign['results'])) {
                foreach ($campaign['results'] as $item) {
                    $campaigns = $item['campaign'];
                    $metrics = $item['metrics'];
                    $campaign_budget = $item['campaignBudget'];
                    $campaign_ctr =
                        $metrics['impressions'] > 0
                            ? ($metrics['clicks'] / $metrics['impressions']) *
                                100
                            : 0;
                    $campaign_cost =
                        $metrics['costMicros'] > 0
                            ? $metrics['costMicros'] / 1000000
                            : 0;
                    $campaign_start_date = $campaigns['startDate'];
                    $format_start_date = date(
                        'jS M Y',
                        strtotime($campaign_start_date)
                    );
                    $cost_per_conversation =
                        $metrics['conversions'] > 0
                            ? $campaign_cost / $metrics['conversions']
                            : 0;
                    $cal_campaign_budget =
                        $campaign_budget['amountMicros'] > 0
                            ? $campaign_budget['amountMicros'] / 1000000
                            : 0;

                    // calculate daily budget

                    // $daily_budg_start_date = new DateTime($campaign_start_date);
                    // $daily_budg_end_date = new DateTime($end_date);
                    // $interval = $daily_budg_start_date->diff($daily_budg_end_date);
                    // $total_days = $interval->days;
                    // $daily_budget = ($total_days > 0) ? ($cost / $total_days * 100) : 0;

                    $campaign_notes_for_campaign = $campaign_notes
                        ->where('campaign_name', $campaigns['name'])
                        ->pluck('note')
                        ->toArray();

                    $campaign_with_notes[] = [
                        'campaign_id' => $campaigns['id'],
                        'name' => $campaigns['name'],
                        'date' => $format_start_date,
                        'total_leads' => $metrics['conversions'] ?? '0',
                        'cost_per_conversation' =>
                            $cost_per_conversation ?? '0',
                        'spend' => $campaign_cost ?? '0',
                        'campaign_budget' => $cal_campaign_budget ?? '0',
                        'campaign_notes' => $campaign_notes_for_campaign,
                    ];
                }
            }

            if (
                isset($ads['results']) &&
                !empty($ads['results']) &&
                !empty($campaign_with_notes)
            ) {
                foreach ($ads['results'] as $ad) {
                    $campaign_id = $ad['campaign']['id'];
                    $ad_final_url =
                        $ad['adGroupAd']['ad']['finalUrls'][0] ??
                        'No Website URL Found';

                    foreach ($campaign_with_notes as &$campaign_note) {
                        if ($campaign_note['campaign_id'] == $campaign_id) {
                            $campaign_note['final_url'] =
                                $ad_final_url ?? 'No Website URL Found';

                            break;
                        }
                    }
                }
            }
        }

        if ($get_google_report) {
            $get_add_account_name = GoogleAdsAccount::where(
                'account_id',
                $get_google_report->act_id
            )->first();
        }

        $data = [
            'breadcrumb_main' => 'Google Ads Report',
            'breadcrumb' => 'Google Ads Report',
            'title' => 'Google Ads Report',
            'campaign' => $campaign ?? '',
            'campaign_notes' => $campaign_notes ?? [],
            'ads_group' => $ads_group ?? '',
            'keywords' => $keywords ?? '',
            'ads' => $ads ?? '',
            'performance_device' => $devices ?? '',
            'ad_account_name' => $get_add_account_name->account_title ?? '',
            'generated_on' => Carbon::now()->format('M-d-Y H:i A'),
            'start_date' => $get_google_report->start_date ?? '',
            'end_date' => $get_google_report->end_date ?? '',
            'campaign_with_notes' => $campaign_with_notes ?? '',

            // summary graph data
            'summary_graph_dates' => $dates,
            'summary_graph_clicks' => $clicks,
            'summary_graph_impressions' => $impressions,
            'summary_graph_conversations' => $conversations,

            // performance graph data
            'performance_graph_dates' => $performance_dates,
            'performance_graph_costs' => $costs,
            'performance_graph_cost_per_1000_imp' => $cost_per_1000_imp,
            'performance_graph_cost_per_click' => $cost_per_click,
            'performance_graph_reveneu_per_click' => $reveneu_per_click,
            'performance_graph_total_value' => $total_value,

            // widget graph data
            'total_impressions' => array_sum(
                $summary_graph['impressions'] ?? [0]
            ),
            'total_clicks' => array_sum($summary_graph['clicks'] ?? [0]),
            'total_conversions' => array_sum(
                $summary_graph['conversions'] ?? [0]
            ),
            'total_cost' => array_sum($summary_graph['cost'] ?? [0]),
        ];

        $html = view('admin.google_report.pdf_file')
            ->with($data)
            ->render();
        $nmp_path = config('app.nmp_path');
        sleep(10);
        $timeout = 50000;
        $pdf = Browsershot::html($html)
            ->setTimeout($timeout)
            ->setIncludePath('$PATH:' . $nmp_path)
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->pdf();

        $accountTitle = '';
        if (isset($get_add_account_name->account_title)) {
            $accountTitle = $get_add_account_name->account_title ?? '';
        }
        $fileName =
            $accountTitle . ' - ' . Carbon::now()->format('Y-m-d') . '.pdf';

        $pdfPath = storage_path('app/public/' . $fileName);
        file_put_contents($pdfPath, $pdf);

        return response()
            ->download($pdfPath)
            ->deleteFileAfterSend(true);
    }

    private function get_google_campaigns(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT campaign.id, campaign.start_date, campaign.end_date, campaign.name, campaign.target_roas.target_roas, campaign_budget.amount_micros, campaign.status, metrics.clicks, metrics.impressions, metrics.ctr, metrics.conversions, metrics.cost_micros FROM campaign WHERE campaign.status != 'REMOVED' AND segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        return json_encode($decoded_response);
    }

    private function get_google_ads_group(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT campaign.id, ad_group.id, ad_group.name, ad_group.status, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.conversions FROM ad_group WHERE ad_group.status != 'PAUSED' AND segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        return json_encode($decoded_response);
    }

    private function get_google_keywords(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT campaign.id, ad_group_criterion.keyword.text, ad_group_criterion.approval_status, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.conversions, metrics.all_conversions_value FROM keyword_view WHERE ad_group_criterion.approval_status = 'APPROVED' AND segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        return json_encode($decoded_response);
    }

    private function get_google_ads(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT campaign.id, ad_group_ad.ad.type, ad_group_ad.ad.name, campaign.id, ad_group_ad.ad.final_urls, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.conversions FROM ad_group_ad WHERE segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        return json_encode($decoded_response);
    }

    private function get_performance_devices(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT segments.device, campaign.id, metrics.clicks, metrics.conversions, metrics.cost_micros, metrics.ctr, metrics.impressions FROM ad_group WHERE segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter($data['results'], function (
                $result
            ) use ($googleAdsResoucesNames) {
                return in_array(
                    $result['campaign']['resourceName'],
                    $googleAdsResoucesNames
                );
            });

            $data['results'] = array_values($filteredResults);
        }

        $device_sums = [
            'DESKTOP' => [
                'impressions' => 0,
                'clicks' => 0,
                'ctr' => 0,
                'conversions' => 0,
                'cost' => 0,
            ],
            'MOBILE' => [
                'impressions' => 0,
                'clicks' => 0,
                'ctr' => 0,
                'conversions' => 0,
                'cost' => 0,
            ],
            'TABLET' => [
                'impressions' => 0,
                'clicks' => 0,
                'ctr' => 0,
                'conversions' => 0,
                'cost' => 0,
            ],
        ];

        if (isset($data['results'])) {
            foreach ($data['results'] as $result) {
                $device = $result['segments']['device'];
                $metrics = $result['metrics'];

                if (!isset($device_sums[$device])) {
                    $device_sums[$device] = [
                        'impressions' => 0,
                        'clicks' => 0,
                        'ctr' => 0,
                        'conversions' => 0,
                        'cost' => 0,
                    ];
                }

                $device_sums[$device]['impressions'] += intval(
                    $metrics['impressions'] ?? 0
                );
                $device_sums[$device]['clicks'] += intval(
                    $metrics['clicks'] ?? 0
                );
                $device_sums[$device]['conversions'] += intval(
                    $metrics['conversions'] ?? 0
                );

                if ($device_sums[$device]['impressions'] > 0) {
                    $device_sums[$device]['ctr'] += floatval(
                        $metrics['ctr'] ?? 0
                    );
                }

                if (
                    isset($metrics['costMicros']) &&
                    $metrics['costMicros'] > 0
                ) {
                    $device_sums[$device]['cost'] +=
                        floatval($metrics['costMicros']) / 1000000;
                }
            }
        }

        foreach ($device_sums as $device => &$sums) {
            $sums['ctr'] =
                $sums['clicks'] > 0 ? $sums['ctr'] / $sums['clicks'] : 0;
        }

        return json_encode($device_sums);
    }

    private function get_accessible_customer($devloper_token, $access_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://googleads.googleapis.com/v16/customers:listAccessibleCustomers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        $ids = [];
        if (!isset($data['resourceNames'])) {
            return false;
        }
        foreach ($data['resourceNames'] as $resourceName) {
            $ids[] = substr($resourceName, strpos($resourceName, '/') + 1);
        }

        return implode(', ', $ids);
    }

    private function get_customer_details_by_id(
        $customer_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $customer_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
            "query": "SELECT customer.descriptive_name, customer.id FROM customer_client"
        }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $customer_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    private function get_summary_graph_data(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT segments.date, campaign.id, campaign.name, metrics.clicks, metrics.impressions, metrics.conversions, metrics.ctr, metrics.average_cpc, metrics.cost_micros, metrics.cost_per_conversion FROM campaign WHERE campaign.status != 'REMOVED' AND segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        $formatted_response = [
            'dates' => [],
            'clicks' => [],
            'impressions' => [],
            'ctr' => [],
            'conversions' => [],
            'average_cpc' => [],
            'cost' => [],
            'conversation_rate' => [],
            'cost_per_conversion' => [],
        ];

        // Initialize progress data array
        if (
            isset($decoded_response['results']) &&
            is_array($decoded_response['results'])
        ) {
            foreach ($decoded_response['results'] as $result) {
                $date = $result['segments']['date'];
                $formatted_response['dates'][] = date(
                    'M d, Y',
                    strtotime($date)
                );
                $formatted_response['impressions'][] =
                    $result['metrics']['impressions'];
                $formatted_response['clicks'][] = $result['metrics']['clicks'];
                $formatted_response['ctr'][] = $result['metrics']['ctr'] * 100;
                $formatted_response['cost'][] =
                    $result['metrics']['costMicros'] / 1000000;
                if (
                    $result['metrics']['clicks'] !== 0 &&
                    $result['metrics']['costMicros']
                ) {
                    $formatted_response['average_cpc'][] =
                        $result['metrics']['costMicros'] /
                        ($result['metrics']['clicks'] * 1000000);
                } else {
                    $formatted_response['average_cpc'][] = 0;
                }
                // $formatted_response["average_cpc"][] = $result['metrics']['costMicros'] / 1000000 / $result['metrics']['clicks'];
                $formatted_response['conversions'][] =
                    $result['metrics']['conversions'];
                if (
                    $result['metrics']['clicks'] !== 0 &&
                    $result['metrics']['conversions']
                ) {
                    $formatted_response['conversation_rate'][] =
                        $result['metrics']['conversions'] /
                        $result['metrics']['clicks'];
                } else {
                    $formatted_response['conversation_rate'][] = 0;
                }
                // $formatted_response["conversation_rate"][] = $result['metrics']['conversions'] / $result['metrics']['clicks'];
                if (isset($result['metrics']['costPerConversion'])) {
                    $formatted_response['cost_per_conversion'][] =
                        $result['metrics']['costPerConversion'] / 1000000;
                } else {
                    $formatted_response['cost_per_conversion'][] = 0;
                }
            }

            return json_encode($formatted_response);
        } else {
            return;
        }
    }

    private function get_performance_data_for_graph(
        $start_date,
        $end_date,
        $acct_id,
        $access_token,
        $devloper_token
    ) {
        $curl = curl_init();

        $query = "SELECT segments.date, campaign.id, campaign.name, campaign.target_roas.target_roas, campaign.status, metrics.clicks, metrics.impressions, metrics.ctr, metrics.conversions, metrics.cost_micros FROM campaign WHERE campaign.status != 'REMOVED' AND segments.date BETWEEN '$start_date' AND '$end_date'";

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                'https://googleads.googleapis.com/v16/customers/' .
                $acct_id .
                '/googleAds:search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'developer-token: ' . $devloper_token,
                'Authorization: Bearer ' . $access_token,
                'login-customer-id: ' . $acct_id,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Filter only registered campaign on panel
        if (isset($decoded_response['results'])) {
            $sessionId = session()->get('sub_account_id');
            $client = User::where(
                'sub_account_id',
                hashids_decode($sessionId)
            )->first();
            $googleAdsResoucesNames = GoogleAd::where('client_id', $client->id)
                ->pluck('campaign_resource_name')
                ->toArray();

            $filteredResults = array_filter(
                $decoded_response['results'],
                function ($result) use ($googleAdsResoucesNames) {
                    return in_array(
                        $result['campaign']['resourceName'],
                        $googleAdsResoucesNames
                    );
                }
            );

            $decoded_response['results'] = array_values($filteredResults);
        }

        // Process the decoded response to format data for the graph
        $graph_data = [
            'dates' => [],
            'cost' => [],
            'cost_per_1000_impressions' => [],
            'cost_per_click' => [],
            'revenue_per_click' => [],
            // "revenue_per_impression" => array(),
            'total_value' => [],
        ];

        if (
            isset($decoded_response['results']) &&
            is_array($decoded_response['results'])
        ) {
            foreach ($decoded_response['results'] as $result) {
                // Extract relevant metrics
                $metrics = $result['metrics'];

                $date = date('M d, Y', strtotime($result['segments']['date']));
                $graph_data['dates'][] = $date;

                $cost = $metrics['costMicros'] / 1000000; // Converting from micros to actual currency
                $cost_per_1000_impressions =
                    $cost / ($metrics['impressions'] / 1000);

                if ($cost !== 0 && $metrics['clicks'] !== 0) {
                    $cost_per_click = $cost / $metrics['clicks'];
                } else {
                    $cost_per_click = 0;
                }

                $revenue_per_click =
                    $metrics['conversions'] > 0
                        ? $cost / $metrics['conversions']
                        : 0;
                // $total_value = max(0, $metrics['conversions'] - $cost);
                $total_value = $metrics['conversions'] - $cost;

                // Push data into respective arrays
                $graph_data['cost'][] = round($cost, 2);
                $graph_data['cost_per_1000_impressions'][] = round(
                    $cost_per_1000_impressions,
                    2
                );
                $graph_data['cost_per_click'][] = round($cost_per_click, 2);
                $graph_data['revenue_per_click'][] = round(
                    $revenue_per_click,
                    2
                );
                $graph_data['total_value'][] = round($total_value, 2);
            }

            return json_encode($graph_data);
        } else {
            return;
        }
    }

    private function save_customer_account_detail()
    {
        $devloper_token = config('services.google.developer_token');

        // google access token
        $get_client = $this->getAdminClient();
        $get_access_token = $get_client->getAccessToken();
        $access_token = $get_access_token['access_token'];

        $customer_ids = $this->get_accessible_customer(
            $devloper_token,
            $access_token
        );

        $customer_ids_array = explode(', ', $customer_ids);

        foreach ($customer_ids_array as $customer_id) {
            $customer_detail = $this->get_customer_details_by_id(
                $customer_id,
                $access_token,
                $devloper_token
            );
            if (
                isset($customer_detail['results']) &&
                !empty($customer_detail['results'])
            ) {
                $save_customer = new GoogleAdsAccount();
                $save_customer->account_title =
                    $customer_detail['results'][0]['customer'][
                        'descriptiveName'
                    ] ?? '';
                $save_customer->account_id =
                    $customer_detail['results'][0]['customer']['id'];
                $save_customer->save();
            }
        }
    }
}
