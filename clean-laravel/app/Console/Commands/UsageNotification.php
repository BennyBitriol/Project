<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\sensor_data;
use App\Models\sensor_status;
use App\Models\users;
use GuzzleHttp;

class UsageNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usage:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Usage Notification After Turning off the device';

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
        $sensor_status = sensor_status::where('device_id',10)->where('unit','W')->first(); 
        $sensor_data3 = sensor_data::where('sensor_id',21)->orderBy('id','desc')->get()[3]; 
        $users = users::where('challenge','!=',"0")->get();
    	  foreach($users as $each){
        if($sensor_status->sensor_value < 10 &&$sensor_data3->value > 10){
          $fetch = sensor_data::where('sensor_id',21)->orderBy('id','desc')->get(); 
          $end_time = $fetch[0]->date;
          $value_before = 0;

          foreach($fetch as $key => $each){
              
              if($value_before>$each->value&&$each->value<5){

                $start_time = $each->date;
                break;
                // return "value_before=".$value_before." ".$key." ID = ".$each->id ."Time ". ;
              }
              $value_before = $each->value;
          }
          //return $start_time . " - " . $end_time;
          $start_time_re = date("Y-m-d H",strtotime($start_time));
          $end_time_re = date("Y-m-d H",strtotime($end_time));

          $start_time_h = date("H",strtotime($start_time));
          $end_time_h = date("H",strtotime($end_time));

          $start_unit = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d %H',date) as date2"))->where('sensor_id','=',22)->where('date2','=', $start_time_re)->get()[0]->value;

          $end_unit = DB::table('sensor_data')->select(DB::raw("max(value) as value,strftime('%Y-%m-%d %H',date) as date2"))->where('sensor_id','=' ,22)->where('date2','=', $end_time_re)->get()[0]->value;


          $total_unit = $end_unit-$start_unit;
          $total_cost = round($total_unit*4.4217,2);
          $hour_usage = $end_time_h-$start_time_h;

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
                                "text"=> "สรุปการใช้งานเครื่องปรับอากาศ",
                                "weight"=> "bold",
                                "size"=> "md",
                                "align"=> "center"
                              ],
                              [
                                "type"=> "separator",
                                "margin"=> "lg"
                              ],
                              [
                                "type"=> "box",
                                "layout"=> "vertical",
                                "spacing"=> "sm",
                                "contents"=> [
                                  [
                                    "type"=> "spacer"
                                  ],
                                  [
                                    "type"=> "box",
                                    "layout"=> "horizontal",
                                    "spacing"=> "sm",
                                    "contents"=> [
                                      [
                                        "type"=> "text",
                                        "text"=> "ใช้งานเป็นเวลา",
                                        "align"=> "start"
                                      ],
                                      [
                                        "type"=> "text",
                                        "text"=> $hour_usage." ชั่วโมง",
                                        "align"=> "end"
                                      ]
                                    ],
                                    "flex"=> 0,
                                    "alignItems"=> "center"
                                  ],
                                  [
                                    "type"=> "box",
                                    "layout"=> "horizontal",
                                    "contents"=> [
                                      [
                                        "type"=> "text",
                                        "text"=> "คิดเป็นเงิน",
                                        "align"=> "start"
                                      ],
                                      [
                                        "type"=> "text",
                                        "text"=> $total_cost." บาท",
                                        "align"=> "end"
                                      ]
                                    ]
                                  ]
                                ],
                                "flex"=> 0
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
