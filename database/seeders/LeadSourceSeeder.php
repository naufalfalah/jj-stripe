<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeadSource::insert([
            [
                'name' => 'MiniZapier',
                'key' => 'MZap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'WPForms',
                'key' => 'WPF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'WordPress',
                'key' => 'WP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MetaLead',
                'key' => 'ML',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPC',
                'key' => 'PPC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RR (Round Robin)',
                'key' => 'RR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Zapier',
                'key' => 'Zap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unknown',
                'key' => 'Unknown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
