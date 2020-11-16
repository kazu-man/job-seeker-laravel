<?php

namespace App\Http\Controllers;

use App\User;
use App\Model\Job;
use App\Model\Like;
use App\Model\Company;
use App\Model\Message;
use App\Model\Profile;
use App\Model\ApplyRecord;
use Illuminate\Http\Request;
use App\Model\JobDescription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function registerJob(Request $request){

        $this->validate($request,[
            'title' => 'required',
            'salary' => 'required',
            'category' => 'required',
            'city' => 'required',
            'type' => 'required',
            'type' => [function($attribute, $value, $fail){
                if($value < 1 && $value > 3){
                    return $fail('エラーが発生しました。');
                }
            },
            'required',
            ]

        ]);
        $description = new JobDescription();

        $description->description = $request->input('description');
        $description->requirement = $request->input('requirement');
        $description->benefit = $request->input('benefit');
        $description->experience = $request->input('experience');
        $description->job_title = $request->input('title');

        $description->save();


        $newDescriptionId = JobDescription::max('id');
        \Log::info("desc Id = " . $newDescriptionId);

        $job = new Job();
        
        $job->job_title = $request->input('title');
        $job->annual_salary = $request->input('salary');
        $job->job_description_id = $newDescriptionId;
        $job->company_id = $request->input('company');
        $job->category_id = $request->input('category');
        $job->city_id = $request->input('city');
        $job->job_type_id = $request->input('type');


        $job->save();


        return [ "registeredJob" => $job, "registeredDescription" => $description ];
    }


    public function updatePost(Request $request){

        $this->validate($request,[
            'title' => 'required',
            'salary' => 'required',
            'category' => 'required',
            'city' => 'required',
            'type' => 'required',
            'type' => [function($attribute, $value, $fail){
                if($value < 1 && $value > 3){
                    return $fail('エラーが発生しました。');
                }
            },
            'required',
            ]

        ]);

        // \Log::info($request->all());
        $jobId = $request->input('jobId');

        $job = Job::find($jobId);
        $description = JobDescription::find($job->job_description_id);
        \Log::info($job);
        \Log::info($description);

        // $descriptionId = $request->input('descriptionId');

        $description->description = $request->input('description');
        $description->requirement = $request->input('requirement');
        $description->benefit = $request->input('benefit');
        $description->experience = $request->input('experience');
        $description->job_title = $request->input('title');

        $description->save();
        
        $job->job_title = $request->input('title');
        $job->annual_salary = $request->input('salary');
        // $job->job_description_id = $description->id;
        // $job->company_id = $request->input('company');
        $job->category_id = $request->input('category');
        $job->city_id = $request->input('city');
        $job->job_type_id = $request->input('type');

        $job->save();


        $updatedPost = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription')
        ->where('id',$job->id)
        ->first();



        return [ "updatedPost" => $updatedPost ];
    }

    public function getSinglePost($id){
        $result = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription')
        ->where('id',$id)
        ->get();

        return $result;
    }
    public function getPosts(Request $request) {
        
        \Log::info("serchInfo");

        $companyId = $request->input('companyId');
        $keyWord = $request->input('keyWord');
        $cityId = $request->input('cityId');
        $provinceId = $request->input('provinceId');
        $countryId = $request->input('countryId');
        $countryName = $request->input('countryName');
        $jobTypeId = $request->input('jobTypeId');
        $categoryName = $request->input('categoryName');
        $categoryId = $request->input('categoryId');
        $likes = $request->input('likes');
        $applies = $request->input('applies');

        $query = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription');
        
        if($applies){
            $applyJobNo = app()->make('App\Http\Controllers\JobsListController')->getApplyList();

            $user = Auth::User();
            $userId = $user->id;
            $query->with('applyRecords.messages');

            $query->with(['applyRecords' => function ($query) use($userId){
                $query->where('user_id', '=', $userId);
            }]);

            $query->whereIn('id',$applyJobNo);

        }else if($companyId == null || $companyId == ""){
            $query->where('job_status','A');
        }
        
        
        if($likes){
            $likedJobNo = app()->make('App\Http\Controllers\JobsListController')->getLikeList();
            $query->whereIn('id',$likedJobNo);
        }


        if($companyId != null && $companyId != ""){
            $query->where('company_id',$companyId);
        }

        if($categoryName != null && $categoryName != ""){
            \Log::info("category name = " . $categoryName);
            $query->whereHas('category', function ($query) use($categoryName)  {
                return $query->where('category_name', '=', $categoryName);
            });
        }
        if($categoryId != null && $categoryId != ""){
            \Log::info("category Id = " . $categoryId);
            $query->whereHas('category', function ($query) use($categoryId)  {
                return $query->where('id', '=', $categoryId);
            });
        }

        if($cityId != null && $cityId != ""){
            \Log::info("cityId Id = " . $cityId);

            $query->where('city_id',$cityId);
        }
        if($provinceId != null && $provinceId != ""){
            \Log::info("provinceId Id = " . $provinceId);
            $query->whereHas('city.province', function ($query) use($provinceId)  {
                return $query->where('id', '=', $provinceId);
            });
        }
        if($countryId != null && $countryId != ""){
            \Log::info("countryId Id = " . $countryId);

            $query->whereHas('city.province.country', function ($query) use($countryId)  {
                return $query->where('id', '=', $countryId);
            });
        }

        if($jobTypeId != null && $jobTypeId != ""){
            \Log::info("jobTypeId Id = " . $jobTypeId);

            $query->where('job_type_id',$jobTypeId);
        }

        if($countryName != null && $countryName != ""){
            \Log::info("country name = " . $countryName);

            $query->whereHas('city.province.country', function ($query) use($countryName)  {
                return $query->where('country_name', '=', $countryName);
            });
        }

        if($keyWord != null && $keyWord != ""){
            \Log::info("keyWord  = " . $keyWord);
            $query->where(function($query) use($keyWord){
                $query->orWhere('job_title','LIKE','%'.$keyWord.'%')
                ->orWhereHas('company', function ($query) use($keyWord)  {
                    return $query->where('company_name','LIKE','%'.$keyWord.'%');
                })
                ->orWhereHas('jobDescription', function ($query) use($keyWord)  {
                    return $query->where('description','LIKE','%'.$keyWord.'%')
                    ->orWhere('requirement','LIKE','%'.$keyWord.'%')
                    ->orWhere('benefit','LIKE','%'.$keyWord.'%')
                    ->orWhere('experience','LIKE','%'.$keyWord.'%')
                    ->orWhere('job_title','LIKE','%'.$keyWord.'%');
                });            
            });
        }

        $post = $query->get();
        return $post;
    }


    public function closePost(Request $request){
        $targetPostId = $request->input('targetPostId');
        \Log::info($targetPostId);


        $post = Job::find($targetPostId);
        if($post->job_status == "D"){
            $post->job_status = "A";
        }else{
            $post->job_status = "D";
        }

        $post->save();

    }
}
