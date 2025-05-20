<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class PushNotificationTemplateController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::guard('admin')->user()->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $pushNotificationTemplates = PushNotificationTemplate::all();

            return DataTables::of($pushNotificationTemplates)
                ->addColumn('action', function ($pushNotificationTemplate) {
                    $btn = '<a href="'.route('admin.setting.push-notification-templates.edit', $pushNotificationTemplate->id).'" class="mx-2"><i class="fa-solid fa-pencil"></i></a>';

                    return $btn;
                })->rawColumns(['action'])->make(true);
        }

        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'Push Notification',
            'title' => 'Push Notification',
        ];

        return view('admin.settings.push_notification_templates.index', $data);
    }

    public function edit(PushNotificationTemplate $push_notification_template): View
    {
        if (Auth::guard('admin')->user()->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'Push Notification',
            'title' => 'Push Notification',
            'push_notification_template' => $push_notification_template,
        ];

        return view('admin.settings.push_notification_templates.edit', $data);
    }

    public function update(PushNotificationTemplate $push_notification_template, Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->user()->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $push_notification_template->title = $request->title;
        $push_notification_template->body = $request->body;
        $push_notification_template->save();

        Alert::success('Success', 'Push Notification Template Saved');

        return redirect()->route('admin.setting.push-notification-templates.index');
    }
}
