<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function create(array $data, array $files = []): Product
    {
        return DB::transaction(function () use ($data, $files) {
            $images = $this->handleImages($files['images'] ?? []);

            $product = Product::create(array_merge($data, [
                'images'     => $images,
                'company_id' => $data['company_id'] ?? 1,
            ]));

            // Create variants
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $index => $variantData) {
                    $variantImage = null;
                    if (!empty($files["variant_images"][$index])) {
                        $variantImage = $files["variant_images"][$index]->store('products/variants', 'public');
                    }
                    $this->createVariant($product, array_merge($variantData, [
                        'image'      => $variantImage,
                        'is_default' => $index === 0,
                    ]));
                }
            } else {
                // Create a default single variant
                $this->createVariant($product, array_merge($data, ['is_default' => true]));
            }

            return $product->fresh(['variants', 'category', 'brand']);
        });
    }

    public function update(Product $product, array $data, array $files = []): Product
    {
        return DB::transaction(function () use ($product, $data, $files) {
            // Handle new images
            if (!empty($files['images'])) {
                $existingImages = $product->images ?? [];
                $newImages = $this->handleImages($files['images']);
                $data['images'] = array_merge($existingImages, $newImages);
            }

            // Remove deleted images
            if (!empty($data['remove_images'])) {
                $currentImages = $product->images ?? [];
                foreach ($data['remove_images'] as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                    $currentImages = array_diff($currentImages, [$imagePath]);
                }
                $data['images'] = array_values($currentImages);
                unset($data['remove_images']);
            }

            $product->update($data);
            return $product->fresh(['variants', 'category', 'brand']);
        });
    }

    public function delete(Product $product): void
    {
        // Soft delete — images remain for order history references
        $product->delete();
    }

    public function createVariant(Product $product, array $data): ProductVariant
    {
        $data['sku'] = $data['sku'] ?? $this->generateSku($product);
        return $product->variants()->create($data);
    }

    public function updateVariant(ProductVariant $variant, array $data, ?UploadedFile $image = null): ProductVariant
    {
        if ($image) {
            if ($variant->image) Storage::disk('public')->delete($variant->image);
            $data['image'] = $image->store('products/variants', 'public');
        }
        $variant->update($data);
        return $variant->fresh();
    }

    public function deleteVariant(ProductVariant $variant): void
    {
        if ($variant->image) Storage::disk('public')->delete($variant->image);
        $variant->delete();
    }

    public function adjustStock(ProductVariant $variant, int $delta): void
    {
        $variant->increment('stock_quantity', $delta);
    }

    private function handleImages(array $files): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $file->store('products', 'public');
            }
        }
        return $paths;
    }

    private function generateSku(Product $product): string
    {
        $prefix = strtoupper(Str::substr(preg_replace('/[^a-zA-Z0-9]/', '', $product->name_en ?? 'PROD'), 0, 4));
        return $prefix . '-' . strtoupper(Str::random(6));
    }
}
