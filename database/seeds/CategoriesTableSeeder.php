<?php
use App\Model\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table = new Category();
        // $table->category_name = 'Engineer';

        // $table->save();
        factory(Category::class, 10)->create();

    }
}
