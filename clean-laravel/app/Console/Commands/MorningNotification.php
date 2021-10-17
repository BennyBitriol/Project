<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp;
use App\Models\Helpler;
use Illuminate\Support\Facades\DB;
use App\Models\sensor_data;
use App\Models\users;

class MorningNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morning:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Line Notification every morning';

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
        //พยากรณ์อากาศวันนี้
        $client = new GuzzleHttp\Client(['base_uri' => 'https://data.tmd.go.th/nwpapi/v1/forecast/location/daily/']);
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijc0ODBhY2YyMGJkOGNhODczYjdhZDg5ZjNjNTQ1ZTNlY2M4MWE0ZjY5ODQzOWY1NmNjMDMxOTRjNzE1MzE5NjIzMDVjNGE5MzQ0ZmY5Yzc4In0.eyJhdWQiOiIyIiwianRpIjoiNzQ4MGFjZjIwYmQ4Y2E4NzNiN2FkODlmM2M1NDVlM2VjYzgxYTRmNjk4NDM5ZjU2Y2MwMzE5NGM3MTUzMTk2MjMwNWM0YTkzNDRmZjljNzgiLCJpYXQiOjE2MTY5MDUyNTEsIm5iZiI6MTYxNjkwNTI1MSwiZXhwIjoxNjQ4NDQxMjUxLCJzdWIiOiIxMzM5Iiwic2NvcGVzIjpbXX0.kaBmj09Vmqatr2ophG8pDN2am202I6TpGqUe8HAqfvbysRbVRsvD1jbfmQgT4JoUu975mglIU2K1Z20LhbAU1xN0kveQQwIhmueEP0T5He2qoIWKZ6XX7QLXix-tt6JkWzlOafb_eq7Et9Mbsh5-7jPYldDv-mBEk064bgdY7jYLOnlKMG5Wq8D2mTrEZ46NzcAJQ4TkEsPXT2FSlr2m2tSzegmH4IbMLkSFTXjqKHSMulw1dySQsxylFwMMMFVgiicYFvo25G--of2uLvlD-LFe4E6GVkTbj7aohh2RW31KBoV16AWzEZWn92NZO6X9nfxdPEOsenHb_uCEztaOyrO8SyHa7T_7139wdJSLlI_pTOOpjm7DuAMDkpIjehszb75cg7705NCaaifMaUTexivsnRQ1Enij3JAbMnGtPtNAIEQy7Z3OYaS87SipdfL6VZPkBmxw59GTMMDk_eE2yVHSJLTwYlaeuF6z-l3TfFuVg7R7_H0MnHtOgScU8udmiky6Rr8odjhBG_MOrG2cldRLcK2HYl087wjNnYSBnwp9Z1fJ8dyeoFUpc5RmsMmIN4ZEt_PTHse5P6ZZcdrE3uC8zvIjR4F9pBjKYCRWI5B18Mc3K-A5VuGJz5nudWS4QOGBVLqXZYdhQ9pFOMlkKYPfYi7aMjMRE0qsb-z5QQA';
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
    	foreach($users as $each){
        
        
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
        $today_day = date("w", time());
        if($today_day == 0){
          $hello = 'สวัสดีวันอาทิตย์';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/1.jpg';
        }
        else if($today_day == 1){
          $hello = 'สวัสดีวันจันทร์';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/2.jpg';
        }
        else if($today_day == 2){
          $hello = 'สวัสดีวันอังคาร';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/3.jpg';
        }
        else if($today_day == 3){
          $hello = 'สวัสดีวันพุธ';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/4.jpg';
        }
        else if($today_day == 4){
          $hello = 'สวัสดีวันพฤหัสบดี';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/5.jpg';
        }
        else if($today_day == 5){
          $hello = 'สวัสดีวันศุกร์';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/6.jpg';
        }
        else if($today_day == 6){
          $hello = 'สวัสดีวันเสาร์';
          $url = 'https://www.gadgetbuck.com/img/icons/dayspic/7.jpg';
        }

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
                  'altText'=> $hello,
                  'contents'=> [
            'type'=> 'bubble',
            'hero'=> [
              'type'=> 'image',
              'url'=> $url,//https://thestandard.co/wp-content/uploads/2019/03/IMG_1471.jpg?x94218
              'aspectMode'=> 'fit',
              'aspectRatio'=> '3:2',
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
    }
  }
}
