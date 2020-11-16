<?php

use App\Model\Job;
use App\Model\City;
use App\Model\Country;
use App\Model\Province;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table = new Country();
        // $table->country_name = 'Japan';
        // $table->save();

        $countries = factory(Country::class, 5)->create();

        
        $countries->each(function($country){//クロージャ内で$countryを使うため、引数に渡している
            
            $provinces = factory(Province::class,5)->create(
                [
                    'country_id' => $country->id                
                ]
            );
            $provinces->each(function($province){//クロージャ内で$threadを使うため、引数に渡している
            
                $cities = factory(City::class,3)->create(
                    [
                        'province_id' => $province->id                
                    ]
                );

                $cities->each(function($city){//クロージャ内で$threadを使うため、引数に渡している
                    $jobs = factory(Job::class,1)->create(
                        [
                            'city_id' => $city->id                
                        ]
                    );
                });

            });

         });
    }
}
