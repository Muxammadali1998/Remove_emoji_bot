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


        if(isset($input['message'])){

        $message = $input['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text']??null;
        $contact = $message['contact']??false;

        $user = Client::where('chat_id',$message['from']['id'])->first();
        if(!$user){
            $user =  Client::create([
                    'chat_id'=>$message['chat']['id'],
                    'nik_name'=>$message['chat']['first_name']
                ]);
        }




        if($contact){

            $user->update(["phone_number" => $contact['phone_number'], 'status'=>3]);

            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Ajoyib! '.$message['chat']['first_name']." endi siz tahrirlangan suratni jo'natishingiz mumkun"
            ]);
        }else{

        if ($text == '/start') {

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

        }elseif ($text != "Ro'yxatdan o'tish" and is_null($user->status)){

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
                'text' => 'Kechirasiz '.$message['chat']['first_name']." botdan to'liq foydalanish uchun avval ro'yhatdan o'ting",
                'reply_markup'=>$replyMarkup
            ]);

        } elseif ($text == "Ro'yxatdan o'tish"){

            $user->update(['status'=>1]);

            $this->telegram->call('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "Ismingizni kiriting",
            ]);

        }else {

           if (empty($text) and !is_null($user->phone_number)){
                $this->telegram->call('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "⏳",
                ]);
            }
            elseif (is_null($user->full_name) and !empty($text)){
                $user->update(['full_name'=>$text,'status'=>2]);
            }elseif (empty($text) and empty($user->full_name)){
               $user->update(['status'=>1]);

               $this->telegram->call('sendMessage', [
                   'chat_id' => $chat_id,
                   'text' => "Ismingizni kiriting",
               ]);
           }

            if (is_null($user->phone_number) and !empty($user->full_name)){

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


            }elseif(!empty($text) and !empty($user->full_name)){

                $this->telegram->call('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "Botga tahrirlangan surat yuboring",
                ]);
            }

        }
    }
        }elseif(isset($input['my_chat_member']) and false){
            $message = $input['my_chat_member'];

            $user = Client::where('chat_id',$message['from']['id'])->first();
            if(!$user){
                $user =  Client::create([
                    'chat_id'=>$message['chat']['id'],
                    'nik_name'=>$message['chat']['first_name']
                ]);
            }
            $this->telegram->call('sendMessage', [
                'chat_id' => $message['chat']['id'],
                'text' => "https://t.me/clear_face_bot",
            ]);
        }


    }
}
