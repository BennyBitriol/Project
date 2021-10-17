<?php
use Illuminate\Support\Facades\Route;
use App\Models\users;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Models\Helpler;
use Illuminate\Support\Facades\DB;
use App\Models\sensor_data;
use App\Models\sensor_status;
use App\Models\sensor_data_cache;
use App\Models\devicelist;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

Route::group(['prefix' => 'auth', 'namespace' =>'Auth'], function () {
    Route::post('signin', 'SignInController');
    Route::post('signout', 'SignOutController');
    Route::get('me', 'MeController');

});

Route::group(['prefix'=>'database'],function(){

    Route::get('delete',function(){
      //$sensor_id = 19;
      $delete = sensor_data::where('sensor_id',$sensor_id)->whereRaw('new is null')->delete();

    });

    Route::get('delete2',function(){
      $sensor_id = 12;
      $delete = sensor_data::where('sensor_id',$sensor_id)->whereRaw('new = 1')->delete();

    });
    Route::get('delete3',function(){
      $sensor_id = 21;
      $delete = sensor_data::where('sensor_id',$sensor_id)->whereRaw('created_at like "2021-05-02 18:5%"')->delete();
      dd($delete);
    });

    Route::get('delete4',function(){
      $sensor_id = [1,2,3,4];
      foreach($sensor_id as $each){
        $delete = sensor_data::where('sensor_id',$each)->delete();
      }
      dd($delete);
    });


    Route::get('copy',function(){
      
    });

    Route::get('checkrow',function(){
      $xxx = DB::table('sensor_data')->select(DB::raw("sensor_id ,count(value) as count"))->groupBy('sensor_id')->get();
      return($xxx);

    });

    Route::get('fixdate',function(){
      set_time_limit(3000);
      $sensor_data = sensor_data::whereRaw(" date LIKE '%/%'")->get();
      dd(count($sensor_data));
      foreach ($sensor_data as $key=> $each) {
        $date = Carbon::parse($each->date)->toIso8601ZuluString('millisecond');//->format('Y-m-d\TH:i:s\Z');
        $each->date = $date;
        $each->save();
      }
    });

    Route::get('group',function(){
      set_time_limit(300000);
      $sensor_id = 14;//,13,14,15,16,17,19,20,21,22
      //foreach($sensor_id as $eachh){
        $group = DB::table('sensor_data')->select(DB::raw("
          avg(value) as value,max(date) as date,
          strftime('%Y-%m-%d %H:%M',date) as date0,
          strftime('%Y-%m-%d %H',date) as date1,
          strftime('%M',date)/5 as date2
          "))
        ->where('sensor_id','=',$sensor_id)->groupBy('date1')->groupBy('date2')->get();



        foreach($group as $each){
          $new = new sensor_data;
          $new->sensor_id =  $sensor_id;
          $new->value = round($each->value,4);
          $new->date = $each->date;
          $new->new = 1;
          $new->save();
        }

        $delete = sensor_data::where('sensor_id',$sensor_id)->whereRaw('new is null')->delete();        
      //}


    });

    Route::get('nankiller',function(){
      $sensor_data = sensor_data::where('sensor_id','NaN')->delete();
      dd(count($sensor_data));

      //$sensor_data;
      // foreach ($sensor_data as $key=> $each) {
        
      // }
    });
});

Route::group(['prefix'=>'weather'],function(){
    Route::get('hourly', 'Auth\WeatherController@hourly');
    Route::get('daily', 'Auth\WeatherController@daily');
});

Route::group(['prefix'=>'challenge'],function(){
    /// ต้นเดือนส่งไปถามว่าจะตั้งเท่าไร
    /// เอาค่ามาหาเฉลี่ยแล้วแจ้งว่าวันนี้ใช้ได้เท่าไร
    ///   - มา 8 โมง
    ///   - แจ้ง % ตอนเหลือ 50%,90%,ตอนหมดแล้ว
    Route::get('change/{userid}/{value}', 'Auth\ChallengeController@changetarget');
    Route::get('alert', 'Auth\ChallengeController@alert');
    Route::get('set', 'Auth\ChallengeController@settarget');

});

Route::group(['prefix'=>'comfort'],function(){
  Route::get('humid','Auth\ComfortController@comfort_humid');
  Route::get('temp','Auth\ComfortController@comfort_temp');
  Route::get('comfortgraph','Auth\ComfortController@predict3');
  Route::get('predict/{room_id}','Auth\ComfortController@room_predict_3');
});

Route::group(['prefix'=>'summary'],function(){
    /// เช็คทุก 5 นาที ว่ามีอะไรเปิดแล้วปิดบ้าง
    /// เช็คว่าเปิดหรือปิด
    Route::get('check', function(){
      
        $sensor_status = sensor_status::where('device_id',10)->where('unit','W')->first(); 
        $sensor_data3 = sensor_data::where('sensor_id',21)->orderBy('id','desc')->get()[3]; 
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
          $total_cost = $total_unit*4.4217;
          $hour_usage = $end_time_h-$start_time_h;
          // return $start_unit." - ".$end_unit;


        }

        return "อุปกรณ์ใช้งานอยู่ยังไม่ได้ปิด";


    });
   

});


Route::group(['prefix'=>'user'],function(){
  Route::get('auth',function(){
    $input = request()->all();
    $user = Helpler::Userinfofromtoken($input['token']);
    $user_id = Helpler::lineregisterornot($user);
    return $user_id;
    
  });
});

Route::group(['prefix'=>'energyreport'],function(){
  Route::get('info','Auth\EnergyController@EnergyInfo');
	Route::get('table','Auth\EnergyController@EnergyTableReport');
	Route::get('chart','Auth\EnergyController@CostChart');
  Route::get('roomusage/{id}','Auth\EnergyController@RoomUsage');
});

Route::group(['prefix'=>'presentcontroller'],function(){
  Route::get('Challenge','Auth\PresentController@Challenge');
	Route::get('ChallengeDaily','Auth\PresentController@ChallengeDaily');
	Route::get('ML2','Auth\PresentController@ML2');
  Route::get('MorningNotification','Auth\PresentController@MorningNotification');
  Route::get('UsageNotification','Auth\PresentController@UsageNotification');
});


Route::group(['prefix'=>'page'],function(){
	Route::get('dashboard/usage','Auth\DashboardController@usage');
  Route::post('dashboard/predict','Auth\DashboardController@predict');
  Route::post('dashboard/predict2','Auth\DashboardController@predict2');

});

Route::prefix('hw')->group(function() {
    Route::get('/dashboard','Auth\DashboardController@dashboard');
    Route::get('/device','Auth\DeviceController@device');
});

//SensorStatus
Route::get('/sensor','Auth\SensorStatus@sensor');
Route::get('/sensor/enable','Auth\SensorStatus@sensorenable');
Route::get('/user','Auth\UserController@User');
Route::get('/sensor/single/statistic','Auth\SensorStatus@statisticsingle');
Route::get('/sensor/room/statistic','Auth\SensorStatus@roomstatistic');

//Sensor data cache
Route::get('/sensor/cache','Auth\SensorStatus@sensor_data_cached');


//SensorData
Route::get('/sensordata','Auth\SensorData@sensordata');
Route::get('/sensordata/1','Auth\SensorData@sensordata1');

//Rooms
Route::get('/room/{id}','Auth\Rooms@roomdetail');
Route::get('/room/devicelist/{id}','Auth\Rooms@room_temp_humid');
Route::get('/room/usage/{id}','Auth\Rooms@room_usage');
Route::get('/room/temp/{id}','Auth\Rooms@room_temp_graph');

//room
Route::get('/room','Auth\Rooms@rooms_data');
Route::get('/addroom','Auth\Rooms@add_room');

//challenge
Route::get('/updatechallenge/{challenge}','Auth\ChallengeController@updatechallenge');
//challengemain
Route::group(['prefix'=>'challengeline'],function(){
  Route::get('graph','Auth\ChallengeController@mainchallenge');
});

Route::get('humidex/suggest',function(){

  $input = request()->all();

  $temp = $input['temp'];
  $humi = $input['humi'];
  
  $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
  $return[0]['temp'] = $temp;
  $return[0]['humi'] = $humi;
  $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
  $pred = $res->getBody();
  $prediction = (json_decode($pred)->prediction);
  $score = $prediction[0];
  $temp_count = 0;
  $offscale = 0;
  /// ทำ temp 


  if($score>29){
    if($humi >=30){ /// ลด temp
      while($score > 29){
        $temp = $temp-1;
        $return[0]['temp'] = $temp;
        $return[0]['humi'] = $humi;
        $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
        $pred = $res->getBody();
        $prediction = (json_decode($pred)->prediction);
        $score = $prediction[0];
        $temp_count++;
        if($temp_count > 15){
          break;
          $over = 1;
        }
      }
      if($temp_count == 1){
        $display_temp =  "Reduce the temperature by " . $temp_count . " degree";
      }
      else{
        $display_temp =  "Reduce the temperature by " . $temp_count . " degrees";
      }
      
    }else{
      $display_temp = "Humidity is too low";
      $offscale = 1;
    }

    $temp = $input['temp'];
    $humi = $input['humi'];

    $humi_count = 0;

    //$score = 9999;
    $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
    $return[0]['temp'] = $temp;
    $return[0]['humi'] = $humi;
    $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
    $pred = $res->getBody();
    $prediction = (json_decode($pred)->prediction);
    $score = $prediction[0];

    if($temp <= 28){ /// ลด humi

      while($score > 29){
        $humi = $humi-5;
        $return[0]['temp'] = $temp;
        $return[0]['humi'] = $humi;
        $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
        $pred = $res->getBody();
        $prediction = (json_decode($pred)->prediction);
        $score = $prediction[0];
        $humi_count++;
        if($humi_count > 15){
          break;
          $over = 1;
        }
      }
      $display_humi = "Reduce humidity by " . $humi_count*5 . " %";

    }else{
      $display_humi = "The temperature is too high";
      $offscale = 1;
    }

    if($offscale != 1){
      return $display_temp. " or " .$display_humi;
    }else{
      return $display_humi . " " .$display_temp;
    }
  }else{
    return "";
  }

});


//devicelists
Route::get('/devicelist','Auth\devicelists@devicelist');
Route::get('/devicelist/check/{id}','Auth\devicelist@devicestatus');
Route::get('/devicelist/room/{id}/{type}/{air}','Auth\devicelists@devicelistroom'); //ดึง device ในห้องตามประเภท
Route::get('/remoteon/{location}/{value}','Auth\devicelists@remoteon');
Route::get('/remoteoff/{location}/{value}','Auth\devicelists@remoteoff');
Route::get('/devicelist/change/{id}/{value}','Auth\devicelists@changedevicevalue'); //เปลี่ยนค่าใน devicelist
Route::get('/devicelist/line/change/{id}/{value}','Auth\devicelists@linechangedevicevalue'); //เปลี่ยนค่าใน devicelist


Route::prefix('jwt')->group(function() {
    // Document Here->>> https://blog.pusher.com/laravel-jwt/
    // This is /user
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('user', function(){
            try {
                if (! $user = JWTAuth::parseToken()->authenticate()) {
                        return response()->json(['user_not_found'], 404);
                }
                } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                        return response()->json(['token_expired'], $e->getStatusCode());
                } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                        return response()->json(['token_invalid'], $e->getStatusCode());
                } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return response()->json(['token_absent'], $e->getStatusCode());
            }
            return response()->json(compact('user'));
        });

        Route::get('closed',function(){
            $data = "Only authorized users can see this";
            return response()->json(compact('data'),200);
        });

        /// this is same as /user you will get all user data
        Route::get('test',function(){
            $xxx = JWTAuth::parseToken()->authenticate();
            return $xxx;
        });
        // Route::get('clear', function(){
        //     $yyy = auth()->factory()->getTTL() * 60;
        //     return $yyy;
        // });

    });


    Route::get('open',function(){
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data'),200);
    });
    /// This is login same as line callback 
    Route::get('/login',function(){
        $user = Users::where('line_id','Uc5ba53e4db1414cb8bb381d2c8a26024')->first();
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('token'),201);
    });  
});


    Route::get('morning', function(){
        //พยากรณ์อากาศวันนี้
        $client = new GuzzleHttp\Client(['base_uri' => 'https://data.tmd.go.th/nwpapi/v1/forecast/location/daily/']);
        $token = env('TMD_TOKEN');
        $headers = [
		    'Authorization' => 'Bearer ' . $token,        
		    'Accept'        => 'application/json',
  		  ];
          $response = $client->request('GET', 'at?lat=13.7523228&lon=100.5308764&fields=cond,tc,rh,tc_max,tc_min&duration=1', [
  	        'headers' => $headers
  	    ]);
  	    $response = json_decode($response->getBody(),true);
          $forecasts = $response['WeatherForecasts'][0]['forecasts'];
          $cond = $forecasts[0]['data']['cond'];
          $cond_data = Helpler::cond_data($cond);
          $forecasts['cond'] = $cond;
          $forecasts['condition_th'] = $cond_data['condition_th'];
          $forecasts['condition_en'] = $cond_data['condition_en'];
          $forecasts['tc_max'] = round($forecasts[0]['data']['tc_max'],0);
          $forecasts['tc_min'] = round($forecasts[0]['data']['tc_min'],0);
          
          // ค่าไฟเมื่อวาน
          date_default_timezone_set("Asia/Bangkok");
          $month = date('n', time()); ///เดือนนี้
          $year = date("Y", time());
          $today = date("Y-m-d", time());
          $yesterday = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $today ) ) . "-1 day" ) );
          $firstday = date("Y-m-d", strtotime('1'.'-'.$month.'-'.$year));
          $first_unit_of_month = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->whereDate('date','==',$firstday)->first();
          $first_unit_of_day = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->whereDate('date','==',$today)->first();
          $first_unit_of_yesterday = DB::table('sensor_data')->select(DB::raw("value,strftime('%Y-%m-%d',date) as date2"))->where('sensor_id','=' ,15)->whereDate('date','==',$yesterday)->first();
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
          $today_unit = $first_unit_of_day-$first_unit_of_yesterday->value;
          $cost_day = $today_unit*$pf_rate;

          
          $forecasts['electric_usage_yesterday'] = round($cost_day,0);
          $forecasts['electric_usage_month'] = round($cost_month,0);

          //ประมาณค่าไฟวันนี้
          $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
          $return = [];  
          $return[0]['date'] = date("Y-m-d",strtotime($today));
          $return[0]['temp'] = $forecasts['tc_max'];
          $return[0]['day']  = date("w",strtotime($today))+1;

          $res = $client->post('https://www.gadgetbuck.com/flask/model', ['json'=>$return]);
          $pred = $res->getBody();
          
          $prediction = (json_decode($pred)->prediction);
          $forecasts['forecast_today_usage'] = round($prediction[0]*4.4217,2);

          $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
          $response = $res->request('POST', 'multicast', [
          'headers' => [
              'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
          ],
          'json' => [
              'to'=> ['Uc5ba53e4db1414cb8bb381d2c8a26024', 'U0b20bf22d6f2264ad12132613f81ff5f', 'Uda4196f196c788bd72519e90e7dc7c93'],
              'messages'=> [
                  [
                    'type'=> 'flex',
                    'altText'=> 'this is a flex message',
                    'contents'=> [
              'type'=> 'bubble',
              'hero'=> [
                'type'=> 'image',
                'url'=> 'https://thestandard.co/wp-content/uploads/2019/03/IMG_1471.jpg?x94218',
                'aspectMode'=> 'fit',
                'size'=> 'full'
              ],
              'body'=> [
                'type'=> 'box',
                'layout'=> 'vertical',
                'contents'=> [
                  [
                    'type'=> 'text',
                    'text'=> $forecasts['condition_th'],
                    'weight'=> 'bold',
                    'size'=> 'xl',
                    'margin'=> 'md',
                    'align'=> 'center'
                  ],
                  [
                    'type'=> 'box',
                    'layout'=> 'vertical',
                    'margin'=> 'xxl',
                    'spacing'=> 'sm',
                    'contents'=> [
                      [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                          [
                            'type'=> 'text',
                            'text'=> 'อุณหภูมิสูงสุด',
                            'size'=> 'sm',
                            'color'=> '#555555',
                            'flex'=> 0
                          ],
                          [
                            'type'=> 'text',
                            'text'=> $forecasts['tc_max'].'°C',
                            'size'=> 'sm',
                            'color'=> '#111111',
                            'align'=> 'end'
                          ]
                        ]
                      ],
                      [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                          [
                            'type'=> 'text',
                            'text'=> 'อุณหภูมิต่ำสุด',
                            'size'=> 'sm',
                            'color'=> '#555555',
                            'flex'=> 0
                          ],
                          [
                            'type'=> 'text',
                            'text'=> $forecasts['tc_min'].'°C',
                            'size'=> 'sm',
                            'color'=> '#111111',
                            'align'=> 'end'
                          ]
                        ]
                      ],
                      [
                        'type'=> 'separator',
                        'margin'=> 'xxl'
                      ],
                      [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'margin'=> 'xxl',
                        'contents'=> [
                          [
                            'type'=> 'text',
                            'text'=> 'ค่าไฟเมื่อวาน',
                            'size'=> 'sm',
                            'color'=> '#555555'
                          ],
                          [
                            'type'=> 'text',
                            'text'=> $forecasts['electric_usage_yesterday'].' บาท', 
                            'size'=> 'sm',
                            'color'=> '#111111',
                            'align'=> 'end'
                          ]
                        ]
                      ],
                      [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                          [
                            'type'=> 'text',
                            'text'=> 'ค่าไฟสะสมเดือนนี้',
                            'size'=> 'sm',
                            'color'=> '#555555'
                          ],
                          [
                            'type'=> 'text',
                            'text'=> $forecasts['electric_usage_month'].' บาท',
                            'size'=> 'sm',
                            'color'=> '#111111',
                            'align'=> 'end'
                          ]
                        ]
                      ],
                      [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                          [
                            'type'=> 'text',
                            'text'=> 'คาดว่าวันนี้จะใช้ไฟ',
                            'size'=> 'sm',
                            'color'=> '#555555'
                          ],
                          [
                            'type'=> 'text',
                            'text'=> $forecasts['forecast_today_usage'].' บาท',
                            'size'=> 'sm',
                            'color'=> '#111111',
                            'align'=> 'end'
                          ]
                        ]
                      ]
                    ]
                  ],
                  [
                    'type'=> 'separator',
                    'margin'=> 'xxl'
                  ],
                  [
                    'type'=> 'box',
                    'layout'=> 'horizontal',
                    'margin'=> 'md',
                    'contents'=> [
                      [
                        'type'=> 'text',
                        'text'=> 'Thailand will be better if \'Prayut\' dies.',
                        'size'=> 'xs',
                        'color'=> '#aaaaaa',
                        'flex'=> 0,
                        'offsetStart'=> '9%'
                      ]
                    ]
                  ]
                ]
              ],
              'styles'=> [
                'footer'=> [
                  'separator'=> true
                ]
              ]
            ]
                    ]
              ]
                
              
            ]
          ]);
    });


