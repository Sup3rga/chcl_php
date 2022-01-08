<?php

abstract class Civil extends Data {
    protected $code, $nom, $prenom, $sexe, $adresse, $email,
              $lieu_naissance,$date_naissance, $nif, $ninu,
            $telephone, $photo, $memo;
    protected $identite = 0, $_poste = 0,$_photo = 0,$age = 0, $_newPhoto = 0;
    protected $poste;
    protected $attachedBranch = [];
    protected $isuser = false;

    public function getAttachedBranch(){
        return $this->attachedBranch;
    }
    protected function getCode() {
        return $this->code;
    }

    protected function setCode(string $code) {
        $this->code = $code;
    }

    protected function getNom() {
        return $this->nom;
    }

    protected function setNom(string $nom) {
        $this->nom = $nom;
    }

    protected function getPrenom() {
        return $this->prenom;
    }

    protected function setPrenom(string $prenom) {
        $this->prenom = $prenom;
    }

    protected function getSexe() {
        return $this->sexe;
    }

    protected function setSexe(string $sexe) {
        $this->sexe = strtoupper($sexe);
    }

    protected function getAdresse() {
        return $this->adresse;
    }

    protected function setAdresse(string $adresse) {
        $this->adresse = $adresse;
    }

    protected function getEmail() {
        return $this->email;
    }

    protected function setEmail(string $email) {
        $this->email = $email;
    }

    protected function getLieu_naissance() {
        return $this->lieu_naissance;
    }

    protected function setLieu_naissance(string $lieu_naissance) {
        $this->lieu_naissance = $lieu_naissance;
    }

    protected function getDate_naissance() {
        return $this->date_naissance;
    }

    protected function setDate_naissance(string $date_naissance) {
        $this->date_naissance = $date_naissance;
    }

    protected function getNif() {
        return $this->nif;
    }

    protected function setNif(string $nif) {
        $this->nif = $nif;
    }

    protected function getNinu() {
        return $this->ninu;
    }

    protected function setNinu(string $ninu) {
        $this->ninu = $ninu;
    }

    protected function getPoste() {
        return $this->_poste;
    }

    protected function setPoste(int $poste) {
        $this->_poste = $poste;
    }

    protected function getTelephone() {
        return $this->telephone;
    }

    protected function setTelephone(string $telephone) {
        $this->telephone = $telephone;
    }

    protected function getPhoto() {
        return $this->_photo;
    }

    protected function setPhoto(int $photo) {
        $this->_photo = $photo;
    }

    protected function getMemo() {
        return $this->memo;
    }

    public function uploadPhoto(string $name = null) {
       if($name != null){
           $this->photo = $name;
           $this->_newPhoto = 1;
       }
       return $name != null;
    }

    protected function setMemo(?string $memo) {
        $this->memo = $memo;
    }

    public function getIdentite() {
        return $this->identite;
    }

    protected function setIdentite(int $identite) {
        $this->identite = $identite;
    }

    private function setAvatar(){
        if($this->photo != null && $this->_newPhoto == 1){
            $db = Storage::Connect();
            $chk = $db->prepare("insert into images(fichier,date_upload) values(:p1,NOW())");
            $chk->execute([
                'p1'=>$this->photo
            ]);
            $chk = $db->prepare("select id from images order by id desc limit 1");
            $chk->execute();
            $this->_photo = (int) $chk->fetch()["id"];
            $chk->closeCursor();
        }
    }

    public function save(){
        $r = "lastname incorrect !";
        if(!CheckIf::isFormalName($this->nom)) return $r;
        $r = "firstname incorrect !";
        if(!CheckIf::isFormalName($this->prenom)) return $r;
        if(!$this->isuser) {
            $r = "incorrect code !";
            if(!CheckIf::isCode($this->code)) return $r;
            $r = "code already exists !";
            if(self::codeExists($this)) return $r;
            $r = "sexe incorrect !";
            if (!$this->sexe == "M" && $this->sexe != "F") return $r;
            $r = "incorrect address !";
            if (!CheckIf::isFormalAddress($this->adresse)) return $r;
            $r = "incorrect birthplace name !";
            if (!CheckIf::isFormalPlace($this->lieu_naissance)) return $r;
            $r = "incorrect birthdate format !";
            if (!CheckIf::isDate($this->date_naissance)) return $r;
            $r = "incorrect phone number !";
            if (!CheckIf::isPhoneNumber($this->telephone)) return $r;
            $r = "incorrect e-mail !";
            if (!CheckIf::isEmail($this->email)) return $r;
            $r = "nif already exists !";
            if (self::emailExists($this)) return $r;
            $r = "incorrect nif !";
            if (!CheckIf::isNIF($this->nif)) return $r;
            $r = "nif already exists !";
            if (self::nifExists($this)) return $r;
            $r = "incorrect ninu !";
            if (!CheckIf::isNINU($this->ninu)) return $r;
            $r = "ninu already exists !";
            if (self::ninuExists($this)) return $r;
        }
        if($this->_poste > 0 && Hierarchy::isSaturated($this->_poste, $this->identite)){
            return "The choosen hierarchy is saturated ! Please check-out for free one.";
        }
        $this->setAvatar();
        $r = "Execution error";
        $db = Storage::Connect();
        $data = [];
        if($this->identite == 0){
            $db = $db->prepare("insert into individu (code,nom,prenom,sexe,adresse,email,lieu_naissance,
                date_naissance,nif,ninu,poste,telephone,photo,memo) 
                values(:p1,:p2,:p3,:p4,:p5,:p6,:p7,:p8,:p9,:p10,:p11,:p12,:p13,:p14)");
        }
        else{
            $db = $db->prepare("update individu set code=:p1,nom=:p2,prenom=:p3,sexe=:p4,adresse=:p5,email=:p6,
                lieu_naissance=:p7,date_naissance=:p8,nif=:p9,ninu=:p10,poste=:p11,telephone=:p12,
                photo=:p13,memo=:p14 where id=:p15");
            $data["p15"] = $this->identite;
        }
        $data["p1"] = $this->code;
        $data["p2"] = $this->nom;
        $data["p3"] = $this->prenom;
        $data["p4"] = $this->sexe;
        $data["p5"] = $this->adresse;
        $data["p6"] = $this->email;
        $data["p7"] = $this->lieu_naissance;
        $data["p8"] = $this->date_naissance;
        $data["p9"] = $this->nif;
        $data["p10"] = $this->ninu;
        $data["p11"] = $this->_poste == 0 ? null : $this->_poste;
        $data["p12"] = $this->telephone;
        $data["p13"] = $this->_photo == 0 ? null : $this->_photo;
        $data["p14"] = $this->memo;
        try{
            $db->execute($data);
            $r = null;
        }catch(Exception $e){
            System\Log::printStackTrace($e);
        }
        \System\Log::println("[ Civil ] Ok : " . $r);
        $db->closeCursor();
        $this->identite = self::getLastId();
        return $r;
    }

    protected function rollback(){
        $db = Storage::Connect();
        $db = $db->prepare("delete from individu where id=:p1");
        $db->execute(["p1" => $this->identite]);
        $db->closeCursor();
    }

    private function setAge(){
        if($this->date_naissance == null) return;
        $cal = explode("-", date('Y-m-d'));
        $date = explode("-",$this->date_naissance);
        $this->age = $cal[0] * 1 - $date[0] * 1;
        for($i = 1; $i < 3; $i++){
            if($cal[$i] < $date[$i]){
                $this->age--;
                break;
            }
            else if($cal[$i] > $date[$i]) break;
        }
    }

    private function fetchAvatar(){
        $db = Storage::Connect();
        if($this->_photo > 0){
            $db = $db->prepare("select * from images where id=:p1");
            $db->execute(["p1"=> $this->_photo]);
            if($db->rowCount()){
                $this->photo = $db->fetch()["fichier"];
            }
            $db->closeCursor();
        }
    }

    public function init(){
        $db = Storage::Connect();
        $db = $db->prepare("select * from individu where id=:p1");
        $db->execute(["p1"=> $this->identite]);
        if($db->rowCount()){
            $res = $db->fetch();
            $this->code = $res["code"];
            $this->nom = $res["nom"];
            $this->prenom = $res["prenom"];
            $this->sexe = $res["sexe"];
            $this->adresse = $res["adresse"];
            $this->email = $res["email"];
            $this->lieu_naissance = $res["lieu_naissance"];
            $this->date_naissance = $res["date_naissance"] == null ? null : $res["date_naissance"];
            $this->nif = $res["nif"];
            $this->ninu = $res["ninu"];
            $this->telephone = $res["telephone"];
            $this->_photo = $res["photo"] == null ? 0 : (int)$res["photo"];
            $this->memo = $res["memo"];
            $this->identite = $res["id"] == null ? 0 : (int)$res["id"];
            $this->_poste = $res["poste"] == null ? 0 : (int) $res["poste"];
            $this->poste = $this->_poste == 0 ? null : Hierarchy::getById($this->_poste);
            $this->setAge();
            $this->fetchAvatar();
        }
        $db->closeCursor();
    }

    public static function getLastId(){
        $r = 0;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu order by id desc limit 1");
        $db->execute();
        if($db->rowCount()){
            $r = (int) $db->fetch()["id"];
        }
        $db->closeCursor();
        return $r;
    }

    protected static function identityExists(int $id){
        $r = false;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where id=:p1");
        $db->execute(["p1" => $id]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public static function codeExists(Civil $c){
        $r = false;
        if($c->getCode() == null) return $r;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where code = :p1 and id != :p2");
        $db->execute(['p1'=>$c->getCode(), 'p2'=>$c->getIdentite()]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public static function ninuExists(Civil $c){
        $r = false;
        if($c->getNinu() == null) return $r;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where ninu = :p1 and id != :p2");
        $db->execute(['p1'=>$c->getNinu(), 'p2'=> $c->getIdentite()]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public static function nifExists(Civil $c){
        $r = false;
        if($c->getNif() == null) return $r;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where nif = :p1 and id != :p2");
        $db->execute(['p1'=>$c->getNif(), 'p2'=> $c->getIdentite()]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public static function phoneExists(Civil $c){
        $r = false;
        if($c->getTelephone() == null) return $r;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where telephone = :p1 and id != :p2");
        $db->execute(['p1'=>$c->getTelephone(), 'p2'=> $c->getIdentite()]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public static function emailExists(Civil $c){
        $r = false;
        if($c->getEmail() == null) return $r;
        $db = Storage::Connect();
        $db = $db->prepare("select id from individu where email = :p1 and id != :p2");
        $db->execute(['p1'=>$c->getEmail(), 'p2'=> $c->getIdentite()]);
        $r = $db->rowCount() > 0;
        $db->closeCursor();
        return $r;
    }

    public function data(){
        return [
            "code" => $this->code,
            "nom" => $this->nom,
            "prenom" => $this->prenom,
            "sexe" => $this->sexe,
            "adresse" => $this->adresse,
            "email" => $this->email,
            "lieu_naissance" => $this->lieu_naissance,
            "date_naissance" => $this->date_naissance,
            "nif" => $this->nif,
            "ninu" => $this->ninu,
            "telephone" => $this->telephone,
            "photo" => $this->photo,
            "memo" => $this->memo,
            "identite" => $this->identite,
            "_poste" => $this->_poste,
            "_photo" => $this->_photo,
            "age" => $this->age,
            "_newPhoto" => $this->_newPhoto,
            "poste" => $this->poste == null ? null : $this->poste,
            "attachedBranch" => $this->attachedBranch,
            "isuser" => $this->isuser
        ];
    }
}