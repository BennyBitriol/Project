<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\users;
use App\Models\LineNoti;
use App\Models\Helpler;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;

class ChallengeDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challengedaily:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Challenge Daily Notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $users = users::where('challenge','!=',"0")->get();

    	foreach($users as $each){
    		/// หา unit แรกของเดือน
    		date_default_timezone_set("Asia/Bangkok");
      	$thismonth = date('Y-m', time());
      	$day = date('d', time());
      	$today = date('Y-m-d', time());
        $today_time = date('H:i', time());

    		$first_unit_of_month = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as date3"))->where('sensor_id','=' ,15)->where('date3','=',$thismonth)->get();
    		$first_unit_of_day = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->where('date2','=',$today)->get();
    		$now_unit = DB::table('sensor_data')->select(DB::raw("max(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as date3"))->where('sensor_id','=' ,15)->where('date3','=',$thismonth)->get();
    		$monthlastday = date("t", time()); // เดือนนี้มีกี่วัน

    		$month_usage = $now_unit[0]->value-$first_unit_of_month[0]->value; // เดือนนี้ใช้ไปแล้วกี่หน่วย
   			$target = $each->challenge;	  // ยอดมาสุดทั้อยากจ่าย
   			$max_usage = $target/4.4217; ////หน่วยที่ใช้ได้มากที่สุด
   			$average_month_usage = round($max_usage/$monthlastday,2);////ให้ใช้ได้วันละเท่านี้หน่วย
   			$month_usage_available = round($max_usage-$month_usage); /// เดือนนี้เหลือให้ใช้ได้กี่หน่วย
   			$day_left = $monthlastday-$day; // วันที่เหลือ

   			$average_left = round($month_usage_available/$day_left,2); /// เหลือให้ใช้ได้วันละกี่หน่วย
   			$now_usage = round($now_unit[0]->value-$first_unit_of_day[0]->value,2);        

   			$linenoti50 = LineNoti::where('user_id',$each->id)->where('date',$today)
   			->where('desc',"usage_exeed_50")->count();/// ส่งไปแล้วหรือยัง

   			$linenoti80 = LineNoti::where('user_id',$each->id)->where('date',$today)
   			->where('desc',"usage_exeed_80")->count();/// ส่งไปแล้วหรือยัง

   			$linenoti100 = LineNoti::where('user_id',$each->id)->where('date',$today)/// ส่งไปแล้วหรือยัง
   			->where('desc',"usage_exeed_100")->count();/// ส่งไปแล้วหรือยัง

        if($today_time != '00:00'){
          if(($now_usage>($average_left*0.5))&&$linenoti50==0){/// วันนี้เกิน 50% แล้วหรือยัง
            $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
            $response = $res->request('POST', 'push', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
            ],
            'json' => [
                'to'=> $each->line_id,
                'messages'=> [
                  [
                    "type"=> "flex",
                    "altText"=> "Challenge Message",
                    "contents"=> [
                      "type"=> "bubble",
                      "body"=> [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> [
                          [
                            "type"=> "text",
                            "text"=> "วันนี้คุณได้ใช้งานไปแล้ว 50%",
                            "size"=> "lg"
                          ],
                          [
                            'type'=> 'separator',
                            'margin'=> 'lg'
                          ],
                          [
                            "type"=> "text",
                            'margin'=> 'lg',
                            "text"=> "คิดเป็นเงิน ".round($now_usage*4.4217,0)." บาท"
                          ]
                        ]
                      ]
                    ]
                  ] 
                ]
            ]
            ]);
  
             LineNoti::insert([
              'desc' => 'usage_exeed_50',
              'date' => $today,
              'value' => 1,
              'user_id' => $each->id,
            ]);
           }
          
          else if(($now_usage>($average_left*0.8))&&$linenoti80==0){
             /// ส่งเตือนว่าใช้งานมา 80% แล้ว
            $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
            $response = $res->request('POST', 'push', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
            ],
            'json' => [
                'to'=> $each->line_id,
                'messages'=> [
                  [
                    "type"=> "flex",
                    "altText"=> "Challenge Message",
                    "contents"=> [
                      "type"=> "bubble",
                      "body"=> [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> [
                          [
                            "type"=> "text",
                            "text"=> "วันนี้คุณได้ใช้งานไปแล้ว 80%",
                            "size"=> "lg"
                          ],
                          [
                            'type'=> 'separator',
                            'margin'=> 'lg'
                          ],
                          [
                            "type"=> "text",
                            'margin'=> 'lg',
                            "text"=> "คิดเป็นเงิน ".round($now_usage*4.4217,0)." บาท"
                          ]
                        ]
                      ]
                    ]
                  ] 
                ]
            ]
            ]);
  
             LineNoti::insert([
              'desc' => 'usage_exeed_80',
              'date' => $today,
              'value' => 1,
              'user_id' => $each->id,
          ]);
           }else if(($now_usage>($average_left))&&$linenoti100==0){
             /// ส่งเตือนว่าใช้งานมา 100% แล้ว
            $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
            $response = $res->request('POST', 'push', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
            ],
            'json' => [
                'to'=> $each->line_id,
                'messages'=> [
                  [
                    "type"=> "flex",
                    "altText"=> "Challenge Message",
                    "contents"=> [
                      "type"=> "bubble",
                      "body"=> [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> [
                          [
                            "type"=> "text",
                            "text"=> "วันนี้คุณได้ใช้งานไปแล้ว 100%",
                            "size"=> "lg"
                          ],
                          [
                            'type'=> 'separator',
                            'margin'=> 'lg'
                          ],
                          [
                            "type"=> "text",
                            'margin'=> 'lg',
                            "text"=> "คิดเป็นเงิน ".round($now_usage*4.4217,0)." บาท"
                          ]
                        ]
                      ]
                    ]
                  ] 
                ]
            ]
            ]);
  
             LineNoti::insert([
              'desc' => 'usage_exeed_100',
              'date' => $today,
              'value' => 1,
              'user_id' => $each->id,
              
            ]);
           }
        }
    	} 
    }
}
