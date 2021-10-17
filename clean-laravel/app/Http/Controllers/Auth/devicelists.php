<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\devicelist;
use GuzzleHttp;
use Illuminate\Support\Facades\Http;

class devicelists extends Controller
{
    public function devicelist(){
        return devicelist::all();
    }

    public function devicestatus($id){

        $client = new GuzzleHttp\Client();
        $res = $client->get('http://www.gadgetbuck.com:1880/check?device_id='.$id);
        $devicelist = devicelist::find($id);
        return $devicelist->switch_status;
    }
    public function devicelistroom($room_id,$type,$air){
        if($air == 'air'){
            $db = devicelist::where('location', $room_id )->where('devicetype', $air)->get();
            $count = count($db);
            if($count == 0 ){
                $airs = devicelist::where('id',10)->where('type', 'device')->get();
                $xxx = collect([]);
                
                foreach($airs as $each1){
                    if($each1->switch_status == "true"){
                        $each1->switch_status = true;
                        $each1->bg_variant = 'warning';
                        $each1->bt_variant = 'dark';
                    }else{
                        $each1->switch_status = false;
                        $each1->bg_variant = 'transparent';
                        $each1->bt_variant = 'outline-primary';
                    }
                    $xxx->push($each1); 
                }
                
                return $xxx;
            }
            else{
                foreach($db as $each){
                    if($each->switch_status == "true"){
                        $each->switch_status = true;
                        $each->bg_variant = 'warning';
                        $each->bt_variant = 'dark';
                    }else{
                        $each->switch_status = false;
                        $each->bg_variant = 'transparent';
                        $each->bt_variant = 'outline-primary';
                    }
                }
                return $db;
            }
        }
        else{
            $db = devicelist::where('location', $room_id )->where('type', $type)->get();
            $count = count($db);
            if($count == 0 ){
                $air = devicelist::where('id',10)->where('type', $type)->get();
                $xxx = collect([]);
                
                foreach($air as $each1){
                    if($each1->switch_status == "true"){
                        $each1->switch_status = true;
                        $each1->bg_variant = 'warning';
                        $each1->bt_variant = 'dark';
                    }else{
                        $each1->switch_status = false;
                        $each1->bg_variant = 'transparent';
                        $each1->bt_variant = 'outline-primary';
                    }
                    $xxx->push($each1); 
                }
                $light = devicelist::where('id',8)->where('type',$type)->get();
                foreach($light as $each2){
                    if($each2->switch_status == "true"){
                        $each2->switch_status = true;
                        $each2->bg_variant = 'warning';
                        $each2->bt_variant = 'dark';
                    }else{
                        $each2->switch_status = false;
                        $each2->bg_variant = 'transparent';
                        $each2->bt_variant = 'outline-primary';
                    }
                    $xxx->push($each2); 
                }
                return $xxx;
            }
            else{
                foreach($db as $each){
                    if($each->switch_status == "true"){
                        $each->switch_status = true;
                        $each->bg_variant = 'warning';
                        $each->bt_variant = 'dark';
                    }else{
                        $each->switch_status = false;
                        $each->bg_variant = 'transparent';
                        $each->bt_variant = 'outline-primary';
                    }
                }
                return $db;
            }
        }

        
    }
    public function changedevicevalue($id,$value){ //// สั่งเปิดปิด
        //http://www.gadgetbuck.com:1880/control?device_id=8&command=on
        // if($id == 10){
        //     $update = devicelist::where('id', $id)->update(['switch_status' => $value]); /// ออกเป็น 1
            
        //     if($value == "true"){
        //         $temp = 'on';
        //     }else{
        //         $temp = 'off';
        //     }

        //     $client = new GuzzleHttp\Client();
        //     $res = $client->get('http://www.gadgetbuck.com:1880/control?device_id='.$id.'&command='.$temp);

        //     $db = devicelist::where('id',10)->get();
            
            
            
        //     foreach($db as $each){
        //         if($each->switch_status == "true"){
        //             $each->switch_status = true;
        //             $each->bg_variant = 'warning';
        //         }else{
        //             $each->switch_status = false;
        //             $each->bg_variant = 'transparent';
        //         }
        //     }
            
        //     return $db;
        // }
        // else if($id == 8){
        //     $update = devicelist::where('id', $id)->update(['switch_status' => $value]); /// ออกเป็น 1
            
        //     if($value == "true"){
        //         $temp = 'on';
        //     }else{
        //         $temp = 'off';
        //     }

        //     $client = new GuzzleHttp\Client();
        //     $res = $client->get('http://www.gadgetbuck.com:1880/control?device_id='.$id.'&command='.$temp);

        //     $db = devicelist::where('id',8)->get();
            
            
            
        //     foreach($db as $each){
        //         if($each->switch_status == "true"){
        //             $each->switch_status = true;
        //             $each->bg_variant = 'warning';
        //         }else{
        //             $each->switch_status = false;
        //             $each->bg_variant = 'transparent';
        //         }
        //     }
            
        //     return $db;
        // }
        // else
        // {
            $update = devicelist::where('id', $id)->update(['switch_status' => $value]); /// ออกเป็น 1
            
            if($value == "true"){
                $temp = 'on';
            }else{
                $temp = 'off';
            }

            $client = new GuzzleHttp\Client();
            $res = $client->get('http://www.gadgetbuck.com:1880/control?device_id='.$id.'&command='.$temp);

            $devicelist = devicelist::find($id);
            $location = $devicelist->location;
            $type = $devicelist->type;
            $user_id =  $devicelist->user_id;

            $db = devicelist::where('location', $location )->where('user_id', $user_id)->where('type',$type)->get();
            
            foreach($db as $each){
                if($each->switch_status == "true"){
                    $each->switch_status = true;
                    $each->bg_variant = 'warning';
                }else{
                    $each->switch_status = false;
                    $each->bg_variant = 'transparent';
                }
            }
            
            return $db;
    // }
    }
 
    public function remoteon($location,$value){
            $update = devicelist::where('location', $location)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
            
            if($update == 0){
                $update = devicelist::where('location', 17)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
                
                if($value == "true" || $value == true){
                    Http::get('https://maker.ifttt.com/trigger/remote_on/with/key/bsaE-IuTs1EaBpHrUVic5I');
                }
                
                
                
                $db = devicelist::where('location', 17)->where('devicetype','air')->get();
                
                
                // $db = devicelist::where('location', $location )->where('user_id', $user_id)->get();
                
                foreach($db as $each){
                    if($each->switch_status == "true"){
                        $each->switch_status = true;
                        $each->bg_variant = 'warning';
                    }else{
                        $each->switch_status = false;
                        $each->bg_variant = 'transparent';
                    }
                }
                
                return $db;
            }
            else{
                $update = devicelist::where('location', $location)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
            
                if($value == "true" || $value == true){
                    Http::get('https://maker.ifttt.com/trigger/remote_on/with/key/bsaE-IuTs1EaBpHrUVic5I');
                }
                
                
                $db = devicelist::where('location', $location)->where('devicetype','air')->get();
    
                
                // $db = devicelist::where('location', $location )->where('user_id', $user_id)->get();
                
                foreach($db as $each){
                    if($each->switch_status == "true"){
                        $each->switch_status = true;
                        $each->bg_variant = 'warning';
                    }else{
                        $each->switch_status = false;
                        $each->bg_variant = 'transparent';
                    }
                }
                
                return $db;
            }
           
    }

    public function remoteoff($location,$value){
        if($value == true || $value == 'true'){
            $value = 'false';
        }
        $update = devicelist::where('location', $location)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
        if($update == 0){
            
            $update = devicelist::where('location', 17)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
            
            if($value == "false" || $value == false){
                Http::get('https://maker.ifttt.com/trigger/remote_off/with/key/bsaE-IuTs1EaBpHrUVic5I');
            }
            
            
            $db = devicelist::where('location', 17)->where('devicetype','air')->get();
            
            
            // $db = devicelist::where('location', $location )->where('user_id', $user_id)->get();
            
            foreach($db as $each){
                if($each->switch_status == "true"){
                    $each->switch_status = true;
                    $each->bg_variant = 'warning';
                }else{
                    $each->switch_status = false;
                    $each->bg_variant = 'transparent';
                }
            }
            
            return $db;
        }
        else{
            $update = devicelist::where('location', $location)->where('devicetype','air')->update(['switch_status' => $value]); /// ออกเป็น 1
            
            if($value == "false" || $value == false){
                Http::get('https://maker.ifttt.com/trigger/remote_off/with/key/bsaE-IuTs1EaBpHrUVic5I');
            }
 
            $db = devicelist::where('location', $location)->where('devicetype','air')->get();

            
            // $db = devicelist::where('location', $location )->where('user_id', $user_id)->get();
            
            foreach($db as $each){
                if($each->switch_status == "true"){
                    $each->switch_status = true;
                    $each->bg_variant = 'warning';
                }else{
                    $each->switch_status = false;
                    $each->bg_variant = 'transparent';
                }
            }
            
            return $db;
        }
       
}

    // public function linechangedevicevalue($id,$value){ //// สั่งเปิดปิด
    //     //http://www.gadgetbuck.com:1880/control?device_id=8&command=on

    //     $update = devicelist::where('id', $id)->update(['switch_status' => $value]); /// ออกเป็น 1
        
    //     if($value == "true"){
    //         $temp = 'on';
    //     }else{
    //         $temp = 'off';
    //     }

    //     $client = new GuzzleHttp\Client();
    //     $res = $client->get('http://www.gadgetbuck.com:1880/control?device_id='.$id.'&command='.$temp);

    //     $devicelist = devicelist::find($id);
    //     $location = $devicelist->location;
    //     $type = $devicelist->type;

    //     $db = devicelist::where('location', $location )->where('type', $type)->get();

    //     foreach($db as $each){
    //         if($each->switch_status == "true"){
    //             $each->switch_status = true;
    //             $each->bg_variant = 'warning';
    //         }else{
    //             $each->switch_status = false;
    //             $each->bg_variant = 'transparent';
    //         }
    //     }
    //     if($each->switch_status == false){
    //         $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
    //         $response = $res->request('POST', 'multicast', [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
    //         ],
    //         'json' => [
    //             "to"=> ["Uc5ba53e4db1414cb8bb381d2c8a26024", "U0b20bf22d6f2264ad12132613f81ff5f", "Uda4196f196c788bd72519e90e7dc7c93"],
    //             "messages"=> [
    //             [
    //                 "type"=> "text",
    //                 "text"=> "ปิดอุปกรณ์แล้ว"
    //             ]
    //             ]
    //         ]
    //         ]);
    //     }
    //     return redirect('https://lin.ee/qhBKet1');
    // }
}

