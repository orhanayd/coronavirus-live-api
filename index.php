<?php
    date_default_timezone_set('Europe/Istanbul');
    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');
    
    include("env.php");

    function sort_data($data, $orderby){
        global $main_response;
        $sort_list=[
            'total',
            'death',
            'recovered'
        ];

        if(in_array($orderby, $sort_list)){
            $sortArray = array();
            foreach($data as $person){ 
                foreach($person as $key=>$value){ 
                    if(!isset($sortArray[$key])){ 
                        $sortArray[$key] = array(); 
                    } 
                    $sortArray[$key][] = $value; 
                } 
            }
            array_multisort($sortArray[$orderby], SORT_DESC, $data);
            $main_response['desc'] = "Sort Ok";
        }

        return $data;
    }

    
    $main_response = array(
        "status" => false,
        "desc" => "",
        "result" => array(
            "total"=>0,
            "death"=>0,
            "recovered"=>0,
            "data"=>[]
        )
    );
    $data = file_get_contents($_ENV["api_url" ]);

    if($data){
        $data = json_decode($data);

        if(isset($_GET['sort'])){
            $data =sort_data($data, strip_tags($_GET['sort']));
        }

        $main_response['result']['data'] = $data;

        foreach($data as $case){
            $main_response['result']['total'] = $main_response['result']['total']+$case->total;
            $main_response['result']['death'] = $main_response['result']['death']+$case->death;
            $main_response['result']['recovered'] = $main_response['result']['recovered']+$case->recovered;
        }

        $main_response['status'] = true;

        if($main_response['desc'] === ""){
            $main_response['desc'] = "OK";
        }

    }else{
        $main_response['desc'] = "Please try again later";
    }

    echo json_encode($main_response);
?>