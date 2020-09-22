<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();

        $users = [
          [
            'first_name' => 'avinash',
            'last_name' => 'negi',
            'username'     => 'avinash.negi@123',
            'email'    => 'avinash@mail.com',
            'password' => \Hash::make('123456'),
            'phone_number' => '1234567890'
          ],
          [
            'first_name' => 'vikash',
            'last_name' => 'negi',
            'username'  => 'vikash@test',
            'email' => 'vikash@mail.com',
            'password' => \Hash::make('123456'),
            'phone_number' => '121212121'
          ],
          [
            'first_name' => 'yogesh',
            'last_name' => 'negi',
            'username'  => 'cool_yogesh',
            'email' => 'yogesh@mail.com',
            'password' => \Hash::make('123456'),
            'phone_number' => '1345345345'
          ],
        ];

        foreach ($users as $key => $user) {
          User::create($user);
        }
    }
}
