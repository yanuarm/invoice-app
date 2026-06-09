<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_logo',
        'invoice_prefix',
        'invoice_footer',
    ];

    public static function getSettings(): self
    {
        return static::first() ?? new static;
    }

    public static function getInvoicePrefix(): string
    {
        $settings = static::getSettings();

        return $settings->invoice_prefix ?: 'INV';
    }
}
