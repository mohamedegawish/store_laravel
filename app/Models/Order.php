<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'company_id', 'order_number',
        'status', 'payment_status', 'payment_method',
        'subtotal', 'discount_amount', 'coupon_code', 'coupon_discount',
        'tax_amount', 'shipping_cost', 'total_amount',
        'shipping_address', 'billing_address',
        'notes', 'admin_notes',
        'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'coupon_discount'  => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'shipping_cost'    => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'shipped_at'       => 'datetime',
        'delivered_at'     => 'datetime',
        'cancelled_at'     => 'datetime',
    ];

    public static $statuses = [
        'pending'    => ['label_ar' => 'معلق',       'label_en' => 'Pending',    'color' => 'warning'],
        'confirmed'  => ['label_ar' => 'مؤكد',       'label_en' => 'Confirmed',  'color' => 'info'],
        'processing' => ['label_ar' => 'قيد التجهيز', 'label_en' => 'Processing', 'color' => 'primary'],
        'shipped'    => ['label_ar' => 'تم الشحن',   'label_en' => 'Shipped',    'color' => 'purple'],
        'delivered'  => ['label_ar' => 'تم التوصيل', 'label_en' => 'Delivered',  'color' => 'success'],
        'cancelled'  => ['label_ar' => 'ملغي',       'label_en' => 'Cancelled',  'color' => 'danger'],
        'refunded'   => ['label_ar' => 'مسترد',      'label_en' => 'Refunded',   'color' => 'secondary'],
    ];

    public static $paymentStatuses = [
        'pending'  => ['label_ar' => 'في الانتظار', 'label_en' => 'Pending',  'color' => 'warning'],
        'paid'     => ['label_ar' => 'مدفوع',       'label_en' => 'Paid',     'color' => 'success'],
        'failed'   => ['label_ar' => 'فشل',         'label_en' => 'Failed',   'color' => 'danger'],
        'refunded' => ['label_ar' => 'مسترد',       'label_en' => 'Refunded', 'color' => 'secondary'],
    ];

    // ── Relationships ──────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    // ── Accessors ──────────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return self::$statuses[$this->status]["label_{$locale}"] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statuses[$this->status]['color'] ?? 'secondary';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return self::$paymentStatuses[$this->payment_status]["label_{$locale}"] ?? $this->payment_status;
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return self::$paymentStatuses[$this->payment_status]['color'] ?? 'secondary';
    }

    // ── Scopes ─────────────────────────────────────────────────────────
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ── Helpers ────────────────────────────────────────────────────────
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
