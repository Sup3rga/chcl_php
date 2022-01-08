<?php


abstract class Data{
    abstract function save();
    abstract function delete();
    abstract function hydrate(array $data);
//    abstract function data();
    abstract static function fetchAll();
    public function __toString(){
        return json_encode($this->data());
    }
}