<?php
require "utils/utils.php";

$basePath = [
    "",
    "data".DIRECTORY_SEPARATOR,
    "utils".DIRECTORY_SEPARATOR,
    "lib".DIRECTORY_SEPARATOR
];
function loadClass($class){
    global $basePath;
    $class = preg_replace("/\\\/U", "/", $class);
    foreach($basePath as $v){
        $file = $v.$class . '.php';
        if(is_file($file)){
            require $file;
        }
        else if(preg_match("#/#", $class)){
            $name = explode("/", $class);
            $found = false;
            $file = null;
            for($i = 0, $j = count($name); $i < $j - 1; $i++){
                array_shift($name);
                if(is_file($v.implode(DIRECTORY_SEPARATOR, $name).".php")){
                    $file = $v.implode(DIRECTORY_SEPARATOR, $name).".php";
                    $found = true;
                    break;
                }
            }
            if($found){
                if(is_file($name[0].".php")){
                    $file = $name[0].".php";
                }
            }
            if($file != null){
                require $file;
            }
        }
    }
}
spl_autoload_register('loadClass');
?>