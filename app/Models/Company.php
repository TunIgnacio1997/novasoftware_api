<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'company';

    protected $fillable = [
        'id',
        'conceptnamecompany',
        'mail',
        'url',
        'logo',
        'access_key',
        'direccion',
        'iva',
        'rfc',
        'regimen_fiscal',
        'onboarding_completed',
        'catalog_import_mode'
    ];
}
