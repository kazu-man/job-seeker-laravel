<?php
use App\Model\City;
use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table = new City();
        // $table->city_name = 'Machida';
        // $table->province_id = 1;
        // $table->save();
        factory(City::class, 10)->create();

    }
}
