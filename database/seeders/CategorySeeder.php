<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
      ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
      ['name' => 'Home & Garden', 'description' => 'Home improvement and gardening'],
      ['name' => 'Sports', 'description' => 'Sports equipment and accessories'],
      ['name' => 'Books', 'description' => 'Books and educational materials'],
    ];

    foreach ($categories as $category) {
      Category::create($category);
    }
  }
}
