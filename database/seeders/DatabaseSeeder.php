<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@store.com'],
            [
                'name'     => 'مدير المتجر',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Store settings
        if (!StoreSetting::first()) {
            StoreSetting::create([
                'store_name_ar'           => 'متجرنا',
                'store_name_en'           => 'Our Store',
                'primary_color'           => '#6C3FC5',
                'secondary_color'         => '#EFE9FA',
                'font_family'             => 'Cairo',
                'currency'                => 'SAR',
                'currency_symbol'         => 'ر.س',
                'shipping_cost'           => 20.00,
                'free_shipping_threshold' => 200.00,
                'tax_rate'                => 15.00,
                'enable_reviews'          => true,
                'enable_wishlist'         => true,
                'enable_newsletter'       => true,
                'default_locale'          => 'ar',
            ]);
        }

        // Sample categories
        $this->seedCategories();
    }

    private function seedCategories(): void
    {
        $cats = [
            ['name' => 'إلكترونيات', 'name_ar' => 'إلكترونيات',  'name_en' => 'Electronics',  'icon' => 'fas fa-laptop'],
            ['name' => 'ملابس',       'name_ar' => 'ملابس',        'name_en' => 'Clothing',      'icon' => 'fas fa-tshirt'],
            ['name' => 'المنزل',      'name_ar' => 'المنزل والمطبخ','name_en'=> 'Home & Kitchen','icon' => 'fas fa-home'],
            ['name' => 'الجمال',      'name_ar' => 'الجمال والعناية','name_en'=> 'Beauty',       'icon' => 'fas fa-spa'],
            ['name' => 'الرياضة',    'name_ar' => 'الرياضة',       'name_en' => 'Sports',        'icon' => 'fas fa-dumbbell'],
            ['name' => 'الكتب',       'name_ar' => 'الكتب',         'name_en' => 'Books',         'icon' => 'fas fa-book'],
        ];

        foreach ($cats as $i => $cat) {
            Category::firstOrCreate(
                ['name_en' => $cat['name_en']],
                array_merge($cat, [
                    'is_active'  => true,
                    'sort_order' => $i + 1,
                ])
            );
        }
    }
}
