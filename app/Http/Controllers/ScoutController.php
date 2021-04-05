<?php

namespace App\Http\Controllers;

use App\User;
use App\Model\Job;
use App\Model\Like;
use App\Model\Company;
use App\Model\Message;
use App\Model\Profile;
use App\Model\ApplyRecord;
use App\Model\Experience;
use App\Model\Scout;
use App\Model\ScoutJobRelation;
use App\Mail\MailController;
use Illuminate\Http\Request;
use App\Model\JobDescription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ScoutController extends Controller
{

    public function searchUser(Request $request){

        $user = Auth::User();
        $senderId = $user->id;
        $onlyMyScoutFlg = $request->input("onlyMyScoutFlg") == "false" ? false : true;

        $experiences = json_decode($request->input("experiences"));
        $keyWords = json_decode($request->input("keyWords"));

        $query = Profile::with('user.recievedScouts')->with('experiences.category');
        
        //通常検索
        if(!$onlyMyScoutFlg){

            foreach($experiences as $ex){
                if($ex->category_id == ""){
                    continue;
                }
                
                $query->whereHas('experiences', function ($query) use($ex)  {
                    \Log::info(json_encode($ex));
                    return $query->where('category_id', '=', $ex->category_id)
                    ->where('experience_years', '>=', $ex->experience_years);
                });
            }
            
            $query->where(function($query) use($keyWords){

                foreach($keyWords as $keyWord){
                    if($keyWord->value == ""){
                        continue;
                    }
                    $pat = '%' . addcslashes($keyWord->value, '%_\\') . '%';
                    
                    \Log::info($pat);

                    $query->where(function($query) use($pat){
                        $query->where('experience','LIKE', $pat)
                                ->orWhere('skill','LIKE', $pat);
                    });


                }
            });
        //送信者に紐付くデータ
        }else{

            $query->whereHas('user.recievedScouts', function ($query) use($senderId)  {

                return $query->where('sender_id', '=', $senderId);

            });

        }
        $profiles = $query->get();

        $response = array(); 
        
        foreach($profiles as $profile){
            $exRow = "";
            foreach($profile->experiences as $ex){
                $exString = $ex->category->category_name . " " . $ex->experience_years . " years\n";
                $exRow .= $exString;
            }

            $scoutFlg = false;
            $scouts = $profile->user->recievedScouts;

            foreach($scouts as $scout){
                if($scout->sender_id == $senderId){
                    $scoutFlg = true;
                    break;
                }
            }

            $res = [
                "profile_id" => $profile->id,
                "user_id" => $profile->user_id,
                "user_name" => $profile->user->user_firstname . " " . $profile->user->user_lastname,
                "experiences" => $exRow,
                "resume" => $profile->resume != null ? $profile->resume : "",
                "scoutFlg" => $scoutFlg
            ];            
            array_push($response,$res);
        }

        return $response;
    }

    public function getScout(Request $request){

        $user = Auth::User();
        $senderId = $user->id;
        $recieverId = $request->input("user_id");
        $scoutJobList = [];

        $targetScout = Scout::with("sender.company.jobs")
        ->with("jobRelations")
        ->where("sender_id",$senderId)
        ->where("reciever_id", $recieverId)
        ->first();

        if($targetScout == null){

            $registeredJobs = Job::where("company_id",$user->company->id)->where("job_status","A")->get();

            $targetScout = new Scout();
            $targetScout->sender_id = $senderId;
            $targetScout->reciever_id = $recieverId;
            $targetScout->registeredJobs = $registeredJobs;
            $targetScout->scoutJobList = $scoutJobList;

        }else{
            $scoutJobList = [];
            foreach($targetScout->jobRelations as $relation){
                array_push($scoutJobList,$relation->job_id);
            }
            $targetScout->scoutJobList = $scoutJobList;
        }

        return $targetScout;
    }

    public function sendScout(Request $request){
        \Log::info($request->all());

        $senderId = $request->input("sender_id");
        $recieverId = $request->input("reciever_id");
        $scoutMessage = $request->input("scout_message");
        $scoutJobList = $request->input("scoutJobList");

        $targetScout = new Scout();
        $targetScout->scout_date = Carbon::now();
        $targetScout->reciever_id = $recieverId;
        $targetScout->sender_id = $senderId;
        $targetScout->scout_message = $scoutMessage;
        $targetScout->save();

        foreach($scoutJobList as $scoutJob){
            $targetScoutJobRelation = new ScoutJobRelation();
            $targetScoutJobRelation->scout_id = $targetScout->id;
            $targetScoutJobRelation->job_id = $scoutJob;
            $targetScoutJobRelation->save();
        }

        $sender = User::where("id",$senderId)->first();
        $reciever = User::where("id",$recieverId)->first();

        \Log::info("スカウト送信");
        //メール送信
        $mailName = config('app.name') . " - スカウトを受信しました。";
        $text = '';
        $view = 'mail.scoutNotification';
        $data = [
            'reset_url' => url('/top'),
            'sender' => $sender->company->company_name,
            'reciever' => $reciever->name != null ? $reciever->name : $reciever->user_firstname . " " . $reciever->user_lastname,
            'scout_message' => $scoutMessage
        ];
        //実際のメールアドレスは登録されないので、全て自分のメールに送る
        $to = config('app.my_temp_address');
        Mail::to($to)
        ->send(new MailController($mailName, $text, $view, $data));  
        \Log::info("スカウト送信完了");

    }


    public function getScoutsByReciever(){

        $user = Auth::User();
        $recieverId = $user->id;

        $scouts = Scout::with("sender.company.jobs")->where("reciever_id",$recieverId)->get();

        return $scouts;
    }
}
