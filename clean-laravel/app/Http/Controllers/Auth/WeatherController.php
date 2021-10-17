<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Helpler;
use GuzzleHttp;


class WeatherController extends Controller
{
    public function Daily()
    {
        date_default_timezone_set("Asia/Bangkok");
    	$client = new GuzzleHttp\Client(['base_uri' => 'https://data.tmd.go.th/nwpapi/v1/forecast/location/daily/']);
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdkM2Y3Y2RkMWM5M2I2ZWVmMGNmNzcyYTUyZWFhMTE4Njk5MjM2MDdiMGU0M2Q4NGMzYzE4NTQyNTc2MDE1OTkyMzc4ZGYyY2QzMDAzNmIzIn0.eyJhdWQiOiIyIiwianRpIjoiN2QzZjdjZGQxYzkzYjZlZWYwY2Y3NzJhNTJlYWExMTg2OTkyMzYwN2IwZTQzZDg0YzNjMTg1NDI1NzYwMTU5OTIzNzhkZjJjZDMwMDM2YjMiLCJpYXQiOjE2MTY5MDUzMTgsIm5iZiI6MTYxNjkwNTMxOCwiZXhwIjoxNjQ4NDQxMzE4LCJzdWIiOiIxMzQ3Iiwic2NvcGVzIjpbXX0.A-jU-kjRht0sdfDlISwGID0WdW76f9K8pCDUF7D7VpUnEl2Qj-3w9X_jzZHJgrSSfeItvhD0RsbjM_mKOJ8DACFnqfECYREkmEhcIYd13p1yVxQ3zT_bTt59_BeMOS-tN9gSGdA65ur8oAT2Q1YcZS94OR6m4h8cGj0lG9kKh4kO53JgOQdV48_hh-MIFROhkDC2VUbrfqa7tbaudSQ14HLT2jkTB7JBFB7Aas7fBD1RY4UktxStYwLzNFamhf_mKooBV13mhV8svcZ7LMZz7CkfggcmlDU98_5c_BURXrOP1WbbqUhrloqIBxxerSOGiG9ddSLdXudbPm_zFK8aGX2qCmUDt3tiesKKCErh1LwHthSZfZuFDxacjKWqsRGcKGRGjziSD6zVYgeGLYFv3ZO3pFTxWIqvA_jPQwYvfkBUo9Sm-etN5z5jyC0ryXOJ7R3f1tZlLizJG7KeKouFRBxP4uZaxAx8GlRQNhgTIgjDh9eLtciKTICQhWU5-4G1LZ1Jq5qoyLlQ_G2nMqjwkYsvgPRK9R3vdf9P-qCeLdGFZOS1muQHJ6uwpWFIzD3nEYd5xgbuZ74NMybAdRK6qEY60_xhcmjt2HaieKIhUL_1YF0R9g6MoaGDO18A7NT2jKJgy1rUd4XiknjfvRSIvFpFIM1FtfbgMQY59DfC5To';
        $headers = [
		    'Authorization' => 'Bearer ' . $token,        
		    'Accept'        => 'application/json',
		];
        $response = $client->request('GET', 'at?lat=13.7523228&lon=100.5308764&fields=cond,tc,rh,tc_max,tc_min&duration=24', [
	        'headers' => $headers
	    ]);
	    $response = json_decode($response->getBody(),true);
        $forecasts = $response['WeatherForecasts'][0]['forecasts'];

        $i = 0;
        foreach($forecasts as $each){
            $cond = $forecasts[$i]['data']['cond'];

            $cond_data = Helpler::cond_data($cond);
            $forecasts[$i]['img'] = $cond_data['img'];
            $forecasts[$i]['condition_th'] = $cond_data['condition_th'];
            $forecasts[$i]['condition_en'] = $cond_data['condition_en'];
            $forecasts[$i]['day'] = date("l",strtotime($each['time'])); //+109760
            $forecasts[$i]['cond'] = $cond;
            $forecasts[$i]['rh'] = $forecasts[$i]['data']['rh'];
            $forecasts[$i]['tc'] = $forecasts[$i]['data']['tc'];
            $forecasts[$i]['tc_max'] = round($forecasts[$i]['data']['tc_max'],0);
            $forecasts[$i]['tc_min'] = round($forecasts[$i]['data']['tc_min'],0);
            $i++;
        }

	    return $forecasts;
    } 
    public function Hourly()
    {
        date_default_timezone_set("Asia/Bangkok");
        $client = new GuzzleHttp\Client(['base_uri' => 'https://data.tmd.go.th/nwpapi/v1/forecast/location/hourly/']);
        // $url = 'https://data.tmd.go.th/nwpapi/v1/forecast/location/hourly/at?lat=13.7523228&lon=100.5308764&fields=tc,rh&duration=10';
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdkM2Y3Y2RkMWM5M2I2ZWVmMGNmNzcyYTUyZWFhMTE4Njk5MjM2MDdiMGU0M2Q4NGMzYzE4NTQyNTc2MDE1OTkyMzc4ZGYyY2QzMDAzNmIzIn0.eyJhdWQiOiIyIiwianRpIjoiN2QzZjdjZGQxYzkzYjZlZWYwY2Y3NzJhNTJlYWExMTg2OTkyMzYwN2IwZTQzZDg0YzNjMTg1NDI1NzYwMTU5OTIzNzhkZjJjZDMwMDM2YjMiLCJpYXQiOjE2MTY5MDUzMTgsIm5iZiI6MTYxNjkwNTMxOCwiZXhwIjoxNjQ4NDQxMzE4LCJzdWIiOiIxMzQ3Iiwic2NvcGVzIjpbXX0.A-jU-kjRht0sdfDlISwGID0WdW76f9K8pCDUF7D7VpUnEl2Qj-3w9X_jzZHJgrSSfeItvhD0RsbjM_mKOJ8DACFnqfECYREkmEhcIYd13p1yVxQ3zT_bTt59_BeMOS-tN9gSGdA65ur8oAT2Q1YcZS94OR6m4h8cGj0lG9kKh4kO53JgOQdV48_hh-MIFROhkDC2VUbrfqa7tbaudSQ14HLT2jkTB7JBFB7Aas7fBD1RY4UktxStYwLzNFamhf_mKooBV13mhV8svcZ7LMZz7CkfggcmlDU98_5c_BURXrOP1WbbqUhrloqIBxxerSOGiG9ddSLdXudbPm_zFK8aGX2qCmUDt3tiesKKCErh1LwHthSZfZuFDxacjKWqsRGcKGRGjziSD6zVYgeGLYFv3ZO3pFTxWIqvA_jPQwYvfkBUo9Sm-etN5z5jyC0ryXOJ7R3f1tZlLizJG7KeKouFRBxP4uZaxAx8GlRQNhgTIgjDh9eLtciKTICQhWU5-4G1LZ1Jq5qoyLlQ_G2nMqjwkYsvgPRK9R3vdf9P-qCeLdGFZOS1muQHJ6uwpWFIzD3nEYd5xgbuZ74NMybAdRK6qEY60_xhcmjt2HaieKIhUL_1YF0R9g6MoaGDO18A7NT2jKJgy1rUd4XiknjfvRSIvFpFIM1FtfbgMQY59DfC5To';
        
        $headers = [
		    'Authorization' => 'Bearer ' . $token,        
		    'Accept'        => 'application/json',
		];
        
        $response = $client->request('GET', 'at?lat=13.7523228&lon=100.5308764&fields=cond,tc,rh&duration=24', [
	        'headers' => $headers
	    ]);

	    $response = json_decode($response->getBody(),true);
        $forecasts = $response['WeatherForecasts'][0]['forecasts'];
        $i = 0;
        foreach($forecasts as $each){
            $cond = $forecasts[$i]['data']['cond'];

            $cond_data = Helpler::cond_data($cond);
            $forecasts[$i]['img'] = $cond_data['img'];
            $forecasts[$i]['condition_th'] = $cond_data['condition_th'];
            $forecasts[$i]['condition_en'] = $cond_data['condition_en'];
            $forecasts[$i]['day'] = date("H:00",strtotime($each['time'])); //+109760
            $forecasts[$i]['cond'] = $cond;
            $forecasts[$i]['rh'] = round($forecasts[$i]['data']['rh'],0);
            $forecasts[$i]['tc'] = round($forecasts[$i]['data']['tc'],1);
            $i++;
        }
        return $forecasts;
    } 
}
