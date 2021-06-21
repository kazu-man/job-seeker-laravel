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

    public function index() {    

        $user = Auth::User();
        if($user == ''){
            $user = new DefaultUser();
        }
        $param = ['user' => $user];
        return view('jobsList.jobsList',$param);
    }

    public function getLikeList(){

        return \JobListService::getLikeList();

    }

    public function getApplyList(){

        return \JobListService::getApplyList();
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
        
        \JobListService::saveProfile($request);

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

        \JobListService::saveApply($request);

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

        \JobListService::checkMessage($messages);

    }

    public function getMessages($id){

        $messages = Message::where('apply_record_id',$id)->get();
        \JobListService::checkMessage($messages);

        return $messages;
    }
    
    public function sendMessage(Request $request){

        $this->validate($request,[
            'message' => 'required',
        ]);

        \JobListService::sendMessage($request);

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

        \InterviewService::saveInterview($interview,$newMessage,$applyId,$user);

    }

    public function findInterviewToken(Request $request){
        $token = $request->input("token");
        $interview = Interview::where("interview_token",$token)->with('applyRecord.job.company')->first();

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
        
        return $result;
    }

    public function cancelInterview(Request $request){

        \InterviewService::cancelInterview($request);
        
    }
}
