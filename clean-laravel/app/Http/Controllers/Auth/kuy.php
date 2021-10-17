        $data = [
                "chartOptions" => [
                    "chart" => [          
                        "height"=> 280,              
                        "type" => "radialBar"
                        ],
                ],
                "series" => [
                  [
                        "data" => 67
                  ]
                ]
                ];
        return response($data, 200);