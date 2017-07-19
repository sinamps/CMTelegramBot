<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use App\unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use App\unreal4u\TelegramAPI\TgLog;

class TestController extends Controller
{
    
	public function start(){
        $api_token = "405294828:AAEK4s19Y_HeXEIbxOjOyedsEIFYC4FeFAs" ;

        $getUpdates = new GetUpdates();
        $tgLog = new TgLog($api_token);

            $response = $tgLog->performApiRequest($getUpdates);

            //$update_id = 0 ;
            foreach($response->data as $updateItem){
                $update_id = $updateItem->update_id ;
                $message = $updateItem->message ;
				//echo $response->data->message->document->file_id ;
				//echo $updateItem->message->document->file_id ;
				echo $message->document->file_id;
				echo "<br/>";
			}

//            $getUpdates->offset = $update_id + 1;

		

    }

}
