<?php

main();

function main(){
    $method = null;
    $formData = null;
    $apiRequsts = array('calc');
    init($method, $formData);

    if(is_null($method)) error(500, "Can't reject the REST method");
    if(is_null($formData)) error(500, "Can't reject form data");

    $url = (isset($_GET['q'])) ? $_GET['q'] : '';
    $url = rtrim($url, '/');
    $routes = explode('/', $url);

    $fileName = $routes[0];
    $data = array_slice($routes, 1);

    if(in_array($fileName, $apiRequsts)){
        include_once 'api/' . $fileName . '.php';
        run($method, $data, $formData);
    } else {
        error(404, "API Request not found");
    }
}

function init(&$method, &$formData){
    $method = $_SERVER['REQUEST_METHOD'];
    $formData = getFormData($method);
}

function getFormData($method) {
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;
 
    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));
 
    foreach($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }
 
    return $data;
}

function error($code, $description){
    header('HTTP/1.0 ' . $code . ' ' . $description);
    echo json_encode(array(
        'error' => $description
    ));
    die();
}

?>