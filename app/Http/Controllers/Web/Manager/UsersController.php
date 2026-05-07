<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsersController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        $query = User::with('company')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $sortField    = $request->get('sort', 'created_at');
        $sortDir      = $request->get('dir', 'desc');
        $allowedSorts = ['name', 'email', 'created_at', 'role'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('company_name')->get();

        return view('admin.users.create', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'role'       => 'required|in:user,company_admin,super_admin',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        User::logActivity('created', "تم إنشاء مستخدم جديد: {$user->name} ({$user->email})");

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function show(User $user): View
    {
        $user->load('company');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $companies = Company::orderBy('company_name')->get();

        return view('admin.users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'   => 'nullable|min:8|confirmed',
            'role'       => 'required|in:user,company_admin,super_admin',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        User::logActivity('updated', "تم تعديل بيانات مستخدم: {$user->name}");

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;

        User::logActivity('deleted', "تم حذف مستخدم: {$name}");

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        // Users table doesn't have status yet — we use role manipulation for now.
        // This toggles between 'user' and keeps other roles intact via a separate active flag.
        // For now redirect back with info.
        return back()->with('info', 'خاصية تعطيل المستخدم ستكون متاحة قريباً.');
    }
}
