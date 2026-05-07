<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user         = auth()->user();
        $recentOrders = $user->orders()->with('orderItems')->latest()->limit(5)->get();
        $ordersCount  = $user->orders()->count();
        $wishlistCount = $user->wishlist()->count();
        $reviewsCount = $user->reviews()->count();
        return view('store.account.dashboard', compact('user', 'recentOrders', 'ordersCount', 'wishlistCount', 'reviewsCount'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()
            ->with('orderItems')
            ->latest()
            ->paginate(10);
        return view('store.account.orders', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['orderItems.product', 'orderItems.variant', 'statusHistory']);
        return view('store.account.order-detail', compact('order'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('store.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'date_of_birth'=> 'nullable|date',
            'gender'       => 'nullable|in:male,female,other',
            'locale'       => 'nullable|in:ar,en',
            'avatar'       => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if (isset($data['locale'])) {
            app()->setLocale($data['locale']);
            session(['locale' => $data['locale']]);
        }

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    // ── Addresses ──────────────────────────────────────────────────────
    public function addresses()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        return view('store.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'label'         => 'nullable|string|max:100',
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:30',
            'address_line1' => 'required|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'city'          => 'required|string|max:100',
            'state'         => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:10',
            'is_default'    => 'nullable|boolean',
        ]);

        $user = auth()->user();

        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $data['is_default'] = $request->boolean('is_default', $user->addresses()->count() === 0);
        $user->addresses()->create($data);

        return back()->with('success', 'تم إضافة العنوان بنجاح');
    }

    public function destroyAddress(\App\Models\Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'تم حذف العنوان بنجاح');
    }

    // ── Wishlist ───────────────────────────────────────────────────────
    public function wishlist()
    {
        $wishlistItems = Wishlist::where('user_id', auth()->id())
            ->with(['product.activeVariants', 'product.brand', 'product.category'])
            ->latest()
            ->paginate(20);
        return view('store.account.wishlist', compact('wishlistItems'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message    = 'تم الإزالة من المفضلة';
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $request->product_id]);
            $wishlisted = true;
            $message    = 'تمت الإضافة للمفضلة';
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function setDefaultAddress(\App\Models\Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return back()->with('success', 'تم تعيين العنوان الافتراضي');
    }
}
