<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            'name' => 'Transporte Turístico y Empresarial Edwin',
            'rnc' => '131285742',
            'address' => 'KM 17 Marginal de las Américas, Urbanización Prado del Este, Av. Los palmeros #23, SDE',
            'office_phone' => '8094223340',
            'mobile_phone' => '8298866699',
            'mail' => 'info@transporteturisticoedwin.com',
            'website' => 'https://transporteturisticoedwin.com/',
            'facebook' => 'https://www.facebook.com/Transporte-Tur%C3%ADstico-Edwin-SRL-927196780676784',
            'instagram' => 'https://www.instagram.com/transporteturisticoedwin/',
        ]);

        DB::table('users')->insert([
            'name' => 'Erickson Suero',
            'email' => 'ericksuero@gmail.com',
            'password' => Hash::make('password')
        ]);

        DB::table('service_statuses')->insert([
            ['status' => 'CANCELADO'],
            ['status' => 'COMPLETADO'],
            ['status' => 'EN PROCESO'],
            ['status' => 'PENDIENTE'],
        ]);

        DB::table('service_types')->insert([
            ['type' => 'CORPORATIVO','color' => '#a16207'],
            ['type' => 'ESTANDAR','color' => '#57534e'],
            ['type' => 'VIP','color' => '#ffda83'],
        ]);

        DB::table('service_currencies')->insert([
            ['currency' => 'DOP'],
            ['currency' => 'USD'],
        ]);
    }
}
