<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::insert($users->toArray());

        $user = User::find(1);
        $user->name = 'loostudy';
        $user->email = 'genius840215@gmail.com';
        $user->password = bcrypt('831109');
        $user->is_admin = true;
        $user->save();
    }
}
