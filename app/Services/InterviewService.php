<?php

namespace App\Services;

use App\Model\ApplyRecord;
use App\Mail\MailController;
use App\Model\Interview;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DB;

class InterviewService {

    //web面接の登録処理
    public function saveInterview($interview,$newMessage,$applyId,$user){

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

    //web面接のキャンセル処理
    public function cancelInterview(Request $request){

        DB::transaction(function () use ($request) {

            $interviewId = $request->input("id");
            $targetInterview = Interview::where("id",$interviewId)->first();

            $targetInterview->delete();
        });
    }

}