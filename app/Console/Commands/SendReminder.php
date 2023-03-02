<?php

namespace App\Console\Commands;

use App\Models\ActionItems;
use Illuminate\Console\Command;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:sendreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WA Message to PICs on H-3 Deadline';

    /**
     * Execute the console command.
     *
     * @return int
     */

    
    public function handle()
    {
        $url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL');

        $action_items = ActionItems::join('pics','action_items.id','=','pics.action_id')
        ->join('users','pics.user_id','=','users.id')
        ->select('action_items.*', 'users.name', 'users.phone')
        ->where('action_items.status','todo')->get();

        $report = array();
        $fail = array();
        $sent = 0;

        foreach($action_items as $item){
            $datediff = strtotime($item->due_date) - time();
            $sisa = round($datediff / (60 * 60 * 24)); // selisih dalam hari

            if($sisa == 3){
                $message = "Berikut ini kami sampaikan pengingat terhadap Action Item *"
                    .$item->note->name."* pada tanggal *".date_format(date_create($item->note->date),"d-m-Y").".*" 
                    ."\n\n*What*\n"
                    .$item->what
                    ."\n\n*How*\n" 
                    .$item->how
                    ."\n\n*Dateline ".date_format(date_create($item->due_date),"d-m-Y")."*"
                    // ."\nTerimakasih 🙏🙏🙏"
                    // ."\n*#".Hashids::decode($item->id)[0]."*"
                    // ."\n*#".$item->id."* "
                    ."\n\n_with ♥  Bot_BPSDM_"
                    ;

                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($url.'/send-message', [
                    'number' => $item->phone,
                    'message' => $message,
                ]);
                $res = json_decode($response);

                array_push($report, $res->status);
                if(!$res->status){
                    array_push($fail, $item->name);
                }
                else{
                    $sent++;
                }
            }
            
        }
        if(in_array(false, $report)){
            $status = false;
        }
        else{
            $status = true;
        }

        Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s')." - ".json_encode(['status'=>$status, 'sent'=> $sent,'fail'=>$fail]));

        return Command::SUCCESS;
    }
}
