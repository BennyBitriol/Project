<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\sensor_status;
use App\Models\sensor_data;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;


use function GuzzleHttp\Promise\each;

class SensorStatus extends Controller
{
    public function sensor() {
        return sensor_status::all();
    }

    
    public function sensorenable(){
        //get * where sensor_enable = 1 
        return sensor_status::where('sensor_enable', "=" , 1)->get();

        //get column
        // $sensor_value = sensor_status::pluck('sensor_value');
    }

    public function statisticsingle()
    {
        $input = request()->all();
        $id = $input['id'];
        $startdate = $input['startdate'];
        $enddate = $input['enddate'];
        $timeframe= $input['timeframe'];

        $startdate_formatted = date("Y-m-d", strtotime($startdate));
        $enddate_formatted = date("Y-m-d", strtotime($enddate));

        if($timeframe == 'month'){
            $month = date("n", strtotime($startdate)); // เดือนที่ตัดเลข 0 ข้างหน้า
            $lastday = date("t", strtotime($startdate)); // วันสุดท้ายของเดือน
            $year = date("Y", strtotime($startdate));
            $startm = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
            $endm = date("Y-m-d", strtotime($lastday.'-'.$month.'-'.$year));
            $date_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$id)
            ->whereDate('date','>=',$startm)->whereDate('date','<=',$endm)->groupBy('date2')->get();
            $type = "datetime";
        }
        else if($timeframe == 'days'){
            $date_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$id)
            ->whereDate('date','>=',$startdate_formatted)->whereDate('date','<=',$enddate_formatted)->groupBy('date2')->get();
            $type = "datetime";
        }
        else if($timeframe == 'day'){
            $date_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m-%dT%H',date) as date3 , strftime('%H:00:00',date) as date2"))
            ->where('sensor_id','=' ,$id)->whereDate('date','=',$startdate_formatted)->groupBy('date3')->get();
            $type = "category";
        }
        else if($timeframe == 'year'){
            $year = date("Y", strtotime($startdate));
            $starty = date("Y-m-d", strtotime('01'.'-'.'01'.'-'.$year)); // วันแรกของปี  2020-01-01   
            $lasty = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.$year)); // วันสุดท้ายของปี 2020-12-31
            $date_avg = DB::table('sensor_data')->select(DB::raw("avg(value) as Avg, strftime('%Y-%m',date) as date2"))->where('sensor_id','=' ,$id)
            ->whereDate('date','>=',$starty)->whereDate('date','<=',$lasty)->groupBy('date2')->get();
            $type = "datetime";
        }
        
        $x = [];
        $y = [];
        foreach( $date_avg as $each ){  
            array_push($x,$each->date2);
            array_push($y,round($each->Avg,2));
        }
        
        
        //รายวัน = 24 ชั่วโมง อาทิตย์ = ค่าเฉลี่ยในแต่ละวัน   รายเดือน = ค่าเฉลี่ยของแต่ละวัน รายปี = ค่าเฉลี่ยของแต่ละเดือน
        //ทำเงื่อนไข if ว่าเป็น cart ไหน chart วัน , chart อาทิตย์ , chart เดือน , chart ปี
        $data = [
                "chartOptions" => [
                    "chart" => [

                        "toolbar" => [
                            "show" => false,
                        ],
                        "type" => "area"

                        ],
    		        	"dataLabels" => [
    		        		"enabled" => false
                        ],
    		          	"xaxis" => [
    		          		"type" => $type, 
    		          		"categories"=> $x
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
                "series" => [[
                        "name" => '',
                        "data" => $y
                        ]
                    ]
                ];
        return response($data, 200);
    }

    
    // เช็ค table cache ว่ามีวันที่นั้นไหม ถ้า ไม่มี insert แถวใหม่ ใน table cache ใส่ date ไป 
    // เช็คต่อ ถ้ามีแล้ว ไม่ต้อง insert  เอาค่ามา รวมกัน ต้องมีตัวแปร count ว่าวิ่งกี่รอบแล้ว แล้วก็มีตัวแปร sum ว่า บวกกันแล้วเท่าไหร่ ค่าบวกกัน / count หา avg เอา avg อัพเดทในแถวของวันที่นั้นใน table cache

    // $ffff = sensor_data::where('sensor_id','=',$id)->whereDate('date','>=',$startdate_formatted)->whereDate('date','<=',$enddate_formatted)->get();
        
        // $ffff = DB::table('sensor_data')->where('sensor_id','=',$id)->whereDate('date','>=',$startdate_formatted)->
        // select(DB::raw('avg(value) as count, DATE(date) date'))->groupBy('date')->get();


        // select * from "sensor_data" where strftime('%Y-%m-%d', "date") = cast(10-03-2021 as text)

        // return $date_avg;

    //    $ffff = DB::table('sensor_data')->select(DB::raw('date , avg(value) as count'))->get()->groupBy(function($yyy){
    //         return Carbon::parse(strval($yyy->date))->format('Y-m-d');
    //    });
    //    return $ffff;
    
}