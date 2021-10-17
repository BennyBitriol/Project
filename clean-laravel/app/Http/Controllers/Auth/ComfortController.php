<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\sensor_status;
use App\Models\devicelist;
use GuzzleHttp;
use App\Models\users;
use App\Models\Helpler;
class ComfortController extends Controller
{
    public function gauge(){  
        $dataSource = [
            "chart" => [
              // caption=> "Pressure",
              "captionontop"=> "0",
              "origw"=> "380",
              "origh"=> "250",
              "gaugestartangle"=> "135",
              "gaugeendangle"=> "45",
              "gaugeoriginx"=> "190",
              "gaugeoriginy"=> "220",
              "gaugeouterradius"=> "130",
              "theme"=> "fusion",
            //   "showvalue"=> "1",
            //   "numbersuffix"=> " MPa",
              "valuefontsize"=> "25"
            ],
            "colorrange"=> [
              "color"=> [
                [
                  "minvalue"=> "0",
                  "maxvalue"=> "0.2",
                  "code"=> "#62B58F"
                ],
                [
                  "minvalue"=> "0.2",
                  "maxvalue"=> "0.4",
                  "code"=> "#FFC533"
                ],
                [
                  "minvalue"=> "0.4",
                  "maxvalue"=> "0.6",
                  "code"=> "#F2726F"
                ]
              ]
            ],
            "dials"=> [
              "dial"=> [
                [
                  "value"=> "0.1",
                  "tooltext"=> "Moderate Pressure"
                ]
              ]
            ],
            "type"=>'angulargauge',
            "width"=> "200",
            "height"=> "150",
            "dataFormat"=> "json",
        ];
        return $dataSource;
    }
    public function predict3 (){

        $indoor_temp = sensor_status::find(16)->sensor_value;
        $indoor_hum = sensor_status::find(18)->sensor_value;
        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
        $input = request()->all();
        $return = [];

        
        $return[0]['temp'] = $indoor_temp;
        $return[0]['humi'] = $indoor_hum;
    
        // $str_json = json_encode($request->education);
        // $res = $client->post('/flask/model',$return);
        // $res = $client->post('/flask/model',);
        $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
        $pred = $res->getBody();
        $prediction = (json_decode($pred)->prediction);
        
        // return $prediction[0];
        
        // ["date"=>"2021-04-09" ,"temp"=>34.77,"day"=>6],["date"=>"2021-04-10","temp"=>33.61,"day"=>7]
        // ,["date"=>"2021-04-11","temp"=>34.76,"day"=>1],["date"=>"2021-04-12","temp"=>35.58,"day"=>2],["date"=>"2021-04-13","temp"=>35.6,"day"=>3]
        // ,["date"=>"2021-04-14","temp"=>35.1,"day"=>4],["date"=>"2021-04-15","temp"=>33.88,"day"=>5],["date"=>"2021-04-16","temp"=>33.85,"day"=>6]
        // ,["date"=>"2021-04-17","temp"=>34.27,"day"=>7],["date"=>"2021-04-18","temp"=>34.17,"day"=>1]
        if($prediction[0] <= 29){
            $condition_status = "Comfort";
        }
        else if($prediction[0]>29 && $prediction[0]<=38){
            $condition_status = "Some Discomfort";
        }
        else if($prediction[0]>38 && $prediction[0]<=45){
            $condition_status = "Great discomfort avoid exertion";
        }
        else if($prediction[0]>45 && $prediction[0]<=54){
            $condition_status = "Dangerous";
        }
        else if($prediction[0]>54){
            $condition_status = "Heat Stroke immonent";
        }
        $dataSource= [
            "chart"=> [
                "theme"=> "fusion",
                "lowerLimit"=> "22",
                "upperLimit"=> "60",
                "type"=>"hlinear",
                "numberSuffix"=> "",
                "bgcolor"=> "#171D30",
                "baseFontColor"=> "#FFFFFF"
            ],
            "colorRange"=> [
                "color"=> [
                    [
                        "minValue"=> "22",
                        "maxValue"=> "29",
                        // "label"=> "Dry",
                        "code"=> "#00BFFF"
                    ],
                    [
                        "minValue"=> "30",
                        "maxValue"=> "38",
                        // "label"=> "Comfort",
                        "code"=> "#95B737"
                    ],
                    [
                        "minValue"=> "39",
                        "maxValue"=> "45",
                        // "label"=> "Wet",
                        "code"=> "#FFFF66"
                    ],
                    [
                        "minValue"=> "46",
                        "maxValue"=> "54",
                        // "label"=> "Wet",
                        "code"=> "#FFA500"
                    ],
                    [
                        "minValue"=> "55",
                        "maxValue"=> "60",
                        // "label"=> "Wet",
                        "code"=> "#B22222"
                    ]
                ]
            ],
            "pointers"=> [
                "pointer"=> [
                    
                    [
                        "bgColor"=> "#FFFFFF",
                        "value"=> $prediction[0]
                    ]
                ]
            ],
           
            "annotations"=> [
                "origw"=> "400",
                "origh"=> "190",
                "autoscale"=> "1",
            ],
            "type"=>'hlineargauge',
            "dataFormat"=> "json",
            "renderAt"=> 'chart-container',
        ];
        $params['graph'] = $dataSource;
        $params['status'] = $condition_status;
        return $params;

    }

    public function comfort_humid()
    {
        
        $indoor_hum = sensor_status::find(18)->sensor_value;
        $indoor_hum = round($indoor_hum,2);
        $dataSource= [
            "chart"=> [
                "theme"=> "fusion",
                "lowerLimit"=> "20",
                "upperLimit"=> "100",
                "type"=>"vlinear",
                "numberSuffix"=> "%",
                "chartBottomMargin"=> "40",
                "valueFontSize"=> "11",
                "valueFontBold"=> "0",
                "bgcolor"=> "#171D30",
                "baseFontColor"=> "#FFFFFF"
            ],
            "colorRange"=> [
                "color"=> [
                    [
                        "minValue"=> "20",
                        "maxValue"=> "40",
                        "label"=> "Dry",
                        "code"=> "#ED7056"
                    ],
                    [
                        "minValue"=> "40",
                        "maxValue"=> "70",
                        "label"=> "Comfort",
                        "code"=> "#95B737"
                    ],
                    [
                        "minValue"=> "70",
                        "maxValue"=> "100",
                        "label"=> "Wet",
                        "code"=> "#2F75BD"
                    ]
                ]
            ],
            "pointers"=> [
                "pointer"=> [
                    
                    [
                        "bgColor"=> "#FFFFFF",
                        "value"=> $indoor_hum
                    ]
                ]
            ],
           
            "annotations"=> [
                "origw"=> "400",
                "origh"=> "190",
                "autoscale"=> "1",
            ],
            "type"=>'hlineargauge',
            "dataFormat"=> "json",
            "renderAt"=> 'chart-container',
        ];
        return $dataSource;
    }
    public function comfort_temp()
    {

        $indoor_temp = sensor_status::find(16)->sensor_value;
        $indoor_temp = round($indoor_temp,2);
        $dataSource= [
            "chart"=> [
                "theme"=> "fusion",
                "lowerLimit"=> "10",
                "upperLimit"=> "45",
                "numberSuffix"=> "°C",
                "chartBottomMargin"=> "40",
                "valueFontSize"=> "11",
                "valueFontBold"=> "0",
                "bgcolor"=> "#171D30",
                "baseFontColor"=> "#FFFFFF"
            ],
            "colorRange"=> [
                "color"=> [
                    [
                        "minValue"=> "10",
                        "maxValue"=> "22",
                        "label"=> "Cold",
                        "code"=> "#2F75BD"
                    ],
                    [
                        "minValue"=> "22",
                        "maxValue"=> "29",
                        "label"=> "Comfort",
                        "code"=> "#95B737"
                    ],
                    [
                        "minValue"=> "30",
                        "maxValue"=> "37",
                        "label"=> "Hot",
                        "code"=> "#ED7056"
                    ]
                ]
            ],
            "pointers"=> [
                "pointer"=> [
                    
                    [
                        "bgColor"=> "#FFFFFF",
                        "value"=> $indoor_temp
                    ]
                ]
            ],
           
            "annotations"=> [
                "origw"=> "400",
                "origh"=> "190",
                "autoscale"=> "1",
            ],
            "type"=>'hlineargauge',
            "dataFormat"=> "json",
            "renderAt"=> 'chart-container',
        ];
        return $dataSource;
    }

    public function room_predict_3($room_id){
        
        $input = request()->all();
        $getuserinfo = Helpler::Userinfofromtoken($input['token']);
        $userid = $getuserinfo['sub'];
        $user_id = users::where('line_id',$userid)->first()->id;

        $temp_id = devicelist::where('user_id',$user_id)->where('location',$room_id)->where('usage',1)->where('type',"temp")->get();
        $humid_id = devicelist::where('user_id',$user_id)->where('location',$room_id)->where('usage',1)->where('type',"humid")->get();
        foreach($temp_id as $each){
            $temp_val = sensor_status::where('device_id',$each->id)->where('unit','°C')->first()->sensor_value;
        }
        
        foreach($humid_id as $each2){
            $humid_val = sensor_status::where('device_id',$each2->id)->where('unit','%')->first()->sensor_value;
        }

        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.gadgetbuck.com']);
        $input = request()->all();
        $return = [];

        
        $return[0]['temp'] = $temp_val;
        $return[0]['humi'] = $humid_val;
    
  
        $res = $client->post('https://www.gadgetbuck.com/flask/modelthree', ['json'=>$return]);
        $pred = $res->getBody();
        $prediction = (json_decode($pred)->prediction);
        

        if($prediction[0] <= 29){
            $condition_status = "Comfort";
        }
        else if($prediction[0]>29 && $prediction[0]<=38){
            $condition_status = "Some Discomfort";
        }
        else if($prediction[0]>38 && $prediction[0]<=45){
            $condition_status = "Great discomfort avoid exertion";
        }
        else if($prediction[0]>45 && $prediction[0]<=54){
            $condition_status = "Dangerous";
        }
        else if($prediction[0]>54){
            $condition_status = "Heat Stroke immonent";
        }
        $dataSource= [
            "chart"=> [
                "theme"=> "fusion",
                "lowerLimit"=> "22",
                "upperLimit"=> "60",
                "type"=>"hlinear",
                "numberSuffix"=> "",
                "bgcolor"=> "#171D30",
                "baseFontColor"=> "#FFFFFF"
            ],
            "colorRange"=> [
                "color"=> [
                    [
                        "minValue"=> "22",
                        "maxValue"=> "29",
                        // "label"=> "Dry",
                        "code"=> "#00BFFF"
                    ],
                    [
                        "minValue"=> "30",
                        "maxValue"=> "38",
                        // "label"=> "Comfort",
                        "code"=> "#95B737"
                    ],
                    [
                        "minValue"=> "39",
                        "maxValue"=> "45",
                        // "label"=> "Wet",
                        "code"=> "#FFFF66"
                    ],
                    [
                        "minValue"=> "46",
                        "maxValue"=> "54",
                        // "label"=> "Wet",
                        "code"=> "#FFA500"
                    ],
                    [
                        "minValue"=> "55",
                        "maxValue"=> "60",
                        // "label"=> "Wet",
                        "code"=> "#B22222"
                    ]
                ]
            ],
            "pointers"=> [
                "pointer"=> [
                    
                    [
                        "bgColor"=> "#FFFFFF",
                        "value"=> $prediction[0]
                    ]
                ]
            ],
           
            "annotations"=> [
                "origw"=> "400",
                "origh"=> "190",
                "autoscale"=> "1",
            ],
            "type"=>'hlineargauge',
            "dataFormat"=> "json",
            "renderAt"=> 'chart-container',
        ];
        $params['graph'] = $dataSource;
        $params['status'] = $condition_status;
        return $params;

    }
}
