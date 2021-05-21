<?php

namespace App\Http\Controllers;

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


class DefaultUser {
    public $userType = 'NoLogin';
}

class JobsListController extends Controller
{
    //

    public function index() {        // phpinfo();

        $user = Auth::User();
        if($user == ''){
            $user = new DefaultUser();
        }
        $param = ['user' => $user];
        return view('jobsList.jobsList',$param);
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


    public function updateProfile(Request $request) {

        $this->validate($request,[
            'email' => 'email',
            'userBirthday' => 'date|nullable',
            'gender' => [function($attribute, $value, $fail){
                if($value != 'Male' && $value != 'Female'){
                    return $fail('エラーが発生しました。');
                }
            },
            'nullable'
            ]
        ]);
        
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

    public function getProfile() {

        $user = Auth::User();
        $profile = "";
        if($user->user_type == 'U'){
            $profile = Profile::where('user_id',$user->id)
            ->with('experiences')
            ->first();
            $profile["profileType"] = "U";
        }else if($user->user_type == 'C'){
            $profile = Company::find($user->company_id);
            $profile["profileType"] = "C";
        }
        // \Log::info($profile);

        return $profile;
    }

    public function addLike(Request $request) {

        $user = Auth::User();
        $userId = $user->id;
        $jobId = $request->input("jobId");

        $targetLike = Like::where('user_id',$userId)->where('job_id',$jobId)->first();

        if($targetLike == null){
            $targetLike = new Like();
            $targetLike->user_id = $userId; 
            $targetLike->job_id = $jobId; 
        }else{
            $targetLike->like_status = "A";   
        }

        $targetLike->save();
        
        \Log::info("like saved");

    }


    public function removeLike(Request $request) {
        $user = Auth::User();
        $userId = $user->id;
        $jobId = $request->input("jobId");
        $targetLike = Like::where('user_id',$userId)->where('job_id',$jobId)->first();
        $targetLike->like_status = "D";

        $targetLike->save();
    }


    public function apply(Request $request){
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
            // $applyRecord->company_id = $companyid;
            $applyRecord->apply_date = Carbon::now();

            $applyRecord->save();
        });
    
    }

    public function getApplyRecords(){
        $user = Auth::User();
        $companyId = $user->company->id;

        $applyRecords = ApplyRecord::with('user.profile')->with('job')->with('messages')->whereHas('job', function ($query) use($companyId)  {
            return $query->where('company_id', '=', $companyId);
        })->get();

        $response = array(); 
        
        foreach($applyRecords as $apply){
            $newMessageFlg = false;
            foreach($apply->messages as $message){
                if($message->checked == 0 && $message->sent_to == 'C'){
                    $newMessageFlg = true;
                    break;
                }
            }

            $res = [
                "id" => $apply->id,
                "jobId" => $apply->job->id,
                "jobTitle" => $apply->job->job_title,
                "applyUserId" =>$apply->user->id,
                "companyId" => $apply->job->company->id,
                "applicantName" => $apply->user->user_lastname . " " .$apply->user->user_firstname,
                "profileId" => $apply->user->profile != null ? $apply->user->profile->id : 1,
                "applyDate" => date_create($apply->apply_date)->format("yy-m-d"),
                "companyLogo" => $apply->job->company->company_image,
                "companyName" => $apply->job->company->company_name,
                "newMessageFlg" => $newMessageFlg
            ];            
            array_push($response,$res);
        }

        return $response;
    }

    public function getSingleApplyRecord($id){
        $user = Auth::User();

        $applyRecord = ApplyRecord::where("id",$id)->with('user.profile')->with('job')->first();

        $res = [
            "id" => $applyRecord->id,
            "jobId" => $applyRecord->job->id,
            "jobTitle" => $applyRecord->job->job_title,
            "applyUserId" =>$applyRecord->user->id,
            "companyId" => $applyRecord->job->company->id,
            "applicantName" => $applyRecord->user->user_lastname . " " .$applyRecord->user->user_firstname,
            "profileId" => $applyRecord->user->profile != null ? $applyRecord->user->profile->id : 1,
            "applyDate" => date_create($applyRecord->applyRecord_date)->format("yy-m-d"),
            "companyLogo" => $applyRecord->job->company->company_image,
            "companyName" => $applyRecord->job->company->company_name
        ];        
        
        return $res;

    }


    public function getApplicantProfile($id){

        $profile = Profile::where("id",$id)->with('user')->with('experiences.category')->first();

        return $profile;
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

    public function getMessages($id){

        $messages = Message::where('apply_record_id',$id)->get();
        $this->checkMessage($messages);

        return $messages;
    }
    
    public function sendMessage(Request $request){

        $this->validate($request,[
            'message' => 'required',
        ]);

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

    public function getNewMessageExistFlg(){
        $user = Auth::User();
        if($user == null){
            return false;
        }
        $userId = $user->id;
        $userType = $user->user_type;
        $messages = 0;
        if($userType == "U"){
            $messages = \DB::table('apply_records')
                        ->join('messages','apply_records.id','=','messages.apply_record_id')
                        ->where('messages.sent_to',$userType)
                        ->where('messages.checked',0)
                        ->where('apply_records.user_id',$userId)
                        ->count();

        }else if($userType == "C"){
            $companyid = $user->company->id;;

            $messages = \DB::table('apply_records')
            ->join('messages','apply_records.id','=','messages.apply_record_id')
            ->join('jobs','apply_records.job_id','=','jobs.id')
                        ->where('messages.sent_to',$userType)
                        ->where('messages.checked',0)
                        ->where('jobs.company_id',$companyid)
                        ->count();
        }

        if($messages > 0){
            return "true";
        }
        return "false";
    }

    
    //履歴書のダウンロード
    public function resumeDownLoad(Request $request){

        $path = $request->input('resumeFilePath');
        $fileName = $request->input('resumeFile');
        $user = Auth::User();
        $id = strstr($path, '/storage/images/resume/'); 

        if(config('app.file_system') == "s3"){
        $replacedPath = strstr($path, '/storage/images/resume/') ;

        }else if(config('app.file_system') == "local"){

            $replacedPath = str_replace('/storage/',"/public/",$path);
            
        }
        
        $id = str_replace('/' . $fileName,"",str_replace('/storage/images/resume/',"", $id));

        if($user->user_type == "U" && $user->id != $id){
            return response()->json([], 401);
        }

        try{
            $pathToFile = Storage::path($replacedPath);  
            $mimeType = Storage::mimeType($replacedPath);            
            $headers = [['Content-Type' => $mimeType]];
            return Storage::download($replacedPath,$fileName,$headers);

        } catch (\Exception $ex) {
            \Log::info("履歴書のダウンロードに失敗しました");
            \Log::info($ex->getMessage());
            return response()->json([], 503);
        }
    }

    public function setUpInterview(Request $request){
        $date = $request->input('interviewDate');
        $time = $request->input('interviewTime');
        $duration = $request->input('interviewDuration');
        $applyId = $request->input('applyId');
        
        $dateTime = $date . " " . $time;

        $token = str_random(16);
        
        $interview = new Interview();

        $interview->apply_record_id = $applyId;
        $interview->interview_duration = $duration;
        $interview->interview_date_time = $dateTime;
        $interview->interview_token = $token;


        $message = new Message();

        $message = "WEB面接が予約されました。 \n"
                    ."\n"
                    ."開始時間: ".$dateTime ."\n"
                    ."予定時間: ".$duration ."分\n"
                    ."URL: " .config('app.url') . "/video_chat/" .$token;

        $user = Auth::User();
        $userType = $user->user_type;
        

        $sendTo = "U";

        $newMessage = new Message();

        $newMessage->apply_record_id = $applyId;
        $newMessage->message = $message;
        $newMessage->sent_to = $sendTo;
        $newMessage->checked = 0;

        DB::transaction(function () use ($interview,$newMessage,$applyId,$user) {
            $interview->save();
            $newMessage->save();
            $apply = ApplyRecord::where("id",$applyId)->with("user")->first();

            $userName = $apply->user->email;
                
            if($apply->user->name != null){
                
                $userName = $apply->user->name;
                
            }
            
            if($apply->user->user_firstname != null && $apply->user->user_lastname != null){
                
                $userName = $apply->user->user_firstname . " " . $apply->user->user_lastname;
                
            }

            \Log::info($userName);
            //メール送信
            $mailName = config('app.name') . " - Web面接予約のお知らせ";
            $text = '';
            $view = 'mail.interviewNotification';
            $data = [
                'reset_url' => url('/top'),
                'name' => $userName,
                'company_name' => $user->company->company_name,
                'dateTime' => $interview->interview_date_time,
                'duration' => $interview->interview_duration
            ];
            //実際のメールアドレスは登録されないので、全て自分のメールに送る
            $to = config('app.my_temp_address');
            Mail::to($to)
            ->send(new MailController($mailName, $text, $view, $data));  
        });




    }

    public function findInterviewToken(Request $request){
        $token = $request->input("token");
        $interview = Interview::where("interview_token",$token)->with('applyRecord.job.company')->first();
        \Log::info($interview);

        $tokenFindFlg = 0;
        $result = [
            "tokenFindFlg" => 0,
            "targetId" => null,
        ];

        if(!empty($interview)){

            $tokenFindFlg = 1;
            $result["tokenFindFlg"] = $tokenFindFlg;

        }else{

            $result["tokenFindFlg"] = $tokenFindFlg;
            return $result;
        }

        $user = Auth::User();
        $loginUserId = $user->id;
        $correctUserFlg = false;

        if($user->user_type == "C"){

            $result["targetId"] = $interview->applyRecord->user_id;
            $correctUserFlg = $interview->applyRecord->job->company->user_id == $loginUserId ? true : false;

        }else{
            
            $result["targetId"] = $interview->applyRecord->job->company->user_id;
            $correctUserFlg = $interview->applyRecord->user_id == $loginUserId ? true : false;

        }

        if(!$correctUserFlg){
            $result["tokenFindFlg"] = 0;
        }

        
        return $result;
    }
    
    public function getInterview(){

        $result = [];
        $user = Auth::User();
        $userId = $user->id;
        $query = Interview::with('applyRecord.job.company')->with('applyRecord.user');

        if($user->user_type == "C"){
            $query->whereHas('applyRecord.job.company', function ($query) use($userId)  {
                return $query->where('user_id', '=', $userId);
            });

        }else{
            $query->whereHas('applyRecord', function ($query) use($userId)  {
                return $query->where('user_id', '=', $userId);
            });

        }
        
        
        $interview = $query->get();
        
        foreach($interview as $each){

            if($user->user_type == "C"){
                
                $userName = $each->applyRecord->user->email;
                
                if($each->applyRecord->user->name != null){
                    
                    $userName = $each->applyRecord->user->name;
                    
                }
                
                if($each->applyRecord->user->user_firstname != null && $each->applyRecord->user->user_lastname != null){
                    
                    $userName = $each->applyRecord->user->user_firstname . " " . $each->applyRecord->user->user_lastname;
                    
                }
                
                $eachRow = [
                    "id" => $each->id,
                    "title" => $userName,
                    "date" => str_replace('/',"-",$each->interview_date_time),
                    "data" => ["video_url" => config('app.url') . "/video_chat/" .$each->interview_token,
                                "video_duration" => $each->interview_duration,
                                "user_type" => "C"
                                ]
                    
                ];
                
            }else{

                $userName = $each->applyRecord->job->company->company_name;
                                
                $eachRow = [
                    "id" => $each->id,
                    "title" => $userName,
                    "date" => str_replace('/',"-",$each->interview_date_time),
                    "data" => ["video_url" => config('app.url') . "/video_chat/" .$each->interview_token,
                                "video_duration" => $each->interview_duration,
                                "user_type" => "U"
                                ]
                    
                ];

            }


            array_push($result,$eachRow);
        }
        
        \Log::info($result);
        return $result;
    }

    public function cancelInterview(Request $request){
        
        $interviewId = $request->input("id");
        $targetInterview = Interview::where("id",$interviewId)->first();

        $targetInterview->delete();

    }
}
