<?php

use Illuminate\Database\Seeder;
use App\Model\TagType;

class TagTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TagType::class, 5)->create();

    }
}
