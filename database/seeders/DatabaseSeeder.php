<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create System Admin first (no shop required)
        $systemAdmin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@shopledger.com',
            'password' => Hash::make('password'),
            'role' => 'system_admin',
            'account_status' => 'active',
        ]);

        // Create Shop 1 Admin (without shop_id first)
        $shopAdmin1 = User::create([
            'name' => 'John Smith',
            'email' => 'john@techstore.com',
            'password' => Hash::make('password'),
            'role' => 'shop_admin',
            'account_status' => 'active',
        ]);

        // Create Approved Shop 1 (created_by shopAdmin1)
        $shop1 = Shop::create([
            'name' => 'Tech Store',
            'slug' => 'tech-store',
            'status' => 'approved',
            'created_by' => $shopAdmin1->id,
            'approved_by' => $systemAdmin->id,
        ]);

        // Update shopAdmin1 with shop_id
        $shopAdmin1->update(['shop_id' => $shop1->id]);

        // Shop 1 Staff
        User::create([
            'name' => 'Alice Seller',
            'email' => 'alice@techstore.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'shop_id' => $shop1->id,
            'account_status' => 'active',
        ]);

        User::create([
            'name' => 'Bob Accountant',
            'email' => 'bob@techstore.com',
            'password' => Hash::make('password'),
            'role' => 'accountant',
            'shop_id' => $shop1->id,
            'account_status' => 'active',
        ]);

        // Create Shop 2 Admin (without shop_id first)
        $shopAdmin2 = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@fashionhub.com',
            'password' => Hash::make('password'),
            'role' => 'shop_admin',
            'account_status' => 'active',
        ]);

        // Create Approved Shop 2
        $shop2 = Shop::create([
            'name' => 'Fashion Hub',
            'slug' => 'fashion-hub',
            'status' => 'approved',
            'created_by' => $shopAdmin2->id,
            'approved_by' => $systemAdmin->id,
        ]);

        // Update shopAdmin2 with shop_id
        $shopAdmin2->update(['shop_id' => $shop2->id]);

        // Shop 2 Staff
        User::create([
            'name' => 'Charlie Seller',
            'email' => 'charlie@fashionhub.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'shop_id' => $shop2->id,
            'account_status' => 'active',
        ]);

        // Create Pending Shop Admin (without shop_id first)
        $pendingAdmin1 = User::create([
            'name' => 'Mike Pending',
            'email' => 'mike@newelectronics.com',
            'password' => Hash::make('password'),
            'role' => 'shop_admin',
            'account_status' => 'active',
        ]);

        // Create Pending Shop
        $pendingShop = Shop::create([
            'name' => 'New Electronics',
            'slug' => 'new-electronics',
            'status' => 'pending',
            'created_by' => $pendingAdmin1->id,
        ]);

        // Update pendingAdmin1 with shop_id
        $pendingAdmin1->update(['shop_id' => $pendingShop->id]);

        // Create another Pending Shop Admin
        $pendingAdmin2 = User::create([
            'name' => 'Sarah Waiting',
            'email' => 'sarah@grocerymart.com',
            'password' => Hash::make('password'),
            'role' => 'shop_admin',
            'account_status' => 'active',
        ]);

        // Create another Pending Shop
        $pendingShop2 = Shop::create([
            'name' => 'Grocery Mart',
            'slug' => 'grocery-mart',
            'status' => 'pending',
            'created_by' => $pendingAdmin2->id,
        ]);

        // Update pendingAdmin2 with shop_id
        $pendingAdmin2->update(['shop_id' => $pendingShop2->id]);

        // Regular user (no shop)
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'account_status' => 'active',
        ]);

        // Add suppliers for Shop 1 (using contact_name to match migration)
        Supplier::create([
            'shop_id' => $shop1->id,
            'name' => 'Tech Wholesale Inc',
            'contact_name' => 'David Tech',
            'email' => 'david@techwholesale.com',
            'phone' => '555-0101',
            'address' => '123 Tech Street',
        ]);

        Supplier::create([
            'shop_id' => $shop1->id,
            'name' => 'Gadget Suppliers',
            'contact_name' => 'Emma Gadget',
            'email' => 'emma@gadgetsuppliers.com',
            'phone' => '555-0102',
        ]);

        // Add products for Shop 1 (using stock to match migration)
        Product::create([
            'shop_id' => $shop1->id,
            'sku' => 'TECH-001',
            'name' => 'Laptop Pro 15',
            'description' => 'High-performance laptop',
            'cost_price' => 800.00,
            'sale_price' => 1200.00,
            'stock' => 25,
        ]);

        Product::create([
            'shop_id' => $shop1->id,
            'sku' => 'TECH-002',
            'name' => 'Wireless Mouse',
            'description' => 'Ergonomic wireless mouse',
            'cost_price' => 15.00,
            'sale_price' => 35.00,
            'stock' => 100,
        ]);

        Product::create([
            'shop_id' => $shop1->id,
            'sku' => 'TECH-003',
            'name' => 'USB-C Hub',
            'description' => '7-in-1 USB-C hub',
            'cost_price' => 25.00,
            'sale_price' => 55.00,
            'stock' => 50,
        ]);

        Product::create([
            'shop_id' => $shop1->id,
            'sku' => 'TECH-004',
            'name' => 'Mechanical Keyboard',
            'description' => 'RGB mechanical keyboard',
            'cost_price' => 45.00,
            'sale_price' => 89.00,
            'stock' => 30,
        ]);

        Product::create([
            'shop_id' => $shop1->id,
            'sku' => 'TECH-005',
            'name' => 'Monitor 27"',
            'description' => '4K IPS monitor',
            'cost_price' => 250.00,
            'sale_price' => 399.00,
            'stock' => 5,  // Low stock
        ]);

        // Add suppliers for Shop 2
        Supplier::create([
            'shop_id' => $shop2->id,
            'name' => 'Fashion Wholesale',
            'contact_name' => 'Fiona Fashion',
            'email' => 'fiona@fashionwholesale.com',
            'phone' => '555-0201',
        ]);

        // Add products for Shop 2
        Product::create([
            'shop_id' => $shop2->id,
            'sku' => 'FASH-001',
            'name' => 'Summer Dress',
            'description' => 'Floral summer dress',
            'cost_price' => 25.00,
            'sale_price' => 65.00,
            'stock' => 40,
        ]);

        Product::create([
            'shop_id' => $shop2->id,
            'sku' => 'FASH-002',
            'name' => 'Denim Jeans',
            'description' => 'Classic fit denim jeans',
            'cost_price' => 30.00,
            'sale_price' => 79.00,
            'stock' => 60,
        ]);
    }
}
