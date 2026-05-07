<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompaniesController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        $query = Company::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('company_email', 'like', "%{$search}%")
                    ->orWhere('chairman_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDir   = $request->get('dir', 'desc');
        $allowedSorts = ['company_name', 'created_at', 'status', 'manager_name'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $companies = $query->paginate(15)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.companies.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'              => 'required|string|max:255',
            'slug'                      => 'required|string|max:255|unique:companies,slug',
            'chairman_name'             => 'required|string|max:255',
            'manager_name'              => 'required|string|max:255',
            'manager_phone'             => 'required|string|max:30',
            'product_link'              => 'nullable|string|max:255',
            'factory_address'           => 'required|string|max:255',
            'branches'                  => 'nullable|string|max:255',
            'exhibitions'               => 'nullable|string|max:255',
            'company_email'             => 'required|email|max:255',
            'website'                   => 'nullable|url|max:255',
            'hotline'                   => 'nullable|string|max:30',
            'whatsapp'                  => 'nullable|string|max:30',
            'location_url'              => 'nullable|string|max:500',
            'company_working_hours'     => 'nullable|string|max:255',
            'branches_working_hours'    => 'nullable|string|max:255',
            'exhibitions_working_hours' => 'nullable|string|max:255',
            'status'                    => 'required|in:active,inactive,pending',
            'user_id'                   => 'nullable|exists:users,id',
            'logo'                      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company = Company::create($validated);

        Company::logActivity('created', "تم إنشاء شركة جديدة: {$company->company_name}", $company);

        return redirect()->route('admin.companies.index')->with('success', 'تم إنشاء الشركة بنجاح.');
    }

    public function show(Company $company): View
    {
        $company->load('user', 'products');

        return view('admin.companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.companies.edit', compact('company', 'users'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'              => 'required|string|max:255',
            'slug'                      => "required|string|max:255|unique:companies,slug,{$company->id}",
            'chairman_name'             => 'required|string|max:255',
            'manager_name'              => 'required|string|max:255',
            'manager_phone'             => 'required|string|max:30',
            'product_link'              => 'nullable|string|max:255',
            'factory_address'           => 'required|string|max:255',
            'branches'                  => 'nullable|string|max:255',
            'exhibitions'               => 'nullable|string|max:255',
            'company_email'             => 'required|email|max:255',
            'website'                   => 'nullable|url|max:255',
            'hotline'                   => 'nullable|string|max:30',
            'whatsapp'                  => 'nullable|string|max:30',
            'location_url'              => 'nullable|string|max:500',
            'company_working_hours'     => 'nullable|string|max:255',
            'branches_working_hours'    => 'nullable|string|max:255',
            'exhibitions_working_hours' => 'nullable|string|max:255',
            'status'                    => 'required|in:active,inactive,pending',
            'user_id'                   => 'nullable|exists:users,id',
            'logo'                      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update($validated);

        Company::logActivity('updated', "تم تعديل بيانات شركة: {$company->company_name}", $company);

        return redirect()->route('admin.companies.index')->with('success', 'تم تحديث الشركة بنجاح.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $name = $company->company_name;

        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        Company::logActivity('deleted', "تم حذف شركة: {$name}");

        $company->delete();

        return redirect()->route('admin.companies.index')->with('success', 'تم حذف الشركة بنجاح.');
    }

    public function toggleStatus(Company $company): RedirectResponse
    {
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $company->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'تفعيل' : 'تعطيل';
        Company::logActivity($newStatus, "تم {$label} شركة: {$company->company_name}", $company);

        return back()->with('success', "تم {$label} الشركة بنجاح.");
    }
}
