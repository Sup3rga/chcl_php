<?php

class Route
{
    public static bool $devlopmentMode = true;
    public static string $rootFolder = "/akademy/";
    public static string $root = "../";
    private ?string $default = null;
    private array $stat = [
        "request"=>0,
        "match"=>0,
        "response"=>0
    ];

    private function getArgs(){
        $r = [];
        $args = explode("&", $_SERVER["QUERY_STRING"]);
        foreach($args as $v){
            $i = preg_replace("/^ *(.+?) *= *(.+?)$/", "$1", $v);
            $j = preg_replace("/^ *(.+?) *= *(.+?) *$/", "$2", $v);
            if(strlen($i) && strlen($j)){
                $r[$i] = $j;
            }
        }
        return $r;
    }

    private function match(array $args, array $list){
        $r = true;
        foreach ($list as $v){
            if(!isset($args[$v])){
                $r = false;
                break;
            }
        }
        return $r;
    }

    private function getMimeType(string $file){
        $r = "text/plain";
        if(preg_match("/\.css$/", $file)){
            $r = "text/css";
        }
        else if(preg_match("/\.js$/", $file)){
            $r = "text/x-javascript";
        }
        else if(is_file($file)){
            $r = mime_content_type($file);
        }
        return $r.";utf-8";
    }

    private function readFile(string $file){
        $content = "";
        if(is_file($file)){
            readfile($file);
        }
        else if($this->default != null){
            header("Content-Type: text/html");
            readfile(self::$root.$this->default);
        }
        return $content;
    }

    public function setDefault($file){
        $this->default = $file;
        return $this;
    }

    public function setPublic(string $directory){
        $directory = preg_replace("#^ *\\\^|/ *|\$$#", "", $directory);
        $this->get("^".$directory."/*", null, function($res){
           $mime = $this->getMimeType(self::$root.$res);
           header("Content-Type: ".$mime);
           $this->readFile(self::$root.$res);
        });
        return $this;
    }

    public function redirect(string $schema, string $url){
        $schema = preg_replace("#^ *\\\^|\$#", "", $schema);
        global $path;
        $path = $url;
        $this->get("^".$schema."$", null, function($res){
            global $path;
            $mime = $this->getMimeType(self::$root.$path);
            header("Content-Type: ".$mime);
            $this->readFile(self::$root.$path);
        });
        return $this;
    }

    public function deliver($file){
        $file = preg_replace("#^ *\\\^|/ *$#", "", $file);
        $file = preg_replace("/(\.|\+|\*|\?|\[|\]|\(|\)|\\\|\|\$)/", "\\\\$1", $file);

        $this->get("^".$file."$", null, function($res){
            $mime = $this->getMimeType(self::$root.$res);
            header("Content-Type: ".$mime);
            $this->readFile(self::$root.$res);
        });
        return $this;
    }

    private function response(string $res, ?array $args = null, ?callable $e = null){
        $_res = preg_replace("#^".self::$rootFolder."#","",$_SERVER['REDIRECT_URL']);
        if(preg_match("#\\\#",$res)) {
            $res = preg_replace("#\\\#", "\\/", $res);
        }
        $res = preg_replace("#\/#", "\\/", $res);
        $params = $this->getArgs();
        $this->stat["request"]++; 
        if(preg_match("#@~#", $_res)){
            $_res = substr(strchr($_res, "@~"), 2);
        }
        if (preg_match("#" . $res . "#", $_res)) {
            $this->stat["match"]++;
            if ($args == null || $this->match($params, $args)) {
                $this->stat["response"]++;
                $e($_res, $params);
            }
        }
    }

    private function request(string $res, ?array $args = null, ?callable $e = null){
        $this->res[] = [
            "res"=>$res,
            "args"=>$args,
            "callback"=>$e
        ];
    }

    public function watch(){
        if($this->stat["response"] == 0 && $this->default != null){
            header("Content-Type: text/html");
            readfile(self::$root.$this->default);
        }
    }

    public function get(string $res, ?array $args = null, ?callable $e = null){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
          $this->response($res, $args, $e);
        }
        return $this;
    }


    public function post(string $res, ?array $args = null, ?callable $e = null){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->response($res, $args, $e);
        }
        return $this;
    }

    public function delegate(string $res, string $include){
        global $url;
        $url = $include;
        $this->response($res, null, function(){
            global $url;
            require_once $url;
        });
        return $this;
    }
}