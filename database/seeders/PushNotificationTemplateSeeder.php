<?php

namespace Database\Seeders;

use App\Models\PushNotificationTemplate;
use Illuminate\Database\Seeder;

class PushNotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PushNotificationTemplate::create([
            'code' => 'TM01',
            'description' => 'Dashboard task status updated',
            'title' => 'Task Status Updated',
            'body' => 'Your task status has been updated',
        ]);

        PushNotificationTemplate::create([
            'code' => 'TM02',
            'description' => 'Receive message from task chat',
            'title' => 'New Message',
            'body' => 'You have received a new message',
        ]);

        PushNotificationTemplate::create([
            'code' => 'LM01',
            'description' => 'New lead added using CRM',
            'title' => 'New Lead',
            'body' => 'New Lead Added',
        ]);

        PushNotificationTemplate::create([
            'code' => 'LM02',
            'description' => 'New lead added using Zapier',
            'title' => 'New Lead',
            'body' => 'New Lead Add From Zapier',
        ]);

        PushNotificationTemplate::create([
            'code' => 'LM03',
            'description' => 'Upcoming follow up lead today, time will at the end of message (d, M Y @ h:i a)',
            'title' => 'Reminder',
            'body' => 'You have follow-up meeting today at  ',
        ]);

        PushNotificationTemplate::create([
            'code' => 'LM04',
            'description' => 'Upcoming follow up lead later, time will at the end of message (d, M Y @ h:i a)',
            'title' => 'Reminder',
            'body' => 'You have follow-up meeting on  ',
        ]);
    }
}
