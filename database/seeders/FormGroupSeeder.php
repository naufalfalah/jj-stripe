<?php

namespace Database\Seeders;

use App\Models\FormGroup;
use Illuminate\Database\Seeder;

class FormGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FormGroup::create(['name' => 'to do']);
        FormGroup::create(['name' => 'pending for approval']);
        FormGroup::create(['name' => 'revision']);
        FormGroup::create(['name' => 'completed']);
    }
}
