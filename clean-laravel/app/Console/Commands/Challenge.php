<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\users;
use App\Models\LineNoti;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;
use App\Models\Helpler;

class Challenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly Challenge';

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
    	/// ค่าไฟเดือนแล้วเท่าไหร่
		date_default_timezone_set("Asia/Bangkok");
		$today = date("Y-m-d", time());
		$month = date("n", strtotime($today)); // เดือนที่ตัดเลข 0 ข้างหน้า
		$year = date("Y", strtotime($today));
		$first_day_month = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
		$lastdayofmonth = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $first_day_month ) ) . "-1 day" ) );
		$lastmonthyear = date("Y-m", strtotime( date( "Y-m-d", strtotime( $first_day_month ) ) . "-1 month" ) );
		$first_lastmonth_unit = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as month_year"))
		->where('sensor_id','=' ,15)->where('month_year',$lastmonthyear)->first();
		$lastdayofmonth_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
		->where('sensor_id','=' ,15)->where('date2',$lastdayofmonth)->orderBy('id', 'DESC')->first()->value;	
		$first_lastmonth_unit = $first_lastmonth_unit->value;
		$lastmonth_total_unit =  $lastdayofmonth_unit - $first_lastmonth_unit;
		$last_month_pf_rate = Helpler::pf_rate($lastmonth_total_unit);
        $lastmonth_total_cost = $last_month_pf_rate*$lastmonth_total_unit;
        $lastmonth_total_cost = round($lastmonth_total_cost,0);	
		
		/// ต้องการประหยัดเท่าไร -10% , -20% , -30%
		$tenpercent =  round($lastmonth_total_cost*0.9,0);	
		$twentypercent =  round($lastmonth_total_cost*0.8,0);	
		$thirtypercent =  round($lastmonth_total_cost*0.7,0);	
		
		/// ส่ง line ไปถามว่าจะตั้งเท่าไร
		$res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
                $response = $res->request('POST', 'push', [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
                ],
                'json' => [
                    'to'=> $each->line_id,
            'messages'=> [
                [
                  'type'=> 'flex',
                  'altText'=> 'Challenge Message',
                  'contents'=> [
                        "type"=> "bubble",
                        "hero"=> [
                            "type"=> "image",
                            "url"=> "https://www.primeum.com/hubfs/Imported_Blog_Media/challenge-commercial.jpg",
                            "size"=> "full",
                            "aspectRatio"=> "20:13",
                            "aspectMode"=> "cover",
                        ],
                        "body"=> [
                            "type"=> "box",
                            "layout"=> "vertical",
                            "contents"=> [
                            [
                                "type"=> "text",
                                "text"=> "Challenge",
                                "weight"=> "bold",
                                "size"=> "xl",
                                "align"=> "center",
                                "color"=> "#000000"
                            ],
                            [
                                "type"=> "box",
                                "layout"=> "vertical",
                                "margin"=> "lg",
                                "spacing"=> "sm",
                                "contents"=> [
                                [
                                    "type"=> "box",
                                    "layout"=> "vertical",
                                    "spacing"=> "sm",
                                    "contents"=> [
                                    [
                                        "type"=> "text",
                                        "text"=> "เดือนที่แล้วใช้ไฟ ".$lastmonth_total_cost." บาท",
                                        "align"=> "center",
                                        "color"=> "#000000"
                                    ],
                                    [
                                        "type"=> "text",
                                        "text"=> "เดือนนี้คุณต้องการจะประหยัดไฟกี่ %",
                                        "wrap"=> true,
                                        "color"=> "#000000",
                                        "size"=> "md",
                                        "align"=> "center"
                                    ]
                                    ]
                                ]
                                ]
                            ]
                            ]
                        ],
                        "footer"=> [
                            "type"=> "box",
                            "layout"=> "vertical",
                            "spacing"=> "sm",
                            "contents"=> [
                            [
                                "type"=> "button",
                                "action"=> [
                                "type"=> "uri",
                                "label"=> $thirtypercent." บาท (30%)",
                                "uri"=> "https://www.gadgetbuck.com/api/challenge/change/".$each->line_id."/".$thirtypercent
                                ],
                                "color"=> "#5FAAFB",
                                "style"=> "secondary"
                            ],
                            [
                                "type"=> "button",
                                "action"=> [
                                "type"=> "uri",
                                "label"=> $twentypercent." บาท (20%)",
                                "uri"=> "https://www.gadgetbuck.com/api/challenge/change/".$each->line_id."/".$twentypercent
                                ],
                                "color"=> "#86A8E7",
                                "style"=> "secondary"
                            ],
                            [
                                "type"=> "button",
                                "action"=> [
                                "type"=> "uri",
                                "label"=> $tenpercent." บาท (10%)",
                                "uri"=> "https://www.gadgetbuck.com/api/challenge/change/".$each->line_id."/".$tenpercent
                                ],
                                "style"=> "secondary",
                                "color"=> "#D16BA5"
                            ],
                            [
                                "type"=> "button",
                                "action"=> [
                                "type"=> "uri",
                                "label"=> $lastmonth_total_cost." บาท (0%)",
                                "uri"=> "https://www.gadgetbuck.com/api/challenge/change/".$each->line_id."/".$lastmonth_total_cost
                                ],
                                "style"=> "secondary",
                                "color"=> "#A9A9A9"
                            ]
                            ],
                            "flex"=> 0
                        ]

                     ]
                  ]
            ]
                ]
                ]);
	}
    }
}
