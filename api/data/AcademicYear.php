<?php


class AcademicYear extends Data
{
    private $academie, $debut, $fin, $etat;
    private $annee_debut = 0, $annee_fin = 0, $id = 0, $nb_cours = 0, $nb_prof = 0, $nb_etu = 0, $nb_filiere = 0;
    public static $list = [];

    public function getNbCourse() {
        return $this->nb_cours;
    }

    public function getNbTeacher() {
        return $this->nb_prof;
    }

    public function getNbStudent() {
        return $this->nb_etu;
    }

    public function getNbBranch() {
        return $this->nb_filiere;
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
        return $this;
    }

    public function getAcademie(){
        return $this->academie;
    }

    public function getDebut() {
        return $this->debut;
    }

    public function setDebut(string $debut) {
        if(!preg_match("/^[0-9]{4}(-[0-9]{2}){2}$/", $debut)) return $this;
        $this->debut = $debut;
        $this->annee_debut = (int) preg_replace("/([0-9]{4})]/", "$1", $debut);
        return $this;
    }

    public function getFin() {
        return $this->fin;
    }

    public function setFin(string $fin) {
        if(!preg_match("/^[0-9]{4}(-[0-9]{2}){2}$/", $fin)) return $this;
        $this->fin = $fin;
        $this->annee_fin = (int) preg_replace("/([0-9]{4})]/", "$1", $fin);
        return $this;
    }

    public function getEtat(){
        return $this->etat;
    }

    public function getAnneeDebut(){
        return $this->annee_debut;
    }

    public function getAnneeFin() {
        return $this->annee_fin;
    }

    private function fetchStat(){
        $db = Storage::Connect();
        //cours
        $chk = $db->prepare("select count(*) as cours from 
            (select DISTINCT c.id from cours c, dispensation h where h.cours = c.id and h.annee_academique = :p1) as fl_cours");
        $chk->execute(["p1"=> $this->id]);
        if($chk->rowCount() > 0){
            $this->nb_cours = (int) $chk->fetch()["cours"];
        }
        //Prof
        $chk = $db->prepare("select count(*) as prof from 
            (select DISTINCT p.id from professeurs p, cours c, dispensation h where 
            (c.titulaire = p.id or c.suppleant = p.id) and h.cours = c.id and h.annee_academique in 
            (select y.id from annee_academique a, annee_academique y where a.annee_debut <= YEAR(NOW()) and a.annee_debut >= y.annee_debut and a.id = :p1)) as fl_prof");
        $chk->execute(["p1"=> $this->id]);
        if($chk->rowCount() > 0){
            $this->nb_prof = (int) $chk->fetch()["prof"];
        }
        //Etudiant
        $chk = $db->prepare("select count(id) as etu from etudiants where annee_academique in 
         (select DISTINCT y.id from annee_academique a, annee_academique y where a.annee_debut <= YEAR(NOW()) and a.id = :p1 and a.annee_debut >= y.annee_debut)");
        $chk->execute(["p1"=> $this->id]);
        if($chk->rowCount() > 0){
            $this->nb_etu = (int) $chk->fetch()["etu"];
        }
        //Filières
        $chk = $db->prepare("select count(id) as fil from filieres where annee_academique in 
        (select y.id from annee_academique a, annee_academique y where a.annee_debut <= YEAR(NOW()) and a.id = :p1 and a.annee_debut >= y.annee_debut)");
        $chk->execute(["p1"=> $this->id]);
        if($chk->rowCount() > 0){
            $this->nb_filiere = (int) $chk->fetch()["fil"];
        }
        $chk->closeCursor();
        return $this;
    }

    public function hydrate(array $data){
        $this->id = (int) $data["id"];
        $this->academie = $data["academie"];
        $this->debut = (int) $data["debut"];
        $this->fin = $data["fin"];
        $this->etat = $data["etat"];
        $this->annee_debut = (int) $data["annee_debut"];
        $this->annee_fin = (int) $data["annee_fin"];
        $this->fetchStat();
    }

    public static function atBeginning(){
        return count(self::fetchAll()) == 0;
    }

    public static function yearExists(AcademicYear $year){
        $r = false;
        foreach (self::fetchAll() as $e){
            if($year->getId() != $e->getId() && $year->getAnneeDebut() == $e->getAnneeDebut() && $year->getAnneeFin() == $e->getAnneeFin()){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public function save(){
        $r = "Invalid beginning year and ending year given !";
        if($this->annee_fin - $this->annee_debut != 1) return $r;
        $r = "Academic year already exists";
        if(self::yearExists($this)) return $r;
        $r = "Execution error";
        $db = Storage::Connect();
        $data = [];
        $chk = null;
        if($this->id == 0){
            $chk = $db->prepare("insert into annee_academique(academie, debut, fin, annee_debut, annee_fin,etat) 
            values(CONCAT(YEAR(:p1), '-', YEAR(:p2)), :p1, :p2, YEAR(:p1), YEAR(:p2), :p4)");
            $data["p4"] = self::atBeginning() ? "O" : "F";
        }
        else{
            $chk = $db->prepare("update annee_academique set academie = CONCAT(YEAR(:p1), '-', YEAR(:p2)), 
            debut =:p1, fin =:p2, annee_debut=YEAR(:p1), annee_fin=YEAR(:p2) where id=:p3");
            $data["p3"] = $this->id;
        }
        $data["p1"] = $this->debut;
        $data["p2"] = $this->fin;
        try {
            $chk->execute($data);
            Storage::update($this);
            $r = null;
        }catch(Exception $e){}
        $chk->closeCursor();

        return $r;
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $db = $db->prepare("delete from annee_academique where id=:p1");
        try{
            $db->execute(["p1"=> $this->id]);
            $r = true;
            Storage::update($this);
        }catch(Exception $e){};
        $db->closeCursor();
        return $r;
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
        $chk = $db->prepare("select * from annee_academique where id=:p1");
        $chk->execute();
        if($chk->rowCount()){
            $r = new AcademicYear();
            $r->hydrate($chk->fetch());
        }
        $chk->closeCursor();
        return $r;
    }

    public static function fetchAll(bool $override=false){
        if(!$override && count(self::$list) > 0){
            return self::$list;
        }
        self::$list = [];
        $db = Storage::Connect();
        $chk = $db->prepare("select * from annee_academique");
        Storage::$currentYear = null;
        $chk->execute();
        while($e = $chk->fetch()){
            $ya = new AcademicYear();
            $ya->hydrate($e);
            self::$list[] = $ya;
            if($ya->getEtat() == "O"){
                Storage::$currentYear = $ya;
            }
        }
        $chk->closeCursor();
        return self::$list;
    }

    public static function getByName(string $name){
        $ya = null;
        foreach(self::fetchAll() as $e){
            if($e->getAcademie() == $name){
                $ya = $e;
                break;
            }
        }
        return $ya;
    }

    public static function alreadyOneOpen(AcademicYear $ya){
        $r = false;
        foreach(self::fetchAll() as $e){
            if($e->getId() != $ya->getId() && $e->getEtat() == "O" ){
                $r = true;
                break;
            }
        }
        return $r;
    }

    private function promoteStudents(){
        $db = Storage::Connect();
        foreach(Student::$list as $s){
            if($s->getEtat() == "A") {
                $average = $s->getAverageByAcademic();
                $pass = $average >= 65;
                if($pass){
                    $data = [];
                    if ($s->getNiveauData()->isTerminal()) {
                        $db = $db->prepare("update etudiants set etat='T' where id=:p1");
                    } else {
                        $db = $db->prepare("update etudiants set niveau=:p2 where id=:p1");
                        $data["p2"] = $s->getNiveauData()->next();
                    }
                    $data["p1"] = $s->getId();
                    $db->execute();
                    $db->closeCursor();
                }
            }
        }
        return $this;
    }

    public function passToNextYear(){
        $r = "";
        $list = Student::$list;
        $all = count($list);
        $session = 2;
        $total = [];
        foreach($list as $s){
            if($s->getEtat() ==  "A"){
                for($i = 1; $i <= $session; $i++) {
                    if(!isset($total[$i - 1])){
                        $total[$i - 1] = 0;
                    }
                    if ($s->hasAllNotes($i)) {
                        $total[$i - 1]++;
                    }
                }
            }
            else{
                for($i = 1; $i <= $session; $i++) {
                    if (!isset($total[$i - 1])) {
                        $total[$i - 1] = 0;
                    }
                    $total[$i - 1]++;
                }
            }
        }

        for($i = 1; $i <= $session; $i++) {
            if($all - $total[$i - 1] > 0){
                $r .= ($r == null ? "" : " and ").($all - $total[$i - 1])." student".($all - $total[$i - 1] > 1 ? "s don't have all their grades yet" : " doesn't have all his grades yet")." for session ".$i;
            }
        }
        $r = strlen($r) == 0 ? null : $r;
        if($r == null){
            $this->promoteStudents();
            $this->goToNextYear();
            Storage::update($this);
        }
        return $r;
    }

    private function goToNextYear(){
        if($this->isLastKnownYear()){
            $db = Storage::Connect();
            $db = $db->prepare("insert into annee_academique (academie, debut, fin, annee_debut, annee_fin, etat)
                values(CONCAT(YEAR(:p1 + INTERVAL 1 YEAR),'-',YEAR(:p2 + INTERVAL 1 YEAR)), 
                :p1 + INTERVAL 1 YEAR, :p2 + INTERVAL 1 YEAR, YEAR(:p1 + INTERVAL 1 YEAR), YEAR(:p2 + INTERVAL 1 YEAR), 'O')");
            $db->execute([
                "p1"=>$this->debut,
                "p2"=>$this->fin
            ]);
            $db->closeCursor();
        }
        else{
            self::setState($this->next()->getId(), true);
            self::setState($this->id, false);
        }
    }

    private static function setState(int $id, bool $open){
        $db = Storage::Connect();
        $db = $db->prepare("update annee_academique set etat =:p2 where id=:p1");
        $db->execute([
            "p1"=> $id,
            "p2"=> $open ? "O" : "F"
        ]);
        Storage::update(new AcademicYear());
        $db->closeCursor();
    }

    public function next(){
        $ya = null;
        foreach(self::fetchAll() as $e){
            if($e->getAnneeDebut() - $this->annee_debut == $e->getAnneeFin() - $this->annee_fin && $e->getAnneeFin() - $this->annee_fin == 1){
                $ya = $e;
                break;
            }
        }
        return $ya;
    }

    public function previous(){
        $ya = null;
        foreach(self::fetchAll() as $e){
            if($e->getAnneeDebut() - $this->annee_debut == $e->getAnneeFin() - $this->annee_fin && $e->getAnneeFin() - $this->annee_fin == -1){
                $ya = $e;
                break;
            }
        }
        return $ya;
    }

    public function isLastKnownYear(){
        $r = true;
        foreach(self::fetchAll() as $ya){
            if($ya->getAnneeDebut() > $this->annee_debut && $ya->getAnneeFin() > $this->annee_fin){
                $r = false;
                break;
            }
        }
        return $r;
    }

    public function getPrecedentsAndNow(){
        $list = [];
        foreach(self::fetchAll() as $ya){
            if($ya->getAnneeFin() <= $this->annee_fin){
                $list[] = $ya;
            }
        }
        if(count($list) >= 0){
//            $minYear = $list[0]->getAnneeDebut();
            for($i = 0, $j = count($list); $i < $j - 1; $i++){
                for($k = $i + 1; $k < $j; $k++){
                    if($list[$i]->getAnneeDebut() >= $list[$k]->getAnneeDebut()){
                        $tmp = $list[$i];
                        $list[$i] = $list[$k];
                        $list[$k] = $tmp;
                    }
                }
            }
        }
        return $list;
    }

    public function data(){
        return [
            "academie" => $this->academie,
            "debut" => $this->debut,
            "fin" => $this->fin,
            "etat" => $this->etat,
            "annee_debut" => $this->annee_debut,
            "annee_fin" => $this->annee_fin,
            "id" => $this->id,
            "nb_cours" => $this->nb_cours,
            "nb_prof" => $this->nb_prof,
            "nb_etu" => $this->nb_etu,
                "nb_filiere" => $this->nb_filiere
        ];
    }
}