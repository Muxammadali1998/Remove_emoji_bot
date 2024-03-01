<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPolling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-polling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offset = 0;

        while (true){
            $updates = $this->getUpdate($offset);
            if(count($updates)>0){
                foreach ($updates as $update){
                    $offset = $update['update_id']+1;
                    (new TelegramController())->hendel(request()->merge($update));
                }
            }
        }
    }

    public function getUpdate($offset): array
    {
        $url = "https://api.telegram.org/bot". config('services.telegram.api_key')."/getupdates?offset={$offset}";
        $response = Http::send('GET',$url);
        return $response->json()['result'];
    }
}
