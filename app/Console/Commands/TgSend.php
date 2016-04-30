<?php

namespace App\Console\Commands;

use App\Helpers\GmailSendMessage;
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


class TgSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgsend {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tmp Garbage Send';

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
        $tg_id = $this->argument('id');


        if(!$tg_id)
            return true;

        DB::beginTransaction();
        try {
            //email queue data
            $values = TmpGarbage::where('id', $tg_id)
                ->first();


            #foreach($tg_tdata as $values){
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
            $reply_to = $values->reply_to;
            $reply_to_name = $values->reply_to_name;
            $campaign_id = $values->campaign_id;
            $auth_token = $values->auth_token;
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
                print "Result : True\n";
                $tmp = TmpGarbage::find($values->id);
                $tmp->delete();
                print "Sent: ".$values->id." Success! Start: ".$start_time." End: ".$end_time."\n";
            }elseif($result === 'na') {
                // No action
                print "Email is not allowed to send now.\n";
            }else
            {
                print "Result : false\n";
                $mailFailed = new SendMailFailed();
                $mailFailed->host = $host;
                $mailFailed->port = $port;
                $mailFailed->sender_email_id = $values->sender_email_id;
                $mailFailed->popped_message_header_id = $values->popped_message_header_id;
                $mailFailed->popped_message_detail_id = $values->popped_message_detail_id;
                $mailFailed->campaign_id = $values->campaign_id;
                $mailFailed->from_email = $from_email;
                $mailFailed->from_name = $from_name;
                $mailFailed->username = $username;
                $mailFailed->password = $password;
                $mailFailed->to_email = $to_email;
                $mailFailed->subject = $subject;
                $mailFailed->body = $body;
                if($file_name)
                    $mailFailed->file_name = implode(":", $file_name);

                $mailFailed->reply_to = $reply_to;
                $mailFailed->reply_to_name = $reply_to_name;
                $mailFailed->start_time = $start_time;
                $mailFailed->end_time = $end_time;
                $mailFailed->msg = $result;
                $mailFailed->auth_token = $values->auth_token;
                $mailFailed->auth_code = $values->auth_code;
                $mailFailed->save();

                $tmp = TmpGarbage::find($values->id);
                $tmp->delete();
                $end_time = date('Y-m-d H:i:s');
                print_r($result);
                print "\nFailed: ".$values->id." Start: ".$start_time." End: ".$end_time."\n";
            }
            DB::commit();
            print "After Commit\n";
            #}

        }catch (Exception $ex){
            print "Exception\n";
            DB::rollback();
        }
    }
}
