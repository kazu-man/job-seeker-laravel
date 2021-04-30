<?php

namespace App\Services;
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
use Laravel\Scout\Builder;

class PostService {

    public function saveJob(Request $request){

        DB::transaction(function () use ($request) {
            $description = new JobDescription();
            $job = new Job();
            
            $description->description = $request->input('description');
            $description->requirement = $request->input('requirement');
            $description->benefit = $request->input('benefit');
            $description->experience = $request->input('experience');
            $description->job_title = $request->input('title');
            $video = $request->input('videoFile');
            
            $description->save();

            $newDescriptionId = $description->id;


            $job->job_title = $request->input('title');
            $job->annual_salary = $request->input('salary');
            $job->job_description_id = $newDescriptionId;
            $job->company_id = $request->input('company');
            $job->category_id = $request->input('category');
            $job->city_id = $request->input('city');
            $job->job_type_id = $request->input('type');

            if($video != null){

                $videoPath = $this->videoUpload($video);
                $job->video_url = Storage::cloud()->url($videoPath);
            }

            $job->save();

            $this->saveTag($job->id, $request->input('tag'));

            if($request->input('mapFlg')){
                $this->saveMapInfo($job->id, $request->input('latLng'), $request->input('addressObj'));
            }

            return [ "registeredJob" => $job, "registeredDescription" => $description ];
        });
     }


    public function updatePost(Request $request){

        return DB::transaction(function () use ($request) {

            // \Log::info($request->all());
            $jobId = $request->input('jobId');

            $job = Job::find($jobId);
            $description = JobDescription::find($job->job_description_id);

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

            $video = $request->input('videoFile');

            if($video != null){

                $videoPath = $this->videoUpload($video);
                $job->video_url = Storage::cloud()->url($videoPath);
            }

            $job->save();

            $this->saveTag($job->id, $request->input('tagList'));

            if($request->input('mapFlg')){

                $this->saveMapInfo($job->id, $request->input('latLng'), $request->input('addressObj'));

            }else{

                $this->deleteMap($job->id);

            }

            $video = $request->input('videoFile');

            if($video != null){

                $this->videoUpload($video);
            }
            $updatedPost = Job::with('company')
            ->with('category')
            ->with('city.province.country')
            ->with('jobType')
            ->with('jobDescription')
            ->with('jobTagRelations.tag')
            ->with('address')
            ->where('id',$job->id)
            ->first();

            return $updatedPost;

        });
    }

    public function closePost($post){

        if($post->job_status == "D"){

            $post->job_status = "A";

        }else{

            $post->job_status = "D";

        }

        DB::transaction(function () use ($post) {

            $post->save();

        });
    }

    public function saveTag($jobId ,$tagList){

        DB::transaction(function () use ($jobId ,$tagList) {

            $currentStoredTags = JobTagRelation::where('job_id',$jobId)->delete();

            foreach($tagList as $tag){

                $tagRelation = new JobTagRelation();
                $tagRelation->job_id = $jobId;
                $tagRelation->tag_id = $tag['id'];
                $tagRelation->save();

            }
        });
    }

    public function saveMapInfo($jobId ,$latLng, $addressObj){

        $address = Address::where('job_id',$jobId)->first();

        if($address == null){
            $address = new Address();
        }

        $address->job_id = $jobId;
        $address->address_line_1 = $addressObj['address_line_1'];
        $address->address_line_2 = $addressObj['address_line_2'];
        $address->city = $addressObj['city'];
        $address->state = $addressObj['state'];
        $address->zip_code = $addressObj['zip_code'];
        $address->country = $addressObj['country'];
        $address->lat = $latLng['lat'] != NULL ? $latLng['lat'] : 35.68944;
        $address->lng = $latLng['lng'] != NULL ? $latLng['lng'] : 139.69167;
        
        DB::transaction(function () use ($address) {

            $address->save();

        });
    }


    public function deleteMap($jobId){

        $address = Address::where('job_id',$jobId)->first();

        if($address == null){
            return;
        }

        DB::transaction(function () use ($address) {

            $address->delete();

        });
    }

    public function videoUpload($file){

        $user = Auth::User();
        $userId = $user->id;

        $region = config('filesystems.disks.s3.region');
        $bucket = config('filesystems.disks.s3.bucket');
        $key = config('filesystems.disks.s3.key');
        $secret = config('filesystems.disks.s3.secret');

        $s3Client = S3Client::factory([
            'version' => 'latest',
            'key' => $key,
            'secret' => $secret,
            'region' => $region
        ]);

        $randomStr = str_random(16);
        $fileName = 'mp4_video_' . $userId . '_'. $randomStr .'.mp4';
        $to_path = 'storage/video/mp4/'.$fileName;

        $uploader = new MultipartUploader($s3Client, $file, [
            'bucket' => $bucket,
            'key'    => $to_path,
        ]);

        try {
            $uploader->upload();

        } catch (MultipartUploadException $e) {

            \Log::info($e->getMessage());

        }

        $resultFileName = str_replace('.mp4','.m3u8',str_replace('mp4_video','hls_video',$fileName));
        $resultPath = 'storage/video/hls/' . $userId .'/'. $randomStr . '/' . $resultFileName;

        return $resultPath;

    }

    //スカウトリストの取得
    public function getScoutList() {

        $user = Auth::User();
        $userId = $user->id;
        $scoutId = 0;
        $scout = Scout::where('reciever_id',$userId)->first();

        //スカウトがなければ0で検索
        if($scout != NULL){
            $scoutId = $scout->id;
        }

        $relations = ScoutJobRelation::with('job.company')
        ->with('job.category')
        ->with('job.city.province.country')
        ->with('job.jobType')
        ->with('job.jobDescription')
        ->with('job.jobTagRelations.tag')
        ->with('job.address')
        ->where('scout_id',$scoutId)
        ->paginate(100);

        $data = collect([]);

        //リレーションから必要なjob情報だけを抜き出す
        foreach($relations as $relation){
            $data->add($relation->job);
        }

        //pagingのオブジェクト内dataにセット
        $relations->setCollection($data);
        
        return $relations;
    }

    //アプライリストの取得
    public function getApplyList($query){

        $user = Auth::User();
        $userId = $user->id;

        $applyJobNo = \JobListService::getApplyList();

        $query->with('applyRecords.messages');

        $query->with(['applyRecords' => function ($query) use($userId){
            $query->where('user_id', '=', $userId);
        }]);

        return $query->whereIn('id',$applyJobNo)->paginate(100);
    }

    //likeリストの取得
    public function getLikeList($query){

        $likedJobNo = \JobListService::getLikeList();

        return $query->whereIn('id',$likedJobNo)->paginate(100);
    }

    
    //全文検索でポストを検索・取得
    public function getPosts(Request $request){

        \Log::info("SCOUT SEARCH");

        $pageType = $request->input('pageType');
        $keyWord = $request->input('keyWord');
        $countryName = $request->input('countryName');
        $categoryName = $request->input('categoryName');
        $likes = $request->input('likes');
        $applies = $request->input('applies');
        $tags = $request->input('tagList');
        $companyId = $request->input('companyId');
        $cityId = $request->input('cityId');
        $provinceId = $request->input('provinceId');
        $countryId = $request->input('countryId');
        $jobTypeId = $request->input('jobTypeId');
        $categoryId = $request->input('categoryId');
        $time_start = microtime(true);

        
        \Log::info("keyWord = " . $keyWord);
        $list = Job::search($keyWord);
        $search = true;
        
        if($companyId == null || $companyId == ""){

            $list = $list->where('status','A');

        }else{

            \Log::info("by company");
            $list = $list->where('company_id',$companyId);
        }

    
        if($countryName != null && $countryName != ""){
            \Log::info("countryName = " . $countryName);

            $list = $list->where('country',$countryName);
        }

        if($categoryName != null && $categoryName != ""){
            \Log::info("categoryName = " . $categoryName);
            $list = $list->where('category',$categoryName);
        }

        if($categoryId != null && $categoryId != ""){
            \Log::info("category Id = " . $categoryId);
            $list = $list->where('category_id', $categoryId);
        }

        if($cityId != null && $cityId != ""){
            \Log::info("cityId Id = " . $cityId);
            $list = $list->where('city_id',$cityId);
        }

        if($provinceId != null && $provinceId != ""){
            \Log::info("provinceId Id = " . $provinceId);
            $list = $list->where('province_id', $provinceId);
        }

        if($countryId != null && $countryId != ""){
            \Log::info("countryId Id = " . $countryId);
            $list = $list->where('country_id', $countryId);
        }

        if($jobTypeId != null && $jobTypeId != ""){

            \Log::info("jobTypeId Id = " . $jobTypeId);
            $list = $list->where('job_type_id',$jobTypeId);
        }


        if($tags != null && $tags != ""){

            $tagNames = [];

            foreach($tags as $tag){
                array_push($tagNames,$tag['tag_name']);
                $list = $list->where('tags', $tag['tag_name']);
            }

            \Log::info("tag list　= " . implode(",",$tagNames));
        }

        $list = $list
        ->paginate(100);

        $loaded = $list->load('company')
        ->load('category')
        ->load('city.province.country')
        ->load('jobType')
        ->load('jobDescription')
        ->load('jobTagRelations.tag')
        ->load('address');

        $list->setCollection($loaded);
        
        $time = microtime(true) - $time_start;
        \Log::info($time);
        
        return $list;
    }

}