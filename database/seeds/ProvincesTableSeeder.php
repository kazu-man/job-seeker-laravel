<?php

use App\Model\Province;
use Illuminate\Database\Seeder;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $table = new Province();
        $table->province_name = 'Tokyo';
        $table->country_id = 1;        
        $table->save();
    }
}
