<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\sensor_status;
use App\Models\sensor_data;
use App\Models\devicelist;
use App\Models\roomlist;
use Illuminate\Http\Request;
use App\Models\Helpler;
use App\Models\users;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;   


class EnergyController extends Controller
{
    public function EnergyInfo(){
        $input = request()->all();
        $device_list = devicelist::find($input['id']);
        return $device_list;
    }

    public function RoomUsage($room_id){

        /// Watt ทั้งหมดในห้องตอนนี้
        $roomlist = roomlist::find($room_id);
        $user_id = $roomlist->user_id;
        $devicelist = devicelist::where('location',$room_id)->where('usage',1)->where('type',"device")->where('user_id',9)->get();
        // $count = count($devicelist);
        // if($count == 0){
            $sensor_statuswat_value = 0;
            foreach($devicelist as $each){
                $sensor_status = sensor_status::where('device_id',10)->where('unit','W')->first();
                $sensor_statuswat_value += $sensor_status->sensor_value;
            }
            $usage['watt'] = round($sensor_statuswat_value,2);
            $usage['on'] = 1;
            $sensor_statuswat_value_hour = round(($sensor_statuswat_value/1000)*4.4217,2);
            $usage['cost_h'] = $sensor_statuswat_value_hour;
            $today = date("Y-m-d", time());
            $sum = 0;
            $cost = Helpler::get_cost_by_device_id($today,$today,'day',10);
                foreach($cost as $each3){
                    $sum += $each3['usage'];
                }
            $sum = round($sum*4.4217,2);
            $usage['cost_today'] = $sum;
            
            $most_watt_usage[] = sensor_status::where('device_id',10)->where('unit','W')->orderBy('id','desc')->first();
            $max_wattage = max($most_watt_usage);
            $max_wattage = $max_wattage->sensor_name;
            $usage['item'] = [
                [
                "a"=> 'Device With most consumption',
                "b"=> $max_wattage,
                ]
            ];
            return  $usage;
            }
            // else{
            //     $sensor_statuswat_value = 0;
            //     foreach($devicelist as $each){
            //         $devicelist_id = $each->id;
            //         $sensor_status = sensor_status::where('device_id',$devicelist_id)->where('unit','W')->first();
            //         $sensor_statuswat_value += $sensor_status->sensor_value;
            //     }
            //     $usage['watt'] = round($sensor_statuswat_value,2);
            //     // return $sensor_statuswat_value;
                
            //     /// จำนวนที่เปิดอยู่
            //     $devicelist_on = devicelist::where('location',$room_id)->where('usage',1)->where('switch_status',"true")->count();
            //     $usage['on'] = $devicelist_on;
    
    
            //     /// ค่าไฟกี่บาท/ชั่วโมง
            //     $sensor_statuswat_value_hour = round(($sensor_statuswat_value/1000)*4.4217,2);
            //     $usage['cost_h'] = $sensor_statuswat_value_hour;
    
            //     /// ค่าไฟในห้องวันนี้
            //     $today = date("Y-m-d", time());
            //     $sum = 0;
            //     foreach($devicelist as $each2){
            //         $devicelist_id = $each2->id;
            //         // $sensor_status = sensor_status::where('device_id',$devicelist_id)->where('unit','Unit')->first();
            //         // $sensor_statusunit_id = $sensor_status->device_id;
            //         $cost = Helpler::get_cost_by_device_id($today,$today,'day',$devicelist_id);
            //         foreach($cost as $each3){
            //             $sum += $each3['usage'];
            //         }
            //     }
            //     $sum = round($sum*4.4217,2);
            //     $usage['cost_today'] = $sum;
    
    
            //     //most watt
            //     foreach($devicelist as $each4){
            //         $devicelist_id = $each4->id;
            //         $most_watt_usage[] = sensor_status::where('device_id',$devicelist_id)->where('unit','W')->orderBy('id','desc')->first();
                    
            //     }
            //     $max_wattage = max($most_watt_usage);
            //     $max_wattage = $max_wattage->sensor_name;
            //     $usage['item'] = [
            //         [
            //         "a"=> 'Device With most consumption',
            //         "b"=> $max_wattage,
            //         ],
            //         // [
            //         //   "a"=> 'การใช้งานเทียบกับเมื่อวาน', //เทียบกับเมื่อวาน ณ เวลาเดียวกัน
            //         //   "b"=> '+5%',
            //         // ],
            //         // [
            //         //   "a"=> 'ค่าไฟในห้องวันนี้โดยประมาณ', //AI
            //         //   "b"=> '999 บาท', 
            //         // ]
            //     ];
            //     return  $usage;
            // }
            
            
        // }
        
    
        
    public function costChart(){
        $input = request()->all();
        $selected_date = $input['startdate'];
        $end_selected_date = $input['enddate'];
        $timeframe = $input['timeframe'];
        $id = $input['id'];
        $type = $input['type'];
        

        $x = [];
        $y = [];
        $challenge_line = [];

        

        if($type=="room"){
            $devicelist = devicelist::where('location',$id)->where('usage',1)->where('type',"device")->get();
            if(count($devicelist) > 0){
                $devicelist = devicelist::where('location',$id)->where('usage',1)->where('type',"device")->get();
            }
            else{
                $devicelist = devicelist::where('id',10)->get();
            }
            
        }
        else if($type=="sensor"){
            $dev_id = $input['dev_id'];
            $devicelist = devicelist::where('id',$dev_id)->get();
        }
        else{
            $monthlastday = date("t", time());
            $getuserinfo = Helpler::Userinfofromtoken($input['token']);
            $userid = $getuserinfo['sub'];
            $user_id = users::where('line_id',$userid)->first()->id;
            $user = users::find($user_id);
            $target = $user->challenge;
            $max_usage = $target/$monthlastday; 
            $devicelist = devicelist::where('id',1)->get();
        }

        $xxx = Helpler::get_cost_by_device_id($selected_date,$end_selected_date,$timeframe,1);
        $size = count($xxx);
        $benny = array_fill(0, $size, 0);

        
        
        foreach($devicelist as $key => $each){ /// อุปกรณ์ในห้องทั้งหมด
            $xxx = Helpler::get_cost_by_device_id($selected_date,$end_selected_date,$timeframe,$each->id);
            
            if(count($xxx) > 0){
                $daily_usage_yes = Helpler::get_cost_by_device_id($selected_date,$end_selected_date,$timeframe,$each->id);
                foreach($daily_usage_yes as $key2 => $each2){ //// หลายจุดเวลา
                    $benny[$key2] += $each2['usage'];
                }
            }
        }
        
        foreach( $daily_usage_yes as $key => $each){  
            array_push($x,(int)$each['x']);
            array_push($y,round($benny[$key]*4.4217,2));
            if($timeframe=="month"&&$type=="home"){
                array_push($challenge_line,round($max_usage,0));
            }
            
        }

        if($timeframe=="month"&&$type=="home"){
            $legend = true;
        }else{
            $challenge_line = [];
            $legend = false;
            
        }

        $data = [
        "chartOptions" => [
            "chart" => [
                "toolbar" => [
                    "show" => false,
                ],
                // "type" => "line"
                ],
                "legend"=> [
                    "show"=> $legend,
                    "labels"=>[
                        "colors"=>"#ffffff",
                    ]
                ],
                // "stroke"=>[
                //     "width" => [5, 3],
                //     "dashArray" => [0,8]
                // ],
                "stroke"=> [
                  "curve"=> 'smooth'
                ],
                "fill" => [
                    "type" => 'solid',
                    "colors" => ['#FF8C00','#FFD700']
                ],
                "dataLabels" => [
                    "enabled" => false
                ],
                "colors" => ['#FF8C00'],
                "xaxis" => [
                    "type" =>'category', 
                    "categories"=> $x ,
                    "labels"=>[
                        "style"=>[
                            "colors"=>'#fff'
                        ]
                    ]
                ],
                "yaxis" => [
                    "show" => false
                ],
                "tooltip" => [
                    "x" => [
                      "format" => ""
                    ],
                ],
                "grid" => [
                    "row" => [
                        "colors" => 'transparent',
                        "opacity" => 100
                ]
            ]
        ],
        "series" => [
                [
                    "type" => 'area',
                    "name" => 'Cost (฿)',
                    "data" => $y
                ],
                [
                    "type" => 'line',
                    "color" => '#FFFFFF',
                    "name" => 'Daily Challenge (฿)',
                    "data" => $challenge_line
                ]
            ]
        ];

        return response($data, 200);
    }


    public function EnergyTableReport() ///energyreport/table
    {
        // date_default_timezone_set("Asia/Bangkok");
        $input = request()->all();
        $startdate = $input['startdate'];
        $enddate = $input['enddate'];
        $timeframe= $input['timeframe'];
        $type = $input['type'];
        $id = $input['id'];
        
        if($type=="room"){
            $devicelist = devicelist::where('location',$id)->where('usage',1)->where('type',"device")->get();
            if(count($devicelist) > 0){
                $devicelist = devicelist::where('location',$id)->where('usage',1)->where('type',"device")->get();
            }
            else{
                $devicelist = devicelist::where('id',10)->get();
            }
            $room = roomlist::find($id);
            $room_name = $room->statistic;
            
        }
        else if($type=="sensor"){
            $dev_id = $input['dev_id'];
            $devicelist = devicelist::where('id',$dev_id)->get();
            $room_name = $devicelist[0]->sensor_name;
        }
        else{
            $devicelist = devicelist::where('id',1)->get();
            $room_name = "Home Usage";
        }

        $sum_today = 0;
        $sum_yesterday = 0;
        $sum_yesterday_same = 0;
        $selected_cost = 0;
        $month_total_cost = 0;
        $lastmonth_total_cost = 0;
        $month_average = 0;
        $year_total_cost = 0;
        $this_month_usage = 0;
        $day_total_cost = 0;
        $days_total_cost = 0;
        $yesterday_total_cost = 0;
        $today_compare_selected = 0;
        $today_cost =0;

        $startdate_formatted = date("Y-m-d", strtotime($startdate));
        $enddate_formatted = date("Y-m-d", strtotime($enddate));

        foreach($devicelist as $each){ /// ตัววัดไฟหลายตัวในห้อง
            $sensor_status = sensor_status::where('device_id',$each->id)->where('unit',"Unit")->first();
            $now_unit = $sensor_status->sensor_value;

            if($timeframe == 'day'){
                // ค่าไฟวันนี้
                $daily_usage = Helpler::get_cost_by_device_id($startdate_formatted,$enddate_formatted,$timeframe,$each->id);
                foreach($daily_usage as $each4){
                    $day_total_cost += $each4['usage'];
                }

                /// เมื่อวาน
                $yesterday_start = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $startdate_formatted ) ) . "-1 day" ) );
                $yesterday_end = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $enddate_formatted ) ) . "-1 day" ) );
                $daily_usage = Helpler::get_cost_by_device_id($yesterday_start,$yesterday_end,$timeframe,$each->id);
                foreach($daily_usage as $key => $each5){
                    $yesterday_total_cost += $each5['usage'];
                }

                /// เมื่อวานเวลาเดียวกัน
                /// Today Now compare with selected date

                $today = date("Y-m-d",time());
                $time_now = date("H:i:s", time());
                $selected_date = $startdate_formatted;

                $selected_date_start_time = $selected_date.' '."00:01:00";
                $selected_date_time_now = $selected_date.' '.$time_now;
               
                $today_start_time = $today.' '."00:01:00";
                $today_time_now = $today.' '.$time_now;

            
                $selected_date_unit = DB::table('sensor_data')->select(DB::raw("max(value)-min(value) as unit,strftime('%Y-%m-%d %H:%M:%S',date) as date2"))->where('sensor_id','=' ,$sensor_status->id)->whereBetween('date2',[$selected_date_start_time,$selected_date_time_now])->first();

                $today_unit = DB::table('sensor_data')->select(DB::raw("max(value)-min(value) as unit,strftime('%Y-%m-%d %H:%M:%S',date) as date2"))->where('sensor_id','=' ,$sensor_status->id)->whereBetween('date2',[$today_start_time,$today_time_now])->first();

                if(!isset($today_unit)||!isset($selected_date_unit)){ ///this is error no value
                    $today_compare_selected = 0;
                    $today_cost = 0;
                }else if(isset($today_unit)&&isset($selected_date_unit)){
                    $today_compare_selected += ($today_unit->unit)-($selected_date_unit->unit);
                    $today_cost += $today_unit->unit;
                }

            }else if($timeframe == 'days'){

                $daily_usage = Helpler::get_cost_by_device_id($startdate_formatted,$enddate_formatted,$timeframe,$each->id);
                foreach($daily_usage as $key => $each6){
                    $days_total_cost += $each6['usage'];
                }


            }else if($timeframe == 'month'){
                /// เดือนนี้
                $daily_usage = Helpler::get_cost_by_device_id($startdate_formatted,$enddate_formatted,$timeframe,$each->id);
                foreach($daily_usage as $key => $each2){
                    $month_total_cost += $each2['usage'];
                }
                /// เดือนแล้ว
                $last_month_start = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $startdate_formatted ) ) . "-1 month" ) );
                $last_month_end = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $enddate_formatted ) ) . "-1 month" ) );
                $month_day = date( "t", strtotime( $startdate_formatted ));

                $daily_usage = Helpler::get_cost_by_device_id($last_month_start,$last_month_end,$timeframe,$each->id);
                foreach($daily_usage as $key => $each3){
                    $lastmonth_total_cost += $each3['usage'];
                }

            }else if($timeframe == 'year'){

                $daily_usage = Helpler::get_cost_by_device_id($startdate_formatted,$enddate_formatted,$timeframe,$each->id);
                foreach($daily_usage as $key => $each7){
                     $year_total_cost += $each7['usage'];
                }

            }
        }

        if($sum_yesterday_same>0){
            $change_day =  round((($p1/$p2)-1)*100,2); 
        }else{
            $change_day = 0;
        }

        if($timeframe == 'day'){
        $data = [[
                    [
                        "a" => "Today Cost",
                        "b" => round($today_cost*4.4217,2)." ฿"
                    ],
                    [
                        "a" => "Selected Date Cost",
                        "b" => round($day_total_cost*4.4217,2)." ฿"
                    ],
                    [
                        "a" => "Today Compare Selected",
                        "b" => round($today_compare_selected*4.4217,2)." ฿"
                    ],
                    [
                        "a" => "Day Before",
                        "b" => round($yesterday_total_cost*4.4217,2)." ฿"
                    ],
                    
                    // [
                    //     "a" => "% เปลี่ยนแปลง",
                    //     "b" => $change_day." %"
                    // ],
                ],
                [

                    "name" => $room_name
                ]];

        }else if($timeframe == 'days'){
        $data = [[
                            [
                                "a" => "Selected Range",
                                "b" => round($days_total_cost*4.4217,2)." ฿"
                            ]
                        ],[
        
                            "name" => $room_name
                        ]];

        }else if($timeframe == 'month'){
        $data = [[
                            [
                                "a" => "This Month",
                                "b" => round($month_total_cost*4.4217,2)." ฿"
                            ],
                            [
                                "a" => "Last Month",
                                "b" => round($lastmonth_total_cost*4.4217,2)." ฿"
                            ],
                            [
                                "a" => "Daily Average",
                                "b" => round(($month_total_cost/$month_day)*4.4217,2)." ฿" 
                            ],
                        ],[
        
                            "name" => $room_name
                        ]];

        }else if($timeframe == 'year'){
        $data = [[
                            [
                                "a" => "This Year",
                                "b" => round($year_total_cost*4.4217,2)." ฿"
                            ]
                        ],[
        
                            "name" => $room_name
                        ]];

        }

        return response($data, 200);
    
    }

}











































