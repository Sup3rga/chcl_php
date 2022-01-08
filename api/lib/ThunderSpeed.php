<?php
/**
 *@name : ThunderSpeed
 *@author: Superga
 *@description: light php class for fast and asynchronous file upload treatment
 */


class ThunderSpeed
{
    public static $baseDir = './';
    public static $uploadDir = './';
    private $resumable = false;
    private $post = [];
    private $file = null;
    private $done = false;
    private $name = null;
    private $filename = null;
    private $timestamp = 0;
    private $chunkIndex = "ths_filepart";
    private $ok = false;

    public function __construct(){
        $this->init();
    }

    private function init(){
        $list = [$this->chunkIndex, "ths_filepartid", "ths_fileextension", "ths_filesector"];
        $this->post = [];
        $r = true;
        foreach($list as $index){
            if(isset($_POST[$index]) && !empty($_POST[$index])){
                $this->post[$index] = $_POST[$index];
            }
            else{
                $r = false;
                break;
            }
        }
        if(isset($_POST["ths_fileuploaddone"])){
            $this->post["ths_fileuploaddone"] = $_POST["ths_fileuploaddone"];
        }
        else{
            $r = false;
        }
        return $r;
    }

    private function isExpired(int $timestamp){
        $r = false;
        return $this->resumable ? false : time() - $timestamp > 60 * 5;
    }

    private function convert(){
        $new = fopen($this->name.'.bak', 'ab');
        fclose($new);
        file_put_contents($this->name.'.bak', base64_decode(file_get_contents($this->name)), FILE_BINARY);
        opendir(self::$baseDir);
        unlink($this->name);
        closedir();
        rename($this->name.'.bak', $this->name);
    }

    private function finish(){
        if($this->post['ths_fileuploaddone'] == "true"){
            $k = 0;
            do{
                $this->filename = $this->timestamp.($k == 0 ? "" : "($k)").".".$this->post['ths_fileextension'];
                $name = self::$uploadDir.$this->filename;
                $k++;
            }while(is_file($name));
            rename($this->name, $name);
            $this->done = true;
        }
    }

    public function upload(){
        $file = null;
        $r = [
            'error'=>false,
            'uploaded'=>true,
            'message'=> '',
            'filename'=> null
        ];
        $this->conformToDir(self::$baseDir);
        $this->conformToDir(self::$uploadDir);
        $files = scandir(self::$baseDir);
        $timestamp = 0;
        $sector = 0;
        $name  = "";
        $exist = true;
        opendir(self::$baseDir);
        foreach($files as $f){
            if(is_file(self::$baseDir.$f)) {
                $name = preg_replace("/\\.(.+?)$/", "", $f);
                $name = explode("%",$name);
                if($this->isExpired((int) $name[0])){
                    unlink(self::$baseDir.$f);
                }
                if($name[1] == $this->post['ths_filepartid']){
                    $timestamp = $name[0];
                    $file = $f;
                    $sector = $name[2];
                    break;
                }
            }
        }
        closedir();
        if($file == null){
            $timestamp = time();
            $exist = false;
        }
        else{
            if($this->isExpired($timestamp)){
                $r['uploaded'] = false;
                $r['message'] = 'expired session';
                return $r;
            }
        }
        $name = self::$baseDir.$timestamp."%".$this->post['ths_filepartid'].'%'.($sector == 0 ? $this->post['ths_filesector'] : $sector ).'.thsfilepart';
        $this->file = $file;
        $this->name = $name;
        $this->timestamp = $timestamp;


        if($file != null && (int) $this->post['ths_filesector'] <= (int) $sector){
            $this->name = self::$baseDir.$timestamp."%".$this->post['ths_filepartid'].'%'.$sector.'.thsfilepart';
            $this->finish();
            $r['filename'] = $this->filename;
            return $r;
        }

        $file = fopen($name, "a");
        fclose($file);
        if($exist){
            $timestamp = time();
            rename($name, self::$baseDir.$timestamp."%".$this->post['ths_filepartid']."%".$this->post['ths_filesector'].'.thsfilepart');
            $name = self::$baseDir.$timestamp."%".$this->post['ths_filepartid']."%".$this->post['ths_filesector'].'.thsfilepart';
        }
        $this->file = $file;
        $this->name = $name;
        $this->timestamp = $timestamp;
        $tmp = fopen($name.'.bak','a');
        fclose($tmp);
        file_put_contents($name.'.bak',base64_decode($this->post[$this->chunkIndex]), FILE_APPEND);
        file_put_contents($name, file_get_contents($name.'.bak'), FILE_APPEND);
        opendir(self::$baseDir);
        unlink($name.'.bak');
        closedir();
        $this->finish();
        $r['filename'] = $this->filename;
        return $r;
    }

    public function isDone(){
        return $this->done;
    }

    public function setResumable(bool $resumable){
        $this->resumable = $resumable;
    }

    public function isResumable(){
        return $this->resumable;
    }

    public function getFileName(){
        return $this->filename;
    }

    public function watch($chunkIndex, bool $breakExecution = true){
        $chunkIndex = is_array($chunkIndex) ? $chunkIndex : [$chunkIndex];
        foreach($chunkIndex as $index) {
            $this->chunkIndex = $index;
            if ($this->init() && !$this->done) {
                die(json_encode($this->upload()));
                break;
            }
        }
        $this->done = true;
        return $this;
    }

    private function conformToDir(string &$val){
        if(!preg_match("#\/$#", $val)){
           $val .= "/";
        }
        return $val;
    }

    public function then(?callable $e){
        if($this->done){
            $e($this);
        }
        return $this;
    }

    public function move(?string $uploadedFileName, string $targetDir, ?string $targetFileName = null){
        if($uploadedFileName == null) return;
        $this->conformToDir($targetDir);
        $this->conformToDir(self::$uploadDir);
        if(is_file(self::$uploadDir.$uploadedFileName)){
            rename(self::$uploadDir.$uploadedFileName, $targetDir.($targetFileName == null ? $uploadedFileName : $targetFileName));
        }
    }

    public function flush(?string $uploadedFileName){
        if($uploadedFileName == null) return;
        $this->conformToDir(self::$uploadDir);
        $r = false;
        opendir(self::$uploadDir);
        if(is_file(self::$uploadDir.$uploadedFileName)) {
            unlink(self::$uploadDir . $uploadedFileName);
            $r = true;
        }
        closedir();
        return $r;
    }

    public function isUploaded(?string $filename = null){
        $this->conformToDir(self::$uploadDir);
        return $filename != null && is_file(self::$uploadDir . $filename);
    }
}