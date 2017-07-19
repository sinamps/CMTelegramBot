<?php

namespace App\Http\Controllers;

use App\RequestsModel;
use App\ResponsesModel;
use Illuminate\Http\Request;
use App\unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendDocument;
use App\unreal4u\TelegramAPI\TgLog;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\String_;


class profgifController extends Controller
{
	    private $profMessage = <<<PROF
    دوست عزیز،
خواهشمندیم از عبارات مناسب‌تری استفاده بفرمایید.
باتشکر
کامنت‌ماینر
http://commentminer.ir
PROF;

	
    public function start(){
        $api_token = "413823645:AAGgD4QXbXb1YlfKYJloPlJmLzNgRLada38" ;
        $getUpdates = new GetUpdates();
        $tgLog = new TgLog($api_token);

        while(true)
        {
            usleep(500) ;
            $response = $tgLog->performApiRequest($getUpdates);
            $update_id = 0 ;
            foreach($response->data as $updateItem){
                $update_id = $updateItem->update_id ;
                $message = $updateItem->message ;
//				$chat_id = $message->chat->id;
                if($message && $message->document){
                    $doc_request = new RequestsModel();
                    $doc_request->doc_file_id = $message->document->file_id;
                    $doc_request->chat_id = $message->chat->id;
                    $doc_request->username = $message->from->username;
                    $doc_request->first_name = $message->from->first_name;
                    $doc_request->last_name = $message->from->last_name;
                    $doc_request->user_id = $message->from->id;
                    $doc_request->save();
					$replyMessage = $message->document->file_id;
					$this->sendReply($tgLog, $replyMessage, $message->chat->id, $message->message_id);
				}
				
				if($message && $message->text) {
                    $txt_request = new RequestsModel();
                    $txt_request->text = $message->text;
                    $txt_request->chat_id = $message->chat->id;
                    $txt_request->username = $message->from->username;
                    $txt_request->first_name = $message->from->first_name;
                    $txt_request->last_name = $message->from->last_name;
                    $txt_request->user_id = $message->from->id;
                    $txt_request->save();
                    try {
                        $cmResponse = $this->sendToCM($message->text);
                        if ($cmResponse){
                            $cm_response = new ResponsesModel();

                            if ($cmResponse && $cmResponse->profanity) {
                                $cm_response->profanity = "true";
                            }
                            if ($cmResponse && !$cmResponse->profanity) {
                                $cm_response->profanity = "false";
                            }
                            /*try {
                                if ($cmResponse->incorrectWords && !empty($cmResponse->incorrectWords)) {
                                $text_incorrectWords = implode(" ", $cmResponse->incorrectWords);
                                $cm_response->incorrect_words = $text_incorrectWords;
                                //die($cmResponse->incorrectWords[0]);
                                }
                            }catch(Exception $e){

                            }
                            try {
                                if ($cmResponse->spellError) {
                                    $cm_response->spell_error = "true";
                                } else {
                                    $cm_response->spell_error = "false";
                                }
                            }catch(Exception $e){

                            }
                            //$cm_response->is_duplicated = $cmResponse->is_duplicated;
                            //$cm_response->duplication_reference = $cmResponse->duplication_reference;
                            //$cm_response->topic = $cmResponse->topic;
                            //$cm_response->request_id = $cmResponse->request_id;*/
                            $cm_response->save();
                        } else if(!$cmResponse){
                            Log::error("CommentMiner is not accessible");
                        }
                        if ($cmResponse && $cmResponse->profanity) {
                            //$replyMessage = $this->profMessage;
                            $chat_id = $message->chat->id;
                        } else if (!$cmResponse) {
                            Log::error("CommentMiner is not accessible");
                        }
                    }
                    catch (Exception $e) {
                        Log::error($e) ;
                    }
                    $replyMessage ="متن پیش پردازش شده: "."\n"."توهین آمیز: ".$this->PersianTrueFalse($cm_response->profanity)."\n"."غلط املایی: ".$this->PersianTrueFalse($cm_response->spell_error)."\n".
                        "تکراری: ".$this->PersianTrueFalse($cm_response->is_duplicated)."\n"."مشابهت با: "."\n".
                        "موضوع: "/*.ReturnTopics(null)*/."\n"."https://t.me/commentminer";
                    $this->sendReply($tgLog, $replyMessage, $message->chat->id, $message->message_id);
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
            CURLOPT_POSTFIELDS => "{\"Text\":\"$text\",\"Api_Key\":\"TGBOT176Y\$HNM^J\$poIKB#23\",\"Flags\":\"all\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl)['http_code'];
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
//        die($replyMessage);
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chat_id;
        $sendMessage->text = $replyMessage;
        $sendMessage->reply_to_message_id = $message_id ;
        return $tgLog->performApiRequest($sendMessage);
    }

    /**
     * @return string
     */
    public function PersianTrueFalse($a)
    {
        if ($a == "true") {
            return "بله";
        } elseif($a == "false") {
            return "خیر";
        }else{
            return "";
        }
    }

    public function ReturnTopics($b)
    {
        return "";
    }

}


