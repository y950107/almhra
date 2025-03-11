<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // إنشاء دور سوبر أدمن إذا لم يكن موجودًا
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // إنشاء المستخدم
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'phone' => '0776794910',
                'type' => 'admin'
            ]
        );

        // تعيين الدور للمستخدم
        $user->assignRole($role);
    }
}
