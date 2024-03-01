<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    private $telegem;

    public function __construct()
    {
        $this->telegram = new Telegram();
    }

    public function hendel(Request $request)
    {
        $input = $request->all();
        $message = $input['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text']??'';
        $contact = $message['contact']??false;
        if($contact){
            $user = Client::where('chat_id',$message['chat']['id'])->first();
            $user->update([
                "phone_number" => $contact['phone_number'],
                "first_name" => $contact['first_name']
            ]);

            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Ajoyib! '.$user->first_name." endi siz tahrirlangan suratni jo'natishingiz mumkun"
            ]);
        }else{

        if ($text == '/start') {
            $user = Client::where('chat_id',$message['from']['id'])->first();
            if(!$user){
                Client::create([
                    'chat_id'=>$message['chat']['id'],
                    'nik_name'=>$message['chat']['first_name']
                ]);
            }

            $buttonLocation = [
                ["Ro'yxatdan o'tish"]
            ];

            $keyboard = [
                'keyboard' => $buttonLocation,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
                'selective' => false
            ];

            $replyMarkup = json_encode($keyboard);

            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Assalomu alaykum '.$message['chat']['first_name']." botdan to'liq foydalanish uchun ro'yhatdan o'ting",
                'reply_markup'=>$replyMarkup
            ]);
        }elseif ($text == "Ro'yxatdan o'tish"){

            $user = Client::where('chat_id',$message['chat']['id'])->first();
            $user->update(['status'=>1]);
            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "Ismingizni kiriting",
            ]);
        } else {
            if (!empty($text)){
                $user = Client::where('chat_id',$message['chat']['id'])->first();
                $user->update(['full_name'=>$text]);
            }

            $requestContactButton = [
                [
                    'text' => 'Telefon raqamini yuborish',
                    'request_contact' => true
                ]
            ];

            $button = [$requestContactButton];

            $keyboard = json_encode([
                'keyboard' => $button,
                'resize_keyboard' => true,

            ]);


            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "Ro'yxatdan otish uchun telefon raqamini tugma yordamida yuboring",
                'reply_markup'=>$keyboard
            ]);
        }
    }


    }
}
