<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use App\Models\LineNoti;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;
use App\Models\Helpler;
use Tymon\JWTAuth\Facades\JWTAuth;
class ChallengeController extends Controller
{
    public function changetarget ($line_id,$value){
    	/// ส่งกลับว่าคุณได้ตั้งค่าไฟเท่านี้บาท
    	$user = users::where('line_id',$line_id)->first();
    	$user->challenge = $value;
    	$user->save();
		$res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
                $response = $res->request('POST', 'multicast', [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
                ],
                'json' => [
                    'to'=> [$line_id],
            		"messages"=> [
						[
						  "type"=> "flex",
						  "altText"=> "Challenge Message",
						  "contents"=> [
							"type"=> "bubble",
							"body"=> [
							  "type"=> "box",
							  "layout"=> "horizontal",
							  "contents"=> [
								[
								  "type"=> "text",
								  "text"=> "เป้าหมายการใช้ไฟเดือนนี้ ".$value." บาท"
								],
							  ]
							]
							]
						  ]
					  ]
			]
			]);
		return redirect('https://lin.ee/qhBKet1');
    }


    public function settarget(){ /// ทุกต้นเดือนรัน

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
		$tenpercent = $lastmonth_total_cost*0.9;
		$twentypercent = $lastmonth_total_cost*0.8;
		$thirtypercent = $lastmonth_total_cost*0.7;
		
		/// ส่ง line ไปถามว่าจะตั้งเท่าไร
		$res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
                $response = $res->request('POST', 'multicast', [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
                ],
                'json' => [
                    'to'=> [$each->line_id],
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
													  "text"=>"เดือนที่แล้วใช้ไฟ".$lastmonth_total_cost." บาท",
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
												"label"=> $lastmonth_total_cost."( 30 %)",
												"uri"=> "https://www.gadgetbuck.com/api/change/".$each->id.$tenpercent
											  ],
											  "color"=> "#5FAAFB",
											  "style"=> "secondary"
											],
											[
											  "type"=> "button",
											  "action"=> [
												"type"=> "uri",
												"label"=> $lastmonth_total_cost."( 20 %)",
												"uri"=> "https://www.gadgetbuck.com/api/change/".$each->id.$twentypercent
											  ],
											  "color"=> "#86A8E7",
											  "style"=> "secondary"
											],
											[
											  "type"=> "button",
											  "action"=> [
												"type"=> "uri",
												"label"=> $lastmonth_total_cost."( 10 %)",
												"uri"=> "https://www.gadgetbuck.com/api/change/".$each->id.$thirtypercent
											  ],
											  "style"=> "secondary",
											  "color"=> "#D16BA5"
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
                ]
                ]);
	}
    }

    public function alert (){

    	$users = users::where('challenge','!=',"0")->get();

    	foreach($users as $each){
    		/// หา unit แรกของเดือน
    		date_default_timezone_set("Asia/Bangkok");
        	$thismonth = date('Y-m', time());
        	$day = date('d', time());
        	$today = date('Y-m-d', time());
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

   			if($now_usage>($average_left*0.5)&&$linenoti50==0){/// วันนี้เดิน 50% แล้วหรือยัง
   				/// ส่งเตือนว่าใช้งานมา 50% แล้ว
   				LineNoti::insert([
				    'desc' => 'usage_exeed_50',
				    'date' => $today,
				    'value' => 1,
				    'user_id' => $each->id,
				]);
   			}else if($now_usage>($average_left*0.8)&&$linenoti80==0){
   				/// ส่งเตือนว่าใช้งานมา 80% แล้ว
   				LineNoti::insert([
				    'desc' => 'usage_exeed_80',
				    'date' => $today,
				    'value' => 1,
				    'user_id' => $each->id,
				]);
   			}else if($now_usage>($average_left)&&$linenoti100==0){
   				/// ส่งเตือนว่าใช้งานมา 100% แล้ว
   				LineNoti::insert([
				    'desc' => 'usage_exeed_100',
				    'date' => $today,
				    'value' => 1,
				    'user_id' => $each->id,
				]);
   			}

    	} 
    	

    	//return $users;

    }
	public function updatechallenge($challenge){
		$input = request()->all();
        $getuserinfo = Helpler::Userinfofromtoken($input['token']);
        $userid = $getuserinfo['sub'];
        $user_id = users::where('line_id',$userid)->first()->id;
		users::where('id',$user_id)->update(['challenge' => $challenge]);;

	}
	public function mainchallenge(){
		{
			$input = request()->all();
			$getuserinfo = Helpler::Userinfofromtoken($input['token']);
			$userid = $getuserinfo['sub'];
			$user_id = users::where('line_id',$userid)->first()->id;
			$users = users::find($user_id);
			/// หา unit แรกของเดือน
			date_default_timezone_set("Asia/Bangkok");
			$thismonth = date('Y-m', time());
			$day = date('d', time());
			$today = date('Y-m-d', time());
			$first_unit_of_month = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as date3"))->where('sensor_id','=' ,15)->where('date3','=',$thismonth)->get();
			$first_unit_of_day = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->where('date2','=',$today)->get();
			$now_unit = DB::table('sensor_data')->select(DB::raw("max(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as date3"))->where('sensor_id','=' ,15)->where('date3','=',$thismonth)->get();
		
			$monthlastday = date("t", time()); // เดือนนี้มีกี่วัน
		
			$month_usage = $now_unit[0]->value-$first_unit_of_month[0]->value; // เดือนนี้ใช้ไปแล้วกี่หน่วย
			$target = $users->challenge;	  // ยอดมาสุดทั้อยากจ่าย
			$max_usage = $target/4.4217; ////หน่วยที่ใช้ได้มากที่สุด
			$average_month_usage = round($max_usage/$monthlastday,2);////ให้ใช้ได้วันละเท่านี้หน่วย
			$month_usage_available = round($max_usage-$month_usage); /// เดือนนี้เหลือให้ใช้ได้กี่หน่วย
			$day_left = $monthlastday-$day; // วันที่เหลือ
			if($day_left <=0){
                $average_left = round($month_usage_available,2);
            }else{
                $average_left = round($month_usage_available/$day_left,2); /// เหลือให้ใช้ได้วันละกี่หน่วย
            }
			$now_usage = round($now_unit[0]->value-$first_unit_of_day[0]->value,2);
			if($average_left <=0){
                $percent = 0;
            }else{
                $percent = round((100/$average_left)*$now_usage,2);
            }
			
		
			$params['now_usage'] = round($now_usage*4.4217,2);
			$params['target'] = round($average_left*4.4217,2);
			$params['challenge_value'] = round($target,0);
			$params['data'] = [
				"chartOptions" => [
					"chart" => [          
						"height"=> 280,              
						"type" => "radialBar"
						],
						"plotOptions"=> [
						  "radialBar"=> [
							"startAngle"=> -135,
							"endAngle"=> 135,
							"hollow"=> [
							  "margin"=> 15,
							  "size"=> "70%"
							],
							"dataLabels"=> [
							  "showOn"=> "always",
							  "name"=> [
								"offsetY"=> -10,
								"show"=> true,
								"color"=> "#FFFFFF",
								"fontSize"=> "13px"
							  ],
							  "value"=> [
								"color"=> "#FFFFFF",
								"fontSize"=> "30px",
								"show"=> true
							  ]
							]
						  ]
						],
						"fill"=> [
						  "type"=> "solid",
						   "colors"=>"#FF8C00"
						],
						"stroke"=> [
						  "lineCap"=> "round",
						],
						"labels"=> ["Today Usage"]
				],
				"series" => [
					$percent
				]
			];
					
			return response($params, 200);
		
		  }
	}
}
