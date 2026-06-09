<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyProfileRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyProfileController extends Controller
{
    public function edit(): Response
    {
        $settings = Setting::getSettings();

        return Inertia::render('settings/Company', [
            'settings' => [
                'company_name' => $settings->company_name,
                'company_email' => $settings->company_email,
                'company_phone' => $settings->company_phone,
                'company_address' => $settings->company_address,
                'company_logo' => $settings->company_logo ? Storage::url($settings->company_logo) : null,
                'invoice_prefix' => $settings->invoice_prefix ?: 'INV',
                'invoice_footer' => $settings->invoice_footer,
            ],
        ]);
    }

    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company', 'public');
        }

        $settings = Setting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            Setting::create($data);
        }

        return to_route('company.edit');
    }
}
