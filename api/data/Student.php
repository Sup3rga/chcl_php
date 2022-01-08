<?php

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use System\Log;

class Student extends Civil{
    private $personne_ref, $telephone_ref, $etat, $annee_academique;
    private $_niveau = 0, $_annee_academique = 0, $id = 0;
    private Grade $niveau;
    public static $list = [];

    public function getNiveauData(){
        return $this->niveau;
    }

    public function getPersonneRef(){
        return $this->personne_ref;
    }

    public function setPersonneRef(string $personne_ref) {
        $this->personne_ref = $personne_ref;
    }

    public function getTelephoneRef(){
        return $this->telephone_ref;
    }

    public function setTelephoneRef(string $telephone_ref) {
        $this->telephone_ref = $telephone_ref;
    }

    public function getEtat() {
        return $this->etat;
    }

    public function setEtat(string $etat) {
        $etat = strtoupper($etat);
        $this->etat = $etat;
        $r = "Invalid state given !";
        if(!CheckIf::inArray($etat, ["A","T","D","E"])) return $r;
        $r = "This operation was rejected because the student is not in the last grade !";
        if($etat == "T" && !Grade::getById($this->_niveau)->isTerminal()) return $r;
        $r = null;
        $db = Storage::Connect();
        $db = $db->prepare("update etudiants set etat=:p1 where id=:p2");
        try {
            $db->execute([
                "p1" => $etat,
                "p2" => $this->id
            ]);
            Storage::update($this);
        }catch(Exception $e) {
            \System\Log::printStackTrace($e);
            $r = "An error occured during operation !";
        }
        $db->closeCursor();
        return $r;

    }

    public function getNiveau() {
        return $this->_niveau;
    }

    public function setNiveau(int $_niveau) {
        $this->_niveau = $_niveau;
    }

    public function getAnneeAcademique() {
        return $this->_annee_academique;
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getCode() {
        return parent::getCode();
    }

    public function setCode(string $code) {
        parent::setCode($code);
    }
    
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

    public function getSexe() {
        return parent::getSexe();
    }

    public function setSexe(string $sexe) {
        parent::setSexe($sexe);
    }

    public function getAdresse() {
        return parent::getAdresse();
    }

    public function setAdresse(string $adresse) {
        parent::setAdresse($adresse);
    }

    public function getEmail(){
        return parent::getEmail();
    }

    public function setEmail(string $email) {
        parent::setEmail($email);
    }

    public function getLieu_naissance() {
        return parent::getLieu_naissance();
    }

    public function setLieu_naissance(string $lieu_naissance) {
        parent::setLieu_naissance($lieu_naissance);
    }

    public function getDate_naissance() {
        return parent::getDate_naissance();
    }

    public function setDate_naissance(string $date_naissance) {
        parent::setDate_naissance($date_naissance);
    }

    public function getNif() {
        return parent::getNif();
    }

    public function setNif(string $nif) {
        parent::setNif($nif);
    }

    public function getNinu() {
        return parent::getNinu();
    }

    public function setNinu(string $ninu) {
        parent::setNinu($ninu);
    }

    public function getTelephone() {
        return parent::getTelephone();
    }

    public function setTelephone($telephone) {
        parent::setTelephone($telephone);
    }

    public function getPhoto() {
        return parent::getPhoto();
    }

    public function setPhoto($photo) {
        parent::setPhoto($photo);
    }

    public function getMemo() {
        return parent::getMemo();
    }

    public function setMemo($memo) {
        parent::setMemo($memo);
    }

    public function save(){
        $r = "Execution error";
        if(
            $this->code == null || $this->nom == null || $this->prenom == null || $this->sexe == null ||
            $this->adresse == null || $this->lieu_naissance == null || $this->date_naissance == null ||
            $this->_niveau == 0 || $this->email == null || $this->nif == null || $this->ninu == null ||
            ($this->id != 0 && $this->identite == 0) || ($this->identite != 0 && $this->id == 0) ||
            $this->personne_ref == null || $this->telephone_ref == null || $this->telephone == null
        ){
            return "Invalid data given for this operation";
        }

        $r = "incorrect reference person name !";
        if(!CheckIf::isFormalName($this->personne_ref)) return $r;
        $r = "incorrect reference person phone !";
        if(!CheckIf::isPhoneNumber($this->telephone_ref)) return $r;

        $r = parent::save();
        if($r != null) return $r;
        $db = Storage::Connect();
        $data = [];
        if($this->id == 0){
            $db = $db->prepare("insert into etudiants (identite,niveau,personne_reference,telephone_reference,
                annee_academique, etat) values(:p1,:p2,:p3,:p4,:p5,:p6)");
        }
        else{
            $db = $db->prepare("update etudiants set niveau=:p2,personne_reference=:p3,
                telephone_reference=:p4,annee_academique=:p5, etat=:p6 
                where id=:p7");
            $data["p7"] = $this->id;
        }
        $data["p1"] = $this->identite;
        $data["p2"] = $this->_niveau;
        $data["p3"] = $this->personne_ref;
        $data["p4"] = $this->telephone_ref;
        $data["p5"] = $this->id == 0 ? Storage::$currentYear->getId() : $this->_annee_academique;
        $data["p6"] = $this->id == 0 ? "A" : $this->etat;
        try {
            $db->execute($data);
            $r = null;
            Storage::update($this);
        }catch(Exception $e){
            \System\Log::printStackTrace($e);
            if($this->id == 0) {
                $this->rollback();
            }
        }
        $db->closeCursor();
        return $r;
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $db = $db->prepare("delete from individu where id=:p1");
        $db->execute(["p1"=> $this->identite]);
        Storage::update($this);
        $r = true;
        $db->closeCursor();
        return $r;
    }

    public function hydrate(array $data) {
        $this->identite = (int) $data["identite"];
        parent::init();
        $this->personne_ref = $data["personne_reference"];
        $this->telephone_ref = $data["telephone_reference"];
        $this->etat = $data["etat"];
        $this->_annee_academique = $data["annee_academique"];
        $this->_niveau = $data["niveau"];
        $this->id = $data["id"];
        $this->niveau = Grade::getById($this->_niveau);
        $this->annee_academique = AcademicYear::getById($this->_annee_academique)->getAcademie();
    }

    public static function fetchAll($override = false){
        if(!$override && count(self::$list) > 0){
            return self::$list;
        }
        self::$list = [];
        $db = Storage::Connect();
        $db = $db->prepare("select e.* from etudiants e, individu i where i.id = e.identite order by i.nom, i.prenom");
        $db->execute();
        while($data = $db->fetch()){
            $e = new Student();
            $e->hydrate($data);
            self::$list[] = $e;
        }
        $db->closeCursor();
        return self::$list;
    }

    public function getAverage($session, $ya = 0){
        $total = 0; $somme = 0; $k = 0;
        if($ya == 0){
            $db = Storage::Connect();
            foreach(Course::getAllFromNow($this->niveau->getFiliere(), $this->niveau->getId(), $session) as $c){
                $total += $c->getCoefficient() * 100;
                $chk = $db->prepare("select note from notes where session=:p1 and id_etu=:p2 and id_cours=:p3 and annee_academique=:p4");
                $chk->execute([
                    "p1"=> $session,
                    "p2"=> $this->id,
                    "p3"=> $c->getId(),
                    "p4"=> Storage::$currentYear->getId()
                ]);
                $somme += $c->getCoefficient() * (double) $chk->fetch()["note"];
                $k++;
                $chk->closeCursor();
            }
        }
        else {
            foreach (Notes::$list as $note) {
                if ($note->getAnneeAcademique() == $ya && $note->getEtudiant() == $this->id) {
                    $c = Course::getById($note->getCours());
                    $total += $c->getCoefficient() * 100;
                    $somme += $note->getNote() * $c->getCoefficient();
                }
            }
        }
        return round((double) $somme / (double) ($total > 0 ? $total : 1) * 100, 2);
    }

    public function hasAllNotes($session, $ya = null){
        $ya = $ya == null ? Storage::$currentYear->getId() : $ya;
        $r = false;
        $db = Storage::Connect();

        foreach(Course::getAllFromNow($this->niveau->getFiliere(), $this->niveau->getId(), $session) as $c){
            $chk = $db->prepare("select id from notes where session=:p1 and id_etu=:p2 and id_cours=:p3 and annee_academique=:p4");
            $chk->execute([
                "p1" => $session,
                "p2" => $this->id,
                "p3" => $c->getId(),
                "p4" => $ya
            ]);
            $r = $chk->rowCount() > 0;
            $chk->closeCursor();
        }
        return $r;
    }

    public function getAverageByAcademic($ya = null){
        $ya = $ya == null ? Storage::$currentYear->getId() : $ya;
        return ($this->getAverage(1,$ya) + $this->getAverage(2, $ya)) / 2;
    }

    public static function getById($id){
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
        $db = Storage::Connect()->prepare("select * from etudiants where id=:p1");
        try {
            $db->execute(['p1' => $id]);
            if ($db->rowCount()) {
                $r = new Student();
                $r->hydrate($db->fetch());
            }
        }catch(Exception $e){\System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    private static function extractData($name){

        $acceptedRow = [
            "code", "nom", "prenom", "sexe", "adresse", "lieu naissance", "date naissance", "telephone",
            "email", "nif", "ninu", "personne reference", "telephone personne reference", "memo",
            "filiere", "niveau"
        ];

        $found = 0;

        
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $reader->setLoadAllSheets();
        $filter = new CellsFilter();
        $reader->setReadFilter($filter);
        $spreadsheet = $reader->load($name);

        $students = [];

        $activeSheet =  $spreadsheet->getActiveSheet();
        for($i = 1, $j = $filter->getRowCount(); $i < $j; $i++){
            $data = [];
            foreach($filter->getAvailableCells() as $column => $rows){
                $index = strtolower(CheckIf::setPonctuationLess($activeSheet->getCell($column.$rows[0])->getValue()));
                if(in_array($index, $acceptedRow)){
                    $data[$index] = !isset($rows[$i]) ? null : $activeSheet->getCell($column.$rows[$i])->getValue();
                    $found++;   
                }
            }
            if($found < count($acceptedRow)) break;
            $students[] = $data;
        }

        if($found < count($acceptedRow)){
            throw new Exception("Invalid file formatting !");
        }

        return $students;
    }

    public static function registerFromXcel($excelFilename){
        $r = null;
        $data = null;
        try{
            $data = self::extractData($excelFilename);
        }catch(Exception $e){
            $r = $e->getMessage();
        }
        if($data == null){
            $r = $r != null ? $r : "An error occured during file extraction !";
        }
        else{
            $k = 1;
            foreach($data as $row){
                $f = Faculty::getBySoundLike($row["filiere"]);
                if($f == null){
                    $r = "Unrecognized faculty name given at entry ".$k;
                    break;
                }
                else {
                    $grade = $f->getGradeSoundLike($row["niveau"]);
                    if($grade == 0){
                        $r = "Unrecognized grade name given at entry ".$k;
                        break;
                    }
                    else{
                        $s = new Student();
                        $s->setCode($row["code"]);
                        $s->setNom($row["nom"]);
                        $s->setPrenom($row["prenom"]);
                        $s->setSexe($row["sexe"]);
                        $s->setAdresse($row["adresse"]);
                        $s->setLieu_naissance($row["lieu naissance"]);
                        $s->setDate_naissance($row["date naissance"]);
                        $s->setTelephone($row["telephone"]);
                        $s->setEmail($row["email"]);
                        $s->setNiveau($grade);
                        $s->setNif($row["nif"]);
                        $s->setNinu($row["ninu"]);
                        $s->setPersonneRef($row["personne reference"]);
                        $s->setTelephoneRef($row["telephone personne reference"]);
                        $s->setMemo($row["memo"]);

                        $r = $s->save();
                        if($r != null){
                            $r = "[ Entry " . $k . " ] " . $r;
                            break;
                        }
                    }
                }
                $k++;
            }
        }
        return $r;
    }

    public function data(){
        return CheckIf::merge(parent::data(), [
            "personne_ref" => $this->personne_ref,
            "telephone_ref" => $this->telephone_ref,
            "etat" => $this->etat,
            "annee_academique" => $this->annee_academique,
            "_niveau" => $this->_niveau,
            "_annee_academique" => $this->_annee_academique,
            "id" => $this->id,
            "niveau" => $this->niveau->data()
        ]);
    }
}