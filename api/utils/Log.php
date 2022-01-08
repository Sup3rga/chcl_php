<?php

namespace System{
    /**
     * Class Log
     * @package System
     */
    class Log
    {
        public static $streamDir = "./";
        private static $logDir = "log/";
        private static $type = "debug";
        public static $timeout = 1;

        private static function activeFileName(){
            $list = scandir(self::$streamDir.self::$logDir);
            $date = date("d-M-Y");
            $time = time();
            $name = self::$type."-".$date."%".($time + self::$timeout * 24 * 60 * 60).".log";
            $r = $name;
            if($list){
                foreach ($list as $file){
                    if(is_file(self::$streamDir.self::$logDir.$file) && preg_match("/^".self::$type."-(.+?)%(.+?).log$/", $file)){
                        $r = preg_replace("/^".self::$type."-(.+?)%(.+?).log$/", "$2", $file);
                        $alive = $r >= $time;
                        $r = $alive ? $file : $name;
                        if($alive) {
                            break;
                        }
                    }
                }
            }
            return $r;
        }

        /**
         * @param string $val
         * @param string $end
         */
        public static function print(string $val, string $type = "debug", string $end = ""){
            self::$type = $type;
            if(!is_dir(self::$streamDir.self::$logDir)){
                mkdir(self::$streamDir.self::$logDir, 0755, true);
            }
            $time = date("d-M-Y H:i:s");
            $file = fopen(self::$streamDir.self::$logDir.self::activeFileName(), "a+");
            fputs($file, "[ ".$time." ] ".$val.$end);
            fclose($file);
        }

        /**
         * @param string $val
         */
        public static function println(string $val, string $type = "debug"){
            self::print($val, $type,"\n");
        }

        /**
         * @param \Exception $e
         */
        public static function printStackTrace(\Exception  $e){
            self::println($e->getMessage());
        }
    }
}