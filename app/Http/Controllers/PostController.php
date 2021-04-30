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
use Laravel\Scout\Builder;

class PostController extends Controller
{
    //新規追加
    public function registerJob(Request $request){
        
        return \PostService::saveJob($request);
     
    }

    // 更新
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

    //単一ポストの取得
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

    //ポストの表示・非表示切り替え
    public function closePost(Request $request){
        $targetPostId = $request->input('targetPostId');
        $post = Job::find($targetPostId);

        \PostService::closePost($post);
    }

    //ポストの取得
    public function getPosts(Request $request){
    
        $pageType = $request->input('pageType');
        
        $likes = $request->input('likes');
        $applies = $request->input('applies');
        $companyId = $request->input('companyId');

        //スカウトリスト
        if($pageType == "scout"){
            \Log::info("scout list");
            return \PostService::getScoutList();

        }

        $query = Job::with('company')
        ->with('category')
        ->with('city.province.country')
        ->with('jobType')
        ->with('jobDescription')
        ->with('jobTagRelations.tag')
        ->with('address');

        //アプライリスト
        if($applies){
            \Log::info("apply list");
            return \PostService::getApplyList($query);

        }
        
        //likeリスト
        if($likes){
            \Log::info("like list");
            return \PostService::getLikeList($query);

        }

        //通常検索
        return \PostService::getPosts($request);
    }
}




