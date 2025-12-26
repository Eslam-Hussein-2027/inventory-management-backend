<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Create permissions
    $permissions = [
      // Products
      'products.view',
      'products.create',
      'products.update',
      'products.delete',

      // Categories
      'categories.view',
      'categories.create',
      'categories.update',
      'categories.delete',

      // Users
      'users.view',
      'users.create',
      'users.update',
      'users.delete',

      // Orders
      'orders.view',
      'orders.view-own',
      'orders.create',
      'orders.update',
      'orders.delete',

      // Suppliers
      'suppliers.view',
      'suppliers.create',
      'suppliers.update',
      'suppliers.delete',

      // Dashboard
      'dashboard.view',
    ];

    foreach ($permissions as $permission) {
      Permission::create(['name' => $permission, 'guard_name' => 'sanctum']);
    }

    // Create Admin Role with all permissions
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
    $adminRole->givePermissionTo(Permission::all());

    // Create User Role with limited permissions
    $userRole = Role::create(['name' => 'user', 'guard_name' => 'sanctum']);
    $userRole->givePermissionTo([
      'products.view',
      'categories.view',
      'suppliers.view',
      'orders.view-own',
      'orders.create',
    ]);
  }
}
