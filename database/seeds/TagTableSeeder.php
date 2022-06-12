<?php

use Illuminate\Database\Seeder;
use App\Model\Tag;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(Tag::class, 50)->create();

        // $table = new Tag();
        // $table->tagName = 'Laravel';
        // $table->type = 'IT';        
        // $table->save();

    }
}
