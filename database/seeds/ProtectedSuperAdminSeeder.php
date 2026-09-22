<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProtectedSuperAdminSeeder extends Seeder
{
    public function run()
    {
        $usuario = User::where('email', 'freyna@lapotosinaexpress.com')->first();

        if (!$usuario) {
            $usuario = new User();
            $usuario->name = 'kiko reyna';
            $usuario->email = 'freyna@lapotosinaexpress.com';
            $usuario->password = Hash::make(env('SUPERADMIN_PASSWORD', 'password'));
        }

        $usuario->rol = 'superadministrador';
        $usuario->activo = true;
        $usuario->save();
    }
}
