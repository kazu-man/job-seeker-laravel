<?php

use App\Model\JobDescription;
use Illuminate\Database\Seeder;

class JobDescriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $table = new JobDescription();
        $table->description = 'description!!';
        $table->requirement = 'requirement!!';
        $table->benefit = 'benefit!!';
        $table->experience = 'experience!!';
        $table->job_title = 'job_title!!';
        $table->save();
        
    }
}
