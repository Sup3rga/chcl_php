<?php


class Hierarchy extends Data
{
    private $notation, $filiere;
    private $capacity = 0, $value = 0, $affectation = 0, $id = 0;
    public static $list = [];

    public function getNotation() {
        return $this->notation;
    }

    public function setNotation(string $notation) {
        $this->notation = $notation;
    }

    public function getFiliere() {
        return $this->filiere;
    }

    public function setFiliere(string $filiere) {
        $this->filiere = $filiere;
    }

    public function getCapacity() {
        return $this->capacity;
    }

    public function setCapacity(int $capacity) {
        $this->capacity = $capacity;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue(int $value) {
        $this->value = $value;
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getAffectation() {
        return $this->affectation;
    }

    public function setAffectation(int $affectation) {
        $this->affectation = $affectation;
    }

    public function save(){
        if(!CheckIf::isFormalName($this->notation)){
            return "Incorrect notation given for this hierarchy !";
        }
        if(Hierarchy::nameExists($this)){
            return "hierarchy already exists !";
        }
        $r = "Execution error !";
        $db = Storage::Connect();
        $chk = null; $data = [];
        if($this->id == 0) {
            $chk = $db->prepare("insert into hierarchie(notation,effectif,affectation,valeur) values(:p1,:p2,:p3,:p4)");
        }
        else{
            $chk = $db->prepare("update hierarchie set notation=:p1, effectif=:p2, affectation=:p3, valeur=:p4 where id=:p5");
            $data["p5"] = $this->id;
        }
        $data["p1"] = $this->notation;
        $data["p2"] = $this->capacity;
        System\Log::println("[affectation] ".$this->affectation);
        $data["p3"] = $this->affectation <= 0 ? null : $this->affectation;
        $data["p4"] = $this->value;
        try{
            $chk->execute($data);
            $r = null;
            Storage::update($this);
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $chk = $db->prepare("delete from hierarchie where id=:p1");
        try{
            $chk->execute(["p1"=> $this->id]);
            $r = true;
            Storage::update($this);
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function hydrate(array $data){
        $this->id = (int) $data["id"];
        $this->notation = $data["notation"];
        $this->capacity = (int) $data["effectif"];
        $this->value = (int) $data["valeur"];
        $this->affectation = $data["affectation"] == null ? 0 : (int) $data["affectation"];
        $this->filiere = $this->affectation == 0 ? null : $data["filiere"];
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
        $chk = $db->prepare("select distinct h.*, f.nom as filiere from hierarchie h, filieres f 
                        where (h.affectation is null or h.affectation = f.id) and h.id = :p1");
        try{
            $chk->execute(["p1"=>$id]);
            while($d = $chk->fetch()){
                $r = new Hierarchy();
                $r->hydrate($d);
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
        $chk = $db->prepare("select distinct h.*, f.nom as filiere from hierarchie h, filieres f 
                        where h.affectation is null or h.affectation = f.id order by h.id asc");
        $doublon = [];
        try{
            $chk->execute();
            while($d = $chk->fetch()){
                $h = new Hierarchy();
                $h->hydrate($d);
                if(!CheckIf::inArray($h->getId(), $doublon)){
                    self::$list[] = $h;
                    $doublon[] = $h->getId();
                }
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return self::$list;
    }

    public static function nameExists(Hierarchy $hr){
        $r = false;
        foreach(self::fetchAll() as $h){
            if(strtolower($h->getNotation()) == strtolower($hr->getNotation()) &&
                $h->getAffectation() == $hr->getAffectation() &&
                $h->getId() != $hr->getId()
            ){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function isSaturated(int $hrId, int $identite){
        $total = 0;
        $h = self::getById($hrId);
        $ids = [];
        if($h == null) return true;
        foreach([Teacher::$list, User::$list] as $list){
            foreach($list as $t){
                if($t->getPoste() == $hrId && $t->getIdentite() != $identite){
                    $total++;
                    $ids[] = $t->getIdentite();
                }
            }
        }

        return $total >= $h->getCapacity();
    }

    public function data(){
        return [
            "notation" => $this->notation,
            "filiere" => $this->filiere,
            "capacity" => $this->capacity,
            "value" => $this->value,
            "affectation" => $this->affectation,
            "id" => $this->id
        ];
    }
}