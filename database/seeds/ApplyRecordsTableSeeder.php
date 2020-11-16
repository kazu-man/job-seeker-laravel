<?php

use App\Model\Message;
use App\Model\ApplyRecord;
use Illuminate\Database\Seeder;

class AppliesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $threads = factory(ApplyRecord::class, 50)->create();

        
        $threads->each(function($thread){//クロージャ内で$threadを使うため、引数に渡している
            
            factory(Message::class,10)->create(
                [
                    'apply_record_id' => $thread->id                
                ]
            );
         });
    }
}
