<?php

namespace App\Http\Controllers\API\V1\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompaniesManageController extends Controller
{
    // Middleware is applied via route group in api.php

    /**
     * 🟢 List Companies
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $companies = Company::with('user')
            ->select([
                'id',
                'company_name',
                'logo',
                'company_email',
                'manager_phone',
                'user_id',
                'created_at'
            ])
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $companies
        ]);
    }

    /**
     * 🟢 Create Company + assign admin user
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'chairman_name' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:30',
            'product_link' => 'required|string|max:255',
            'factory_address' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',

            //  اختيار الأدمن
            'user_id' => 'required|exists:users,id',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // رفع الصورة
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }

        // إنشاء الشركة
        $company = Company::create([
            'company_name' => $data['company_name'],
            'chairman_name' => $data['chairman_name'],
            'manager_name' => $data['manager_name'],
            'manager_phone' => $data['manager_phone'],
            'product_link' => $data['product_link'],
            'factory_address' => $data['factory_address'],
            'company_email' => $data['company_email'],
            'logo' => $data['logo'] ?? null,

            //  ربط الأدمن
            'user_id' => $data['user_id'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    /**
     * 🟢 Show Company
     */
    public function show($id)
    {
        $company = Company::with('user')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $company
        ]);
    }

    /**
     * 🟡 Update Company
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'chairman_name' => 'sometimes|string|max:255',
            'manager_name' => 'sometimes|string|max:255',
            'manager_phone' => 'sometimes|string|max:30',
            'product_link' => 'sometimes|string|max:255',
            'factory_address' => 'sometimes|string|max:255',
            'company_email' => 'sometimes|email|max:255',

            //  تغيير الأدمن
            'user_id' => 'sometimes|exists:users,id',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // تغيير الصورة
        if ($request->hasFile('logo')) {

            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update(array_filter($data));

        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ]);
    }

    /**
     * 🔴 Delete Company
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company deleted successfully'
        ]);
    }
}
