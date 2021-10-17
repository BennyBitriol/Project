<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\sensor_data;
use App\Models\sensor_status;
use App\Models\Helpler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;

class DashboardController extends Controller 
{

    public function predict (){

        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
        $input = request()->all();
        $return = [];

        foreach ($input as $key => $each) {
            $return[$key]['date'] = date("Y-m-d",strtotime($each['time']));
            $return[$key]['temp'] = $each['data']['tc_max'];
            $return[$key]['day']  = date("w",strtotime($each['time']))+1;
        }
        
        // $str_json = json_encode($request->education);
        // $res = $client->post('/flask/model',$return);
        // $res = $client->post('/flask/model',);
        $res = $client->post('https://www.gadgetbuck.com/flask/model', ['json'=>$return]);
        $pred = $res->getBody();
        $prediction = (json_decode($pred)->prediction);
        $sum = 0;
        foreach($prediction as $each){
            $sum+= $each;
        }
        $predict['today'] = round($prediction[0]*4.4217,2);
        $predict['f10day'] = round($sum*4.4217,2);
        return  $predict;
        
        // ["date"=>"2021-04-09" ,"temp"=>34.77,"day"=>6],["date"=>"2021-04-10","temp"=>33.61,"day"=>7]
        // ,["date"=>"2021-04-11","temp"=>34.76,"day"=>1],["date"=>"2021-04-12","temp"=>35.58,"day"=>2],["date"=>"2021-04-13","temp"=>35.6,"day"=>3]
        // ,["date"=>"2021-04-14","temp"=>35.1,"day"=>4],["date"=>"2021-04-15","temp"=>33.88,"day"=>5],["date"=>"2021-04-16","temp"=>33.85,"day"=>6]
        // ,["date"=>"2021-04-17","temp"=>34.27,"day"=>7],["date"=>"2021-04-18","temp"=>34.17,"day"=>1]

    }
    public function predict2(){
        date_default_timezone_set("Asia/Bangkok");
        
        //เช็คอุปกรณ์เปิดหรือปิด
        $air_power = sensor_status::find(21)->sensor_value;

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
                /// แจ้ง Line 
            }
        }
    }
    public function usage(){
        date_default_timezone_set("Asia/Bangkok");
        $month = date('n', time()); ///เดือนนี้
        $year = date("Y", time());
        $today = date("Y-m-d", time());
        $firstday = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
        $first_unit_of_month = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->whereDate('date','==',$firstday)->first();
        $first_unit_of_day = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->whereDate('date','==',$today)->first();

        $first_unit_of_month = $first_unit_of_month->value;
        $first_unit_of_day = $first_unit_of_day->value;

        $now_unit = DB::table('sensor_data')->where('sensor_id','=' ,15)->orderBy('id', 'desc')->limit(1)->get();
        $now_unit = $now_unit[0]->value;

        $this_mounth_unit = (int)$now_unit-(int)$first_unit_of_month;

        $pf_rate = Helpler::pf_rate($this_mounth_unit);

        $now_power = sensor_data::where('sensor_id',14)->where('value','!=','NaN')->orderBy('id', 'desc')->first();
        $cost_hour = ($now_power->value/1000)*$pf_rate;

        /// ค่าไฟตั้งแต่ต้นเดือน
        $unit_month = $now_unit-$first_unit_of_month;
        // $pf_rate = Helpler::pf_rate($this_mounth_unit);
        $cost_month = Helpler::cost_month($unit_month);

        /// ค่าไฟวันนี้
        $today_unit = $now_unit-$first_unit_of_day;
        $cost_day = $today_unit*$pf_rate;
        //16	
        $indoor_temp = sensor_status::find(16)->sensor_value;
        $indoor_humi = sensor_status::find(18)->sensor_value;

        $volt_sensor_status = sensor_status::where('id',12)->first();
        $volt_sensor_status = $volt_sensor_status->sensor_value;

        $amp_sensor_status = sensor_status::where('id',13)->first();
        $amp_sensor_status = $amp_sensor_status->sensor_value;

        $watt_sensor_status = sensor_status::where('id',14)->first();
        $watt_sensor_status = $watt_sensor_status->sensor_value;
        
        $unit_sensor_status = sensor_status::where('id',15)->first();
        $unit_sensor_status = $unit_sensor_status->sensor_value;

        $params['hour'] = round($cost_hour,0);
        $params['day'] = round($cost_day,0);
        $params['month'] = round($cost_month,0);
        $params['indoortemp'] = round($indoor_temp,1);
        $params['indoorhumi'] = round($indoor_humi,0);
        $params['volt'] = round($volt_sensor_status,0);
        $params['amp'] = round($amp_sensor_status,2);
        $params['watt'] = round($watt_sensor_status/1000,2);
        $params['unit'] = round($unit_sensor_status,0);
        return $params;
        
        // return response()->json([
        //     // energy usage ก้อนแรก
        // 	'hour' => round($cost_hour,0),
        // 	'day' => round($cost_day,0),
        // 	'month' => round($cost_month,0),
        //     //temp
        //     'indoortemp' =>  round($indoor_temp,1),
        //     'indoorhumi' => round($indoor_humi,0),
        //     // energy usage ก้อน 2
        //     'volt'=> round($volt_sensor_status,0),
        //     'amp'=> round($amp_sensor_status,2),
        //     'watt'=> round($watt_sensor_status/1000,2),
        //     'unit' => round($unit_sensor_status,0)
        // ]);

    }
}