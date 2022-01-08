<?php
$response = [
    "error" => false,
    "code"=>0,
    "message"=>"Access denied !"
];

//@GET
if($get = CheckIf::isRequest($_GET, ['akatoken', 'akauid'])){
    extract($get);
    $response["message"] = "Violation error !";
    //token verification
    if($akatoken != null && $akauid != null){
        $response["error"] = false;
        try{
            if(Storage::userIdExists($akauid)){
                $akatoken = $_GET['akatoken'];
                $exist = Storage::tokenUserMatch($akauid, $akatoken);
                $response["code"] = $exist ? 1 : 2;
                $response["akatoken"] = $akatoken;
                $response["message"] = $exist ? "Ok" : "Expired session ! Please reconnect your account.";
            }
        }catch (Exception $e){
            \System\Log::printStackTrace($e);
            $response["message"] = "invalid request !";
        }
    }
}


//@POST
if($post = CheckIf::isRequest($_POST, ['username', 'passcode'])){
    extract($post);
    $usr = null;
    $ok = true;
    try{
        $usr  = User::isMatch($username, $passcode);
    }catch(Exception $e){
        $response["message"] = $e->getMessage();
        $ok = false;
    }
    if($ok) {
        $userData = [];
        $response["error"] = false;
        if ($usr != null) {
            try {
                $userData = $usr->data();
                $token = Storage::getToken();
                Storage::addUser($usr, $token);
                $userData["token"] = $token;
            } catch (Exception $e) {
                \System\Log::printStackTrace($e);
            }
        }
        $response["data"] = $userData;
        $response["code"] = $usr == null ? 0 : 1;
        $response["message"] = $usr != null ? "success" : "invalid username or passcode !";
    }
}
else if($post = CheckIf::isRequest($_POST, ['uid', 'disconnect'])){
    extract($post);
    $e = Storage::getTokenUser($disconnect);
    if($e != null && $e->getId() == (int) $uid){
        Storage::removeUser($e,$disconnect);
        $response["error"] = false;
        $response["code"] = 0;
        $response["message"] = "GoodBye !";
    }
}

echo json_encode($response, JSON_INVALID_UTF8_IGNORE);