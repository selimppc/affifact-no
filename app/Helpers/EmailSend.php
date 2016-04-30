<?php
/**
 * Created by PhpStorm.
 * User: selimets
 * Date: 11/5/15
 * Time: 10:25 AM
 */

namespace App\Helpers;

use App\SenderEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


#use Illuminate\Support\Facades\Queue;
#use Illuminate\Foundation\Console\QueuedJob;
#use GuzzleHttp;
#use GuzzleHttp\Promise\TaskQueue;
#use Illuminate\Support\Facades\Mail;
#use Illuminate\Queue\Queue;
#use Illuminate\Contracts\Mail\MailQueue;

class EmailSend{

    /**
     * @param $host
     * @param $port
     * @param $from_email
     * @param $from_name
     * @param $username
     * @param $password
     * @param $to_email
     * @param $subject
     * @param $body
     * @param $file_name
     * @param $reply_to
     * @param null $reply_to_name
     * @param null $camp_id
     * @return bool|string
     */
    public static function reply_email($host, $port, $from_email, $from_name, $username, $password, $to_email, $subject, $body, $file_name, $reply_to, $reply_to_name = null, $camp_id = null){

        print "Host : ".$host."\n";
        print "Port : ".$port."\n";
        print "from email : ".$from_email."\n";
        print "from name : ".$from_name."\n";
        print "username : ".$username."\n";
        print "password : ".$password."\n";
        print "to email : ".$to_email."\n";
        print "subject : ".$subject."\n";
        print "body : ".$body."\n";
        #$file_name;
        print "reply to : ".$reply_to."\n";
        #exit;

        /*
         * Configure Mail.php // @Overriding  TODO:: not done yet .. configure them all
         */
        Config::set('mail.driver', 'smtp');
        Config::set('mail.host', $host);
        Config::set('mail.port', $port);
        Config::set('mail.from', ['address' => $from_email, 'name' => $from_name]);
        Config::set('mail.encryption', 'ssl');
        Config::set('mail.username', $username);
        Config::set('mail.password', $password);
        Config::set('mail.sendmail', '/usr/sbin/sendmail -bs');
        Config::set('mail.pretend', false);

        if(!$reply_to_name){
            $reply_to_name = 'Real World';
            $reply_to_name_arr = @explode('@', $reply_to);
            $reply_to_name = @$reply_to_name_arr[0];
        }

        // Checkign time limt and eq_count is ok or not.
        $check = EmailSend::se_time_limit_checking($from_email, $camp_id);
        if($check == 'na'){
            return 'na';
        }else {
            // Send email
            try {
                Mail::send('email_template.common', array('body' => $body), function ($message) use ($from_email, $from_name, $to_email, $subject, $file_name, $reply_to, $reply_to_name, $body) {
                    $message->from($from_email, $from_name);
                    $message->to($to_email);
                    $message->subject($subject);
                    $message->replyTo($reply_to, $name = $reply_to_name);
                    $message->setBody($body, 'text/html');

                    //TODO:: configure attachment in laravel
                    if (count($file_name) > 0) {
                        $size = sizeOf($file_name); //get the count of number of attachments
                        for ($i = 0; $i < $size; $i++) {
                            $message->attach($file_name[$i]);
                        }
                    }

                });

                //update sender email eq_count
                if ($camp_id)
                    $model = SenderEmail::where('email', $from_email)->where('campaign_id', $camp_id)->first();
                else
                    $model = SenderEmail::where('email', $from_email)->first();

                $model->count = $model->count + 1;
                $model->eq_count = $model->eq_count + 1;
                $model->save();

                return true;
            } catch (\Exception $e) {
                return $e->getMessage();
                #return false;
            }
        }
    }

    /**
     * @param $host
     * @param $port
     * @param $from_email
     * @param $from_name
     * @param $username
     * @param $password
     * @param $to_email
     * @param $subject
     * @param $body
     * @param $file_name
     * @param $reply_to
     * @param null $reply_to_name
     * @param null $camp_id
     * @return bool
     */
    public static function custom_reply_email($host, $port, $from_email, $from_name, $username, $password, $to_email, $subject, $body, $file_name, $reply_to, $reply_to_name = null, $camp_id = null){



        /*
         * Configure Mail.php // @Overriding  TODO:: not done yet .. configure them all
         */
        Config::set('mail.driver', 'smtp');
        Config::set('mail.host', $host);
        Config::set('mail.port', $port);
        Config::set('mail.from', ['address' => $from_email, 'name' => $from_name]);
        Config::set('mail.encryption', 'ssl');
        Config::set('mail.username', $username);
        Config::set('mail.password', $password);
        Config::set('mail.sendmail', '/usr/sbin/sendmail -bs');
        Config::set('mail.pretend', false);

        if(!$reply_to_name){
            $reply_to_name = 'Real World';
            $reply_to_name_arr = @explode('@', $reply_to);
            $reply_to_name = @$reply_to_name_arr[0];
        }


        // Send email
        try {
            Mail::send('email_template.common', array('body' => $body), function ($message) use ($from_email, $from_name, $to_email, $subject, $file_name, $reply_to, $reply_to_name, $body) {
                $message->from($from_email, $from_name);
                $message->to($to_email);
                $message->subject($subject);
                $message->replyTo($reply_to, $name = $reply_to_name);
                $message->setBody($body, 'text/html');

                //TODO:: configure attachment in laravel
                if (count($file_name) > 0) {
                    $size = sizeOf($file_name); //get the count of number of attachments
                    for ($i = 0; $i < $size; $i++) {
                        $message->attach($file_name[$i]);
                    }
                }

            });

            //update sender email eq_count
            if ($camp_id)
                $model = SenderEmail::where('email', $from_email)->where('campaign_id', $camp_id)->first();
            else
                $model = SenderEmail::where('email', $from_email)->first();

            $model->count = $model->count + 1;
            $model->eq_count = $model->eq_count + 1;
            $model->save();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
            #return false;
        }

    }

    /**
     * @param $sender_email
     * @param null $camp_id
     * @return string
     */
    public static function se_time_limit_checking($sender_email, $camp_id = null){
        if($camp_id) {
            $se = SenderEmail::where('email', '=', $sender_email)
                ->where('campaign_id', $camp_id)
                ->first();
        }
        else
            $se = SenderEmail::where('email', '=', $sender_email)->first();

        $time_limit = $se->time_limit;
        $email_quota = $se->email_quota;
        $eq_count = $se->eq_count;
        $eq_starting_time = $se->eq_starting_time;

        if(!$eq_starting_time){
            $se->eq_starting_time = date("Y-m-d H:i:s");
            $se->eq_count = 0;
            $se->save();
            return 'allowed';
        }

        $now = time();
        //print "EQ TIME: ".$eq_starting_time."\n";
        $tmp_time = strtotime($eq_starting_time) + $time_limit * 60;
        /*print "Now : ".(date("Y-m-d H:i:s"))."\n";
        print "Now : ".strtotime(date("Y-m-d H:i:s"))."\n";
        print "Prev: ".date("Y-m-d H:i:s", $tmp_time)."\n";
        print "Prev: ".$tmp_time."\n";*/
        if(strtotime(date("Y-m-d H:i:s")) > $tmp_time){
            $se->eq_starting_time = date("Y-m-d H:i:s");
            $se->eq_count = 0;
            $se->save();
            return 'allowed';
        }else{
            if($eq_count >= $email_quota){
                return 'na';
            }else{
                #$se->eq_count = $se->eq_count + 1;
                #$se->save();
                return 'allowed';
            }
        }
    }
}