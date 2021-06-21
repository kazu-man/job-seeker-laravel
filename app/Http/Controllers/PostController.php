<?php

namespace App\Http\Controllers;

use App\User;
use App\Model\Job;
use App\Model\Like;
use App\Model\Company;
use App\Model\Message;
use App\Model\Profile;
use App\Model\ApplyRecord;
use App\Model\JobTagRelation;
use App\Model\Scout;
use App\Model\ScoutJobRelation;
use App\Model\Address;
use Illuminate\Http\Request;
use App\Model\JobDescription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use DB;

class PostController extends Controller
{
    
    public function registerJob(Request $request){
        
        return \PostService::saveJob($request);
     
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

        $updatedPost = \PostService::updatePost($request);
        return [ "updatedPost" => $updatedPost ];
    }

    public function getSinglePost($id){
        $result = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription')
        ->where('id',$id)
        ->with('jobTagRelations.tag')
        ->with('address')
        ->get();

        return $result;
    }

    public function getPosts(Request $request) {

        $pageType = $request->input('pageType');
        
        if($pageType == "scout"){
            
            $user = Auth::User();
            $userId = $user->id;

            $scouts = $query = Scout::with('jobRelations.job.company')
            ->with('jobRelations.job.category')
            ->with('jobRelations.job.city.province.country')
            ->with('jobRelations.job.jobType')
            ->with('jobRelations.job.jobDescription')
            ->with('jobRelations.job.jobTagRelations.tag')
            ->with('jobRelations.job.address')
            ->where('reciever_id',$userId)
            ->first();

            $resultPosts = [];
            if($scouts != null){
                foreach($scouts["jobRelations"] as $scoutPost){
                    array_push($resultPosts,$scoutPost->job);
                }
            }

            return $resultPosts;

        }
        
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
        $tags = $request->input('tagList');
        
        $query = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription')
        ->with('jobTagRelations.tag')
        ->with('address');

        
        if($applies){
            $user = Auth::User();
            $userId = $user->id;
    
            $applyJobNo = \JobListService::getApplyList();

            $query->with('applyRecords.messages');

            $query->with(['applyRecords' => function ($query) use($userId){
                $query->where('user_id', '=', $userId);
            }]);

            $query->whereIn('id',$applyJobNo);

        }else if($companyId == null || $companyId == ""){
            $query->where('job_status','A');
        }
        
        
        if($likes){
            $likedJobNo = \JobListService::getLikeList();
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

        if($tags != null && $tags != ""){

            $tagIds = [];
            $tagNames = [];

            foreach($tags as $tag){
                array_push($tagIds,$tag['id']);
                array_push($tagNames,$tag['tag_name']);
            }

            \Log::info("tag list　= " . implode(",",$tagNames));

            $query->whereHas('jobTagRelations.tag', function ($query) use($tagIds)  {
                return $query->whereIn('id', $tagIds);
            });
        }

        $post = $query->get();
        \Log::info($post);
        return $post;
    }


    public function closePost(Request $request){
        $targetPostId = $request->input('targetPostId');
        $post = Job::find($targetPostId);

        \PostService::closePost($post);
    }

}




