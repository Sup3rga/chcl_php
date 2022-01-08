<?php

class Ressource{

    private static String $basePath = "../";

    private static $ressource = [
        "." => ["akademy/index.html", -1],
        "/" => ["akademy/view/dashboard.html", 0],
        "/academic-year" => ["akademy/view/anneeAcademique.html", 1],
        "/student" => ["akademy/view/etudiant.html",2],
        "/course" => ["akademy/view/cours.html",3],
        "/teacher" => ["akademy/view/professeur.html",4],
        "/admin" => ["akademy/view/administration.html",5],
        "/users" => ["akademy/view/utilisateurs.html",6],
        "/notes" => ["akademy/view/notes.html",7]
    ];
    private static $moduleIndexes = [
        1 => [10,11],
        2 => [12,13,14,15,16],
        3 => [17,18,19,20,21,22],
        4 => [23,24,25,26,27],
        5 => [28,29,30,31,32,33,34,35,36],
        6 => [37,38,39,40],
        7 => [41,42,43],
        8 => [44]
    ];

    public static function data(string $res, bool $serialize = true){
        $e = [];
        \System\Log::println("[ ressource ] ".json_encode([
            "ip"=> Ressource::clientIP(),
            "ressource"=>$res
        ]));
        switch ($res){
            case "/admin":
                $e["faculty"] = Faculty::fetchAll();
                $e["hierarchy"] = Hierarchy::fetchAll();
                break;
            case "/teacher":
                $e["teacher"] = Teacher::fetchAll();
                $e["hierarchy"] = Hierarchy::fetchAll();
                break;
            case "/student":
                $e["student"] =  Student::fetchAll();
                $e["faculty"] = Faculty::fetchAll();
                break;
            case "/course":
                $e["faculty"] = Faculty::fetchAll();
                $e["teacher"] = Teacher::fetchAll();
                $e["course"] = Course::fetchAll();
                $e["workDays"] = Storage::$workableDays;
                break;
            case "/notes":
                $e["course"] = Course::fetchAll();
                $e["faculty"] = Faculty::fetchAll();
                $e["student"] =  Student::fetchAll();
                $e["notes"] = Notes::fetchAll();
                break;
            case "/users":
                $e["hierarchy"] = Hierarchy::fetchAll();
                $e["modules"] = self::$moduleIndexes;
                $e["teacher"] = Teacher::fetchAll();
                break;
        }
        $e["currentYear"] = Storage::$currentYear == null ? null : Storage::$currentYear->data();
        $e["academic"] = AcademicYear::fetchAll();
        $e["dashboard"] = new Summary(Storage::$currentYear);
        $e["users"] = User::fetchAll();
        $e["ccc"] = self::getCCC();
        return $serialize ? self::serialize($e) : $e;
    }

    public static function stringify(array $val){
        $r = "{";
        foreach ($val as $k => $v){
            if(is_object($v)){
                $v = (string) $v;
            }
            else if(is_array($v)){
                $v = self::stringify($v);
            }
            else if(is_numeric($v) || is_bool($v)){
                $v = is_numeric($v) ? $v : ($v ? "true" : "false");
            }
            else{
                $v = '"'.preg_replace("/\"/",'\\"',$v).'"';
            }
            $r .= (strlen($r) > 1 ? "," : "").(is_numeric($k) ? $k : '"'.$k.'"').":".$v;
        }
        $r .= "}";
        return $r;
    }

    public static function serialize(array $data, bool $toString = false){
        $r = [];
        foreach ($data as $k => $v){
            if(is_array($v)){
                $r[$k] = self::serialize($v);
            }
            else if(is_object($v)){
                $r[$k] = (new ReflectionObject($v))->hasMethod("data") ? $v->data() : (string) $v;
            }
            else{
                $r[$k] = $v;
            }
        }
        return $toString ? json_encode($r) : $r;
    }

    public static function clientIP(){
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // IP derrière un proxy
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // Sinon : IP normale
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public static function allData(bool $serialize = true){
        $r = [
            "academic"=>AcademicYear::fetchAll(),
            "faculty"=>Faculty::fetchAll(),
            "hierarchy"=>Hierarchy::fetchAll(),
            "course"=> Course::fetchAll(),
            "teacher"=>Teacher::fetchAll(),
            "workDays"=>json_encode(Storage::$workableDays),
            "student"=>Student::fetchAll(),
            "notes"=>Notes::fetchAll(),
            "currentYear"=>Storage::$currentYear,
            "ccc"=>self::getCCC()
        ];
        return $serialize ? self::serialize($r) : $r;
    }

    private static function getCCC(){
        $ac = [];
        $r = 0;
        foreach ([Student::fetchAll(), Teacher::fetchAll()] as $list) {
            foreach ($list as $t) {
                $last = explode("-", $t->getCode());
                if ($last[count($last) - 1] == substr(Storage::$currentYear->getAcademie(), 7) || $last[count($last) - 1] == substr(Storage::$currentYear->getAcademie(), 8)) {
                    $ac[] = (int) preg_replace("/^[a-zA-Z]{2,3}-([0-9]{3})-[0-9]{1,2}$/", "$1", $t->getCode());
                }
            }
        }
        \System\Log::println("[Total] ".json_encode($ac));
        if(count($ac) > 0) {
            sort($ac);
            $max = $ac[0];
            $r = $max;
            foreach($ac as $i) {
                if ($i > $max) {
                    $r = $i;
                    if($i - $max > 2) {
                        break;
                    }
                    $max = $i;
                }
            }
        }
        return $r;
    }

    public static function get(?string $view = null, ?string $token = null){
        $res = self::$ressource[$view == null ? "." : $view][0];
        $index = $view == null ? -1 : self::$ressource[$view][1];
        if($token != null && $res != null){
            $e = Storage::getTokenUser($token);
            if($e != null){
                $access[] = explode(",", $e->getPrivileges());
                if(!CheckIf::inArray($index, $access)){
                    $token = null;
                    $view = "";
                }
            }
            else{
                $token = null;
                $view = "";
            }
        }
        $file = $res == null || ($token == null && $view != null) ? "akademy/view/error.html" : $res;
        return self::readFile(self::$basePath.$file);
    }

    private static function readFile(string $file){
        $content = "";
        if(is_file($file)){
            $stream = fopen($file, "r");
            while($line = fgets($stream)){
                $content .= $line;
            }
            fclose($stream);
        }
        return preg_replace("/\\\\\\\\/","\\",$content);
    }
}