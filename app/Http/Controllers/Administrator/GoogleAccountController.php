<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GoogleTrait;
use App\Models\Admin;
use App\Models\GoogleAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Validator;
use Google\Service\Calendar;
use Illuminate\Support\Facades\DB;
use App\Models\ClientCalendarEvent;
use DateTimeImmutable;

class GoogleAccountController extends Controller
{
    use GoogleTrait;

    public function index(Request $request)
    {
        if (Auth::user('admin')->can('calendar-management-read') != true) {
            abort(403, 'Unauthorized action.');
        }

        if (isset($request->client)) {
            $user = User::where(
                'id',
                hashids_decode($request->client)
            )->first();

            if ($this->checkRefreshToken($user)) {
                $data = [
                    'breadcrumb_main' => 'Google Calender',
                    'breadcrumb' => 'Calender',
                    'title' => 'Google Calender',
                    'user_id' => $request->client,
                    'clients' => User::whereNotNull('email_verified_at')
                        ->latest()
                        ->get(['id', 'client_name', 'email']),
                ];
                return view('admin.calender')->with($data);
            } else {
                $data = [
                    'breadcrumb_main' => 'Google Calender',
                    'breadcrumb' => 'Calender',
                    'title' => 'Google Calender',
                    'google_accounts' => GoogleAccount::all(),
                    'clients' => User::whereNotNull('email_verified_at')
                        ->latest()
                        ->get(['id', 'client_name', 'email']),
                    'error' => true,
                ];

                return view('admin.google_connect')->with($data);
            }
        } else {
            $data = [
                'breadcrumb_main' => 'Google Calender',
                'breadcrumb' => 'Calender',
                'title' => 'Google Calender',
                'google_accounts' => GoogleAccount::all(),
                'clients' => User::whereNotNull('email_verified_at')
                    ->latest()
                    ->get(['id', 'client_name', 'email']),
                'error' => false,
            ];

            return view('admin.google_connect')->with($data);
        }
    }

    public function get_google_calender_events()
    {
        if (
            Auth::user('admin')->can('calendar-management-read') != true ||
            Auth::user('admin')->can('calendar-management-write') != true
        ) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('id', hashids_decode(request()->user_id))->first();

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService(
            $user->google_access_token
        );

        if (!$user->calendar_id) {
            $jomeCalendar = $googleCalendarService->createCalendar();
            User::where('id', $user->id)->update([
                'calendar_id' => $jomeCalendar['id'],
            ]);
        }

        // Define the time range for events
        $timeMin = now()
            ->parse(request()->start)
            ->toIso8601String(); // or specify a start date
        $timeMax = now()
            ->parse(request()->end)
            ->toIso8601String(); // or specify an end date

        $calendarId = $user->calendar_id;
        $calendarEvents = $googleCalendarService->listEvents(
            $calendarId,
            $timeMin,
            $timeMax
        );

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
        if (Auth::user('admin')->can('calendar-management-write') != true) {
            abort(403, 'Unauthorized action.');
        }

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

        $user = User::where('id', hashids_decode($request->user_id))->first();
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
        $googleCalendarService = new GoogleCalendarService(
            $user->google_access_token
        );

        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $calendarEvent = $googleCalendarService->createEvent(
                $calendarId,
                $summary,
                $description,
                $startDateTime,
                $endDateTime
            );

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

            // ActivityLogHelper::save_activity($userId, 'Google Calendar Events Save', 'users');

            DB::commit();

            return response()->json([
                'success' => 'New Calendar Event Add Successfully',
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_event(Request $request)
    {
        if (Auth::user('admin')->can('calendar-management-update') != true) {
            abort(403, 'Unauthorized action.');
        }

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

        $user = User::where('id', hashids_decode($request->user_id))->first();
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
        $googleCalendarService = new GoogleCalendarService(
            $user->google_access_token
        );

        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $googleCalendarService->updateEvent(
                $calendarId,
                $eventId,
                $summary,
                $description,
                $startDateTime,
                $endDateTime
            );

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
        if (Auth::user('admin')->can('calendar-management-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('id', hashids_decode($request->user_id))->first();

        $calendarId = $user->calendar_id;
        $eventId = $request->event_id;

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService(
            $user->google_access_token
        );
        $googleCalendarService->deleteEvent($calendarId, $eventId);

        return response()->json([
            'success' => 'Event deleted successfully',
        ]);
    }

    public function oauth(Request $request)
    {
        /**
         * Get authcode from the query string
         */
        $authCode = urldecode($request->input('code'));
        /**
         * Google client
         */
        $client = $this->getClient($user = false);

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

        if (auth('admin')->check()) {
            $admin = Admin::find(auth('admin')->id());
            $admin->provider_id = $userFromGoogle->id;
            $admin->provider_name = 'google';
            $admin->google_access_token = json_encode($accessToken);
            $admin->save();

            // Save into google accounts table
            GoogleAccount::updateOrCreate(
                [
                    'email' => $userFromGoogle->email,
                ],
                [
                    'name' => $userFromGoogle->name,
                    'google_id' => $userFromGoogle->id,
                    'access_token' => json_encode($accessToken),
                ]
            );
        }

        return redirect(route('admin.setting.google_account'));
    }

    public function getAuthUrl()
    {
        $client = $this->getClient($user = false);

        /**
         * Generate the url at google we redirect to
         */
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function refresh_token($id)
    {
        $googleAccount = GoogleAccount::find($id);
        $refreshToken = $this->checkRefreshTokenNew($googleAccount);

        if (!$refreshToken) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function disconnect()
    {
        $admin = Admin::find(auth('admin')->id());
        $admin->provider_id = null;
        $admin->provider_name = null;
        $admin->google_access_token = null;
        $admin->save();

        return redirect(route('admin.setting.google_account'));
    }
}
