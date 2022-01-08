<?php
require "db_connect.php";

class Storage{
    public static ?AcademicYear $currentYear = null;
    public static $workableDays = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"];
    private static $db;
    private static $tokens = [];

    public static function getToken(){
        $token = "";
        $max = random_int(0,32);
        $abc = ['a','b','c','e','f','0','1','2','3','4','5','6','7','8','9','_','@','&'];
        do {
            $token = "";
            for ($i = 0; $i < $max; $i++) {
                if (random_int(0, 2) == 0) {
                    $token .= strtoupper($abc[random_int(0, count($abc) - 1)]);
                } else {
                    $token .= strtolower($abc[random_int(0, count($abc) - 1)]);
                }
            }
        }while(CheckIf::inArray($token, self::fetchTokens()));
        return $token;
    }

    public static function initBase(){
        AcademicYear::fetchAll();
        Faculty::fetchAll();
        Hierarchy::fetchAll();
        Grade::fetchAll();
        Teacher::fetchAll();
        Course::fetchAll();
        Student::fetchAll();
        Notes::fetchAll();
        User::fetchAll();
    }

    public static function Connect(){
        try{
            if(self::$db == null) {
                $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                self::$db = new PDO('mysql:host=' . HOST . ';dbname=' . DATABASE, USER, PASSWORD,
                    $pdo_options);
            }
        }
        catch(Exception $e){
            \System\Log::printStackTrace($e);
            die;
        }
        return self::$db;
    }

    private static function fetchTokens($override = false){
        if($override && count(self::$tokens) > 0) return self::$tokens;
        self::$tokens = [];
        $db = self::Connect();
        $chk = $db->prepare("select user,token from sessiontrace");
        try{
            $chk->execute();
            while($d = $chk->fetch()){
                self::$tokens[$d["user"]] = $d["token"];
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return self::$tokens;
    }

    public static function updateTokens(){
        self::fetchTokens(true);
    }

    public static function userExists(User $e){
        $r = false;
        $db = self::Connect();
        $chk = $db->prepare("select * from sessiontrace where user=:p1 and token is not NULL");
        try{
            $chk->execute(['p1'=>$e->getId()]);
            if($chk->rowCount()){
                $r = true;
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function userIdExists(int $id){
        return isset(self::fetchTokens()[$id]);
    }

    public static function tokenExists(string $token){
        $r = false;
        foreach (self::fetchTokens() as $_token){
            if($token == $_token){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function getTokenUser(string $token){
        $usr = null;
        foreach (self::fetchTokens() as $userid => $_token){
            if($token == $_token){
                $usr = User::getById($userid);
                break;
            }
        }
        return $usr;
    }

    public static function tokenUserMatch(int $uid, string $token){
        if(!isset(self::fetchTokens()[$uid])) return false;
        return self::fetchTokens()[$uid] == $token;
    }

    public static function addUser(User $e, string $token, bool $permanent = false){
        $chk = null;
        $db = self::Connect();
        $data = [];
        $online = $e->isOnline();
        $r = false;
        if(!self::userExists($e)){
            $chk = $db->prepare("insert into sessiontrace(user, token, last_seen, permanent, online) values(:p1,:p2,NOW(),:p3,1)");
            $data["p3"] = $permanent ? 1 : 0;
        }
        else{
            $chk = $db->prepare("update sessiontrace set token=:p2, last_seen=NOW(), online=1 ".(!$online ? ", permanent=:p3" : "")." where user=:p1");
            if(!$online){
                $data["p3"] = $permanent ? 1 : 0;
            }
        }
        $data["p1"] = $e->getId();
        $data["p2"] = $token;
        try{
            $chk->execute($data);
            $r =  true;
            \System\Log::println("[ connection ] ".json_encode([
                "user"=>$e->getId(),
                "name"=>$e->getFullName(),
                "token"=> $token,
                "ip"=> Ressource::clientIP()
            ]));
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function removeUser(User $e, string $token){
        if(self::userExists($e)){
            $db = self::Connect();
            $chk = $db->prepare("delete from sessiontrace where user=:p1");
            try{
                $chk->execute(['p1'=>$e->getId()]);
                \System\Log::println("[ removesession ] ".json_encode([
                    "user"=>$e->getId(),
                    "token"=> $token,
                    "name"=>$e->getFullName(),
                    "ip"=> Ressource::clientIP()
                ]));
            }catch(Exception $e){\System\Log::printStackTrace($e);}
            $chk->closeCursor();
        }
    }

    public static function disconnectUser(User $e){
        if(self::userExists($e)){
            $db = self::Connect();
            $chk = $db->prepare("update sessiontrace set online = 1 where user=:p1");
            try{
                $chk->execute(['p1'=>$e->getId()]);
                \System\Log::println("[ removesession ] ".json_encode([
                    "user"=>$e->getId(),
                    "name"=>$e->getFullName(),
                    "ip"=> Ressource::clientIP()
                ]));
            }catch(Exception $e){\System\Log::printStackTrace($e);}
            $chk->closeCursor();
        }
    }

    public static function update(Data $class){
        $name = (new ReflectionObject($class))->getName();
        System\Log::println("[Reflection] ".$name);
        switch ($name){
            case "Faculty":
                Faculty::fetchAll(true);
                Grade::fetchAll(true);
                break;
            case "AcademicYear":
                AcademicYear::fetchAll(true);
                Faculty::fetchAll(true);
                break;
            case "Grade":
                Grade::fetchAll(true);
                Faculty::fetchAll(true);
                break;
            case "Course":
                Course::fetchAll(true);
                Faculty::fetchAll(true);
                Teacher::fetchAll(true);
                break;
            case "Hierarchy":
                Hierarchy::fetchAll(true);
                break;
            case "Notes":
                Notes::fetchAll(true);
                Faculty::fetchAll(true);
                Course::fetchAll(true);
                Student::fetchAll(true);
                break;
            case "Student":
                Student::fetchAll(true);
                Faculty::fetchAll(true);
                break;
            case "Teacher":
                Teacher::fetchAll(true);
                Course::fetchAll(true);
                Hierarchy::fetchAll(true);
                break;
            case "User":
                User::fetchAll(true);
                Hierarchy::fetchAll(true);
                Teacher::fetchAll(true);
                break;
        }
    }
}