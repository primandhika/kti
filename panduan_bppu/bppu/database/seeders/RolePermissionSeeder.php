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

        // Create roles with professional descriptions
        $roles = [
            [
                'name' => 'head',
                'description' => 'University leadership with executive dashboard access. Can view comprehensive reports, financial statements, and cash flow analytics. Read-only access for strategic oversight and decision-making.'
            ],
            [
                'name' => 'sysadmin',
                'description' => 'System administrator with full user management capabilities. Responsible for managing user accounts, assigning roles, and maintaining system security. Has comprehensive access to all cash books and system configurations.'
            ],
            [
                'name' => 'officer',
                'description' => 'Administrative officer responsible for daily operational tasks. Manages content, financial entries, reports, and unit-specific operations. Full access to create, edit, and manage operational data.'
            ],
            [
                'name' => 'canteen',
                'description' => 'Canteen staff member with access to order management and inventory tracking. Can view incoming orders, manage stock levels, and monitor canteen-specific transactions.'
            ],
            [
                'name' => 'shop',
                'description' => 'Shop staff member with access to retail operations. Can manage stock opname, view incoming orders, track inventory levels, and handle shop-specific transactions.'
            ],
            [
                'name' => 'buyer',
                'description' => 'Canteen member (pembeli) with ordering access and points collection. Can place orders at canteen and accumulate loyalty points for discounts on future purchases.'
            ],
            [
                'name' => 'supplier',
                'description' => 'Supplier partner providing goods and products to the institution.'
            ],
            [
                'name' => 'tenant',
                'description' => 'Tenant partner managing booth or space within the institution premises.'
            ],
            [
                'name' => 'vendor',
                'description' => 'Vendor partner providing specific services or products.'
            ],
            [
                'name' => 'distributor',
                'description' => 'Distributor partner for product distribution and supply chain.'
            ],
            [
                'name' => 'produsen',
                'description' => 'Manufacturer/producer partner creating and supplying products.'
            ],
            [
                'name' => 'reseller',
                'description' => 'Reseller partner for product resale activities.'
            ],
            [
                'name' => 'konsinyasi',
                'description' => 'Consignment partner managing goods on consignment basis.'
            ],
            [
                'name' => 'mesin',
                'description' => 'Akun perangkat layar/mesin self-order di kantin. Digunakan sebagai kredensial untuk perangkat yang terpasang di area kantin agar pengunjung bisa memesan menu secara mandiri.'
            ],
            [
                'name' => 'data_clerk',
                'description' => 'Data clerk with exclusive access to archive and media management. Responsible for organizing, uploading, and managing institutional files and documentation.'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                    'description' => $roleData['description'],
                ]
            );
        }

        // Create permissions for different modules
        $permissions = [
            // Dashboard permissions
            'view-executive-dashboard',
            'view-officer-dashboard',
            'view-canteen-dashboard',
            'view-shop-dashboard',
            'view-sysadmin-dashboard',

            // User management permissions
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Content management permissions
            'manage-content',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'manage-pages',
            'manage-menus',

            // Financial permissions
            'view-all-cashbooks',
            'view-own-cashbook',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'view-financial-reports',

            // Canteen permissions
            'view-canteen-orders',
            'manage-canteen-stock',
            'manage-canteen-menus',
            'view-canteen-transactions',

            // Shop permissions
            'view-shop-orders',
            'manage-shop-stock',
            'view-stock-opname',

            // Buyer permissions
            'view-buyer-dashboard',
            'create-canteen-order',
            'view-own-orders',
            'view-own-points',

            // Mesin permissions
            'access-self-order-kiosk',

            // Mitra permissions
            'view-mitra-dashboard',
            'manage-own-products',
            'view-mitra-orders',
            'view-mitra-transactions',

            // Unit management permissions
            'manage-work-units',
            'view-work-units',

            // Report permissions
            'create-reports',
            'view-reports',
            'manage-archives',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Assign permissions to roles

        // HEAD Role - Read-only executive access
        $headRole = Role::findByName('head');
        $headRole->givePermissionTo([
            'view-executive-dashboard',
            'view-all-cashbooks',
            'view-financial-reports',
            'view-reports',
            'view-work-units',
        ]);

        // SYSADMIN Role - Full system and user management
        $sysadminRole = Role::findByName('sysadmin');
        $sysadminRole->givePermissionTo([
            'view-sysadmin-dashboard',
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-all-cashbooks',
            'view-financial-reports',
        ]);

        // OFFICER Role - Full operational access
        $officerRole = Role::findByName('officer');
        $officerRole->givePermissionTo([
            'view-officer-dashboard',
            'manage-content',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'manage-pages',
            'manage-menus',
            'manage-canteen-menus',
            'view-own-cashbook',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'manage-work-units',
            'view-work-units',
            'create-reports',
            'view-reports',
            'manage-archives',
            'view-stock-opname',
        ]);

        // CANTEEN Role - Canteen operations
        $canteenRole = Role::findByName('canteen');
        $canteenRole->givePermissionTo([
            'view-canteen-dashboard',
            'view-canteen-orders',
            'manage-canteen-stock',
            'view-canteen-transactions',
        ]);

        // SHOP Role - Shop operations
        $shopRole = Role::findByName('shop');
        $shopRole->givePermissionTo([
            'view-shop-dashboard',
            'view-shop-orders',
            'manage-shop-stock',
            'view-stock-opname',
        ]);

        // BUYER Role - Canteen member operations
        $buyerRole = Role::findByName('buyer');
        $buyerRole->givePermissionTo([
            'view-buyer-dashboard',
            'create-canteen-order',
            'view-own-orders',
            'view-own-points',
        ]);

        // MITRA Roles - Business partner operations (all mitra types get same permissions)
        $mitraRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];
        foreach ($mitraRoles as $roleName) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo([
                'view-mitra-dashboard',
                'manage-own-products',
                'view-mitra-orders',
                'view-mitra-transactions',
            ]);
        }

        // MESIN Role - Self-order kiosk device account
        $mesinRole = Role::findByName('mesin');
        $mesinRole->givePermissionTo([
            'access-self-order-kiosk',
            'create-canteen-order',
        ]);

        // DATA_CLERK Role - Archive and barang management
        $dataClerkRole = Role::findByName('data_clerk');
        $dataClerkRole->givePermissionTo([
            'manage-archives',
            'manage-shop-stock',
            'view-stock-opname',
        ]);
    }
}
