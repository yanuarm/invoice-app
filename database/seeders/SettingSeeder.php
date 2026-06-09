<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'company_name' => 'My Company',
            'company_email' => 'company@example.com',
            'company_phone' => '+62 21 1234 5678',
            'company_address' => "123 Business Street\nJakarta, Indonesia",
            'invoice_prefix' => 'INV',
            'invoice_footer' => 'Thank you for your business!',
        ]);
    }
}
