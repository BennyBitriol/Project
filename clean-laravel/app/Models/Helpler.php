<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\sensor_data;
use App\Models\sensor_status;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;
use Illuminate\Http\Request;
use App\Models\users;

class Helpler extends Model
{

    public static function get_cost_by_device_id($selected_date,$end_selected_date,$timeframe,$device_id){
        date_default_timezone_set("Asia/Bangkok");
        $year_month_start = date('Y-m', strtotime($selected_date));
        $first_day_next_month = "";
        $year = date("Y", strtotime($selected_date));
        $month = date("n", strtotime($selected_date));
        $lastday = date("t", strtotime($selected_date));
        $last_day_month = date("Y-m-d", strtotime($lastday.'-'.$month.'-'.$year));
        $first_day_next_month = date("Y-m-d",strtotime($last_day_month)+(60 * 60 * 24));

        $lastday_month_before = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year)-(60 * 60 * 24));

        $sensor_status = sensor_status::where('device_id',$device_id)->where('unit',"Unit")->first(); ///38
        
        $unit_id = $sensor_status->id;

        if($timeframe == "day"){

            $selected_date = date("Y-m-d", strtotime($selected_date));

            $daily_usage = DB::table('sensor_data')
            ->select(DB::raw("value,max(value)-min(value) as usage,strftime('%Y-%m-%d %H',date) as date1 ,strftime('%Y-%m-%d',date) as date2,strftime('%H',date) as date3"))->where('sensor_id','=' ,$unit_id)->where('date2','=',$selected_date)->groupBy('date1')->get();

            $daily_usage_yes = json_decode(json_encode($daily_usage), true);

            foreach ($daily_usage_yes as $key => $each) {
                $daily_usage_yes[$key]['x'] = $each['date3'];
            }
            $x_type = "numeric";
        }


        else if($timeframe == "days"){
            $selected_date = date("Y-m-d", strtotime($selected_date));
            $end_selected_date = date("Y-m-d", strtotime($end_selected_date));

            
            $daily_usage = DB::table('sensor_data')
            ->select(DB::raw("max(value)-min(value) as usage, strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->where('date2','>=',$selected_date)->where('date2','<=',$end_selected_date)->groupBy('date2')->get();

                $daily_usage_yes = json_decode(json_encode($daily_usage), true);

                // $daily_usage2 = $daily_usage; 
                // $daily_usage_yes = $daily_usage;
                // array_shift($daily_usage2);
                foreach ($daily_usage_yes as $key => $each) {
                    $daily_usage_yes[$key]['x'] = date("d", strtotime($daily_usage_yes[$key]['date2']));
                }
                // $remove = array_pop($daily_usage_yes); 
                $x_type = "datetime";
        }
        else if($timeframe == "month")
        {
            /// reset every month 
            $reset_every_month = sensor_status::find($unit_id)->reset_every_month;

        
            $daily_usage = DB::table('sensor_data')
            ->select(DB::raw("max(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as month_year"))->where('sensor_id','=',$unit_id)->where('date2','>=',$lastday_month_before)->where('month_year','<=',$year_month_start)->groupBy('date2')->get();
            $daily_usage = json_decode(json_encode($daily_usage), true);

            $daily_usage2 = $daily_usage;
            $daily_usage_yes = $daily_usage;
            array_shift($daily_usage2);

            
            foreach ($daily_usage2 as $key => $each) {
                $daily_usage_yes[$key]['key'] = $key;

                if($reset_every_month == 1 && $key==0){
                    $daily_usage_yes[$key]['usage'] = $daily_usage2[$key]['value'];
                }else{
                    $daily_usage_yes[$key]['usage'] = (float)$daily_usage2[$key]['value']-(float)$daily_usage[$key]['value'];
                }


                $daily_usage_yes[$key]['x'] = date("d", strtotime($daily_usage2[$key]['date2']));
            }
            $x_type = "datetime";
            $remove = array_pop($daily_usage_yes); 

        }

        else if($timeframe == "year"){
            $selected_date = date("Y-m-d", strtotime($selected_date));
            $year = date("Y", strtotime($selected_date));
            // $starty = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.($year-1))); // วันแรกของปี  2021-01-01   
            // $lasty = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.$year)); // วันสุดท้ายของปี 2021-12-31

            $starty = date("Y-m-d", strtotime('01'.'-'.'01'.'-'.($year))); // วันแรกของปี  2021-01-01   
            $lasty = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.$year)); // วันสุดท้ายของปี 2021-12-31

            $daily_usage = DB::table('sensor_data')
            ->select(DB::raw("max(value)-min(value) as usage,strftime('%m',date) as x,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as month_year"))->where('sensor_id','=' ,$unit_id)
            ->where('date2','>=',$starty)->where('date2','<=',$lasty)->groupBy('month_year')->get();

            $daily_usage_yes = json_decode(json_encode($daily_usage), true);

            $x_type = "numeric";
        }


        return $daily_usage_yes;

    }
   	public static function pf_rate($unit){	
    	if($unit<=150){
            return 3.2484;
        }else if($unit<=400){
            return 4.2218;
        }
        else{
            return 4.4217;
        }
    }
    // ค่าไฟวันนี้ตอนนี้


    // public static function usage_today2($unit_id){
    //     date_default_timezone_set("Asia/Bangkok");
    //     $today = date("Y-m-d", time());
    //     // $sensor_status = sensor_status::where('device_id',$device_id)->get();
    //     $month = date('n', time()); ///เดือนนี้
    //     $year = date("Y", time());
    //     $first_day_month = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
    //     // foreach($sensor_status as $each){
    //     //     $unit = $each->unit; 
    //     //     if($unit == "Unit"){
    //     //         $unit_id = $each->id;
    //     //     }
    //     // }
    //     $first_unit_of_day = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$today)->first()->value; ///ค่าไฟหน่วยแรกของวัน
    //     $now_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$today)->orderBy('id','desc')->first()->value; /// หน่วยตอนนี
    //     $first_unit_of_month = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$first_day_month)->first()->value;
    //     $pf_rate = Helpler::pf_rate($now_unit-$first_unit_of_month);
    //     $today_cost = ((float)$now_unit-(float)$first_unit_of_day)*$pf_rate;

    //     return round($today_cost,2);
    // }

    // public static function usage_yesterday($device_id){

    // }

    // public static function usage_yesterday_now($device_id){


    // }

    // public static function usage_days($unit_id,$startdate_formatted,$enddate_formatted){
    //     date_default_timezone_set("Asia/Bangkok");

    //     $startdate = $startdate_formatted;
    //     $enddate = $enddate_formatted;
    //     $time_now = date("H:i:s", time());
    //     $month = date("n", strtotime($startdate_formatted));
    //     $year = date("Y", strtotime($startdate_formatted));
    //     // $year = date("Y", time());
    //     // $first_day_month = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));

    //     // $sensor_status = sensor_status::where('device_id',$device_id)->get();
    //     // foreach($sensor_status as $each){
    //     //     $unit = $each->unit; 

    //     //     if($unit == "Unit"){
    //     //         $unit_id = $each->id;
    //     //     }
    //     // }

    //     $start_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$startdate)->first();

    //     $start_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$startdate)->first();

    //     $end_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$enddate)->orderBy('id','desc')->first();


    //     if(!isset($start_unit)){ ///this is error no value
    //         $params['selected_cost'] = "No Data";
    //         return $params;
    //     }else{
    //         $start_unit = $start_unit->value;
    //     }

    //     if(!isset($end_unit)){ ///this is error no value
    //         $params['selected_cost'] = "No Data";
    //         return $params;
    //     }else{
    //         $end_unit = $end_unit->value;
    //     }

    //     $pf_rate = 4.4217;

    //     $selected_unit = abs((float)$end_unit-(float)$start_unit);
    //     $selected_cost = round($selected_unit*$pf_rate,2);

    //     $params['selected_cost'] = $selected_cost;
    //     return $params;

    // }

    // public static  function usage_year($unit_id,$startdate_formatted){
    //     $year = date("Y", strtotime($startdate_formatted));
    //     $firstdayofyear = date("Y-m-d", strtotime('01'.'-'.'01'.'-'.$year)); // วันแรกของปี  2020-01-01   
    //     $lastdayofyear = date("Y-m-d", strtotime('31'.'-'.'12'.'-'.$year)); // วันสุดท้ายของปี 2020-12-31
    //     // $sensor_status = sensor_status::where('device_id',$device_id)->get();
    //     $year_month = date("Y-m", strtotime($startdate_formatted));
    //     // foreach($sensor_status as $each){
    //     //     $unit = $each->unit; 
    //     //     if($unit == "Unit"){
    //     //         $unit_id = $each->id;
    //     //     }
    //     // }

    //     $first_unit_of_year = DB::table('sensor_data')
    //     ->select(DB::raw("min(value) as value,strftime('%Y',date) as yyyy,strftime('%Y-%m-%d',date) as date2"))
    //     ->where('value','LIKE','%29%')
    //     ->where('sensor_id',$unit_id)
    //     ->where('yyyy',$year)
    //     ->first()->value;

    //     $last_unit_of_year = DB::table('sensor_data')
    //     ->select(DB::raw("max(value) as value,strftime('%Y',date) as yyyy,strftime('%Y-%m-%d',date) as date2"))
    //     ->where('value','LIKE','%29%')
    //     ->where('sensor_id',$unit_id)
    //     ->where('yyyy',$year)
    //     ->first()->value;
         
        
    //     $total_year_unit = $last_unit_of_year - $first_unit_of_year;
    //     $pf_rate = Helpler::pf_rate($total_year_unit);
    //     $year_total_cost = $pf_rate*$total_year_unit;
    //     $year_total_cost = (round($year_total_cost,2));

    //     $params['year_total_cost'] = $year_total_cost;
    //     return $params;
    // }

    // public static function usage_month($unit_id,$startdate_formatted){
    //     //ค่าไฟเฉลี่ยแต่ละวัน
    //     //ค่าไฟเดือนนี้  done
    //     //ค่าไฟเดือนที่แล้ว done but not work
        
    //     date_default_timezone_set("Asia/Bangkok");
    //     $today = date("Y-m-d", time());
    //     $today_day = date("d", time());
    //     $month = date("n", strtotime($startdate_formatted)); // เดือนที่ตัดเลข 0 ข้างหน้า
    //     $month_input = date("m", strtotime($startdate_formatted));
    //     $month_now = date("m", time());
    //     $lastday = date("t", strtotime($startdate_formatted)); // วันสุดท้ายของเดือน
    //     $year = date("Y", strtotime($startdate_formatted));
    //     $year_month = date("Y-m", strtotime($startdate_formatted));
    //     $first_day_month = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
    //     $last_day_month = date("Y-m-d", strtotime($lastday.'-'.$month.'-'.$year));
    //     $last_day_of_month = date("d", strtotime($lastday.'-'.$month.'-'.$year));
    //     $endm = date("Y-m-d", strtotime($lastday.'-'.$month.'-'.$year));
    //     // $sensor_status = sensor_status::where('device_id',$device_id)->get();
    //     // foreach($sensor_status as $each){
    //     //     $unit = $each->unit; 

    //     //     if($unit == "Unit"){
    //     //         $unit_id = $each->id;
    //     //     }
    //     // }

    //     // BUG เดือน 2 bug เวลาไม่มีวันแรกของเดือน
    //     $first_unit_of_month = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2 ,strftime('%Y-%m',date) as month_year"))->where('sensor_id','=' ,$unit_id)->where('month_year',$year_month)->first();

    //     if(!isset($first_unit_of_month)){ ///this is error no value
    //         $first_unit_of_month = 0;
    //     }else{
    //         $first_unit_of_month = $first_unit_of_month->value;
    //     }

    //     $now_unit_of_month = DB::table('sensor_data')->select(DB::raw("max(value) as value,strftime('%Y-%m',date) as date2"))->where('sensor_id','=' ,$unit_id)->where('date2',$year_month)->first()->value;

    //     $first_lastmonth = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $first_day_month ) ) . "-1 month" ) );

    //     $lastmonthyear = date("Y-m", strtotime( date( "Y-m-d", strtotime( $first_day_month ) ) . "-1 month" ) );

    //     $lastdayofmonth = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $first_day_month ) ) . "-1 day" ) );
        

    //     // find avg cost per day
    //     if($month_now == $month_input){ //เดือนเดียวกัน
    //         $total_unit = (float)$now_unit_of_month - (float)$first_unit_of_month;
    //         $day_avg = $total_unit / $today_day ;
    //     }
    //     else{
    //         $total_unit = $now_unit_of_month - $first_unit_of_month;
    //         $day_avg = $total_unit / $last_day_of_month ;
    //     }
    //     $pf_rate = Helpler::pf_rate($day_avg);
    //     $day_avg_cost = $pf_rate*$day_avg;
    //     $day_avg_cost = (round($day_avg_cost,2));
    //     // dd($day_avg_cost);

    //     //BUG เอา unit เดือนที่แล้วออกมาไม่ได้

    //     $first_lastmonth_unit = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2,strftime('%Y-%m',date) as month_year"))->where('sensor_id','=' ,$unit_id)->where('month_year',$lastmonthyear)->first();

    //     if(!isset($first_lastmonth_unit)){ ///this is error no value
    //         $first_lastmonth_unit = 0;
    //     }else{
    //         $first_lastmonth_unit = $first_lastmonth_unit->value;
    //     }


    //     // dd($first_lastmonth_unit);
    //     $lastdayofmonth_unit = DB::table('sensor_data')->select(DB::raw("max(value) as value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->where('date2',$lastdayofmonth)->orderBy('id', 'DESC')->first()->value;

    //     $total_unit =  $now_unit_of_month - $first_unit_of_month;
    //     $pf_rate = Helpler::pf_rate($total_unit);
    //     $month_total_cost = $pf_rate*$total_unit;
    //     $month_total_cost = (round($month_total_cost,2));

    //     $lastmonth_total_unit =  $lastdayofmonth_unit - $first_lastmonth_unit;
    //     $last_month_pf_rate = Helpler::pf_rate($lastmonth_total_unit);
    //     $lastmonth_total_cost = $last_month_pf_rate*$lastmonth_total_unit;
    //     $lastmonth_total_cost = (round($lastmonth_total_cost,2));
        
    //     if($lastmonth_total_cost>0){
    //         $percent_change = round((($month_total_cost / $lastmonth_total_cost)-1) * 100,2);
    //     }else{
    //         $percent_change = $month_total_cost*100;
    //     }
        

    //     $params['month_total_cost'] = $month_total_cost;
    //     $params['lastmonth_total_cost'] = $lastmonth_total_cost;
    //     $params['month_percent'] = $percent_change;
    //     $params['average'] = $day_avg_cost;
    //     return $params;
    // }


    // public static function usage_today($unit_id,$startdate_formatted){///devicelist){ //// ค่าไฟวันนี้ตาม Device
        
    //     date_default_timezone_set("Asia/Bangkok");
    //     // $today = date("Y-m-d", time());
    //     $today = $startdate_formatted;
    //     $time_now = date("H:i:s", time());
    //     // $month = date('n', time()); ///เดือนนี้
    //     $month = date("n", strtotime($startdate_formatted));
    //     $year = date("Y", strtotime($startdate_formatted));
    //     // $year = date("Y", time());
    //     $yesterday = date("Y-m-d",strtotime($startdate_formatted)- 60 * 60 * 24);
    //     $yesterday_start = $yesterday." 00:00:00";
    //     // $yesterday_now = date("Y-m-d H:i:s",strtotime($startdate_formatted)- 60 * 60 * 24);
    //     $yesterday_now = $yesterday.' '.$time_now;
    //     $first_day_month = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
        
    //     // $sensor_status = sensor_status::where('device_id',$device_id)->get();
    //     // foreach($sensor_status as $each){
    //     //     $unit = $each->unit; 

    //     //     if($unit == "Unit"){
    //     //         $unit_id = $each->id;
    //     //     }
    //     // }

    //     $first_unit_of_month = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date2','>=',$first_day_month)->first()->value;

    //     $first_unit_of_day = DB::table('sensor_data')->select(DB::raw("min(value) as value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$today)->first()->value; ///ค่าไฟหน่วยแรกของวัน

    //     $now_unit = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$today)->orderBy('id','desc')->first()->value;

    //     $first_unit_yesterday  = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$yesterday)->first()->value;

    //     // $end_unit_yesterday = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereDate('date','==',$yesterday)->orderBy('id','desc')->first()->value;

    //     $unit_yesterday_same_time = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d %H:%M:%S',date) as date2"))->where('sensor_id','=' ,$unit_id)->whereBetween('date2',[$yesterday_start,$yesterday_now])->orderBy('id','desc')->first()->value;

    //     $pf_rate = Helpler::pf_rate(abs((float)$now_unit-(float)$first_unit_of_month));
        
    //     $today_unit = abs((float)$now_unit-(float)$first_unit_of_day);
    //     $yesterday_unit = $first_unit_of_day-$first_unit_yesterday;
    //     $yesterday_same_time_unit = (abs((float)$unit_yesterday_same_time-(float)$first_unit_yesterday));
    
    //     $today_cost = round($today_unit*$pf_rate,2);
    //     $yesterday_cost = round($yesterday_unit*$pf_rate,2);
    //     $yesterday_same_cost = round($yesterday_same_time_unit*$pf_rate,2);

    //     if($yesterday_same_cost>0){
    //         $percent_change = round((($today_cost / $yesterday_same_cost)-1) * 100,2);
    //     }else{
    //         $percent_change = $today_cost*100;
    //     }

    //     $params['today_cost'] = $today_cost;
    //     $params['yesterday_cost']   = $yesterday_cost;
    //     $params['yesterday_same_cost']  = $yesterday_same_cost;
    //     $params['percent_change']  =$percent_change;

    //     return $params;
    // }

    public static function cond_data($cond){
        $forecasts = [];

        // ความหมาย
        // 1 = ท้องฟ้าแจ่มใส (Clear)
        // 2 = มีเมฆบางส่วน (Partly cloudy)
        // 3 = เมฆเป็นส่วนมาก (Cloudy)
        // 4 = มีเมฆมาก (Overcast)
        // 5 = ฝนตกเล็กน้อย (Light rain)
        // 6 = ฝนปานกลาง (Moderate rain)
        // 7 = ฝนตกหนัก (Heavy rain)
        // 8 = ฝนฟ้าคะนอง (Thunderstorm)
        // 9 = อากาศหนาวจัด (Very cold)
        // 10 = อากาศหนาว (Cold)
        // 11 = อากาศเย็น (Cool)
        // 12 = อากาศร้อนจัด (Very hot)

        if($cond==1){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-03.png";
            $forecasts['condition_th'] = "ท้องฟ้าแจ่มใส";
            $forecasts['condition_en'] = "Clear";
            
        }else if($cond==2){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-02.png";
            $forecasts['condition_th'] = "มีเมฆบางส่วน";
            $forecasts['condition_en'] = "Partly cloudy";
        }else if($cond==3){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-04.png";
            $forecasts['condition_th'] = "เมฆเป็นส่วนมาก";
            $forecasts['condition_en'] = "Cloudy";
        }else if($cond==4){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-05.png";
            $forecasts['condition_th'] = "มีเมฆมาก";
            $forecasts['condition_en'] = "Overcast";
        }else if($cond==5){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-06.png";
            $forecasts['condition_th'] = "ฝนตกเล็กน้อย";
            $forecasts['condition_en'] = "Light rain";
        }else if($cond==6){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-07.png";
            $forecasts['condition_th'] = "ฝนปานกลาง";
            $forecasts['condition_en'] = "Moderate rain";
        }else if($cond==7){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-08.png";
            $forecasts['condition_th'] = "ฝนตกหนัก";
            $forecasts['condition_en'] = "Heavy rain";
        }else if($cond==8){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-09.png";
            $forecasts['condition_th'] = "ฝนฟ้าคะนอง";
            $forecasts['condition_en'] = "Thunderstorm";
        }else if($cond==9){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-10.png";
            $forecasts['condition_th'] = "อากาศหนาวจัด";
            $forecasts['condition_en'] = "Very Cold";
        }else if($cond==10){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-11.png";
            $forecasts['condition_th'] = "อากาศหนาว";
            $forecasts['condition_en'] = "Cold";
        }else if($cond==11){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-12.png";
            $forecasts['condition_th'] = "อากาศเย็น";
            $forecasts['condition_en'] = "Cool";
        }else if($cond==12){
            $forecasts['img'] = "https://www.gadgetbuck.com/img/icons/weather/weather-13.png";
            $forecasts['condition_th'] = "อากาศร้อนจัด";
            $forecasts['condition_en'] = "Very hot";
        }

        return $forecasts;
    }

    public static function cost_month($unit){
    	$rate_1 = 3.2484;
    	$rate_2 = 4.2218;
    	$rate_3 = 4.4217;

    	if($unit<=150){
    		$cost = $unit*$rate_1;
    		
            return $cost;
        }else if($unit<=400){
        	$left_unit = $unit-150;
        	$cost = ($left_unit*$rate_2)+(150*$rate_1);

            return $cost;
        }
        else{
        	$one = 150*$rate_1;
        	$two = 250*$rate_2;
        	$cost = $one+$two+($unit-400)*$rate_3;
        	
            return $cost;
        }
    }
    public static function Userinfofromtoken($token){
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/oauth2/v2.1/verify']);
        $response = $client->request('POST', 'https://api.line.me/oauth2/v2.1/verify', [
            'form_params' => [
                'id_token' => $token,
                'client_id' => '1655594666',
            ]
        ]);
  	    $response = json_decode($response->getBody(),true);
        return $response;
    }

    public static function  Verifyaccesstoken($accesstoken){
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/oauth2/v2.1/verify']);
        $response = $client->request('GET', 'https://api.line.me/oauth2/v2.1/verify', [
            'form_params' => [
                'access_token' => $accesstoken,
            ]
        ]);
  	    $response = json_decode($response->getBody(),true);
        return $response;
    }

    public static function  lineregisterornot($user){

        $line_id_count = users::where('line_id',$user['sub'])->count();
        $date = date("Y-m-d", time());
        $random = rand(0,99);
        if($line_id_count==0){ /// ยังไม่ได้ลงใน DB
            $data = users::create([
                    'email' => $random.'@gmail.com',
                    'name' => 'BOBO',
                    'email_verified_at' =>  $date,
                    'password' => "bobo",
                    'line_id' => $user['sub'],
                    'line_avatar' => $user['picture'],
                    'line_name' => $user['name'],
                    'remember_token' => 'asdzxc',
                    'created_at' => "2021-01-30 22:28:23" , 
                    'updated_at' => "2021-01-30 22:28:23",
                ]);
        
            $data->save();
            $user = users::where('line_id',$user['sub'])->first();
            return $user;
        }
        else if($line_id_count>0){
            $user = users::where('line_id',$user['sub'])->first();
            return $user;
        }
    }

}