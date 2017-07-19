<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendDocument;
use App\unreal4u\TelegramAPI\TgLog;
use Illuminate\Support\Facades\Log;

class getIDController extends Controller
{
	public function start(){
        $api_token = "405294828:AAEK4s19Y_HeXEIbxOjOyedsEIFYC4FeFAs" ;

        $getUpdates = new GetUpdates();
        $tgLog = new TgLog($api_token);
		while(true){
            $response = $tgLog->performApiRequest($getUpdates);

            //$update_id = 0 ;
            foreach($response->data as $updateItem){
                $update_id = $updateItem->update_id ;
                $message = $updateItem->message ;
			    $doc_id = $message->document->file_id;
				$chat_id = $message->chat->id;
				$this->sendReply($tgLog, $doc_id, $chat_id, $message->message_id);
				echo "<br/>";
			}

            $getUpdates->offset = $update_id + 1;

		}

    }
	
	
    public function sendReply(TgLog $tgLog, $replyMessage, $chat_id, $message_id)
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chat_id;
        $sendMessage->text = $replyMessage;
        $sendMessage->reply_to_message_id = $message_id ;
        return $tgLog->performApiRequest($sendMessage);
    }
}
