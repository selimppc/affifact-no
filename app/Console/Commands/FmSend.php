<?php

namespace App\Console\Commands;

use App\SendMailFailed;
use App\TmpGarbage;
use Illuminate\Console\Command;

use App\Helpers\SenderEmailCheck;
use App\CentralSettings;
use App\FollowupSubMessage;
use App\FollowupSubMessageAttachment;
use App\PoppedMessageDetail;
use App\SubMessage;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\EmailQueue;
use App\PoppedMessageHeader;
use App\SenderEmail;
use App\Helpers\EmailSend;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\Mailer;


class FmSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fmsend {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Failed Email Send 1';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fm_id = $this->argument('id');
        if(!$fm_id)
            return true;

        DB::beginTransaction();
        try {
            //email queue data
            $values = SendMailFailed::where('id', $fm_id)->first();

            #foreach($fm_data as $values){
            $host = $values->host;
            $port = $values->port;
            $from_email = $values->from_email;
            $from_name = $values->from_name;
            $username = $values->username;
            $password = $values->password;
            $to_email  = $values->to_email;
            $subject = $values->subject;
            $body = $values->body;
            if($values->file_name)
                $file_name = explode(":",$values->file_name);
            else
                $file_name = null;
            $reply_to= $values->reply_to;
            $reply_to_name = $values->reply_to_name;
            $campaign_id = $values->campaign_id;
            $auth_token = $values->auth_token ;
            $auth_code = $values->auth_code;

            // fire email
            $start_time = date('Y-m-d H:i:s');

            if($host == "smtp.gmail.com"){
                $result = GmailSendMessage::sendMessage($host, $port, $from_email,$from_name,$username,$password,$to_email, $subject, $body, $file_name, $reply_to, $reply_to_name, $campaign_id, $auth_token, $auth_code);
            }else{
                $result = EmailSend::reply_email($host, $port, $from_email,$from_name,$username,$password,$to_email, $subject, $body, $file_name, $reply_to, $reply_to_name, $campaign_id);
            }

            #$result = Mailer::send_mail($host, $port, $from_email,$from_name,$username,$password,$to_email, $subject, $body, $file_name, $reply_to);
            $end_time = date('Y-m-d H:i:s');

            if($result === true){
                $tmp = SendMailFailed::find($values->id);
                $tmp->delete();
                print "FM_ID: ".$fm_id." Success ! -> Start Time: ".$start_time."-> End Time: ".$end_time."\n";
            }elseif($result === 'na') {
                // No action 
                print "Email is not allowed to send now.\n";
            }else{
                //Update failed message and add a new filed of try
                $tmp = SendMailFailed::find($values->id);
                $tmp->no_of_try = $tmp->no_of_try + 1;
                $tmp->msg = $result;
                $tmp->save();
                print "FM_ID: ".$fm_id." Failed ! -> Start Time: ".$start_time."-> End Time: ".$end_time."\n";
            }
            DB::commit();
        }catch (Exception $ex){
            DB::rollback();
        }

    }
}
