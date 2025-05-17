<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientCalendarEvent;
use App\Models\User;
use App\Traits\GoogleTrait;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ActivityLogHelper;
use App\Services\GoogleCalendarService;
use DateTimeImmutable;

class GoogleAccountController extends Controller
{
    use GoogleTrait;

    public function index()
    {
        $auth_id = auth('web')->id();

        if ($this->checkRefreshToken(auth('web')->user())) {
            if (!request()->ajax()) {
                ActivityLogHelper::save_activity($auth_id, 'Google Calendar', 'users');
            }
            $data = [
                'breadcrumb_main' => 'Google Calender',
                'breadcrumb' => 'Calender',
                'title' => 'Google Calender',
            ];
            return view('client.calender')->with($data);
        }

        if (!request()->ajax()) {
            ActivityLogHelper::save_activity($auth_id, 'Google Calendar Integration', 'users');
        }

        $data = [
            'breadcrumb' => 'Google Integration',
            'title' => 'Google Integration',
        ];

        return view('client.google_connect')->with($data);
    }

    public function oauth(Request $request)
    {

        $auth_id = auth('web')->id();

        /**
         * Get authcode from the query string
         */
        $authCode = urldecode($request->input('code'));
        /**
         * Google client
         */
        $client = $this->getClient();

        /**
         * Exchange auth code for access token
         * Note: if we set 'access type' to 'force' and our access is 'offline', we get a refresh token. we want that.
         */
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        /**
         * Set the access token with google. nb json
         */
        $client->setAccessToken(json_encode($accessToken));

        /**
         * Get user's data from google
         */
        $service = new \Google\Service\Oauth2($client);
        $userFromGoogle = $service->userinfo->get();

        if (auth('web')->check()) {
            $user = User::find(auth('web')->id());
            $user->provider_id = $userFromGoogle->id;
            $user->provider_name = 'google';
            $user->google_access_token = json_encode($accessToken);
            $user->save();
        }

        return redirect(route('user.google.index'));
    }

    public function getAuthUrl()
    {

        $auth_id = auth('web')->id();


        ActivityLogHelper::save_activity($auth_id, 'Connect Google Calendar', 'users');

        /**
         * Create google client
         */
        $client = $this->getClient();

        /**
         * Generate the url at google we redirect to
         */
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function get_google_calender_events()
    {
        $user = auth('web')->user();

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);

        if (!$user->calendar_id) {
            $jomeCalendar = $googleCalendarService->createCalendar();
            User::where('id', $user->id)->update([
                'calendar_id' => $jomeCalendar['id'],
            ]);
        }

        // Define the time range for events
        $timeMin = now()->parse(request()->start)->toIso8601String(); // or specify a start date
        $timeMax = now()->parse(request()->end)->toIso8601String(); // or specify an end date
        
        $calendarId = $user->calendar_id;
        $calendarEvents = $googleCalendarService->listEvents($calendarId, $timeMin, $timeMax);

        $events = [];
        if (isset($calendarEvents) && count($calendarEvents['items'])) {
            foreach ($calendarEvents['items'] as $event) {
                $start = $event['start']['dateTime'] ?? $event['start']['date'];
                $end = $event['end']['dateTime'] ?? $event['end']['date'];
    
                $events[] = [
                    'id' => $event['id'],
                    'title' => $event['summary'] ?? 'Untitled Event',
                    'description' => $event['description'] ?? '',
                    'start' => $start,
                    'end' => $end,
                    'allDay' => isset($event['start']['date']) ? true : false,
                ];
            }
        }

        return response()->json($events);
    }

    public function save_event(Request $request)
    {
        $rules = [
            'title' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = auth('web')->user();
        $userId = $user->id;

        $summary = $request->title;
        $description = $request->description;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        $startDatetimeString = $date . ' ' . $startTime;
        $endDatetimeString = $date . ' ' . $endTime;

        $startDatetimeImmutable = new DateTimeImmutable($startDatetimeString);
        $endDatetimeImmutable = new DateTimeImmutable($endDatetimeString);
        
        $startDateTime = $startDatetimeImmutable->format('Y-m-d\TH:i:s');
        $endDateTime = $endDatetimeImmutable->format('Y-m-d\TH:i:s');

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);

        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $calendarEvent = $googleCalendarService->createEvent($calendarId, $summary, $description, $startDateTime, $endDateTime);

            ClientCalendarEvent::create([
                'client_id' => $userId,
                'title' => $summary,
                'event_date' => $date ?? null,
                'start_time' => $startTime ?? null,
                'end_time' => $endTime ?? null,
                'description' => $description ?? null,
                'calender_event_id' => $calendarEvent['id'],
                'added_by_id' => auth('web')->id(),
            ]);
            
            ActivityLogHelper::save_activity($userId, 'Google Calendar Events Save', 'users');

            DB::commit();

            return response()->json([
                'success' => 'New Calendar Event Add Successfully',
                'reload' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_event(Request $request)
    {
        $rules = [
            'title' => 'required',
            'date' => 'required|date|after:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = auth('web')->user();
        $userId = $user->id;

        $eventId = $request->event_id;
        $summary = $request->title;
        $description = $request->description;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        $startDatetimeString = $date . ' ' . $startTime;
        $endDatetimeString = $date . ' ' . $endTime;

        $startDatetimeImmutable = new DateTimeImmutable($startDatetimeString);
        $endDatetimeImmutable = new DateTimeImmutable($endDatetimeString);
        
        $startDateTime = $startDatetimeImmutable->format('Y-m-d\TH:i:s');
        $endDateTime = $endDatetimeImmutable->format('Y-m-d\TH:i:s');

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);
        
        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $googleCalendarService->updateEvent($calendarId, $eventId, $summary, $description, $startDateTime, $endDateTime);
            
            ActivityLogHelper::save_activity($userId, 'Google Calendar Events Update', 'users');

            DB::commit();

            return response()->json([
                'success' => 'Event updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_event(Request $request)
    {
        $user = auth('web')->user();

        $calendarId = $user->calendar_id;
        $eventId = $request->event_id;
        
        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);
        $googleCalendarService->deleteEvent($calendarId, $eventId);
        
        return response()->json([
            'success' => 'Event deleted successfully',
        ]);
    }


    public function google_calender_disconnected(Request $request)
    {

        $auth_id = auth('web')->id();
        ActivityLogHelper::save_activity($auth_id, 'Disconnect Google Calendar', 'users');

        if ($request->ajax()) {
            $userId = auth('web')->id();
            $get_user = User::find($userId);
            $get_user->google_access_token = null;
            $get_user->save();
            return response()->json([
                'success' => 'Calender Disconnected Successfully',
            ]);
        }
    }

}
