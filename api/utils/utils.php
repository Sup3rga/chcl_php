<?php
function post_verifier($tab){
    $n_tab = array();
    $s_tab = array();
    $ss_tab = array();
    foreach ($tab as $key => $value){
        if(!is_array($value))
            $n_tab[$key] = htmlspecialchars($value);
        else{
            $n_tab[$key] = post_verifier($value);
        }
    }
    return $n_tab;
}

function is_array_full($tab, $list){
    $rep = true;
    if(is_array($tab) && is_array($list)){
        for($i=0, $j = count($list); $i < $j; $i++){
            if(!(isset($tab[$list[$i]]) AND !empty($tab[$list[$i]])))
                $rep = false;
        }
    }
    return $rep;
}