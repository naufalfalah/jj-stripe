<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\SubAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SendLeadTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function add_lead()
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $subAccounts = SubAccount::where('status', 'Active')->latest()->get();

        $data = [
            'breadcrumb_main' => 'Send Lead',
            'breadcrumb' => '',
            'title' => 'Send Lead',
            'sub_accounts' => $subAccounts,
        ];

        return view('admin.send_lead.index', $data);
    }

    public function get_clients(Request $request)
    {
        $sub_account_id = hashids_decode($request->subaccount_id);
        $users = User::where('sub_account_id', $sub_account_id)->latest()->get();
        $html = '<option value="">select...</option>';
        foreach ($users as $user) {
            $html .= '<option value="'.$user->hashid.'">'.$user->client_name.' ('.$user->email.')</option>';
        }
        echo $html;
    }

    public function save_lead(Request $request)
    {
        $sub_account = hashids_decode($request->sub_account);
        $client = hashids_decode($request->client);
        $ads = Ads::with('client')->where('client_id', $client)->where('sub_account_id', $sub_account)->first();

        $leadMessage = 'New Lead Please take note!
===========================
Hello '.$ads->client->client_name.', you have a new lead:';
        if (!empty($request->user_name)) {
            $leadMessage .= "\n- Name: {$request->user_name}";
        }
        if (!empty($request->email)) {
            $leadMessage .= "\n- Email: {$request->email}";
        }
        if (!empty($request->mobile_number)) {
            $leadMessage .= "\n- Mobile Number: https://wa.me/+65{$request->mobile_number}";

        }

        if (!empty($request->data) && count($request->data) > 0) {
            foreach ($request->data as $val) {
                if (!empty($val['key'])) {
                    $leadMessage .= "\n- {$val['key']}: {$val['value']}";
                }
            }
        }
        $url = $ads->discord_link;
        $send_descord_msg = $this->send_discord_msg($url, $leadMessage);

        $ads_lead = new LeadClient;
        $ads_lead->client_id = $ads->client_id;
        $ads_lead->name = $request->user_name ?? '';
        $ads_lead->email = $request->email ?? '';
        $ads_lead->mobile_number = $request->mobile_number ?? '';
        $ads_lead->lead_type = 'manual';
        $ads_lead->added_by_id = Auth::user('admin')->id;
        $ads_lead->user_type = 'admin';
        $ads_lead->is_send_discord = 1;
        $ads_lead->save();

        $lead_key_data = [];

        if (!empty($request->data) && count($request->data) > 0) {
            foreach ($request->data as $k => $val) {

                if (is_array($val)) {
                    $lead_key_data[] = [
                        'lead_client_id' => $ads_lead->id,
                        'key' => $val['key'],
                        'value' => $val['value'],
                        'added_by_id' => $ads->client_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $lead_key_data[] = [
                        'lead_client_id' => $ads_lead->id,
                        'key' => $val->key,
                        'value' => $val->value,
                        'added_by_id' => $ads->client_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            LeadData::insert($lead_key_data);
        }
        $ads->lead_status = 1;
        $ads->save();
        $msg = [
            'success' => 'Lead add Successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }

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
}
