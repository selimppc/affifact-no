<?php
/**
 * Created by PhpStorm.
 * User: selimreza
 * Date: 4/5/16
 * Time: 5:07 PM
 */

namespace app\Helpers;

use Google_Service_Gmail;
use Google_Service_Gmail_ModifyMessageRequest;
use Google_Client;
use Laravel\Socialite\Facades\Socialite;
use Google_Service_Books;
use Google_Auth_AssertionCredentials;
use Google_Service_Datastore;
use Google_Service_Urlshortener;
use Google_Service_Urlshortener_Url;

use App\SenderEmail;

class GmailSendMessage
{
    /**
     * Send Message.
     *
     * @param  Google_Service_Gmail $service Authorized Gmail API instance.
     * @param  string $userId User's email address. The special value 'me'
     * can be used to indicate the authenticated user.
     * @param  Google_Service_Gmail_Message $message Message to send.
     * @return Google_Service_Gmail_Message sent Message.
     *
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

    public static function sendMessage($host, $port, $from_email,$from_name,$username,$password,$to_email, $subject, $body, $file_name, $reply_to, $reply_to_name, $campaign_id=null, $auth_token, $auth_code=null) {

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
        print "reply to Name : ".$reply_to_name."\n";
        print "auth_token : ".$auth_token."\n";
        print "auth_code : ".$auth_code."\n";
        #exit;


        // Gmail API
        #session_start();
        define('SCOPES', implode(' ', array(
                Google_Service_Gmail::MAIL_GOOGLE_COM,
                Google_Service_Gmail::GMAIL_COMPOSE,
                Google_Service_Gmail::GMAIL_READONLY,
                Google_Service_Gmail::GMAIL_MODIFY,
                //Google_Service_Gmail::GMAIL_SEND,
                "https://www.googleapis.com/auth/gmail.send",
                "https://www.googleapis.com/auth/urlshortener"
            )
        ));

        //Google client
        $client = new Google_Client();
        $client->setAuthConfigFile(public_path().'/apis/api_for_sender_email.json');
        $client->addScope(SCOPES);
        $client->setLoginHint($from_email);
        $client->setAccessType('offline');
        //$client->setApprovalPrompt("force");

        $json_token = $auth_token;

        #print_r($json_token);exit();

        if ($json_token) {
            $client->setAccessToken($json_token);
            // If access token is not valid use refresh token
            if ($client->isAccessTokenExpired()) {
                $refresh_token = $client->getRefreshToken();
                $client->refreshToken($refresh_token);
                // TODO :: we got refresh token : we need to save to db for life time
                #exit($client->getAccessToken());
            }

            // Gmail Service
            $gmail_service = new \Google_Service_Gmail($client);
            $userID = 'me';

            //prepare the mail with PHPMailer
            $mail = new \PHPMailer();
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";

            //supply with your header info, body etc...

            //TODO::Enable SMTP debugging.
            $mail->SMTPDebug = 3;
            $mail->isSMTP();
            $mail->Host = $host; // "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = $username; //"devdhaka405@gmail.com";
            $mail->Password = $password; //"etsb1234";
            $mail->SMTPSecure = "ssl"; // "tls";
            $mail->Port = $port; //587;

            //From email address and name
            $mail->From = $from_email; //"selimppc@gmail.com";
            $mail->FromName = $from_name; //"Selim Reza";

            //TODO::To address and name
            #$mail->addAddress("devdhaka405@gmail.com", "Dev Dhaka 405");
            #$mail->addAddress("tanintjt@gmail.com", "Tanvir Jahan"); //Recipient name is optional
            $mail->addAddress($to_email, $to_email); //Recipient name is optional

            //TODO::Address to which recipient will reply
            $mail->addReplyTo($reply_to, $reply_to_name);

            //TODO::CC and BCC
            #$mail->addCC("selimppc@gmail.com");
            #$mail->addBCC("shajjadhossain81@gmail.com");

            //TODO::Provide file path and name of the attachments
            #$mail->addAttachment("file.txt", "File.txt");
            #$mail->addAttachment("images/profile.png"); //Filename is optional

            //TODO:: configure attachment in laravel
            if (count($file_name) > 0) {
                $size = sizeOf($file_name); //get the count of number of attachments
                for ($i = 0; $i < $size; $i++) {
                    $mail->addAttachment($file_name[$i]);
                }
            }

            //Send HTML or Plain Text email
            $mail->isHTML(true);

            $mail->Subject = $subject;
            $mail->Body = $body; //"<i>Ho ho .. please ignore this one for your safety </i>";
            $mail->AltBody = $body;


            //create the MIME Message
            $mail->preSend();
            $mime = $mail->getSentMIMEMessage();
            $mime = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');

            //create the Gmail Message
            $message = new \Google_Service_Gmail_Message();
            $message->setRaw($mime);
            $message = $gmail_service->users_messages->send($userID, $message);

            //update sender email eq_count
            if ($campaign_id)
                $model = SenderEmail::where('email', $from_email)->where('campaign_id', $campaign_id)->first();
            else
                $model = SenderEmail::where('email', $from_email)->first();

            $model->count = $model->count + 1;
            $model->eq_count = $model->eq_count + 1;
            $model->save();

            return true;
        }


    }








}