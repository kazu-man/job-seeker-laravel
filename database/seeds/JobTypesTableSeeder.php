<?php

use App\Model\JobType;
use Illuminate\Database\Seeder;

class JobTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $table = new JobType();
        $table->job_type = 'FullTime';
        $table->save();

        $table = new JobType();
        $table->job_type = 'PartTime';
        $table->save();

        $table = new JobType();
        $table->job_type = 'Fleerance';
        $table->save();
    }
}
