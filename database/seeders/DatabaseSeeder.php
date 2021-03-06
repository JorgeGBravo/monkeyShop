<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    static $users = [
        'Jorge',
        'Jennifer',
        'Nayara',
        'Luca',
        'Hugo',
        'Caridad',
        'Issac',
        'Eric',
        'Concepcion',
        'Domingo',
        'Javier'
    ];

    static $names = [
        'Tomas',
        'Jennifer',
        'Julian',
        'Dayana',
        'Hugo',
        'Caridad',
        'Elisa',
        'Melisa',
        'Concepcion',
        'Domingo',
        'Alberto'
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */

    public function run()
    {
        foreach (self::$users as $user) {
            DB::table('users')->insert([
                'name' => strtolower($user),
                'surname' => strtolower('Surname'),
                'email' => strtolower($user) . '@gmail.com',
                'password' => Hash::make('password'),
            ]);
        }

        foreach (self::$names as $name) {
            DB::table('clients')->insert([
                'name' => strtolower($name),
                'surname' => strtolower('Surname'),
                'cif' => Str::random(10),
                'idUser' => random_int(1, 10),
                'lastUserWhoModifiedTheField' => random_int(1, 10),
            ]);
        }
    }
}
