<?php

namespace App\Http\Controllers;

use App\User;
use App\Model\Job;
use App\Model\Like;
use App\Model\Scout;
use App\Model\Company;
use App\Model\Message;
use App\Model\Profile;
use App\Model\Experience;
use App\Model\ApplyRecord;
use App\Mail\MailController;
use Illuminate\Http\Request;
use App\Model\JobDescription;
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
        \Log::info("Single Apply Record");

        \Log::info($id);
        \Log::info($applyRecord);

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
        
        \Log::info($res);

        
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
            return "message sent";
        });

    }

    public function getNewMessageExistFlg(){
        $user = Auth::User();
        if($user == null){
            return false;
        }
        \Log::info($user);
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
}
