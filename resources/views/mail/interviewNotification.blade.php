<!DOCTYPE html>
<html lang="ja">
<style>
  #button a:hover {
    background-color: #2a88bd;
    color: #FFFFFF;
  }
</style>
<body>
<p>
{{$name}}　様
</p>
<p>
 JobSeekerをご利用いただき、誠にありがとうございます。<br>
 {{$company_name}}様よりWeb面接が予約されました。
</p>
<p>
    面接開始時間：{{$dateTime}}<br>
    面接予定時間：{{$duration}}
</p>

<p>
  時間になりましたら以下のボタンからログインし、[ApplyRecords]ページ右上のカレンダーより面接を開始して下さい。
</p>
<p id="button" 
    style="width: 200px;
    text-align: center;">
  <a href="{{$reset_url}}" 
    style="    
    padding: 10px 20px;
    display: block;
    border: 1px solid #2a88bd;
    background-color: #FFFFFF;
    color: #2a88bd;
    text-decoration: none;
    box-shadow: 2px 2px 3px #f5deb3;">ログイン</a>
</p>

<p>
※本メールは「JobSeeker」にメールアドレスをご登録いただいた方にお送りしております。 <br>
※本メールに心当たりの無い方は、本メールの破棄をお願いいたします。<br>
※本メールの送信アドレスは送信専用となっております。<br>
　返信メールでのお問い合わせは承りかねますので、あらかじめご了承願います。
</p>
</body>
</html>