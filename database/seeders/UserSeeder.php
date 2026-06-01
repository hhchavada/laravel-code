<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $check = User::where('name', 'Admin')->where('type', 'admin')->where('email', 'admin@gmail.com')->first();
        if (!$check) {
            $user = User::create([
                'name' => 'Admin', 
                'type' => 'admin', 
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin')
            ]);

            $role = Role::where('name', 'admin')->where('guard_name', 'web')->first();

            if (!$role) {
                // If the 'admin' role for the 'web' guard doesn't exist, create it
                $role = Role::create([
                    'name' => 'admin',
                    'guard_name' => 'web', // Make sure the guard name matches your configuration
                    'status' => 1,
                    'created_at' => now()
                ]);
            }

            $permissions = Permission::pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
            $user->assignRole([$role->id]);
        }
    }
}
