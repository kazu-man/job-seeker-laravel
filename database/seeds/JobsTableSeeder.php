<?php

use App\Model\Job;
use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table = new Job();
        // $table->job_title = 'job_title!!';
        // $table->annual_salary = 'annual_salary!!';
        // $table->job_description_id = 1;
        // $table->company_id =  1;
        // $table->category_id = 1;
        // $table->city_id = 1;
        // $table->job_type_id = 1;
        // $table->save();
        factory(Job::class, 1000)->create();

    }
}
