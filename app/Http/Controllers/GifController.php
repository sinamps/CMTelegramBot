<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendDocument;
use App\unreal4u\TelegramAPI\TgLog;
use Illuminate\Support\Facades\Log;
//$doc_id = "CgADBAADcQEAAuds2FKFdWEbLiPcjgI";
class GifController extends Controller
{
	private $profMessage = <<<PROF
خجالت بکش!
کامنت‌ماینر
http://commentminer.ir
PROF;
	private $gif = "CgADBAADcQEAAuds2FKFdWEbLiPcjgI";
    public function start(){
        $api_token = "413823645:AAGgD4QXbXb1YlfKYJloPlJmLzNgRLada38" ;
        $getUpdates = new GetUpdates();
        $tgLog = new TgLog($api_token);

        while(true)
        {
            usleep(500000) ;
            $response = $tgLog->performApiRequest($getUpdates);
            $update_id = 0 ;
            foreach($response->data as $updateItem){
                $update_id = $updateItem->update_id ;
                $message = $updateItem->message ;
                if($message && $message->text) {
                    try {
                        $cmResponse = $this->sendToCM($message->text);
                        if ($cmResponse && $cmResponse->profanity) {
                            $replyMessage = $this->gif;
                            $chat_id = $message->chat->id;
                            //$this->sendReply($tgLog, $replyMessage, $chat_id, $message->message_id);
							$this->sendReply($tgLog, $replyMessage, $chat_id, $message->message_id);
                        } else if (!$cmResponse) {
                            Log::error("CommentMiner is not accessible");
                        }
                    }
                    catch (\Exception $e) {
                        Log::error($e) ;
                    }
                }
            }
            $getUpdates->offset = $update_id + 1;
        }
    }

    private function sendToCM($text){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.commentminer.ir/inference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"Text\":\"$text\",\"Api_Key\":\"TGBOT176Y\$HNM^J\$poIKB#23\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl)['http_code'] ;
        curl_close($curl);
        if ($err || $response == null || $http_code != 200) {
            throw new \Exception() ;
        } else {
            return json_decode($response);
        }
    }

    /**
     * @param $tgLog
     * @return \App\unreal4u\TelegramAPI\Abstracts\TelegramTypes
     */
    public function sendReply(TgLog $tgLog, $replyMessage, $chat_id, $message_id)
    {
        $sendMessage = new SendDocument();
        $sendMessage->chat_id = $chat_id;
        $sendMessage->document = $replyMessage;
        $sendMessage->reply_to_message_id = $message_id ;
        return $tgLog->performApiRequest($sendMessage);
    }
}
