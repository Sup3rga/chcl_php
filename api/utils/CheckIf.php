<?php


class CheckIf
{
    public static function escape(array $tab){
        $n_tab = array();
        $s_tab = array();
        $ss_tab = array();
        foreach ($tab as $key => $value){
            if(!is_array($value))
                $n_tab[$key] = htmlspecialchars($value);
            else{
                $n_tab[$key] = self::escape($value);
            }
        }
        return $n_tab;
    }

    /**
     * @param $var
     * <p>
     *  Variable à tester
     * </p>
     * @param $val
     * <p>
     *  Valeur par défaut à prendre dans le cas où la valeur de $var n'est pas définie
     * </p>
     * @return mixed|null
     */
    public static function set(&$var, $val = null){
        return isset($var) ? $var : $val;
    }

    public static function isRequest(array $_VAR, array $array){
        Storage::initBase();
        $rep = false;
        if(is_array($_VAR) && is_array($array)){
            $rep = [];
            foreach($array as $i){
                if(!(isset($_VAR[$i]) AND !empty($_VAR[$i]))) {
                    $rep = false;
                    break;
                }
                else{
                    $rep[$i] = $_VAR[$i];
                }
            }
        }
        return $rep ? self::escape($rep) : $rep;
    }

    public static function inArray($el, $array = []){
        $r = false;
        foreach($array as $i){
            if($el == $i){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function contains($el, $array = [], $key = true){
        $r = false;
        foreach($array as $i => $j){
            if(($el == $i && $key) || ($el == $j && !$key)){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function isFormalAddress($addr){
        return preg_match("/^(#? *[0-9]+ *, *)?([A-Za-zÀ-ÖØ-öø-ÿ]+[\\s\\S]* *, *)?[A-Za-zÀ-ÖØ-öø-ÿ]+[A-Za-zÀ-ÖØ-öø-ÿ '-]*[A-Za-zÀ-ÖØ-öø-ÿ]( *, *[A-Za-zÀ-ÖØ-öø-ÿ]+[A-Za-zÀ-ÖØ-öø-ÿ '-]*)?$/i", $addr);
    }

    public static function isFormalPlace($addr){
        return preg_match("/^([A-Za-zÀ-ÖØ-öø-ÿ]+[\\s\\S]* *, *)?[A-Za-zÀ-ÖØ-öø-ÿ]+[A-Za-zÀ-ÖØ-öø-ÿ '-]*[A-Za-zÀ-ÖØ-öø-ÿ]( *, *[A-Za-zÀ-ÖØ-öø-ÿ]+[A-Za-zÀ-ÖØ-öø-ÿ '-]*)?$/i", $addr);
    }

    public static function isFormalName($name){
        return preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ]+[A-Za-zÀ-ÖØ-öø-ÿ '-]*[A-Za-zÀ-ÖØ-öø-ÿ]$/i", $name);
    }

    public static function setPonctuationLess($val){
        return preg_replace("/ +/", " ",
                preg_replace(
                    "/(ó|ò|ö|ô)/", "o",
                    preg_replace(
                        "/(ú|ü|ù|û)/", "u",
                        preg_replace(
                        "/(í|ì|î|ï)/", "i",
                            preg_replace(
                            "/(á|à|â|ä)/", "a",
                                    preg_replace(
                                        "/(é|è|ê|ë)/", "e", mb_strtolower($val)
                                    )
                            )
                        )
                    )
                )
            );
    }

    public static function isInteger($num){
        return preg_match("#^[0-9]+$#", $num);
    }

    public static function isNumber($num){
        return preg_match("/^[0-9]+(\\.[0-9]+)?$/", $num);
    }

    public static function isPhoneNumber($num){
        return preg_match("/^((\\+?509) *)?(3|4)[0-9]{3}-?[0-9]{4}$/", $num);
    }

    public static function isNIF($num){
        return preg_match("/^([0-9]{3}-){3}[0-9]$/", $num);
    }

    public static function isNINU($num){
        return preg_match("/^[12][0-9]{9}$/", $num);
    }

    public static function isDate($val){
        return preg_match("/^[0-9]{4}(-[0-9]{2}){2}$/", $val);
    }

    public static function isEmail($val){
        return preg_match("/^[a-z]+[a-z0-9.]*@[a-z]+[a-z0-9.]*\\.[a-z]+[a-z0-9]*$/", $val);
    }

    public static function isCode($val){
        return preg_match("/^[A-Z]{2}-[0-9]{3}-[0-9]{2}$/", $val) ||
            preg_match("/^(?)[A-Z]{3}-[0-9]{3}-[0-9]$/", $val);
    }

    public static function clearMeta($regexp){
        return preg_replace("/([\\\\()?+.*\\[\\]-])/", "\\\\$1", $regexp);
    }

    public static function toDate($date){
//        return date.getYear()+"-"+date.getMonth()+"-"+date.getDate();
    }

    public static function merge($array, $more){
        foreach($more as $k => $v){
            $array[$k] = $v;
        }
        return $array;
    }
}