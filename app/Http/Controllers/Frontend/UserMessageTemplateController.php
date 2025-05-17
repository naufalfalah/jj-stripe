<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\admin_message_template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ClientMessageTemplate;

class UserMessageTemplateController extends Controller
{
    //

    public function index()
    {

        $clientId = auth('web')->id();
        $whatsap = ClientMessageTemplate::where('client_id', $clientId)->first();

        $data = [
            'breadcrumb_main' => 'WhatsApp Message Template',
            'breadcrumb' => 'WhatsApp Message Template',
            'title' => 'WhatsApp Message Template',
            'whatsapp_temp' => $whatsap,
        ];

        return view('client.whatsap_message_template.show', $data);
    }


    public function wp_message_update(Request $request)
    {
        $rules = [
            'wp_message' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $clientId = auth('web')->id();
            $messageTemplate = $request->wp_message;

            $update = ClientMessageTemplate::where('client_id', $clientId)->update([
                'message_template' => $messageTemplate
            ]);

            if ($update) {
                DB::commit();
                $msg = [
                    'success' => 'WhatsApp Message Template Saved Successfully',
                    'reload' => true
                ];
                return response()->json($msg);
            } else {
                DB::rollback();
                return response()->json(['error' => 'No record found to update'], 404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'WhatsApp Message Template: ' . $e->getMessage()], 500);
        }
    }
}
