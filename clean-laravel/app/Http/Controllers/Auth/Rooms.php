<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\roomlist;
use App\Models\devicelist;
use App\Models\sensor_status;
use App\Models\sensor_data;
use App\Models\Helpler;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\users;
class Rooms extends Controller
{
    public function add_room(){
        $input = request()->all();
        $getuserinfo = Helpler::Userinfofromtoken($input['token']);
        $userid = $getuserinfo['sub'];
        $user_id = users::where('line_id',$userid)->first()->id;
        if($input['roomtype'] == 'Kitchen'){
            $roomtype = 'https://www.gadgetbuck.com/img/icons/room/room-white-05.png';
        }
        else if($input['roomtype'] == 'Bathroom'){
            $roomtype = 'https://www.gadgetbuck.com/img/icons/room/room-white-04.png';
        }
        else if($input['roomtype'] == 'Bed Room'){
            $roomtype = 'https://www.gadgetbuck.com/img/icons/room/room-white-02.png';
        }
        else if($input['roomtype'] == 'Living room'){
            $roomtype = 'https://www.gadgetbuck.com/img/icons/room/room-white-03.png';
        }
        // $roomid = roomlist::insert([
        //     'icon' => $roomtype,
        //     'statistic' => $input['roomname'],
        //     'style'=> 'max-width:200px',
        //     'user_id'=>$user_id
        // ]);
        // dd($roomid);
        $roomlist = new roomlist;
        $roomlist->icon = $roomtype;
        $roomlist->statistic = $input['roomname'];
        $roomlist->style = 'max-width:200px';
        $roomlist->user_id = $user_id;
        $roomlist->save();
        
        $devicelist_temp = new devicelist;
        $devicelist_temp->user_id = $user_id;
        $devicelist_temp->sensor_enable = 1;
        $devicelist_temp->module_id = '-';
        $devicelist_temp->icon = '-';
        $devicelist_temp->order = '-';
        $devicelist_temp->group_id = '-';
        $devicelist_temp->switch_status = '-';
        $devicelist_temp->devicetype = '-';
        $devicelist_temp->sensor_name = $input['roomname']."_temp";
        $devicelist_temp->type = 'temp';
        $devicelist_temp->location = $roomlist->id;
        $devicelist_temp->usage =  1;
        $devicelist_temp->save();
        
        sensor_status::insert([
            'sensor_name' => $input['roomname']."_temp",
            'sensor_value'=>'25',
            'unit'=>'°C',
            'device_id'=> $devicelist_temp->id
        ]);
        
        $devicelist_humid = new devicelist;
        $devicelist_humid->user_id = $user_id;
        $devicelist_humid->sensor_enable = 1;
        $devicelist_humid->module_id = '-';
        $devicelist_humid->icon = '-';
        $devicelist_humid->order = '-';
        $devicelist_humid->group_id = '-';
        $devicelist_humid->switch_status = '-';
        $devicelist_humid->devicetype = '-';
        $devicelist_humid->sensor_name = $input['roomname']."_humid";
        $devicelist_humid->type = 'humid';
        $devicelist_humid->location = $roomlist->id;
        $devicelist_humid->usage =  1;
        $devicelist_humid->save();

        sensor_status::insert([
            'sensor_name' => $input['roomname']."_humid",
            'sensor_value'=>'60',
            'unit'=>'%',
            'device_id'=> $devicelist_humid->id
        ]);

        // devicelist::insert([
        //     'user_id'=> $user_id,
        //     'sensor_name' => $input['roomname']."_temp",
        //     'type'=>'temp',
        //     'location'=>$data->id
        // ]);

        $roomlist = roomlist::where('user_id',$user_id)->get();
        foreach($roomlist as $each){
            $device_count = devicelist::where('user_id',$user_id)->where('location',$each->id)->count(); 
            if($device_count == 0){
                $each->statistictitle = "No Device";
            }
            else if($device_count == 1){
                $each->statistictitle = $device_count." Device";
            }
            else{   
                $each->statistictitle = $device_count." Devices";
            }
        }
        return $roomlist;
    }

    public function delete_room($roomid){
        roomlist::where('id', $roomid)->delete();
        $devicelist = devicelist::where('location', $roomid)->get();
        foreach($devicelist as $each){
            sensor_status::where('device_id',$each->id)->delete();
        }
        devicelist::where('location', $roomid)->delete();
    }
    public function rooms_data(){
        $input = request()->all();
        $getuserinfo = Helpler::Userinfofromtoken($input['token']);
        $userid = $getuserinfo['sub'];
        $user_id = users::where('line_id',$userid)->first()->id;
        $roomlist = roomlist::where('user_id',$user_id)->get();
        foreach($roomlist as $each){
            $device_count = devicelist::where('user_id',$user_id)->where('location',$each->id)->count(); 
            if($device_count == 0){
                $each->statistictitle = "No Device";
            }
            else if($device_count == 1){
                $each->statistictitle = $device_count." Device";
            }
            else{   
                $each->statistictitle = $device_count." Devices";
            }
        }
        return $roomlist;
    }

    public function roomdetail($id)
    {
        $roomlist = roomlist::find($id);
        return $roomlist;
    }

    public function roomdetails(){
        // $roomlist = roomlist::find($id);
        $input = request()->all();
        $id = $input['id'];
        $getuserinfo = Helpler::Userinfofromtoken($input['token']);
        $userid = $getuserinfo['sub'];
        //$userid = 9;
        $user_id = users::where('line_id',$userid)->first()->id;
        $devicelist = devicelist::where('user_id',$user_id)->where('location',$id)->where('usage',1)->get(); ///มีหลาย id
        $perd_you = devicelist::where('user_id',$user_id)->where('location',$id)->where('switch_status',"true")->count(); ///มีหลาย id
        $sum_watt = 0;
        foreach($devicelist as $eachx){
            $watt  = sensor_status::where('device_id',$eachx->id)->where('unit',"W")->get(); 
            foreach($watt as $eaches){
                $watts = $watt->first()->sensor_value;
                $sum_watt += $watts;
            }
        }
        
        $torn_nee = round(($sum_watt/1000)*4.4217,2);
        // return $torn_nee;

        //total_day_cost
        $input = request()->all();
        $startdate = $input['startdate'];
        $sum_unit = 0;
        $startdate_formatted = date("Y-m-d", strtotime($startdate));
        $today = $startdate_formatted;
        $first_unit_sum = 0;
        $now_unit_sum = 0;
        foreach($devicelist as $each){
            $unit_id  = sensor_status::where('device_id',$each->id)->where('unit',"Unit")->get(); 
            foreach($unit_id as $each2){
                $first_unit_of_day = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
                ->where('sensor_id','=' ,$each2->id)->whereDate('date','==',$today)->first()->value; ///ค่าไฟหน่วยแรกของวัน
                (int)$first_unit_sum += (int)$first_unit_of_day;
                $now_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
                ->where('sensor_id','=' ,$each2->id)->whereDate('date','==',$today)->orderBy('id','desc')->first()->value;
                (int)$now_unit_sum += (int)$now_unit;
            }
        }
        $pf_rate = Helpler::pf_rate($now_unit_sum - $first_unit_sum);
        $today_unit = $now_unit_sum - $first_unit_sum;
        $today_cost = round($today_unit*$pf_rate,0);
        
        // $first_unit_of_day1 = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
        // ->where('sensor_id','=' ,$unit_id[0])->whereDate('date','==',$today)->first()->value; ///ค่าไฟหน่วยแรกของวัน
        
        // $first_unit_of_day2 = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
        // ->where('sensor_id','=' ,$unit_id[1])->whereDate('date','==',$today)->first()->value; ///ค่าไฟหน่วยแรกของวัน
        
        // $now_unit1 = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
        // ->where('sensor_id','=' ,$unit_id[0])->whereDate('date','==',$today)->orderBy('id','desc')->first()->value;

        // $now_unit2 = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))
        // ->where('sensor_id','=' ,$unit_id[1])->whereDate('date','==',$today)->orderBy('id','desc')->first()->value;

        
        // $total_unit_of_day = $first_unit_of_day1+$first_unit_of_day2;
        // $total_now_unit = $now_unit1+$now_unit2;
        // $pf_rate = Helpler::pf_rate($total_now_unit - $total_unit_of_day);
        // $today_unit = $total_now_unit - $total_unit_of_day;
        // $today_cost = round($today_unit*$pf_rate,0);
        // dd($today_cost);

        // end of total_day_cost
        
        $params['total_day_cost'] = $today_cost;
        $params['sum_watt'] = round($sum_watt,0);
        $params['perd_you'] = $perd_you;
        $params['torn_nee'] = $torn_nee;
        return $params;

        ///return roomlist::find($id);


    }
    public  function room_temp_humid($id){ //id ห้อง
        $sensor_statusid_temp = devicelist::where('location',$id)->where('type','temp')->first()->id;
        $sensor_statusid_humid = devicelist::where('location',$id)->where('type','humid')->first();
        
        if(!isset($sensor_statusid_humid)){ ///this is error no value
            $params['indoorhumi'] = 69;
        }else{
            $humid = sensor_status::where('device_id',$sensor_statusid_humid->id)->first()->sensor_value;
            $params['indoorhumi'] = round($humid,0);
        }
        $temp = sensor_status::where('device_id',$sensor_statusid_temp)->first()->sensor_value;
        $params['indoortemp'] = round($temp,0);
        return $params;
        
        
    }
    public function room_usage($room_id){
        $input = request()->all();
        $selected_date = $input['startdate'];
        $end_selected_date = $input['enddate'];
        $timeframe = $input['timeframe'];
        $x = [];
        $y = [];
        
        $room_device = devicelist::where('location',$room_id)->where('usage',1)->get(); /// ห้องมี device อะไรบ้าง

        $benny = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        //$x = $benny;
        $max = 0;

        foreach($room_device as $each){ /// มี device 1   10 loop 2 รอบ
            $device_id = $each->id;
            $daily_usage_yes = Helpler::get_cost_by_device_id($selected_date,$end_selected_date,$timeframe,$device_id);
            
            $i = 0;    

            foreach ($daily_usage_yes as $each2) { //loop s4 รอบ
                $benny[$i] += $each2['usage'];
                //$x = $i+1;
                if($i>=$max){
                    $max = $i;
                }
                $i++;
            }
        }

        for ($k = 0 ; $k <= $max; $k++){
            array_push($x,$k+1);
            array_push($y,round($benny[$k]*4.4217,2));
        }
        
        // $i = 0;
        // foreach( $daily_usage_yes as $each ){  
        //     array_push($x,(int)$each['x']);
        //     array_push($y,round($benny[$i]*4.4217,2));
        //     $i++;
        // }

        $data = [
        "chartOptions" => [
            "chart" => [
                "toolbar" => [
                    "show" => false,
                ],
                "type" => "area"
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
                    "type" =>'numeric', 
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
                        "name" => 'ค่าไฟ (฿)',
                        "data" => $y
                        ]
                    ]
                ];
        
        return response($data, 200);
        
    }
    public function room_temp_graph($id){
        $input = request()->all();
        $startdate = $input['startdate'];
        $enddate = $input['enddate'];
        $timeframe= $input['timeframe'];
        $room_temp_devicelist = devicelist::where('location',$id)->where('usage',1)->where('type','temp')->first()->id; /// ห้องมี device อะไรบ้าง
        $room_humid_devicelist = devicelist::where('location',$id)->where('usage',1)->where('type','humid')->first()->id; /// ห้องมี device อะไรบ้าง
        $room_temp_sensor_status = sensor_status::where('device_id',$room_temp_devicelist)->where('unit','°C')->first()->id;
        $room_humid_sensor_status = sensor_status::where('device_id',$room_humid_devicelist)->where('unit','%')->first()->id;

        
        
        
        $startdate_formatted = date("Y-m-d", strtotime($startdate));
        $enddate_formatted = date("Y-m-d", strtotime($enddate));

        if($timeframe == 'month'){
            $month = date("n", strtotime($startdate)); // เดือนที่ตัดเลข 0 ข้างหน้า
            $lastday = date("t", strtotime($startdate)); // วันสุดท้ายของเดือน
            $year = date("Y", strtotime($startdate));
            $startm = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
            $endm = date("Y-m-d", strtotime($lastday.'-'.$month.'-'.$year));
            $temp_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$room_temp_sensor_status)
            ->whereDate('date','>=',$startm)->whereDate('date','<=',$endm)->groupBy('date2')->get();
            $humid_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$room_humid_sensor_status)
            ->whereDate('date','>=',$startm)->whereDate('date','<=',$endm)->groupBy('date2')->get();
            $outdoor_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,17)
            ->whereDate('date','>=',$startm)->whereDate('date','<=',$endm)->groupBy('date2')->get();
            $type = "numeric";
        }
        else if($timeframe == 'day'){
            $temp_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%dT%H',date) as date3 , strftime('%H',date) as date2"))
            ->where('sensor_id','=' ,$room_temp_sensor_status)->whereDate('date','=',$startdate_formatted)->groupBy('date3')->get();
            $humid_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%dT%H',date) as date3 , strftime('%H',date) as date2"))
            ->where('sensor_id','=' ,$room_humid_sensor_status)->whereDate('date','=',$startdate_formatted)->groupBy('date3')->get();
            $outdoor_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%dT%H',date) as date3 , strftime('%H',date) as date2"))
            ->where('sensor_id','=' ,17)->whereDate('date','=',$startdate_formatted)->groupBy('date3')->get();
            $type = "numeric";
        }
        else if($timeframe == 'year'){
            $year = date("Y", strtotime($startdate));
            $starty = date("Y-m-d", strtotime('01'.'-'.'01'.'-'.$year)); // วันแรกของปี  2020-01-01   
            $lasty = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.$year)); // วันสุดท้ายของปี 2020-12-31
            $temp_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m',date) as date2,strftime('%H',date) as date3"))->where('sensor_id','=' ,$room_temp_sensor_status)
            ->whereDate('date','>=',$starty)->whereDate('date','<=',$lasty)->groupBy('date2')->get();
            $humid_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m',date) as date2"))->where('sensor_id','=' ,$room_humid_sensor_status)
            ->whereDate('date','>=',$starty)->whereDate('date','<=',$lasty)->groupBy('date2')->get();
            $outdoor_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m',date) as date2"))->where('sensor_id','=' ,17)
            ->whereDate('date','>=',$starty)->whereDate('date','<=',$lasty)->groupBy('date2')->get();
            $type = "numeric";
        }
        
        $x = [];
        $y1 = [];
        $y2 = [];
        $y3 = [];
        foreach( $temp_avg as $each ){  
            array_push($x,$each->date2);
            array_push($y1,round($each->Avg,2));
        }
        foreach( $outdoor_avg as $each ){  
            array_push($y2,round($each->Avg,2));
        }
        foreach( $humid_avg as $each ){  
            array_push($y3,round($each->Avg,2));
        }

        
        
        //รายวัน = 24 ชั่วโมง อาทิตย์ = ค่าเฉลี่ยในแต่ละวัน   รายเดือน = ค่าเฉลี่ยของแต่ละวัน รายปี = ค่าเฉลี่ยของแต่ละเดือน
        //ทำเงื่อนไข if ว่าเป็น cart ไหน chart วัน , chart อาทิตย์ , chart เดือน , chart ปี
        $data = [
                "chartOptions" => [
                    "chart" => [
                        "toolbar" => [
                            "show" => false,
                        ],
                        "type" => "line",
                        ],
                        "legend"=> [
                            "labels"=> [
                                "colors"=> "#FFFFFF",
                            ]
                        ],
                        "colors" => ["#80FF00", "#FFFF00","#FF0000",],
                        "fill" =>  [
                            "type" => "solid"
                        ],
    		        	"dataLabels" => [
    		        		"enabled" => false
                        ],
    		          	"xaxis" => [
    		          		"type" => "category", 
    		          		"categories"=> $x,
                            "labels"=>[
                                "rotate"=> 0,
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
    			              "format" => 'yy-MM-dd HH:mm:ss'
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
                        "name" => 'Temperature',
                        "data" => $y1
                        ],
                        [
                        "name" => 'Outdoor Temp',
                        "data" => $y2
                        ],
                        [
                        "name" => 'Humidity',
                        "data" => $y3
                        ]
                    ]
                ];
        return response($data, 200);
    }
    }


