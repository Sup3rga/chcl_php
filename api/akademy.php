<?php
$response = [
    "error" => false,
    "code"=>0,
    "message"=>"Access denied !"
];
$_ENV;
if($get = CheckIf::isRequest($_GET, ['res', 'akatoken'])){
    extract($get);
    $response["error"] = false;
    $response["code"] = 1;
    $response["data"] = Ressource::data($res);
    $response["message"] = "Access granted !";
    $response["template"] = Ressource::get($res, $akatoken);
    echo json_encode($response, JSON_INVALID_UTF8_IGNORE);
}
else{
    echo Ressource::get();
}
