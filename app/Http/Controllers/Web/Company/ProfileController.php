<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        
        if (! $company) {
            abort(403, 'لا توجد شركة مرتبطة بهذا الحساب.');
        }

        return view('company.profile.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'company_name'              => 'required|string|max:255',
            'chairman_name'             => 'nullable|string|max:255',
            'manager_name'              => 'nullable|string|max:255',
            'manager_phone'             => 'nullable|string|max:50',
            'product_link'              => 'nullable|url|max:500',
            'factory_address'           => 'nullable|string',
            'branches'                  => 'nullable|string',
            'exhibitions'               => 'nullable|string',
            'company_email'             => 'nullable|email|max:255',
            'website'                   => 'nullable|url|max:255',
            'hotline'                   => 'nullable|string|max:50',
            'whatsapp'                  => 'nullable|string|max:50',
            'location_url'              => 'nullable|url|max:500',
            'company_working_hours'     => 'nullable|string|max:255',
            'branches_working_hours'    => 'nullable|string|max:255',
            'exhibitions_working_hours' => 'nullable|string|max:255',
            'logo'                      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update($validated);

        return redirect()->route('company.profile.edit')
            ->with('success', 'تم تحديث بيانات الشركة بنجاح!');
    }
}
