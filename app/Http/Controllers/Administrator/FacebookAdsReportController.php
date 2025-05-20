<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\CampaignNote;
use App\Models\FacebookAccessToken;
use App\Models\FacebookAdsAccount;
use App\Models\FacebookReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Socialite;
use Spatie\Browsershot\Browsershot;

class FacebookAdsReportController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function oauth()
    {

        try {
            $user = Socialite::driver('facebook')->user();
            $fb_access_token = new FacebookAccessToken;
            $fb_access_token->admin_id = auth('admin')->user()->id;
            $fb_access_token->access_token = $user->token;
            $fb_access_token->last_update = now();
            $fb_access_token->save();

            $ad_accounts = $this->get_ad_accounts();
            if (isset($ad_accounts['data']) && !empty($ad_accounts['data'])) {
                foreach ($ad_accounts['data'] as $account) {
                    if (isset($account['name']) && isset($account['id'])) {
                        $save_ad_account = new FacebookAdsAccount;
                        $save_ad_account->access_token_id = $fb_access_token->id;
                        $save_ad_account->act_name = $account['name'];
                        $save_ad_account->act_id = $account['id'];
                        $save_ad_account->save();
                    }

                }
            }

        } catch (\Exception $e) {
            return redirect()->route('admin.facebook-ads-report.fb_ads_report');
        }

        return redirect()->route('admin.facebook-ads-report.fb_ads_report');
    }

    public function fb_disconnect(Request $request)
    {
        if ($request->ajax()) {
            $admin_id = auth('admin')->id();
            FacebookAccessToken::truncate();
            FacebookAdsAccount::truncate();
            FacebookReport::truncate();
            CampaignNote::where('ads_report', 'facebook_ads_report')->delete();

            return response()->json([
                'success' => 'Facebook Account Disconnected Successfully',
            ]);
        }
    }

    public function update_act_expiry_date(Request $request)
    {

        $fb_ads_account = FacebookAdsAccount::where('act_id', $request->account_id)->first();

        if ($fb_ads_account) {

            $fb_ads_account->account_expiry_date = $request->expiry_date;
            $fb_ads_account->save();

            $msg = [
                'success' => 'Facebook Ads Account Expiry Date Updated Successfully',
                'reload' => true,
            ];

            return response()->json($msg);
        } else {

            $msg = [
                'error' => 'Facebook Ads Account Not Found',
                'reload' => true,
            ];

            return response()->json($msg);
        }
    }

    public function fb_ads_report()
    {

        $campaigns = null;
        $adsets = null;
        $ads = null;
        $facebook_report = FacebookReport::select('act_id', 'campaigns', 'adsets_daily_budget', 'adsets', 'ads', 'pie_chart_data', 'summary_graph', 'summary_detail', 'gender_graph_data', 'age_graph_data', 'start_date', 'end_date')->latest()->first();
        if ($facebook_report) {
            $campaigns = json_decode($facebook_report->campaigns);
            $adsets = json_decode($facebook_report->adsets);
            $ads = json_decode($facebook_report->ads);
            $campaign_daily_budget = json_decode($facebook_report->adsets_daily_budget);
        }

        $last_update = FacebookAccessToken::where('admin_id', auth('admin')->id())->latest()->first();
        $format_last_update = null;

        if ($last_update) {
            $format_last_update = Carbon::parse($last_update->last_update);
        }

        $campaign_notes = CampaignNote::where('ads_report', 'facebook_ads_report')->get();
        $campaigns_with_notes = [];

        if (isset($campaigns->data)) {
            foreach ($campaigns->data as $val) {
                if (isset($val->insights) && isset($val->insights->data[0])) {
                    $campaign_notes_for_campaign = $campaign_notes->where('campaign_name', $val->name)->pluck('note')->toArray();

                    $total_leads = 0;
                    $cost_per_lead = 0;
                    if (isset($val->insights->data[0]->cost_per_action_type)) {
                        foreach ($val->insights->data[0]->cost_per_action_type as $action_type) {
                            if ($action_type->action_type === 'lead') {
                                $total_leads = $action_type->value;
                                break;
                            }
                        }
                    }

                    if ($total_leads !== 0) {
                        $total_spend = $val->insights->data[0]->spend;
                        $cost_per_lead = $total_spend / $total_leads;
                    }

                    if (!isset($val->daily_budget) || $val->daily_budget === '0') {

                        $startDate = new \DateTime($val->start_time ?? now());
                        $endDate = new \DateTime($val->stop_time ?? now());

                        $interval = $startDate->diff($endDate);

                        $total_days = $interval->days + 1;

                        if (isset($val->lifetime_budget) && $val->lifetime_budget != 0) {
                            $val->daily_budget = round($val->lifetime_budget / $total_days);
                        }

                    }

                    if (!isset($val->daily_budget) && !isset($val->lifetime_budget)) {
                        $daily_budget = 0;
                        foreach ($campaign_daily_budget->data as $item) {
                            if ($item->campaign_id === $val->id && isset($item->daily_budget)) {
                                $daily_budget = $daily_budget + $item->daily_budget;
                                $val->daily_budget = $daily_budget;
                            }
                        }
                    }

                    $campaigns_with_notes[] = [
                        'name' => $val->name,
                        'campaign_notes' => $campaign_notes_for_campaign,
                        'daily_budget' => $val->daily_budget ?? 0,
                        'lifetime_budget' => $val->lifetime_budget ?? 0,
                        'total_leads' => $total_leads,
                        'cost_per_lead' => $cost_per_lead,
                        'status' => $val->status,
                        'id' => $val->id,
                        'insights' => $val->insights ?? [],
                    ];
                }
            }
        }

        $facebookAdsAccount = null;
        if (isset($facebook_report->act_id)) {
            $facebookAdsAccount = FacebookAdsAccount::where('act_id', $facebook_report->act_id)->first();
        }

        $data = [
            'breadcrumb' => 'Facebook Ads Report',
            'title' => 'Facebook Ads Report',
            'campaigns' => $campaigns->data ?? '',
            'adsets' => $adsets->data ?? '',
            'ads' => $ads->data ?? '',
            'country_detail' => $country_detail->data ?? '',
            'get_accounts' => FacebookAdsAccount::get(),
            'acct_id' => $facebook_report->act_id ?? '',
            'start_date' => $facebook_report->start_date ?? '',
            'end_date' => $facebook_report->end_date ?? '',
            'campaign_notes' => $campaign_notes,
            'summary_graph' => $facebook_report ? json_decode($facebook_report->summary_graph, true) : '',
            'pie_chart_data' => $facebook_report ? json_decode($facebook_report->pie_chart_data, true) : '',
            'summary_detail' => $facebook_report ? json_decode($facebook_report->summary_detail) : '',
            'gender_graph' => $facebook_report ? json_decode($facebook_report->gender_graph_data) : '',
            'age_graph' => $facebook_report ? json_decode($facebook_report->age_graph_data, true) : '',
            'last_updated' => isset($format_last_update) ? $format_last_update->diffForHumans() : 'No Data Found',
            'last_updated_date' => isset($format_last_update) ? $format_last_update->format('M d, Y') : 'No Data Found',
            'check_access_token' => FacebookAccessToken::where('admin_id', auth('admin')->user()->id)->count(),
            'get_facebook_ads_account' => $facebookAdsAccount,
            'campaigns_with_notes' => $campaigns_with_notes,
        ];

        return view('admin.fb_report.index', $data);
        exit();
    }

    public function save_fb_report(Request $request)
    {

        if ($request->daterange) {
            $dates = explode('-', $request->daterange);
            $startDate = trim($dates[0]);
            $endDate = trim($dates[1]);

            $startDateFormatted = date('Y-m-d', strtotime($startDate));
            $endDateFormatted = date('Y-m-d', strtotime($endDate));

            $act_id = $request->date_act_id;
            $startDate = $startDateFormatted;
            $endDate = $endDateFormatted;

        } else {

            $act_id = $request->account_id;
            $startDate = $request->act_start_date;
            $endDate = $request->act_end_date;
        }

        FacebookReport::truncate();
        $save_fb_report = new FacebookReport;

        // get campaigns code start
        $get_fb_campaigns = $this->get_facebook_campaigns($startDate, $endDate, $act_id);
        $save_fb_report->campaigns = json_encode($get_fb_campaigns);
        // get campaigns code end

        // get campaigns adset daily budget code start
        $get_fb_campaigns_daily_budget = $this->get_facebook_adsets_daily_budget($startDate, $endDate, $act_id);
        $save_fb_report->adsets_daily_budget = json_encode($get_fb_campaigns_daily_budget);
        // get campaigns adset daily budget code end

        // get adset code start
        $get_fb_adsets = $this->get_facebook_adsets($startDate, $endDate, $act_id);
        $save_fb_report->adsets = json_encode($get_fb_adsets);
        // get adset code end

        // get ads code start
        $get_fb_ads = $this->get_facebook_ads($startDate, $endDate, $act_id);
        $save_fb_report->ads = json_encode($get_fb_ads);
        // get ads code end

        // Summary graph code start
        $commaSeparatedIDs = null;
        if (isset($get_fb_campaigns['data'])) {
            $filteredArray = array_filter($get_fb_campaigns['data'], function ($item) {
                return isset($item['insights']);
            });
            $campaignIDs = array_column($filteredArray, 'id');
            $commaSeparatedIDs = implode(',', $campaignIDs);
            $save_fb_report->campaign_ids = $commaSeparatedIDs;
        }
        $get_summary_graph_data = $this->get_summary_graph_data($startDate, $endDate, $act_id, $commaSeparatedIDs);
        $save_fb_report->summary_graph = $get_summary_graph_data;
        // Summary graph code end

        // pie chart code start
        $get_pie_chart_data = $this->get_pie_chart_data($startDate, $endDate, $act_id, $commaSeparatedIDs);
        $save_fb_report->pie_chart_data = $get_pie_chart_data;
        // pie chart code end

        // get summart data ads code start
        $get_summary_data = $this->get_summary_data($startDate, $endDate, $act_id, $commaSeparatedIDs);
        $save_fb_report->summary_detail = $get_summary_data;
        // get summart data ads code end

        // get gender graph code start
        $get_gender_graph_data = $this->get_gender_graph_data($startDate, $endDate, $act_id, $commaSeparatedIDs);
        $save_fb_report->gender_graph_data = $get_gender_graph_data;
        // get gender graph code end

        // get age graph code start
        $get_age_graph_data = $this->get_age_graph_data($startDate, $endDate, $act_id, $commaSeparatedIDs);
        $save_fb_report->age_graph_data = $get_age_graph_data;
        // get age graph code start
        $save_fb_report->act_id = $act_id;
        $save_fb_report->start_date = $startDate;
        $save_fb_report->end_date = $endDate;
        $save_fb_report->save();

        $msg = [
            'success' => 'Facebook Report Data Fetch Successfully',
            'reload' => true,
        ];

        return response()->json($msg);
        exit();
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

        $campaign_note = new CampaignNote;
        $campaign_note->note_date = $request->note_date;
        $campaign_note->campaign_name = $request->campaign;
        $campaign_note->note = $request->notes;
        $campaign_note->save();

        $msg = [
            'success' => 'Campaign Notes Added Successfully',
            'redirect' => route('admin.facebook-ads-report.fb_ads_report'),
        ];

        return response()->json($msg);
    }

    public function campaign_note_delete($id)
    {
        $campaign_note = CampaignNote::hashidFind($id);
        $campaign_note->delete();
        $msg = [
            'success' => 'Campaign Notes Deleted Successfully',
            'redirect' => route('admin.facebook-ads-report.fb_ads_report'),
        ];

        return response()->json($msg);
    }

    public function download_pdf()
    {
        $campaigns = null;
        $adsets = null;
        $ads = null;
        $country_detail = null;
        $widgets_data = null;
        $get_fb_report = FacebookReport::latest()->first();
        if ($get_fb_report) {
            $campaigns = json_decode($get_fb_report->campaigns);
            $adsets = json_decode($get_fb_report->adsets);
            $ads = json_decode($get_fb_report->ads);
            $country_detail = json_decode($get_fb_report->summary_detail);
            $widgets_data = json_decode($get_fb_report->summary_detail);
            $summary_graph_info = json_decode($get_fb_report->summary_graph, true);

            $summary_dates = '';
            $summary_click = '';
            $summary_cpc = '';
            $summary_ctr = '';
            $summary_impressions = '';
            if (is_array($summary_graph_info) && !empty($summary_graph_info['dates'])) {
                foreach ($summary_graph_info['dates'] as $key => $summary_graph_v) {
                    $summary_dates .= "'".$summary_graph_v."',";
                    $summary_click .= "'".round($summary_graph_info['clicks'][$key])."',";
                    $summary_cpc .= "'".round($summary_graph_info['cpc'][$key])."',";
                    $summary_ctr .= "'".round($summary_graph_info['ctr'][$key])."',";
                    $summary_impressions .= "'".round($summary_graph_info['impressions'][$key])."',";
                }
            }
        }

        $campaign_notes = CampaignNote::where('ads_report', 'facebook_ads_report')->get();
        $campaigns_with_notes = [];

        if (isset($campaigns->data)) {

            foreach ($campaigns->data as $val) {
                if (isset($val->insights) && isset($val->insights->data[0])) {
                    $campaign_notes_for_campaign = $campaign_notes->where('campaign_name', $val->name)->pluck('note')->toArray();

                    $total_leads = 0;
                    $cost_per_lead = 0;
                    if (isset($val->insights->data[0]->cost_per_action_type)) {
                        foreach ($val->insights->data[0]->cost_per_action_type as $action_type) {
                            if ($action_type->action_type === 'lead') {
                                $total_leads = $action_type->value;
                                break;
                            }
                        }
                    }

                    if ($total_leads !== 0) {
                        $total_spend = $val->insights->data[0]->spend;
                        $cost_per_lead = $total_spend / $total_leads;
                    }

                    $campaigns_with_notes[] = [
                        'name' => $val->name,
                        'campaign_notes' => $campaign_notes_for_campaign,
                        'daily_budget' => $val->daily_budget ?? 0,
                        'lifetime_budget' => $val->lifetime_budget ?? 0,
                        'total_leads' => $total_leads,
                        'cost_per_lead' => $cost_per_lead,
                        'status' => $val->status,
                        'id' => $val->id,
                        'insights' => $val->insights ?? [],
                    ];
                }
            }
        }

        $get_add_account_name = FacebookAdsAccount::where('act_id', $get_fb_report->act_id)->first();

        $summary_data = $this->convert_to_google_chart_format($get_fb_report->summary_graph);

        $data = [
            'breadcrumb' => 'Facebook Ads Report',
            'title' => 'Facebook Ads Report',
            'campaigns' => $campaigns->data ?? '',
            'adsets' => $adsets->data ?? '',
            'ads' => $ads->data ?? '',
            'ad_account_name' => $get_add_account_name->act_name ?? '',
            'summary_detail' => $country_detail ?? '',
            'clicks' => $widgets_data->data[0]->clicks ?? '0',
            'impressions' => $widgets_data->data[0]->impressions ?? '0',
            'cpc' => $widgets_data->data[0]->cpc ?? '0',
            'ctr' => $widgets_data->data[0]->ctr ?? '0',
            'campaign_notes' => $campaign_notes,
            'generated_on' => Carbon::now()->format('M-d-Y H:i A'),
            'summary_graph' => $get_fb_report ? json_decode($get_fb_report->summary_graph, true) : '',
            'pie_chart_data' => $get_fb_report ? json_decode($get_fb_report->pie_chart_data, true) : '',
            'summary_detail' => $get_fb_report ? json_decode($get_fb_report->summary_detail) : '',
            'gender_graph' => $get_fb_report ? json_decode($get_fb_report->gender_graph_data) : '',
            'age_graph' => $get_fb_report ? json_decode($get_fb_report->age_graph_data, true) : '',
            'summary_dates' => '['.$summary_dates.']' ?? '0',
            'summary_click' => '['.$summary_click.']' ?? '0',
            'summary_impressions' => '['.$summary_impressions.']' ?? '0',
            'summary_cpc' => '['.$summary_cpc.']' ?? '0',
            'summary_ctr' => '['.$summary_ctr.']' ?? '0',
            'summary_data' => $summary_data,
            'campaigns_with_notes' => $campaigns_with_notes,
        ];
        // return view('admin.fb_report.pdf_file')->with($data);
        $html = view('admin.fb_report.pdf_file')->with($data)->render();
        $nmp_path = config('app.nmp_path');
        sleep(20);
        $timeout = 90000;
        $pdf = Browsershot::html($html)
            ->setTimeout($timeout)
            ->setIncludePath('$PATH:'.$nmp_path)
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->pdf();

        $fileName = $get_add_account_name->act_name.' - '.Carbon::now()->format('Y-m-d').'.pdf';

        $pdfPath = storage_path('app/public/'.$fileName);
        file_put_contents($pdfPath, $pdf);

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    private function get_facebook_campaigns($startDate, $endDate, $act_id)
    {

        $access_token = $this->get_admin_access_token();
        $ad_account_id = $act_id;
        $url = "https://graph.facebook.com/v19.0/$ad_account_id/campaigns";
        $fields = "name,status,id,daily_budget,lifetime_budget,start_time,stop_time,created_time,insights.time_range({'since':'$startDate','until':'$endDate'}){cpc,ctr,impressions,clicks,spend,cost_per_action_type}";
        $limit = 500;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$url?fields=$fields&limit=$limit&access_token=$access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);

            return "cURL Error: $error_message";
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status !== 200) {
            return "HTTP Error: $http_status";
        }

        $campaigns = json_decode($response, true);
        if (isset($campaigns['error'])) {
            return 'Facebook API Error: '.$campaigns['error']['message'];
        }

        $active_campaigns = array_filter($campaigns['data'], function ($campaign) {
            return $campaign['status'] === 'ACTIVE';
        });

        // return $active_campaigns;
        return $campaigns;
    }

    private function get_facebook_adsets_daily_budget($startDate, $endDate, $act_id)
    {

        $access_token = $this->get_admin_access_token();
        $ad_account_id = $act_id;

        $url = "https://graph.facebook.com/v19.0/$ad_account_id/adsets";
        $time_range = urlencode(json_encode(['since' => $startDate, 'until' => $endDate]));
        $fields = 'bid_amount,name,daily_budget,campaign_id';
        $access_token_param = urlencode($access_token);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$url?time_range=$time_range&fields=$fields&access_token=$access_token_param",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=1; ps_n=1',
            ],
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);

            return "cURL Error: $error_message";
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status !== 200) {
            return "HTTP Error: $http_status";
        }

        $adsets = json_decode($response, true);
        if (isset($adsets['error'])) {
            return 'Facebook API Error: '.$adsets['error']['message'];
        }

        return $adsets;
    }

    private function get_facebook_adsets($startDate, $endDate, $act_id)
    {

        $access_token = $this->get_admin_access_token();
        $ad_account_id = $act_id;

        $url = "https://graph.facebook.com/v19.0/$ad_account_id/insights";
        $level = 'adset';
        $fields = "adset_id,adset_name,campaign_id,campaign_name,clicks,impressions,ctr,cpc,spend,cost_per_action_type&time_range={'since':'$startDate','until':'$endDate'}";
        $limit = 500;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$url?level=$level&fields=$fields&access_token=$access_token&limit=$limit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);

            return "cURL Error: $error_message";
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status !== 200) {
            return "HTTP Error: $http_status";
        }

        $adsets = json_decode($response, true);
        if (isset($ads['error'])) {
            return 'Facebook API Error: '.$ads['error']['message'];
        }

        return $adsets;
    }

    private function get_facebook_ads($startDate, $endDate, $act_id)
    {

        $access_token = $this->get_admin_access_token();
        $ad_account_id = $act_id;
        $url = "https://graph.facebook.com/v19.0/$ad_account_id/insights";
        $level = 'ad';
        $fields = "ad_id,ad_name,adset_name,adset_id,created_time,campaign_name,clicks,impressions,ctr,cpc,spend,cost_per_action_type&time_range={'since':'$startDate','until':'$endDate'}";
        $limit = 500;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "$url?level=$level&fields=$fields&access_token=$access_token&limit=$limit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);

            return "cURL Error: $error_message";
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status !== 200) {
            return "HTTP Error: $http_status";
        }

        $ads = json_decode($response, true);
        if (isset($ads['error'])) {
            return 'Facebook API Error: '.$ads['error']['message'];
        }

        return $ads;
    }

    private function get_summary_data($startDate, $endDate, $act_id, $campaignIDs)
    {
        // Base URL
        $base_url = 'https://graph.facebook.com/v19.0/';

        // Construct the time range JSON
        $time_range = '{"since":"'.$startDate.'","until":"'.$endDate.'"}';

        // Construct the filtering value
        $filtering = '[{field: "campaign.id", operator: "IN", value: ['.$campaignIDs.']}]';

        $access_token = $this->get_admin_access_token();

        $url = $base_url.$act_id.'/insights?fields=clicks,impressions,ctr,cpc,spend,cost_per_action_type&default_summary=true&date_preset=maximum&time_range='.urlencode($time_range).'&level=ad&limit=9999&filtering='.urlencode($filtering).'&access_token='.urlencode($access_token).'&time_increment=1';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $response = 'Error: '.curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        $jsonData = $response;
        $data = json_decode($jsonData, true);
        $dateRange = generateDateRange($startDate, $endDate, 'Y-m-d');
        $graphdateRange = generateDateRange($startDate, $endDate, 'M d, Y');

        $datewiseData = [
            'dates' => $graphdateRange,
            'clicks' => [],
            'impressions' => [],
            'ctr' => [],
            'cpc' => [],
            'summary' => [],
        ];

        foreach ($dateRange as $date) {
            $datewiseData['clicks'][] = 0;
            $datewiseData['impressions'][] = 0;
            $datewiseData['ctr'][] = 0;
            $datewiseData['cpc'][] = 0;
        }

        if (empty($data['data'])) {
            return json_encode(['error' => 'No data found'], true);
        }

        foreach ($data['data'] as $campaign) {
            $startDate = $campaign['date_start'];
            if (in_array($startDate, $dateRange)) {
                $index = array_search($startDate, $dateRange);
                $datewiseData['clicks'][$index] += intval($campaign['clicks']) ?? 0;
                $datewiseData['impressions'][$index] += intval($campaign['impressions']) ?? 0;
                $datewiseData['ctr'][$index] += floatval($campaign['ctr']) ?? 0;
                $datewiseData['cpc'][$index] += isset($campaign['cpc']) ? floatval($campaign['cpc']) : 0;
            }
        }
        $datewiseData['summary'] = [
            'clicks' => $data['summary']['clicks'],
            'impressions' => $data['summary']['impressions'],
            'ctr' => round($data['summary']['ctr'], 2),
            'cpc' => round($data['summary']['cpc'], 2),
            'spend' => round($data['summary']['spend'], 2),
            'cost_per_result' => round($data['summary']['cost_per_action_type'][0]['value'], 2),
        ];

        return json_encode($datewiseData, true);
    }

    private function get_pie_chart_data($startDate, $endDate, $act_id, $campaignIDs)
    {

        $base_url = "https://graph.facebook.com/v19.0/$act_id/insights";
        $access_token = $this->get_admin_access_token();
        $params = [
            'breakdowns' => 'publisher_platform',
            'fields' => 'clicks,impressions,ctr,cpc',
            'default_summary' => 'true',
            'date_preset' => 'maximum',
            'time_range' => '{"since":"'.$startDate.'","until":"'.$endDate.'"}',
            'level' => 'ad',
            'limit' => '9999',
            'filtering' => '[{field: "campaign.id", operator: "IN", value: ['.$campaignIDs.']}]',
            'access_token' => $access_token,
        ];

        $url = $base_url.'?'.http_build_query($params);

        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        $jsonData = $response;

        $data = json_decode($jsonData, true);

        // Initialize arrays to store aggregated data
        $clicks = [];
        $impressions = [];
        $ctr = [];
        $cpc = [];

        // Loop through each data entry
        foreach ($data['data'] as $entry) {
            // Extract relevant data
            $platform = $entry['publisher_platform'];
            $click = isset($entry['clicks']) ? intval($entry['clicks']) : 0; // Check if 'clicks' key exists
            $impression = isset($entry['impressions']) ? intval($entry['impressions']) : 0; // Check if 'impressions' key exists
            $ctrValue = isset($entry['ctr']) ? floatval($entry['ctr']) : 0; // Check if 'ctr' key exists
            $cpcValue = isset($entry['cpc']) ? floatval($entry['cpc']) : 0; // Check if 'cpc' key exists

            // Update aggregated data for each platform
            if (!isset($clicks[$platform])) {
                $clicks[$platform] = 0;
                $impressions[$platform] = 0;
                $ctr[$platform] = 0;
                $cpc[$platform] = 0;
            }
            $clicks[$platform] += $click;
            $impressions[$platform] += $impression;
            $ctr[$platform] += $ctrValue;
            $cpc[$platform] += $cpcValue;
        }

        // Calculate average CTR and CPC for each platform
        foreach ($clicks as $platform => $value) {
            $ctr[$platform] /= count($data['data']);
            $cpc[$platform] /= count($data['data']);
        }

        // Final result array
        $result = [
            'clicks' => $clicks,
            'impressions' => $impressions,
            'ctr' => $ctr,
            'cpc' => $cpc,
        ];

        return json_encode($result, true);
    }

    private function get_summary_graph_data($startDate, $endDate, $act_id, $campaignIDs)
    {
        // Base URL
        $base_url = 'https://graph.facebook.com/v19.0/';

        // Construct the time range JSON
        $time_range = '{"since":"'.$startDate.'","until":"'.$endDate.'"}';

        // Construct the filtering value
        $filtering = '[{field: "campaign.id", operator: "IN", value: ['.$campaignIDs.']}]';

        $access_token = $this->get_admin_access_token();

        $url = $base_url.$act_id.'/insights?fields=clicks,impressions,ctr,cpc&date_preset=maximum&time_range='.urlencode($time_range).'&level=campaign&limit=9999&filtering='.urlencode($filtering).'&access_token='.urlencode($access_token).'&time_increment=1';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $response = 'Error: '.curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        $jsonData = $response;
        $data = json_decode($jsonData, true);
        $dateRange = generateDateRange($startDate, $endDate, 'Y-m-d');
        $graphdateRange = generateDateRange($startDate, $endDate, 'M d, Y');

        $datewiseData = [
            'dates' => $graphdateRange,
            'clicks' => [],
            'impressions' => [],
            'ctr' => [],
            'cpc' => [],
        ];

        foreach ($dateRange as $date) {
            $datewiseData['clicks'][] = 0;
            $datewiseData['impressions'][] = 0;
            $datewiseData['ctr'][] = 0;
            $datewiseData['cpc'][] = 0;
        }

        foreach ($data['data'] as $campaign) {
            $startDate = $campaign['date_start'];
            if (in_array($startDate, $dateRange)) {
                $index = array_search($startDate, $dateRange);
                $datewiseData['clicks'][$index] += intval($campaign['clicks']) ?? 0;
                $datewiseData['impressions'][$index] += intval($campaign['impressions']) ?? 0;
                $datewiseData['ctr'][$index] += floatval($campaign['ctr']) ?? 0;
                $datewiseData['cpc'][$index] += isset($campaign['cpc']) ? floatval($campaign['cpc']) : 0;
            }
        }

        return json_encode($datewiseData, true);
    }

    private function get_gender_graph_data($startDate, $endDate, $act_id, $campaignIDs)
    {
        // Base URL
        $base_url = 'https://graph.facebook.com/v19.0/';

        // Construct the time range JSON
        $time_range = '{"since":"'.$startDate.'","until":"'.$endDate.'"}';

        // Construct the filtering value
        $filtering = '[{field: "campaign.id", operator: "IN", value: ['.$campaignIDs.']}]';

        $access_token = $this->get_admin_access_token();

        $url = $base_url.$act_id.'/insights?fields=clicks,impressions,ctr,cpc,spend,cost_per_action_type&default_summary=true&date_preset=maximum&time_range='.urlencode($time_range).'&level=ad&limit=9999&filtering='.urlencode($filtering).'&access_token='.urlencode($access_token).'&time_increment=1&breakdowns=gender';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $response = 'Error: '.curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        $jsonData = $response;
        $data = json_decode($jsonData, true);
        // Initialize arrays to store gender-wise data
        $gender_data = [
            'male' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            'female' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            'unknown' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
        ];

        if (empty($data['data'])) {
            return json_encode(['error' => 'No data found'], true);
        }

        // Calculate gender-wise data
        foreach ($data['data'] as $entry) {
            $gender = $entry['gender'];
            $clicks = intval($entry['clicks']);
            $ctr = floatval($entry['ctr']);
            $impressions = intval($entry['impressions']);

            // Check if 'cpc' key exists
            if (array_key_exists('cpc', $entry)) {
                $cpc = floatval($entry['cpc']);
                // Update gender-wise data
                $gender_data[$gender]['cpc'] += $cpc;
            }

            // Update gender-wise data
            $gender_data[$gender]['clicks'] += $clicks;
            $gender_data[$gender]['ctr'] += $ctr;
            $gender_data[$gender]['impressions'] += $impressions;
        }

        return json_encode($gender_data, true);
    }

    private function get_age_graph_data($startDate, $endDate, $act_id, $campaignIDs)
    {
        // Base URL
        $base_url = 'https://graph.facebook.com/v19.0/';

        // Construct the time range JSON
        $time_range = '{"since":"'.$startDate.'","until":"'.$endDate.'"}';

        // Construct the filtering value
        $filtering = '[{field: "campaign.id", operator: "IN", value: ['.$campaignIDs.']}]';

        $access_token = $this->get_admin_access_token();

        $url = $base_url.$act_id.'/insights?fields=clicks,impressions,ctr,cpc,spend,cost_per_action_type&default_summary=true&date_preset=maximum&time_range='.urlencode($time_range).'&level=ad&limit=9999&filtering='.urlencode($filtering).'&access_token='.urlencode($access_token).'&time_increment=1&breakdowns=age';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $response = 'Error: '.curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        $jsonData = $response;
        $data = json_decode($jsonData, true);
        // Initialize arrays to store age group-wise data
        $age_data = [
            '18-24' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            '25-34' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            '35-44' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            '45-54' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            '55-64' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
            '65+' => ['clicks' => 0, 'cpc' => 0, 'ctr' => 0, 'impressions' => 0],
        ];

        if (empty($data['data'])) {
            return json_encode(['error' => 'No data found'], true);
        }

        // Calculate age group-wise data
        foreach ($data['data'] as $entry) {
            $age = $entry['age'];
            $clicks = intval($entry['clicks']);
            $ctr = floatval($entry['ctr']);
            $impressions = intval($entry['impressions']);

            // Check if 'cpc' key exists
            if (array_key_exists('cpc', $entry)) {
                $cpc = floatval($entry['cpc']);
                // Update age group-wise data
                $age_data[$age]['cpc'] += $cpc;
            }

            // Update age group-wise data
            $age_data[$age]['clicks'] += $clicks;
            $age_data[$age]['ctr'] += $ctr;
            $age_data[$age]['impressions'] += $impressions;
        }

        return json_encode($age_data, true);
    }

    private function get_ad_accounts()
    {

        $access_token = $this->get_admin_access_token();
        $url = 'https://graph.facebook.com/v19.0/me/adaccounts';
        $fields = 'name,id';
        $limit = 500;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "$url?fields=$fields&access_token=$access_token&limit=$limit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Cookie: ps_l=0; ps_n=0',
            ],
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);

            return "cURL Error: $error_message";
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_status !== 200) {
            return "HTTP Error: $http_status";
        }

        $ad_accounts = json_decode($response, true);
        if (isset($ad_accounts['error'])) {
            return 'Facebook API Error: '.$ad_accounts['error']['message'];
        }

        return $ad_accounts;
    }

    private function get_admin_access_token()
    {
        $get_access_token = FacebookAccessToken::where('admin_id', auth('admin')->user()->id)->first();
        if (asset($get_access_token) && !empty($get_access_token->access_token)) {
            $admin_access_token = $get_access_token->access_token;
            $get_access_token->last_update = now();
            $get_access_token->save();

            return $admin_access_token;
        }

    }

    private function convert_to_google_chart_format($jsonData)
    {
        $data = json_decode($jsonData, true);

        $dataTable = "var data = new google.visualization.DataTable();
                      data.addColumn('string', 'Date');
                      data.addColumn('number', 'Impressions');
                      data.addColumn('number', 'Clicks');
                      data.addColumn('number', 'CTR');
                      data.addColumn('number', 'CPC');
                      data.addRows([";

        $dates = $data['dates'];
        $impressions = $data['impressions'];
        $clicks = $data['clicks'];
        $ctr = $data['ctr'];
        $cpc = $data['cpc'];

        $numRows = count($dates);

        for ($i = 0; $i < $numRows; $i++) {
            $date = $dates[$i];
            $impression = $impressions[$i];
            $click = $clicks[$i];
            $ctrVal = $ctr[$i];
            $cpcVal = $cpc[$i];

            $dataTable .= "['".$date."', ".$impression.', '.$click.', '.$ctrVal.', '.$cpcVal.']';

            if ($i < $numRows - 1) {
                $dataTable .= ', ';
            }
        }

        $dataTable .= ']);';

        return $dataTable;
    }
}
