<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Model\Job;
use App\Model\City;
use App\Model\Company;
use App\Model\Country;
use App\Model\Message;
use App\Model\Profile;

use App\Model\Category;
use App\Model\Province;
use App\Model\ApplyRecord;
use Illuminate\Support\Str;
use App\Model\JobDescription;
use Faker\Generator as Faker;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'user_firstname' => $faker->firstName, // password
        'user_lastname' => $faker->name, // password
        'user_birthday' => $faker->date, // password
        'user_profile' => 'password', // password
        'user_status' => 'A', // password
        'user_type' => 'U', // password
        'company_id' => null, // password
        'remember_token' => Str::random(10),
    ];
});



$factory->define(Profile::class, function (Faker $faker) {
    return [
        //
        'user_id' => factory(App\User::class)->create()->id, // password
        'experience' => $faker->sentence, // password
        'skill' => $faker->sentence, // password
        'gender' => 'Male', // password
        'education' => $faker->sentence, // password

    ];
});


$factory->define(Company::class, function (Faker $faker) {
    $companyImages = array(
        '/images/japan.jpg',
        '/images/Australia.jpg',
        '/images/cambodia.jpg',
        '/images/ireland.jpg',
        '/images/japan.jpg',
        '/images/USA.jpg',
        '/images/Philippines.jpeg'
    );
    return [
        'company_name' => $faker->name, // password
        'company_image' => $companyImages[random_int(0,6)], // password
        'company_status' => 'A',
        'user_id' => factory(User::class)->create()->id // password
    ];
});

$factory->define(Country::class, function (Faker $faker) {
    
    $countryImages = array(
        '/images/japan.jpg',
        '/images/Australia.jpg',
        '/images/cambodia.jpg',
        '/images/ireland.jpg',
        '/images/japan.jpg',
        '/images/USA.jpg',
        '/images/Philippines.jpeg'
    );

    return [
        'country_name' => $faker->country, // password
        'country_status' => 'A', // password
        'country_image' => $countryImages[random_int(0,6)], // password
    ];
});

$factory->define(Province::class, function (Faker $faker) {
    return [
        'province_name' => $faker->country, // password
        'country_id' => 1, // password
        'province_status' => 'A', // password
    ];
});
$factory->define(City::class, function (Faker $faker) {
    return [
        'city_name' => $faker->country, // password
        'province_id' => 1, // password
        'city_status' => 'A', // password
    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'category_name' => $faker->jobTitle, // password
        'category_status' => 'A', // password

    ];
});

$factory->define(Job::class, function (Faker $faker) {
    return [
        'job_title' => $faker->jobTitle, // password
        'annual_salary' => $faker->numberBetween(1,999999), // password
        'job_description_id' => factory(JobDescription::class)->create()->id, // password
        'company_id' => $faker->numberBetween(1,10), // password
        'category_id' => $faker->numberBetween(1,10), // password
        'city_id' => $faker->numberBetween(1,10), // password
        'job_type_id' => $faker->numberBetween(1,3), // password
        'job_created_date' => $faker->dateTime, // password
        'job_status' => 'A', // password
    ];
});

$factory->define(JobDescription::class, function (Faker $faker) {
    return [
        'description' => $faker->paragraph, // password
        'requirement' => $faker->paragraph, // password
        'benefit' => $faker->paragraph, // password
        'experience' => $faker->paragraph, // password
        'job_title' => $faker->paragraph, // password
    ];
});

$factory->define(ApplyRecord::class, function (Faker $faker) {

    $userId = random_int(11,20);
    $jobId = random_int(1,50);

    // if($target != null){
    //     $doneFlg = true;
    //     while($doneFlg){
    //         \Log::info($userId);
    //         $userId = random_int(11,20);
    //         $jobId = random_int(1,50);
    //         if(ApplyRecord::where('job_id',$jobId)->where('user_id',$userId)->get() == null){
    //             break;
    //         }
    //     }
    // }
    return [
        'user_id' => $userId, // password
        'job_id' => $jobId, // password
        'apply_status' => 'A', // password
    ];
});


$factory->define(Message::class, function (Faker $faker) {
    return [
        'message' => $faker->paragraph, // password
        'checked' => 0, // password
        'sent_to' => random_int(0,1) == 1 ? 'U' : 'C', // password
        'apply_record_id' => 1, // password
    ];
});
