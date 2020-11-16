<?php
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        // $table = new User();
        // $date = Carbon::now();
        // $table->user_firstname = 'user_firstname';
        // $table->user_lastname = 'user_lastname';
        // $table->email = 'user_'.random_int(100, 999).'gmail.com';
        // $table->user_birthday = $date;
        // $table->user_profile = 'user_profile';
        // $table->user_status = 'A';
        // $table->user_type = 'user_type';
        // $table->password = 'password';
        
        // $table->save();


        factory(App\User::class, 10)->create();
    }
}
