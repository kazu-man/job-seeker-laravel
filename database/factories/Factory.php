<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Model\Job;
use App\Model\City;
use App\Model\Company;
use App\Model\Country;
use App\Model\Message;
use App\Model\Profile;
use App\Model\Tag;
use App\Model\TagType;

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
        'user_firstname' => $faker->firstName, 
        'user_lastname' => $faker->name, 
        'user_birthday' => $faker->date, 
        'user_profile' => 'password', 
        'user_status' => 'A', 
        'user_type' => 'U', 
        'company_id' => null, 
        'remember_token' => Str::random(10),
    ];
});



$factory->define(Profile::class, function (Faker $faker) {
    return [
        //
        'user_id' => factory(App\User::class)->create()->id, 
        'experience' => $faker->sentence, 
        'skill' => $faker->sentence, 
        'gender' => 'Male', 
        'education' => $faker->sentence, 

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
        'company_name' => $faker->name, 
        'company_image' => $companyImages[random_int(0,6)], 
        'company_status' => 'A',
        'user_id' => factory(User::class)->create()->id 
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
        'country_name' => $faker->country, 
        'country_status' => 'A', 
        'country_image' => $countryImages[random_int(0,6)], 
    ];
});

$factory->define(Province::class, function (Faker $faker) {
    return [
        'province_name' => $faker->country, 
        'country_id' => 1, 
        'province_status' => 'A', 
    ];
});
$factory->define(City::class, function (Faker $faker) {
    return [
        'city_name' => $faker->country, 
        'province_id' => 1, 
        'city_status' => 'A', 
    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'category_name' => $faker->jobTitle, 
        'category_status' => 'A', 

    ];
});

$factory->define(Job::class, function (Faker $faker) {
    return [
        'job_title' => $faker->jobTitle, 
        'annual_salary' => $faker->numberBetween(1,999999), 
        'job_description_id' => factory(JobDescription::class)->create()->id, 
        'company_id' => $faker->numberBetween(1,10), 
        'category_id' => $faker->numberBetween(1,10), 
        'city_id' => $faker->numberBetween(1,10), 
        'job_type_id' => $faker->numberBetween(1,3), 
        'job_created_date' => $faker->dateTime, 
        'job_status' => 'A', 
    ];
});

$factory->define(JobDescription::class, function (Faker $faker) {
    return [
        'description' => $faker->paragraph, 
        'requirement' => $faker->paragraph, 
        'benefit' => $faker->paragraph, 
        'experience' => $faker->paragraph, 
        'job_title' => $faker->jobTitle, 
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
        'user_id' => $userId, 
        'job_id' => $jobId, 
        'apply_status' => 'A', 
    ];
});


$factory->define(Message::class, function (Faker $faker) {
    return [
        'message' => $faker->paragraph, 
        'checked' => 0, 
        'sent_to' => random_int(0,1) == 1 ? 'U' : 'C', 
        'apply_record_id' => 1, 
    ];
});

$factory->define(TagType::class, function (Faker $faker) {
    return [
        'type_name' => $faker->colorName, 
    ];
});

$factory->define(Tag::class, function (Faker $faker) {
    $tagTypeLength = count(TagType::all());
    return [
        'tag_name' => $faker->colorName, 
        'tag_type_id' => random_int(1,$tagTypeLength), 
    ];
});
