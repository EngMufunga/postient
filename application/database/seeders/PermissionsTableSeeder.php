<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'groupby' => 'Dashboard',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Role',
                'slug' => 'role',
                'groupby' => 'Role Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Staff Management',
                'slug' => 'staff',
                'groupby' => 'Staff Mangement',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'User Management',
                'slug' => 'user-management',
                'groupby' => 'User Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Subscriber Management',
                'slug' => 'subscriber-management',
                'groupby' => 'Subscriber Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Deposit Management',
                'slug' => 'deposit-management',
                'groupby' => 'Deposit Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Payment Method',
                'slug' => 'payment-method',
                'groupby' => 'Payment Method',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Support Ticket',
                'slug' => 'support-ticket',
                'groupby' => 'Support Ticket',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Reports',
                'slug' => 'reports',
                'groupby' => 'Report Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Settings',
                'slug' => 'settings',
                'groupby' => 'Global Settings',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Page Management',
                'slug' => 'page-management',
                'groupby' => 'Page Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Section Management',
                'slug' => 'section-management',
                'groupby' => 'Section Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Language Management',
                'slug' => 'language-management',
                'groupby' => 'Language Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Plugin Management',
                'slug' => 'plugin-management',
                'groupby' => 'Plugin Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Admin Notification',
                'slug' => 'admin-notification',
                'groupby' => 'Topbar Notification',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Website Menu Management',
                'slug' => 'website-menu-management',
                'groupby' => 'Website Menu Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Plan Management',
                'slug' => 'plan-management',
                'groupby' => 'Plan Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Platform Management',
                'slug' => 'platform-management',
                'groupby' => 'Platform Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'name' => 'Post Management',
                'slug' => 'post-management',
                'groupby' => 'Post Management',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}

