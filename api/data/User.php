<?php


class User extends Civil
{
    private $pseudo, $privileges, $etat, $date_creation;
    private $id = 0;
    private $actif = false, $online = false;
    public static $list = [];

    public function getNom() {
        return parent::getNom();
    }

    public function setNom(string $nom) {
        parent::setNom($nom);
    }

    public function getPrenom() {
        return parent::getPrenom();
    }

    public function setPrenom(string $prenom) {
        parent::setPrenom($prenom);
    }

    public function getFullName(){
        return $this->prenom." ".$this->nom;
    }

    public function getPoste() {
        return parent::getPoste();
    }

    public function setPoste(int $poste) {
        parent::setPoste($poste);
    }

    public function getEtat() {
        return $this->etat;
    }

    public function getId() {
        return $this->id;
    }

    public function getPseudo() {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo) {
        $this->pseudo = $pseudo;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function isActif() {
        return $this->actif;
    }

    public function setActif(bool $actif) {
        $oldState = $this->actif;
        $this->actif = $actif;
        $r = "Execution error !";
        if($actif && !$oldState){
            $t = Teacher::getByIdentity($this->identite);
            if($t != null && $t->getEtat() == "E"){
                return "This user is linked to an excluded teacher ! Impossible to set it active.";
            }
        }
        $db = Storage::Connect();
        $chk = $db->prepare("update utilisateurs set etat=:p1 where id=:p2");
        try{
            $chk->execute([
                "p1"=> $actif ? "actif" : "inactif",
                "p2"=>$this->id
            ]);
            $this->actif = $oldState;
            $r = null;
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function getPrivileges() {
        return $this->privileges;
    }

    public function setPrivileges(array $privileges) {
        $string = ""; $r = null;
        foreach($privileges as $k => $index){
            $index = (int) $index;
            if($index >= 0 && $index <= 44 && $index != 9){
                $string .= (strlen($string) > 0 ? "," : "").$index;
            }
            else{
                $r = "Module ".$index." is undefined !";
            }
        }
        $this->privileges = $string;
        return $r;
    }

    public function hasPrivilege(int $privilege){
        return preg_match("/^".$privilege."(.+?)?|(.+?)".$privilege.",(.+?)|(.+?)?".$privilege."$/", $this->privileges);
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $chk = $db->prepare("delete from utilisateurs where id=:p1");
        try{
            $chk->execute(["p1"=> $this->id]);
            $r = true;
            Storage::update($this);
            Storage::disconnectUser($this);
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    private function _save(?string $password, bool $withIdentity){
        $r = "Invalid data given !";
        if((!$withIdentity && ($this->nom == null || $this->prenom == null)) || $this->pseudo == null || $this->privileges == null) return $r;
        $r = "Pseudo already exists !";
        if(User::pseudoExists($this)) return $r;
        $this->isuser = true;
        if(!$withIdentity){
            $r = parent::save();
        }
        else if(!$this->identityExists($this->identite)){
            $r = "Error ! Violation of identity !";
        }
        else{
            $r = null;
        }
        \System\Log::println("[Before] ". $r);
        if($r != null) return $r;
        \System\Log::println("[After] ". $r);
        $r = "Execution error !";
        $db = Storage::Connect();
        $chk = null;
        if($this->id == 0) {
            $chk = $db->prepare("insert into utilisateurs (identite,pseudo,".($password == null ? "" : "passcode,")." access,date_creation) Values(:p1,:p2,".($password == null ? "": "SHA1(:p3)," ).":p4,NOW())");
        }
        else{
            $chk = $db->prepare("update utilisateurs set pseudo=:p2,".($password == null ? "": "passcode=SHA1(:p3)," )."access=:p4 where id=:p1");
        }
        try{
            $data = [
                "p1"=> $this->id == 0 ? $this->identite : $this->id,
                "p2"=> $this->pseudo,
                "p4"=> $this->privileges
            ];
            if($password != null){
                $data["p3"] = $password;
            }
            $chk->execute($data);
            $r = null;
            Storage::update($this);
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function save(?string $password = null, Teacher $teacher = null){
        if($teacher != null){
            $this->identite = $teacher->getIdentite();
        }
        return $this->_save($password, $teacher != null);
    }

    public function hydrate(array $data){
        $this->identite = (int) $data["identite"];
        $this->init();
        $this->id = (int) $data["id"];
        $this->pseudo = $data["pseudo"];
        $this->privileges = $data["access"];
        $this->actif = $data["etat"] == "actif";
        $this->date_creation = $data["date_creation"];
        $this->online = $this->isOnline();
        $this->fetchAttachement();
    }

    private function fetchAttachement(){
    //if is a teacher
        $prof = Teacher::getByIdentity($this->identite);
        $attachment = [];
        if($prof != null){
            $attachment = $prof->getAttachedBranch();
        }
        if($this->poste != null){
            $fac = Faculty::getById($this->poste->getAffectation());
            if(!CheckIf::contains($fac->getId(), $attachment)){
                $attachment[$fac->getId()] = [];
            }
            foreach($fac->getGrades() as $k => $v){
                if(!CheckIf::contains($k, $attachment[$fac->getId()]) ){
                    $attachment[$fac->getId()][] = $k;
                }
            }
        }
        if($this->hasPrivilege(44)){
            foreach(Faculty::$list as $fac){
                if(!CheckIf::contains($fac->getId(), $attachment)){
                    $attachment[$fac->getId()] = [];
                }
                foreach($fac->getGrades() as $k => $v){
                    if(!CheckIf::contains($k, $attachment[$fac->getId()])) {
                        $attachment[$fac->getId()][] = $k;
                    }
                }
            }
        }
        $this->attachedBranch = $attachment;
        $attachment = null;
    }

    public function equals(User $e){
        return $this->id == $e->getId();
    }

    public static function isMatch(string $user, string $passcode, bool $checkOnly = false){
        $r = null;
        $db = Storage::Connect();
        $chk = $db->prepare("select distinct u.*, i.nom, i.prenom from utilisateurs u, individu i where pseudo =:p1 and passcode=SHA1(:p2) and i.id = u.identite");
        System\Log::println("Connecting !");
        try{
            $chk->execute([
                "p1"=> $user,
                "p2"=> $passcode
            ]);
            if($chk->rowCount()){
                $r = new User();
                $r->hydrate($chk->fetch());
                if(!$checkOnly) {
                    if (!$r->isActif()) {
                        throw new Exception("The account is locked ! Please contact the service to fix this.");
                    }
                }
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function isOnline(){
        $r = false;
        $chk = Storage::Connect()->prepare("select * from sessiontrace where user=:p1 and online=1");
        try{
            $chk->execute(["p1"=>$this->id]);
            if($chk->rowCount()){
                $r = true;
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function fetchAll($override = false){
        if(!$override && count(self::$list) > 0){
            return self::$list;
        }
        self::$list = [];
        $db = Storage::Connect();
        $chk = $db->prepare("select u.* from utilisateurs u, individu i where i.id = u.identite order by i.prenom, i.nom asc");
        try{
            $chk->execute();
            while($d = $chk->fetch()) {
                $t = new User();
                $t->hydrate($d);
                self::$list[] = $t;
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return self::$list;
    }

    public static function getById(int $id){
        $r = null;
        if(count(self::$list)){
            foreach(self::$list as $e){
                if($e->getId() == $id){
                    $r = $e;
                    break;
                }
            }
            if($r != null) return $r;
        }
        $db = Storage::Connect();
        $chk = $db->prepare("select u.* from utilisateurs u, individu i where i.id = u.identite and u.id = :p1");
        try{
            $chk->execute(["p1"=>$id]);
            if($d = $chk->fetch()) {
                $r = new User();
                $r->hydrate($d);
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function getByIdentity(int $id){
        $r = null;
        if(count(self::$list) == 0){
            $db = Storage::Connect();
            $chk = $db->prepare("select * from utilisateurs where identite=:p1");
            $chk->execute(["p1"=>$id]);
            if($chk->rowCount()){
                $r = new User();
                $r->hydrate($chk->fetch());
            }
        }
        else {
            foreach (self::fetchAll() as $e) {
                if ($e->getIdentite() == $id) {
                    $r = $e;
                    break;
                }
            }
        }
        return $r;
    }

    public static function exists(int $identity){
        $r = false;
        $db = Storage::Connect();
        $chk = $db->prepare("select * from utilisateurs where identite=:p1");
        try {
            $chk->execute(["p1" => $identity]);
            $r = $chk->rowCount() > 0;
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function pseudoExists(User $e){
        $r = false;
        foreach(self::fetchAll() as $u){
            if($u->getId() != $e->getId() && strtolower($u->getPseudo()) == $e->getPseudo() ){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public function data(){
        return CheckIf::merge(parent::data(), [
            "pseudo" => $this->pseudo,
            "privileges" => $this->privileges,
            "etat" => $this->etat,
            "date_creation" => $this->date_creation,
            "id" => $this->id,
            "actif" => $this->actif,
            "online" => $this->online
        ]);
    }
}