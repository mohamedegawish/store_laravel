<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use LogsActivity;

    protected $table = 'companies';

    protected $fillable = [
        'company_name',
        'slug',
        'chairman_name',
        'manager_name',
        'manager_phone',
        'product_link',
        'factory_address',
        'branches',
        'exhibitions',
        'company_email',
        'website',
        'hotline',
        'whatsapp',
        'location_url',
        'company_working_hours',
        'branches_working_hours',
        'exhibitions_working_hours',
        'logo',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storeSetting()
    {
        return $this->hasOne(StoreSetting::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active'   => 'نشطة',
            'inactive' => 'معطلة',
            'pending'  => 'قيد المراجعة',
            default    => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active'   => 'success',
            'inactive' => 'danger',
            'pending'  => 'warning',
            default    => 'secondary',
        };
    }
}
