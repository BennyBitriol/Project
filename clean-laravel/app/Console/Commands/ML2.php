<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp;
use App\Models\sensor_status;
use App\Models\users;
class ML2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ML2:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Machine Learning No.2';

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
        date_default_timezone_set("Asia/Bangkok");
        
        //เช็คอุปกรณ์เปิดหรือปิด
        $air_power = sensor_status::find(21)->sensor_value;
        $users = users::where('challenge','!=',"0")->get();
    	  foreach($users as $each){

        if($air_power>20){ /// คือเปิดอยู่
            $return = [];
            $temp = sensor_status::find(17)->sensor_value;
            $hour = date("H",time()+60*60);
            $minute = date("i",time()+60*60);
            $weekday = date("N",time()+60*60);

            $hour_sin = sin(  (2*pi()*$hour)/23.0  );
            $hour_cos = cos(  (2*pi()*$hour)/23.0  );

            $minute_sin = sin(  (2*pi()*$minute)/30.0  );
            $minute_cos = cos(  (2*pi()*$minute)/30.0  );

            $weekday_sin = sin(  (2*pi()*$weekday)/6.0  );
            $weekday_cos = cos(  (2*pi()*$weekday)/6.0  );

            $return[0]['date'] = "00";
            $return[0]['temp'] = $temp;
            $return[0]['hour_sin']  = $hour_sin;
            $return[0]['hour_cos']  = $hour_cos;
            $return[0]['minute_sin']  = $minute_sin;
            $return[0]['minute_cos']  = $minute_cos;
            $return[0]['weekday_sin']  = $weekday_sin;
            $return[0]['weekday_cos']  = $weekday_cos;

            $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
            
            $res = $client->post('/flask/modeltwo', ['json'=>$return]);
            $pred = $res->getBody();
            $prediction = (json_decode($pred)->prediction);

            $predict = $prediction[0];
            $time = $hour.":".$minute;

            if($predict == 0){
            $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
            $response = $res->request('POST', 'push', [
            'headers' => [
                'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
            ],
            'json' => [
                "to"=> $each->line_id,
                "messages"=> [
                   [
                "type"=> "flex",
                "altText"=> "ดูเหมือนอีก 1 ชั่วโมงคุณจะไม่ใช้แอร์แล้ว",
                "contents"=> [
                  "type"=> "bubble",
                "hero"=> [
                  "type"=> "image",
                  "url"=> "https://images-ext-2.discordapp.net/external/lI1A4lZddwepinYdaxjN5w6zEUqz5FhzjZG2jTWcklY/https/s.isanook.com/he/0/ud/1/7041/air.jpg",
                  "size"=> "full",
                  "aspectRatio"=> "20:15",
                  "aspectMode"=> "cover",
                  "backgroundColor"=> "#000000"
                ],
                "body"=> [
                  "type"=> "box",
                  "layout"=> "vertical",
                  "contents"=> [
                    [
                      "type"=> "text",
                      "text"=> "ดูเหมือนอีก 1 ชั่วโมงคุณจะไม่ใช้แอร์แล้ว",
                      "size"=> "sm",
                      "align"=> "center"
                    ],
                    [
                      "type"=> "text",
                      "text"=> "คุณต้องการจะปิดเลยหรือไม่ ?",
                      "align"=> "center",
                      "size"=> "lg"
                    ]
                  ]
                ],
                "footer"=> [
                  "type"=> "box",
                  "layout"=> "vertical",
                  "contents"=> [
                    [
                      "type"=> "button",
                      "action"=> [
                        "type"=> "uri",
                        "label"=> "ใช่",
                        "uri"=> "https://gadgetbuck.com/api/devicelist/line/change/8/false"
                      ],
                      "style"=> "primary",
                      "offsetStart"=> "none",
                      "margin"=> "none"
                    ]
                  ]
                ]
                  
                ]
              ]
                ]
            ]
            ]);
            }
        }
      }
    }
    
}
