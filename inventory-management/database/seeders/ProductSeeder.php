<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $products = [
            ['name' => 'Laptop Pro', 'sku' => 'LAP-001', 'price' => 1299.99, 'quantity' => 50],
            ['name' => 'Wireless Mouse', 'sku' => 'MOU-001', 'price' => 29.99, 'quantity' => 200],
            ['name' => 'USB-C Hub', 'sku' => 'HUB-001', 'price' => 49.99, 'quantity' => 150],
            ['name' => 'Mechanical Keyboard', 'sku' => 'KEY-001', 'price' => 89.99, 'quantity' => 75],
            ['name' => '4K Monitor', 'sku' => 'MON-001', 'price' => 399.99, 'quantity' => 30],
            ['name' => 'Webcam HD', 'sku' => 'CAM-001', 'price' => 79.99, 'quantity' => 100],
            ['name' => 'Headphones', 'sku' => 'HEA-001', 'price' => 149.99, 'quantity' => 80],
            ['name' => 'Desk Lamp', 'sku' => 'LAM-001', 'price' => 34.99, 'quantity' => 5], // Low stock
            ['name' => 'Chair Mat', 'sku' => 'MAT-001', 'price' => 44.99, 'quantity' => 3], // Low stock
            ['name' => 'Cable Organizer', 'sku' => 'ORG-001', 'price' => 14.99, 'quantity' => 8], // Low stock
        ];

        foreach ($products as $product) {
            Product::create([
                ...$product,
                'category_id' => $categories->random()->id,
                'description' => "High quality {$product['name']} for everyday use.",
            ]);
        }
    }
}
