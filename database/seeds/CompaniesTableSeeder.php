<?php
use App\Model\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table = new Company();
        // $table->company_name = 'TestCompany'.random_int(100, 999);
        // $table->save();
        $companies = factory(Company::class, 10)->create();

        $companies->each(function($company){
            $company->user->company_id = $company->id;
            $company->user->user_type = 'C';
            $company->user->save();
            
         });
  
    }
}
