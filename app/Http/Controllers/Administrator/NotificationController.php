<?php

namespace App\Http\Controllers\Administrator;

use App\Constants\NotificationConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationTemplateRequest;
use App\Models\Notification;
use App\Models\NotificationSchedule;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {

        if (Auth::user('admin')->can('send-notification-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.notification.index', [
            'breadcrumb_main' => 'All Notifications',
            'breadcrumb' => 'All Notifications',
            'title' => 'All Notifications',
            'notificationTemplates' => NotificationTemplate::all(),
            'types' => NotificationConstant::TYPES,
        ]);
    }

    public function create()
    {
        if (Auth::user('admin')->can('send-notification-write') != true) {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.notification.create', [
            'breadcrumb_main' => 'Create Notifications',
            'breadcrumb' => 'Create Notifications',
            'title' => 'Create Notifications',
            'types' => NotificationConstant::TYPES,
        ]);
    }

    public function store(NotificationTemplateRequest $request)
    {

        if (Auth::user('admin')->can('send-notification-write') != true || Auth::user('admin')->can('send-notification-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        NotificationTemplate::create([
            'title' => $request->title,
            'body' => $request->body,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => 'Notification Template Added Successfully',
            'redirect' => route('admin.notification.index'),
        ]);
    }

    public function edit($id)
    {

        if (Auth::user('admin')->can('send-notification-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.notification.edit', [
            'breadcrumb_main' => 'All Notifications',
            'breadcrumb' => 'All Notifications',
            'title' => 'All Notifications',
            'notification' => NotificationTemplate::find($id),
            'types' => NotificationConstant::TYPES,
        ]);
    }

    public function update($id, NotificationTemplateRequest $request)
    {
        if (Auth::user('admin')->can('send-notification-write') != true || Auth::user('admin')->can('send-notification-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $notificationTemplate = NotificationTemplate::find($id);

        $notificationTemplate->title = $request->title;
        $notificationTemplate->body = $request->body;
        $notificationTemplate->save();

        return response()->json([
            'success' => 'Notification Template Updated Successfully',
            'redirect' => route('admin.notification.index'),
        ]);
    }

    public function destory($id)
    {

        if (Auth::user('admin')->can('send-notification-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $notification = NotificationTemplate::find($id);
        $notification->delete();

        return response()->json([
            'success' => 'Notification Template Deleted Successfully',
            'redirect' => route('admin.notification.index'),
        ]);
    }

    public function schedule($id)
    {
        if (Auth::user('admin')->can('schedule-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        $notificationSchedules = NotificationSchedule::where('notification_id', $id)
            ->get();
        
        return view('admin.notification.schedule', [
            'breadcrumb_main' => 'Notification Schedule',
            'breadcrumb' => 'Notification Schedule',
            'title' => 'Notification Schedule',
            'notification' => NotificationTemplate::find($id),
            'types' => NotificationConstant::TYPES,
            'notificationSchedules' => $notificationSchedules,
        ]);
    }

    public function scheduleCreate($id)
    {
        if (Auth::user('admin')->can('schedule-write') != true || Auth::user('admin')->can('schedule-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.notification.schedule-create', [
            'breadcrumb_main' => 'Create Notification Schedule',
            'breadcrumb' => 'Create Notification Schedule',
            'title' => 'Create Notification Schedule',
            'notification' => NotificationTemplate::find($id),
            'types' => NotificationConstant::TYPES,
            'users' => User::all(),
        ]);
    }

    public function Schedulestore(Request $request, $id)
    {
        if (Auth::user('admin')->can('schedule-write') != true || Auth::user('admin')->can('schedule-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $publishedTo = 'All';
        if ($request->published_to > 0) {
            $publishedTo = 'Single';
        }

        NotificationSchedule::create([
            'notification_id' => $id,
            'published_to' => $publishedTo,
            'client_id' => $request->client_id,
            'published_at' => $request->published_at,
            'body' => $request->body,
        ]);

        return response()->json([
            'success' => 'Notification Schedule Added Successfully',
            'redirect' => route('admin.notification.schedule', $id),
        ]);
    }

    public function sendPushNotification()
    {
        if (Auth::user('admin')->can('schedule-write') != true || Auth::user('admin')->can('schedule-update') != true) {
            abort(403, 'Unauthorized action.');
        }
        
        $deviceToken = 'YOUR_DEVICE_TOKEN';
        $title = 'Notification Title';
        $body = 'This is the notification body.';
        $data = ['key' => 'value'];

        try {
            $response = $this->firebaseService->sendNotification($deviceToken, $title, $body, $data);
            return response()->json(['success' => true, 'response' => $response]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
