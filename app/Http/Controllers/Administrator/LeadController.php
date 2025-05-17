<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\LeadClient;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $data = [
            'breadcrumb' => 'Client Leads',
            'title' => 'Client Leads',
            'users' => User::all(),
        ];
        return view('admin.lead.index')->with($data);
    }

    public function update(Request $request)
    {
        $leadId = (int) $request->id;
        $lead = LeadClient::find($leadId);
        $lead->client_email = $request->email;
        $lead->save();
        
        $msg = [
            'success' => 'Lead Updated Successfully',
            'redirect' => route('admin.lead.index'),
        ];
        return response()->json($msg);
    }
}
