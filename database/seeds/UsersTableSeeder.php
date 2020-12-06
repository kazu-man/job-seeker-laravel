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

        $table = new User();
        $date = Carbon::now();
        $table->user_firstname = 'admin';
        $table->user_lastname = 'admin';
        $table->email = 'admin@gmail.com';
        $table->user_birthday = $date;
        // $table->user_profile = 'user_profile';
        $table->user_status = 'A';
        $table->user_type = 'A';
        $table->password = Hash::make('password');
        
        $table->save();


        factory(App\User::class, 10)->create();
    }
}
