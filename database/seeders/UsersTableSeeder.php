<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'      => 'Автор не известен',
                'email'     => 'author_unknown@g.g',
                'password'  => bcrypt (str::random(16)), 
            ],
            [
                'name'      => 'Автор',
                'email'     => 'authorl@g.g',
                'password'  => bcrypt ('123123'),
            ],
        ];

        \DB::table('users')->insert($data);
    }
}
