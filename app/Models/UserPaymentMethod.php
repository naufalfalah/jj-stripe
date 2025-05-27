<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentMethod extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'card_type',
        'card_number',
        'last_four',
        'expiry_month',
        'expiry_year',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'is_default',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function unsetOtherDefaults($userId, $exceptId = null)
    {
        $query = self::where('user_id', $userId)->where('is_default', true);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        $query->update(['is_default' => false]);
    }
}
