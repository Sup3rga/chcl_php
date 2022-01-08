<?php


class Course extends Data
{
    private $nom, $annee_academique, $etat, $titulaire, $suppleant, $code;
    private $session = 0, $_niveau = 0, $_titulaire = 0, $_filiere = 0, $_suppleant = 0, $_annee_academique = 0, $coefficient = 0, $id = 0;
    private $horaire = [];
    private Grade $niveau;
    public static $list = [];

    public function getHoraire() {
        return $this->horaire;
    }

    public function setHoraire(array $horaire) {
        $this->horaire = $horaire;
    }

    public function getNom() {
        return $this->nom;
    }

    public function setNom(string $nom) {
        $this->nom = $nom;
    }

    public function getNiveau() {
        return $this->_niveau;
    }

    public function setNiveau(int $niveau) {
        $this->_niveau = $niveau;
    }

    public function getFiliere(){
        return $this->_filiere;
    }

    public function getEtat() {
        return $this->etat;
    }

    public function setEtat(string $etat) {
        $r = "Invalid state given !";
        $this->etat = $etat;
        $oldState = $this->etat;
        if(!CheckIf::inArray($etat, ["E","D","S","N"])) return $r;
        $r = "This course has no teacher to dispense seance !";
        if($this->_titulaire == 0 && $this->_suppleant == 0) return $r;
        $this->etat = $etat;
        $r = "Session hours must be update before this operation to aconflict !";
        if(Course::seancesIsAllowed($this) != null){
            $this->etat = $oldState;
            return $r;
        }
        $r = "Execution error !";
        $db = Storage::Connect();
        $db = $db->prepare("update cours set etat = :p1 where id=:p2");
        $db->execute([
            "p1"=> $etat,
            "p2"=> $this->id
        ]);
        try{
            $db->execute();
            $r = null;
            Storage::update($this);
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    public function getSession() {
        return $this->session;
    }

    public function setSession(int $session) {
        $this->session = $session;
    }

    public function getTitulaire(){
        return $this->_titulaire;
    }

    public function setTitulaire(int $_titulaire) {
        $this->_titulaire = $_titulaire;
    }

    public function getSuppleant(){
        return $this->_suppleant;
    }

    public function setSuppleant(int $_suppleant) {
        $this->_suppleant = $_suppleant;
    }

    public function getAnneeAcademique(){
        return $this->_annee_academique;
    }

    public function setAnneeAcademique(int $_annee_academique) {
        $this->_annee_academique = $_annee_academique;
    }

    public function getCoefficient(){
        return $this->coefficient;
    }

    public function setCoefficient($coefficient) {
        $this->coefficient = $coefficient;
    }

    public function getId(){
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode(string $code) {
        $this->code = $code;
    }

    public function save(){
        if(
            $this->nom == null || $this->_niveau == 0 || $this->session == 0 ||
            $this->coefficient == 0 || $this->_titulaire == 0 || $this->code == null ||
            count($this->horaire) == 0
        ){
            return "Invalid data given !";
        }
        if($this->id == 0){
            $this->etat = "E";
            $this->_annee_academique = Storage::$currentYear->getId();
        }
        if(Course::codeExists($this)){
            $this->etat = null;
            return "Course code already exists !";
        }
        if($this->id == 0){
            $this->_annee_academique = Storage::$currentYear->getId();
        }
        $principal = Teacher::getById($this->_titulaire);
        $suppleant = Teacher::getById($this->_suppleant);
        $r = Course::seancesIsAllowed($this);
        if($r != null) return $r;
        $r = "Principal or Suppleant is not recognized by the system !";
        if(($principal == null && $this->_titulaire > 0) || ($suppleant == null && $this->_suppleant > 0)) return $r;
        $r = "Principal teacher is not active or available to dispense this course !";
        if(!CheckIf::inArray(strtolower($principal->getEtat()), ["m", "a"])) return $r;
        $r = "Suppleant teacher is not active or available to dispense this course !";
        if($suppleant != null && !CheckIf::inArray(strtolower($suppleant->getEtat()), ["m", "a"])) return $r;
        $r = "Execution error";
        $db = Storage::Connect();
        $chk = null;
        $data = [];
        if($this->id == 0){
            $chk = $db->prepare("insert into cours(nom,niveau,session, coefficient,titulaire,suppleant, annee_academique, etat, code) 
             values(:p1,:p2,:p3,:p4,:p5,:p6,:p7,:p8,:p10)");
        }
        else{
            $chk = $db->prepare("update cours set nom=:p1,niveau=:p2,session=:p3, coefficient=:p4,
                                titulaire=:p5,suppleant=:p6, annee_academique=:p7, etat=:p8, code=:p10 
                                 where id=:p9");
            $data["p9"] = $this->id;
        }
        $data["p1"] = $this->nom;
        $data["p2"] = $this->_niveau;
        $data["p3"] = $this->session;
        $data["p4"] = $this->coefficient;
        $data["p5"] = $this->_titulaire;
        $data["p6"] = $this->_suppleant == 0 ? null : $this->_suppleant;
        $data["p7"] = $this->_annee_academique;
        $data["p8"] = $this->etat;
        $data["p10"] = $this->code;
        try{
            $chk->execute($data);
            $edit = $this->id > 0;
            if(!$edit) $this->id = self::getLastId();
            $r = null;
            if($edit){
                $chk = $db->prepare("delete from dispensation where cours = :p1 and annee_academique=:p2");
                $chk->execute([
                    "p1"=>$this->id,
                    "p2"=> Storage::$currentYear->getId()
                ]);
            }
            foreach($this->horaire as $index => $_val) {
                $chk = $db->prepare("insert into dispensation(jour,heure_debut,heure_fin,cours,tp,annee_academique) 
                                        values (:p1,:p2,:p3,:p4,:p5,:p6)");
                try{
                    $chk->execute([
                        "p1" => (int) $_val["day"],
                        "p2" => $_val["begin"],
                        "p3" => $_val["end"],
                        "p4" => $this->id,
                        "p5" => (bool) $_val["tp"],
                        "p6" => Storage::$currentYear->getId()
                    ]);
                    $r = null;
                }catch(Exception $e){}
            }
            $principal->setEtat("A");
            if($this->suppleant != null){
                $this->suppleant->setEtat("A");
            }
            Storage::update($this);
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $chk->closeCursor();
        return $r;
    }

    public function delete() {
        $r = false;
        $db = Storage::Connect();
        $db = $db->prepare("delete from cours where id=:p1");
        try{
            $db->execute(["p1" => $this->id]);
            $r = true;
            Storage::update($this);
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    private function teacherFromList(int $id){
        $r = null;
        if(count(Teacher::$list)) {
            foreach(Teacher::$list as $teacher){
                if($teacher->getId() == $id){
                    $r = $teacher;
                    break;
                }
            }
        }
        return $r;
    }

    public function hydrate(array $data) {
        $this->nom = $data["nom"];
        $this->_niveau = (int) $data["niveau"];
        $this->_titulaire = (int) $data["titulaire"] == null ? "0" : $data["titulaire"];
        $this->_suppleant = $data["suppleant"] == null ? 0 : (int) $data["suppleant"];
        $this->_annee_academique = (int) $data["annee_academique"];
        $this->etat = $data["etat"];
        $this->code = $data["code"];
        $this->session = (int) $data["session"];
        $this->coefficient = (int) $data["coefficient"];
        $teacher = $this->teacherFromList($this->_titulaire);
        $this->titulaire = $teacher != null ? $teacher->getFullName() : null;
        $teacher = $this->teacherFromList($this->_suppleant);
        $this->suppleant = $teacher != null ? $teacher->getFullName() : null;
        $this->id = (int) $data["id"];
        $this->annee_academique = AcademicYear::getById($this->_annee_academique)->getAcademie();
        $this->niveau = Grade::getById($this->_niveau);
        $fac = $this->niveau->getFiliereData();
        $this->_filiere = $fac != null ? $fac->getId() : 0;
        $this->fetchHoraire();
        return $this;
    }

    private function fetchHoraire(){
        $db = Storage::Connect();
        $db = $db->prepare("select * from dispensation where cours=:p1");
        try{
            $db->execute(["p1"=> $this->id]);
            while($r = $db->fetch()){
                $this->horaire[] = [
                    "day"=>(int) $r["jour"],
                    "begin"=> $r["heure_debut"],
                    "end"=>$r["heure_fin"],
                    "id"=>(int) $r["id"],
                    "annee_academique"=> (int) $r["annee_academique"],
                    "tp"=> (bool) $r["tp"]
                ];
            }
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
    }

    public static function getLastId(){
        $db = Storage::Connect();
        $r = 0;
        $db = $db->prepare("select id from cours order by id desc limit 1");
        try{
            $db->execute();
            if($db->rowCount() > 0) {
                $r = (int) $db->fetch()["id"];
            }
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    public static function codeExists(Course $c){
        $r = false;
        foreach(self::fetchAll() as $e){
            if(strtolower($e->getCode()) == strtolower($c->getCode()) && $e->getId() != $c->getId()){
                $r = true;
                break;
            }
        }
        return $r;
    }

    public static function getAll(){
        $db = Storage::Connect();
        $db = $db->prepare("select * from cours");
        $r = [];
        try{
            $db->execute();
            while($data = $db->fetch()){
                $c = new Course();
                $c->hydrate($data, false);
                $r[] = $c;
            }
            $c = null;
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    public static function fetchAll($override = false){
        if(!$override && count(self::$list) > 0){
            return self::$list;
        }
        self::$list = [];
        $db = Storage::Connect();
        $db = $db->prepare("select * from cours");
        try{
            $db->execute();
            while($data = $db->fetch()){
                $c = new Course();
                $c->hydrate($data);
                self::$list[] = $c;
            }
            $c = null;
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return self::$list;
    }

    public static function getAllFrom(int $filiere, int $niveau, int $session, int $ya = 0){
        $r = [];
        foreach(self::fetchAll() as $c){
            if($c->getEtat() == "E" && $c->getSession() == $session && $c->getAnneeAcademique() == $ya &&
                (
                    $filiere < 1 || ($filiere == $c->getFiliere() && ($niveau < 1 || $niveau == $c->getNiveau()))
                )
            ){
                $r[] = $c;
            }
        }
        return $r;
    }

    public static function getAllFromNow(int $filiere, int $niveau, int $session){
        return self::getAllFrom($filiere, $niveau, $session,Storage::$currentYear->getId());
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
        $db = $db->prepare("select * from cours where id=:p1");
        try{
            $db->execute(["p1"=>$id]);
            if($db->rowCount()){
                $r = new Course();
                $r->hydrate($db->fetch());
            }
        }catch(Exception $e){System\Log::printStackTrace($e);}
        $db->closeCursor();
        return $r;
    }

    private static function sigma(string $hour){
        $h = explode(":",$hour);
        $r = 0;
        for($i = 0, $j = count($h); $i < $j; $i++){
            $r += ((int) $h[$i]) * ($i == 0 ? 60 : 1);
        }
        return $r;
    }

    private static function compareHours(
        array $h1,
        array $h2,
        string $course,
        bool $sameFac,
        bool $sameGrade,
        bool $samePrincipal,
        bool $sameSuppleant,
        bool $interPrincipal,
        bool $interSuppleant
    ){
        $r = null;
        $stop = false;
        foreach($h1 as $k => $d1){
            foreach($h2 as $j => $d2){
                $sameYear = $d1["annee_academique"] == $d2["annee_academique"];
                $s = [
                    self::sigma($d1["begin"]),
                    self::sigma($d1["end"]),
                    self::sigma($d2["begin"]),
                    self::sigma($d2["end"])
                ];
                $sameDay = (int) $d1["day"] == (int) $d2["day"];
                if(($s[0] < $s[3] && $s[1] >= $s[3] && $sameDay && $sameYear) || ($s[0] <= $s[2] && $s[1] > $s[2] && $sameDay && $sameYear)){
                    if($sameFac && $sameGrade){
                        $r = "session interval ".$d1["begin"]." - ".$d1["end"]." creates conflict with session interval ".
                            $d2["begin"]." - ".$d2["end"]." reserved for course ".$course." !";
                    }
                    if(((bool) $d1["tp"]) && ((bool) $d2["tp"]) && ($sameSuppleant || $interSuppleant) ){
                        $r = "session interval ".$d1["begin"]." - ".$d1["end"]." creates conflict with suppleant teacher availability !";
                    }
                    if(!((bool) $d1["tp"]) && !((bool) $d2["tp"]) && ($samePrincipal || $interPrincipal) ){
                        $r = "session interval ".$d1["begin"]." - ".$d1["end"]." creates conflict with principal teacher availability !";
                    }
                    if($r != null){
                        $stop = true;
                        break;
                    }
                }
            }
            if($stop) break;
        }
        return $r;
    }

    public static function seancesIsAllowed(Course $c){
        $r = null;
        if($c->getEtat() == null || !$c->getEtat() == "E") return $r;
        foreach(self::fetchAll() as $e){
            if($e->getId() != $c->getId() && $e->getEtat() == "E" &&
                (
                    $e->getFiliere() == $c->getFiliere() ||
                    $e->getTitulaire() == $c->getTitulaire() ||
                    $e->getSuppleant() == $c->getSuppleant() ||
                    $e->getSuppleant() == $c->getTitulaire() ||
                    $e->getTitulaire() == $c->getSuppleant() ||
                    $e->getNiveau() == $c->getNiveau()
                ) &&
                $e->getSession() == $c->getSession()
            ){
                System\Log::println("=== start ====");
                System\Log::println($e->getId()." / ".$c->getId()." >> ".$e->getSession()." | ".$c->getSession());
                System\Log::println($e->getHoraire()."\n".$c->getHoraire());
                $r = self::compareHours(
                    $c->getHoraire(),
                    $e->getHoraire(),
                    $e->getNom(),
                    $e->getFiliere() == $c->getFiliere(),
                    $e->getNiveau() == $c->getNiveau(),
                    $e->getTitulaire() == $c->getTitulaire(),
                    $e->getSuppleant() == $c->getSuppleant(),
                    $e->getSuppleant() == $c->getTitulaire(),
                    $e->getTitulaire() == $c->getSuppleant()
                );
                if($r != null) break;
            }
        }
        return $r;
    }

    public function data(){
        return [
            "nom" => $this->nom,
            "annee_academique" => $this->annee_academique,
            "etat" => $this->etat,
            "titulaire" => $this->titulaire,
            "suppleant" => $this->suppleant,
            "code" => $this->code,
            "session" => $this->session,
            "_niveau" => $this->_niveau,
            "_titulaire" => $this->_titulaire,
            "_filiere" => $this->_filiere,
            "_suppleant" => $this->_suppleant,
            "_annee_academique" => $this->_annee_academique,
            "coefficient" => $this->coefficient,
            "id" => $this->id,
            "horaire" => $this->horaire,
            "niveau"=>$this->niveau->data(),
        ];
    }
}