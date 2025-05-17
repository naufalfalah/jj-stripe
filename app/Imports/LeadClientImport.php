<?php

namespace App\Imports;

use App\Models\LeadClient;
use App\Models\LeadData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class LeadClientImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        if (!empty($collection)) {
            try {
                DB::beginTransaction();

                foreach ($collection as $key => $value) {

                    if ($key > 0) {

                        if (empty($value[0]) || empty($value[1]) || empty($value[2])) {

                            continue;
                        }

                        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $value[0])) {
                            continue;
                        }

                        if (!filter_var($value[1], FILTER_VALIDATE_EMAIL)) {
                            continue;
                        }

                        if (!preg_match('/^[98]\d{7}$/', $value[2])) {
                            continue;
                        }

                        $existingLead = LeadClient::where('email', $value[1])
                            ->orWhere('mobile_number', $value[2])
                            ->first();

                        if (!$existingLead) {
                            $client_lead = new LeadClient;
                            $client_lead->client_id = auth('web')->id();
                            $client_lead->name = $value[0];
                            $client_lead->email = $value[1];
                            $client_lead->mobile_number = $value[2];
                            $client_lead->user_type = 'user';
                            $client_lead->added_by_id = auth('web')->id();
                            $client_lead->save();

                            // Save additional lead data
                            for ($i = 3; $i < count($value); $i++) {
                                if (isset($collection[0][$i], $value[$i]) && !empty($value[$i])) {
                                    $lead_data = new LeadData;
                                    $lead_data->lead_client_id = $client_lead->id;
                                    $lead_data->key = $collection[0][$i] ?? '';
                                    $lead_data->value = $value[$i] ?? '';
                                    $lead_data->user_type = 'user';
                                    $lead_data->added_by_id = auth('web')->id();
                                    $lead_data->save();
                                }
                            }
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {

                DB::rollback();

                $errorMessage = $e->getMessage();
                throw new \Exception("Error saving lead clients: $errorMessage");
            }
        }
    }
}
