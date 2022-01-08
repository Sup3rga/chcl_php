<?php


class Notes extends Data
{
    private $id = 0, $session = 0, $_etudiant = 0, $_cours = 0, $_annee_academique = 0;
    private $note = 0.0;
    private $annee_academique = null;
    private $etudiant = null;
    private $cours = null;
    public static $list = [];

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getSession() {
        return $this->session;
    }

    public function setSession(int $session) {
        $this->session = $session;
    }

    public function getEtudiant() {
        return $this->_etudiant;
    }

    public function setEtudiant(int $_etudiant) {
        $this->_etudiant = $_etudiant;
    }

    public function getCours() {
        return $this->_cours;
    }

    public function setCours(int $_cours) {
        $this->_cours = $_cours;
    }

    public function getAnneeAcademique() {
        return $this->_annee_academique;
    }

    public function setAnneeAcademique(int $_annee_academique) {
        $this->_annee_academique = $_annee_academique;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote(float $note) {
        $this->note = $note;
    }

    public function save() {
        $r = "Execution error";
        $db = Storage::Connect();
        $chk = null; $data = [];
        if($this->id == 0){
            $chk = $db->prepare("insert into notes (session, id_etu, id_cours, note, annee_academique) 
                                   values(:p1,:p2,:p3,:p4,:p5)");
        }
        else{
            $chk = $db->prepare("update notes session=:p1, id_etu=:p2, id_cours=:p3, note=:p4, annee_academique=:p5 where id=:p6");
            $data["p6"] = $this->id;
        }
        $data["p1"] = $this->session;
        $data["p2"] = $this->_etudiant;
        $data["p3"] = $this->_cours;
        $data["p4"] = $this->note;
        $data["p5"] = $this->_annee_academique;
        try{
            $chk->execute($data);
            $r = null;
            Storage::update($this);
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $chk = $db->prepare("delete from notes where id=:p1");
        try{
            $chk->execute(["p1"=> $this->id]);
            $r = true;
            Storage::update($this);
        }catch (Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function hydrate(array $data) {
        $this->id = (int) $data["id"];
        $this->session = (int) $data["session"];
        $this->_etudiant = (int) $data["id_etu"];
        $this->_cours = (int) $data["id_cours"];
        $this->_annee_academique = (int) $data["annee_academique"];
        $this->note = (double) $data["note"];
        $this->etudiant = Student::getById($this->_etudiant);
        $this->cours = Course::getById($this->_cours);
        $this->annee_academique = AcademicYear::getById($this->_annee_academique)->getAcademie();
    }

    public static function fetchAll($override = false){
        if(!$override && count(self::$list) > 0){
            return self::$list;
        }
        self::$list = [];
        $db = Storage::Connect();
        $chk = $db->prepare("select * from notes");
        try{
            $chk->execute();
            while($d = $chk->fetch()){
                $n = new Notes();
                $n->hydrate($d);
                self::$list[] = $n;
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
        $chk = $db->prepare("select * from notes where id=:p1");
        try{
            $chk->execute(["p1"=>$id]);
            if($d = $chk->fetch()){
                $r = new Notes();
                $r->hydrate($d);
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public static function filterBy(array $filters){
        $r = [];
        foreach(self::fetchAll() as $n){
            //session
            if(CheckIf::contains("session", $filters) && $n->getSession() == (int) $filters["session"]){
                $r[] = $n;
            }
            //etudiant
            if(!CheckIf::inArray($n, $r) && CheckIf::contains("etudiant", $filters) && $n->getEtudiant() == (int) $filters["etudiant"]){
                $r[] = $n;
            }
            //annee academique
            if(!CheckIf::inArray($n, $r) && CheckIf::contains("academie", $filters) && $n->getAnneeAcademique() == (int) $filters["academie"]){
                $r[] = $n;
            }
            //filiere
            if(
                !CheckIf::inArray($n, $r) && CheckIf::contains("filiere", $filters)  &&
                Faculty::getById(Course::getById($n->getCours())->getFiliere())->getNom() == $filters["filiere"]
            ){
                $r[] = $n;
            }
            //cours
            if(
                !CheckIf::inArray($n, $r) && CheckIf::contains("cours", $filters) &&
                $n->getCours() == (int) $filters["cours"]
            ){
                $r[] = $n;
            }
        }
        return $r;
    }

    public static function studentAlreadyHasNote(int $student, int $course, int $session, int $year){
        $r = false;
        foreach(self::fetchAll() as $n){
            if($n->getEtudiant() == $student && $n->getCours() == $course &&
               $n->getAnneeAcademique() == $year && $n->getSession() == $session
            ){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function evaluate(int $course, int $session, array $studnote){
        $r = "Unrecognized course id given !";
        $c = Course::getById($course);
        if($c == null) return $r;
        $r = "Invalid session number given";
        if($c->getSession() != $session) return $r;
        $r = null;
        foreach($studnote as $k => $v){
            $s = Student::getById((int) $k);
            if($s == null){
                $r = "Student id [ ".$k." ] is not recognized by the system !";
                break;
            }
            if(((double) $v) < 0 || ((double) $v) > 100){
                $r = "Invalid note given for ".$s->getCode();
                break;
            }
        }
        if($r != null) return $r;
        $db = Storage::Connect();
        $chk = null;
        foreach($studnote as $k => $v){
            $s = Student::getById((int) $k);
            $edit = self::studentAlreadyHasNote((int) $k, $course, $session, Storage::$currentYear->getId());
            if(!$edit){
                $chk = $db->prepare("insert into notes (session,id_etu,id_cours,note,annee_academique) values(:p1,:p2,:p3,:p4,:p5)");
            }else{
               $chk = $db->prepare("update notes set note=:p4 where session=:p1 and id_etu=:p2 and id_cours=:p3 and annee_academique=:p5");
            }
            try{
                $chk->execute([
                    "p1"=> $session,
                    "p2"=> (int) $k,
                    "p3"=> $course,
                    "p4"=> (double) $v,
                    "p5"=> Storage::$currentYear->getId()
                ]);
                $chk->closeCursor();
            }catch(Exception $e){
                \System\Log::printStackTrace($e);
                $r = "An error occured while saving student ".$s->getCode()." note";
            }
        }
        if($chk != null){
            Storage::update(new Notes());
            $chk->closeCursor();
        }
        return $r;
    }

    public function data(){
        return [
            "id" => $this->id,
            "session" => $this->session,
            "_etudiant" => $this->_etudiant,
            "_cours" => $this->_cours,
            "_annee_academique" => $this->_annee_academique,
            "note" => $this->note,
            "annee_academique" => $this->annee_academique,
            "etudiant" => $this->etudiant->data(),
            "cours" => $this->cours->data()
        ];
    }
}