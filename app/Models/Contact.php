<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'contactable_id',
        'contactable_type',
        'first_name',
        'father_last_name',
        'mother_last_name',
        'job_title',
        'phone',
        'extension',
        'mobile_whatsapp',
        'email',
        'country',
        'state',
        'city_delegation',
        'street',
        'postal_code',
        'neighborhood',
        'notify_order',
        'notify_quote',
        'notify_invoice',
        'notify_statement',
        'notify_tracking',
    ];

    protected $casts = [
        'notify_order' => 'boolean',
        'notify_quote' => 'boolean',
        'notify_invoice' => 'boolean',
        'notify_statement' => 'boolean',
        'notify_tracking' => 'boolean',
    ];

    public function contactable()
    {
        return $this->morphTo();
    }
}
