<?php
    $calcRepeatCount = 0;

    function run($method, $data, $formData){
        if($method == 'GET') error(404, "API Request not found");
        if($method == 'POST'){
            if($data[0] == 'json'){
                $body = getJsonBodyData();
            } else 
            if($data[0] == 'xml'){
                $body = getXmlBodyData();
            } else 
            error(404, "API request not found");
            
            var_dump($body);

            if(!isset($body) || is_null($body) || $body == '') error(400, "Bad Request");

            foreach($body as $key => $value){
                if($key == "sum" || $key == "sub" || $key == "div" || $key == "multi"){
                    var_dump(calc($key, $value));
                } else {
                    error(404, "Bad Request");
                }
                
            }
        }
    }

    function getJsonBodyData(){
        return json_decode(file_get_contents('php://input'), true);
    }

    function getXmlBodyData(){
        $xml = simplexml_load_string(file_get_contents('php://input'), "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json,TRUE);
    }

    function calc($key, $array){
        global $calcRepeatCount;
        if($calcRepeatCount >= 9) error(400, "Bad Request: too many MATH actions (limit 10 actions)");
        
        if(!is_array($array)){
            error(404, "Bad Request");
        }

        foreach($array as $el){
            $val = 0;
            if(is_numeric($el)){
                $val = floatval($el);
            } else {
                $calcRepeatCount++;

                if(!is_array($el)){
                    error(404, "Bad Request");
                }

                if(key($el) == "sum" || key($el) == "sub" || key($el) == "div" || key($el) == "multi"){
                    $val = calc(key($el), $el[key($el)]);
                } else {
                    error(404, "Bad Request");
                }
            }

            if($key == "sum"){
                if(isset($result)){
                    $result += $val;
                } else {
                    $result = $val;
                }
            } else
            if($key == "sub"){
                if(isset($result)){
                    $result -= $val;
                } else {
                    $result = $val;
                }
            } else
            if($key == "multi"){
                if(isset($result)){
                    $result = $result * $val;
                } else {
                    $result = $val;
                }
            } else
            if($key == "div"){
                if($val == 0) error(400, "Bad Request: Division by zero");

                if(isset($result)){
                    $result = $result / $val;
                } else {
                    $result = $val;
                }
            }
        }
        
        return $result;
    }
?>