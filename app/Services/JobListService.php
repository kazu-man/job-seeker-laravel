<?php

namespace App\Services;


use App\User;
use App\Model\Job;
use App\Model\Like;
use App\Model\Scout;
use App\Model\Company;
use App\Model\Message;
use App\Model\MessageNoticeModel;
use App\Model\Profile;
use App\Model\Experience;
use App\Model\ApplyRecord;
use App\Mail\MailController;
use App\Model\JobDescription;
use App\Model\Interview;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use DB;

class JobListService {

    //プロフィールの更新
    public function saveProfile(Request $request){

        DB::transaction(function () use ($request) {

            $accountName = $request->input('accountName');
            $firstName = $request->input('userFirstname');
            $lastName = $request->input('userLastname');
            $email = $request->input('email');
            $birthday = $request->input('userBirthday');

            $gender = $request->input('gender');
            $experience = $request->input('experience');
            $skill = $request->input('skill');
            $education = $request->input('education');
            $companyName = $request->input('companyName');
            $experiences = $request->input('experiences');


            $user = Auth::User();
            $userType = $user->user_type;
            

            $user->name = $accountName; 
            $user->user_firstname = $firstName; 
            $user->user_lastname = $lastName; 
            $user->email = $email; 
            $user->user_birthday = $birthday; 

            if($userType == 'U'){

                $profile = Profile::where('user_id',$user->id)->first();

                if($profile == null){
                    $profile = new Profile();
                    $profile->user_id = $user->id;
                }

                if($request->resume){
                    \Log::info($request->resume);
                    \Log::info("resume stored");

                    $resumeFile = $request->resume->getClientOriginalName();

                    $profile->resume = $resumeFile != "" ? '/storage/images/resume/' . $user->id . '/' . $resumeFile : $profile->resume;

                    if(config('app.file_system') == "s3"){
                        
                        Storage::putFileAs('/storage/images/resume/'.$user->id, request()->resume, $resumeFile);
                        $profile->resume = Storage::cloud()->url($profile->resume);
        
                    }else if(config('app.file_system') == "local"){
        
                        request()->resume->storeAs('/public/images/resume/' . $user->id . '/',$resumeFile);        
                        
                    }
                    
                    \Log::info("resume stored");
                }
                
                $profile->experience = $experience; 
                $profile->skill = $skill; 
                $profile->gender = $gender; 
                $profile->education = $education; 
                $profile->save();

                if($experiences != null){
                    $experienceArray = json_decode($experiences);

                    foreach($experienceArray as $ex){

                        // 更新
                        if($ex->id != null && $ex->id != ""){

                            $exModel = Experience::where('id',$ex->id)->first();

                            if($ex->category_id == "" || $ex->experience_years == ""){

                                $exModel->delete();

                            }else{

                                $exModel->category_id = $ex->category_id;
                                $exModel->experience_years = $ex->experience_years;
                                $exModel->save();
                            }

                        // 新規
                        }else{
                            if($ex->category_id == "" || $ex->experience_years == ""){
                                continue;
                            }
                            $exModel = new Experience();
                            $exModel->category_id = $ex->category_id;
                            $exModel->profile_id = $profile->id;
                            $exModel->experience_years = $ex->experience_years;

                            $exModel->save();

                        }
                    }
                }

            }else if($userType == 'C'){

                $profile = Company::find($user->company_id);

                $profile->company_name = $companyName; 
                
                if($request->companyLogo){
                    $companyLogoFile = $request->companyLogo->getClientOriginalName();

                    $profile->company_image = $companyLogoFile != "" ? '/storage/images/companyLogos/' . $user->id . '/' . $companyLogoFile : $profile->company_image;
                    \Log::info("company logo stored");

                    if(config('app.file_system') == "s3"){
                        
                        Storage::putFileAs('/storage/images/companyLogos/'. $user->id, request()->companyLogo, $companyLogoFile);
                        $profile->company_image = Storage::cloud()->url($profile->company_image);
        
                    }else if(config('app.file_system') == "local"){
        
                        request()->companyLogo->storeAs('/public/images/companyLogos/' . $user->id . '/',$companyLogoFile);        

                    }                
                }

                $profile->save();
            }

            $user->save();

        });        
    }

    public function getLikeList(){
        $user = Auth::User();
        if($user == null){
            return false;
        }

        $userId = $user->id;
        $likeList = Like::
        where("user_id",$userId)
        ->where("like_status","A")
        ->get();

        $post = array();
        $likedJobNo = array();

        foreach($likeList as $like){
            array_push($likedJobNo,(int) $like->job_id);
        }

        return $likedJobNo;
    }

    public function getApplyList(){

        $user = Auth::User();
        if($user == null){
            return false;
        }
        $userId = $user->id;
        $applyList = ApplyRecord::
        where("user_id",$userId)
        ->where("apply_status","A")
        ->get();

        $post = array();
        $applydJobNo = array();

        foreach($applyList as $apply){
            array_push($applydJobNo,(int) $apply->job_id);
        }

        return $applydJobNo;
    }   

    //applyの更新
    public function saveApply(Request $request){

        DB::transaction(function () use ($request) {

        
            $user = Auth::User();
            $userId = $user->id;
            $postId = $request->input("postId");
            // $companyid = $request->input("companyId");

            $profile = Profile::where('user_id',$userId)->get();

            if(count($profile) < 1){
                return ["status" => "プロフィールを設定した後、応募してください。"];
            }

            $applyRecord = new ApplyRecord();

            $applyRecord->user_id = $userId;
            $applyRecord->job_id = $postId;
            $applyRecord->apply_date = Carbon::now();

            $applyRecord->save();
        });
    }

    public function checkMessage($messages){
        DB::transaction(function () use ($messages) {

            $user = Auth::User();
            foreach($messages as $message){
                if($message->checked == 0 && $message->sent_to == $user->user_type){
                    $message->checked = 1;
                    $message->save();
                }
            }
        });
    }

    //メッセージの更新
    public function sendMessage(Request $request){
            
        DB::transaction(function () use ($request) {

            $applyRecordId = $request->input('applyRecordId');
            $message = $request->input('message');
            $user = Auth::User();
            $userType = $user->user_type;
            
            $applyRecord = ApplyRecord::where('id', $applyRecordId) ->with("user") ->with("job.company") ->first();

            if($userType == "C"){
                $sendTo = "U";
                $to = $applyRecord->user->email;
                $name = $applyRecord->user->name != null ? $applyRecord->user->name : "";
            }else{
                $sendTo = "C";
                $to = $applyRecord->job->company->user->email;
                $name = $applyRecord->job->company->user->name != null ? $applyRecord->job->company->user->name : "";
            }

            $newMessage = new Message();

            $newMessage->apply_record_id = $applyRecordId;
            $newMessage->message = $message;
            $newMessage->sent_to = $sendTo;
            $newMessage->checked = 0;

            $lastMessage = Message::where('apply_record_id', $applyRecordId)
            ->orderBy('created_at', 'desc')->first();

            $oneHourBefore = new Carbon();
            $oneHourBefore->subHours(1);

            //最初の送信もしくは最後の送信が自分あて、もしくは1時間以上立っている場合は、メール通知
            if($lastMessage == null || $lastMessage->created_at->lt($oneHourBefore) || $lastMessage->sent_to == $userType){

                \Log::info("メッセージ送信");
                //メール送信
                $mailName = config('app.name') . " - メッセージを受信しました。";
                $text = '';
                $view = 'mail.messageNotification';
                $data = [
                    'reset_url' => url('/top'),
                    'name' => $name
                ];
                //実際のメールアドレスは登録されないので、全て自分のメールに送る
                $to = config('app.my_temp_address');
                Mail::to($to)
                ->send(new MailController($mailName, $text, $view, $data));  
            }
            $newMessage->save();

            $targetMessage = Message::where("id",$newMessage->id)->with("ApplyRecord.user")->with("ApplyRecord.job.company")->first();
            $notice = new MessageNoticeModel();

            if($userType == "C"){
                $notice->toId = $targetMessage->applyRecord->user->id;
            }else{
                $notice->toId = $targetMessage->applyRecord->job->company->user_id;
            }

            $notice->date = $targetMessage->created_at;
            $notice->applyId = $targetMessage->applyRecord->id;
            //PUSHERに通知
            event(new MessageSent($user, $notice));

            return "message sent";
        });
    }
}