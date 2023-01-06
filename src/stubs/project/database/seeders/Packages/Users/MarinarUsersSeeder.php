<?php
namespace Database\Seeders\Packages\Users;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MarinarUsersSeeder extends Seeder {

    public function run() {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::upsert([
            ['guard_name' => 'admin', 'name' => 'users.view'],
            ['guard_name' => 'admin', 'name' => 'user.create'],
            ['guard_name' => 'admin', 'name' => 'user.view'],
            ['guard_name' => 'admin', 'name' => 'user.update'],
            ['guard_name' => 'admin', 'name' => 'user.delete'],
        ], ['guard_name','name']);
    }
}
