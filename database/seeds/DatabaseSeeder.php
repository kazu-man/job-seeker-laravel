<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call([
            UsersTableSeeder::class,
            CompaniesTableSeeder::class,
            ProfilesTableSeeder::class,
            CategoriesTableSeeder::class,
            JobTypesTableSeeder::class,
            CountriesTableSeeder::class,
            AppliesTableSeeder::class,
            // JobsTableSeeder::class,
            // ProvincesTableSeeder::class,
            // JobDescriptionsTableSeeder::class,
            // JobsTableSeeder::class,

            ]);
    }
}
