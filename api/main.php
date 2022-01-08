<?php

use System\Log;

header("Content-Type: text/plain;utf-8;charset=UTF-8");
ThunderSpeed::$baseDir = realpath("../assets/tmp/")."/";
ThunderSpeed::$uploadDir = realpath("../assets/ths_dir/")."/";
$ths = new ThunderSpeed();
$ths->watch(['st_avatar', 'st_excel', 'th_upl_avatar', 'usr_upl_avatar']);
$result = [
    "error"=> true,
    "code"=> 2,
    "message"=> "Access denied"
];

//Si le token et l'uid ne sont pas renseigné, la requête n'est pas autorisée
if(!($post = CheckIf::isRequest($_POST, ['reqtoken', 'requid']))){
    echo json_encode($result);
    exit(0);
}
extract($post);
$reqtoken = $_POST['reqtoken'];
$client = Storage::getTokenUser($reqtoken);

if($client == null){
    echo  json_encode($result);
    die;
}

$result["message"] = "Invalid request !";

//@Academic year registration
if($reqVar = CheckIf::isRequest($_POST, ["anneeAka_begin", "anneeAka_end"])){
    if(!$client->hasPrivilege(10)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $editId = CheckIf::isRequest($_POST, ["reqeditid"]);
    $edit = $editId != false;
    if($edit) $editId = $editId["reqeditid"];
    $ya = $edit ? AcademicYear::getById((int) $editId) : new AcademicYear();
    $ya->setDebut($reqVar["anneeAka_begin"]);
    $ya->setFin($reqVar["anneeAka_end"]);
    $exec = $ya->save();
    $result["message"] = "incoherent data given !";
    if($exec == null){
        $result["code"] = 1;
        $result["error"] = false;
        try {
            $result["data"] = Ressource::data("/academic-year");
        } catch (Exception $e) {
            Log::printStackTrace($e);
        }
        $result["message"] = "Success";
    }
}
//@AcademicYear passation
else if($reqVar = CheckIf::isRequest($_POST, ["akid", "akpass"])){
    if(!$client->hasPrivilege(11)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $ya = AcademicYear::getById((int) $reqVar["akid"]);
    if($ya != null){
        $result["message"] = "Can't pass an academic year already closed !";
        if(strtoupper($ya->getEtat()) == "O"){
            $last = $ya->isLastKnownYear();
            $msg = $ya->passToNextYear();
            $result["message"] = $msg;
            if($msg == null){
                $result["error"] = false;
                $result["code"] = 1;
                if($last){
                    $result["message"] = "There's a new academic year created ! Please remember to edit his deadlines.";
                }
                else{
                    $nya = Storage::$currentYear;
                    $result["message"] = "Welcome to new academic year of ".$nya->getAcademie()." ! ";
                }
                try{
                    $result["data"] = Ressource::allData();
                }catch(Exception $e){
                    Log::printStackTrace($e);}
            }
        }
    }
}
//@Faculty registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["facname"])){
    $fac = new Faculty();
    $facid = htmlentities($_POST["facid"]);
    $fac->setNom($reqVar["facname"]);
    if($facid != null && CheckIf::isNumber($facid)){
        if(!$client->hasPrivilege(32)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
        $fac->setId((int) $facid);
    }
    else if(!$client->hasPrivilege(31)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $msg = $fac->save();
    $result["message"] = $msg;
    if($msg == null){
        $result["error"] = false;
        $result["message"] = "Access granted !";
        try {
            $result["data"] = Ressource::data("/admin");
        } catch (Exception $e) {
            Log::printStackTrace($e);
        }
    }
}
//@Faculty wiping
else if($reqVar = CheckIf::isRequest($_POST, ["facdelid"])){
    if(!$client->hasPrivilege(33)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $fac = Faculty::getById((int) $reqVar["facdelid"]);
    if($fac == null){
        $result["message"] = "Invalid faculty identifiant given !";
    }
    else{
        if($fac->delete()){
            $result["error"] = false;
            $result["message"] = "Access granted !";
            try {
                $result["data"] = Ressource::data("/admin");
            } catch (Exception $e) {
                Log::printStackTrace($e);}
        }
        else{
            $result["message"] = "Execution error !";
        }
    }
}
//@Hierarchy registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["poste_name", "poste_capacity", "poste_value"])){
    $linked = htmlentities($_POST["poste_linked"]);
    $id = htmlentities($_POST["poste_id"]);
    $hr = new Hierarchy();
    $hr->setCapacity((int) $reqVar["poste_capacity"]);
    $hr->setNotation($reqVar["poste_name"]);
    $hr->setValue((int) $reqVar["poste_value"]);
    if($linked != null && CheckIf::isNumber($linked)) {
        $hr->setAffectation((int) $linked);
    }
    if($id != null && CheckIf::isNumber($id)) {
        if(!$client->hasPrivilege(29)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
        $hr->setId((int) $id);
    }
    else if(!$client->hasPrivilege(28)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $msg = $hr->save();
    $result["message"] = $msg;
    if($msg == null){
        $result["message"] = "Success !";
        $result["code"] = 1;
        $result["error"] = false;
        try {
            $result["data"] = Ressource::data("/admin");
        } catch (Exception $e) {
            Log::printStackTrace($e);
        }
    }
}
//@Hierarchy wiping
else if($reqVar = CheckIf::isRequest($_POST, ["postedelid"])){
    if(!$client->hasPrivilege(30)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $hr = Hierarchy::getById((int) $reqVar["postedelid"]);
    if($hr == null){
        $result["message"] = "Invalid faculty identifiant given !";
    }
    else{
        if($hr->delete()){
            $result["error"] = false;
            $result["message"] = "Access granted !";
            try {
                $result["data"] = Ressource::data("/admin");
            } catch (Exception $e) {
                Log::printStackTrace($e);
            }
        }
        else{
            $result["message"] = "Execution error !";
        }
    }
}
//@Grade registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["fac-grade-notation", "fac-grade-value", "fac-id"])){
    $g = new Grade();
    $g->setFiliere((int) $reqVar["fac-id"]);
    $g->setAnnee((int) $reqVar["fac-grade-value"]);
    $g->setNotation($reqVar["fac-grade-notation"]);
    $gradeId = CheckIf::set($_POST["fac-grade-id"]);
    if($gradeId != null && strlen($gradeId) > 0){
        if(!$client->hasPrivilege(35)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
        $g->setId((int) $gradeId);
    }
    else if(!$client->hasPrivilege(34)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $msg = $g->save();
    $result["message"] = $msg;
    if($msg == null){
        try {
            $result["data"] = Ressource::data("/admin");
            $result["error"] = false;
            $result["code"] = 1;
        } catch (Exception $e) {
            Log::printStackTrace($e);
            $result["code"] = 2;
        }
    }
}
//@Grade wiping
else if($reqVar = CheckIf::isRequest($_POST, ["fac-grade-delid"])){
    if(!$client->hasPrivilege(36)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $g = Grade::getById((int) $reqVar["fac-grade-delid"]);
    if($g == null){
        $result["message"] = "Invalid grade id given !";
    }
    else{
        if($g->delete()){
            $result["message"] = "Success !";
            $result["error"] = false;
            try {
                $result["data"] = Ressource::data("/admin");
            } catch (Exception $e) {
                Log::printStackTrace($e);
            }
        }
        else{
            $result["message"] = "Execution error !";
        }
    }
}
//@Teacher registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["th_nom", "th_birthdate","th_birthplace",
    "th_email","th_nif","th_ninu", "th_address","th_phone","th_prenom","th_sexe",
    "th_skill","th_status","th_code", "th_sales"])
){
    $memo = CheckIf::set($_POST["th_memo"]);
    $id = CheckIf::set($_POST["th_id"]);
    $avatar = CheckIf::set($_POST["th_avatar"]);
    $avatar = $ths->isUploaded($avatar) ? $avatar : null;
    $hierarchy = CheckIf::set($_POST["th_hierarchy"]);
    $t = $id != null && CheckIf::isNumber($id) ? Teacher::getById((int) $id) : new Teacher();
    if($id != null && CheckIf::isNumber($id)) {
        if (!$client->hasPrivilege(24)) {
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
    }
    else if(!$client->hasPrivilege(23)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    if($t == null){
        $result["message"] = "User doesn't exist in the system !";
    }
    else {
        $t->uploadPhoto($avatar);
        $t->setNom($reqVar["th_nom"]);
        $t->setPrenom($reqVar["th_prenom"]);
        $t->setAdresse($reqVar["th_address"]);
        $t->setCode($reqVar["th_code"]);
        $t->setDate_naissance($reqVar["th_birthdate"]);
        $t->setLieu_naissance($reqVar["th_birthplace"]);
        $t->setEmail($reqVar["th_email"]);
        $t->setNiveauEtude($reqVar["th_skill"]);
        $t->setNif($reqVar["th_nif"]);
        $t->setNinu($reqVar["th_ninu"]);
        $t->setTelephone($reqVar["th_phone"]);
        $t->setSexe($reqVar["th_sexe"]);
        $t->setSalaire( (double) $reqVar["th_sales"]);
        $t->setStatus_matrimonial($reqVar["th_status"]);
        if ($hierarchy != null && strlen($hierarchy) > 0) {
            $t->setPoste((int) $hierarchy);
        }
        if ($id != null && CheckIf::isNumber($id)) {
            $t->setId((int) $id);
        }
        if ($memo != null) {
            $t->setMemo($memo);
        }

        $msg = $t->save();
        $result["message"] = $msg;
        if ($msg == null) {
            $result["error"] = false;
            $result["message"] = "Success !";
            $result["data"] = Ressource::data("/teacher");
            $ths->move($avatar, realpath("../assets/avatars/"));
        }
        else{
            $ths->flush($avatar);
        }
    }
}
//@Teacher wiping
else if($reqVar = CheckIf::isRequest($_POST, ["th_del_id"])){
    if(!$client->hasPrivilege(25)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $t = Teacher::getById((int) $reqVar["th_del_id"]);
    if($t == null){
        $result["message"] = "The teacher doesn't exist in the system";
    }
    else{
        if($t->delete()){
            $result["error"] = false;
            $result["code"] = 1;
            $result["message"] = "Success !";
            try{
                $result["data"] = Ressource::data("/teacher");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
        else{
            $result["message"] = "An error occured during operation";
        }
    }
}
//@Teacher state
else if($reqVar = CheckIf::isRequest($_POST, ["th_state", "th_id"])){
    if(!$client->hasPrivilege(26)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $t = Teacher::getById((int) $reqVar["th_id"]);
    if($t == null){
        $result["message"] = "The teacher doesn't exist in the system";
    }
    else{
        $msg = $t->setEtat($reqVar["th_state"]);
        $result["message"] = $msg;
        if($msg == null){
            $result["message"] = "Success !";
            $result["error"] = false;
            $result["code"] = 1;
            try{
                $result["data"] = Ressource::data("/teacher");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
    }
}
//@Student registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["st_nom", "st_birthdate","st_birthplace",
        "st_email","st_nif","st_ninu", "st_address","st_phone","st_prenom","st_sexe",
        "st_skill","st_code", "st_person_ref", "st_phone_ref"])
){
    $memo = CheckIf::set($_POST["st_memo"]);
    $avatar = CheckIf::set($_POST["st_upl_avatar"]);
    $avatar = $ths->isUploaded($avatar) ? $avatar : null;
    $id = CheckIf::set($_POST["st_id"]);
    if($id != null && CheckIf::isNumber($id)){
        if(!$client->hasPrivilege(13)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
    }
    else if(!$client->hasPrivilege(12)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $t = $id != null && CheckIf::isNumber($id) ? Student::getById((int) $id) : new Student();
    if($t == null){
        $result["message"] = "User doesn't exist in the system !";
    }
    else {
        $t->setNom($reqVar["st_nom"]);
        $t->setPrenom($reqVar["st_prenom"]);
        $t->setAdresse($reqVar["st_address"]);
        $t->setCode($reqVar["st_code"]);
        $t->setDate_naissance($reqVar["st_birthdate"]);
        $t->setLieu_naissance($reqVar["st_birthplace"]);
        $t->setEmail($reqVar["st_email"]);
        $t->setNiveau((int) $reqVar["st_skill"]);
        $t->setNif($reqVar["st_nif"]);
        $t->setNinu($reqVar["st_ninu"]);
        $t->setTelephone($reqVar["st_phone"]);
        $t->setSexe($reqVar["st_sexe"]);
        $t->setPersonneRef($reqVar["st_person_ref"]);
        $t->setTelephoneRef($reqVar["st_phone_ref"]);
        $t->uploadPhoto($avatar);
        if ($id != null && CheckIf::isNumber($id)) {
            $t->setId((int) $id);
        }
        if ($memo != null) {
            $t->setMemo($memo);
        }

        $msg = $t->save();
        $result["message"] = $msg;
        if ($msg == null) {
            $result["error"] = false;
            $result["message"] = "Success !";
            $result["data"] = Ressource::data("/student");
            $ths->move($avatar, realpath("../assets/avatars/"));
        }
        else{
            $ths->flush($avatar);
        }
    }
}
//@Student registration from file
else if($reqVar = CheckIf::isRequest($_POST, ["st_list"])){
    if(!$client->hasPrivilege(12)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $msg = Student::registerFromXcel(ThunderSpeed::$uploadDir . $reqVar["st_list"]);
    $result["message"] = $msg;
    $ths->flush($reqVar["st_list"]);
    if($msg == null){
        $result["message"] = "Success !";
        $result["code"] = 1;
        $result["error"] = false;
        $result["data"] = Ressource::data("/student");
    }
}
//@Student wiping
else if($reqVar = CheckIf::isRequest($_POST, ["st_del_id"])){
    if(!$client->hasPrivilege(14)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $t = Student::getById((int) $reqVar["st_del_id"]);
    if($t == null){
        $result["message"] = "The teacher doesn't exist in the system";
    }
    else{
        if($t->delete()){
            $result["error"] = false;
            $result["code"] = 1;
            $result["message"] = "Success !";
            try{
                $result["data"] = Ressource::data("/student");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
        else{
            $result["message"] = "An error occured during operation";
        }
    }
}
//@Student state
else if($reqVar = CheckIf::isRequest($_POST, ["st_state", "st_id"])){
    if(!$client->hasPrivilege(15)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $t = Student::getById((int) $reqVar["st_id"]);
    if($t == null){
        $result["message"] = "The teacher doesn't exist in the system";
    }
    else{
        $msg = $t->setEtat($reqVar["st_state"]);
        $result["message"] = $msg;
        if($msg == null){
            $result["message"] = "Success !";
            $result["error"] = false;
            $result["code"] = 1;
            try{
                $result["data"] = Ressource::data("/teacher");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
    }
}
//@Course registration/edition
else if($reqVar = CheckIf::isRequest($_POST, ["cr_grade", "cr_name", "cr_principale", "cr_hours", "cr_session", "cr_rate", "cr_code"])){
    $ok = true;
    $hours = $reqVar["cr_hours"];
    if(!is_array($hours)){
        $result["message"] = "Error from parsing data !";
        $ok = false;
    }
    if($ok){
        $id = CheckIf::set($_POST["cr_id"]);
        $suppleant = CheckIf::set($_POST["cr_suppleant"]);
        $edit = $id != null && CheckIf::isNumber($id);
        if($edit){
            if(!$client->hasPrivilege(18)){
                $result["message"] = "Access denied !";
                echo json_encode($result);
                die;
            }
        }else if(!$client->hasPrivilege(17)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
        $c = $edit ? Course::getById((int) $id) : new Course();
        if($c == null){
            $result["message"] = "This course doesn't exist in the system !";
        }
        else {
            $c->setTitulaire((int) $reqVar["cr_principale"]);
            if ($suppleant != null && CheckIf::isNumber($suppleant)) {
                $c->setSuppleant((int) $suppleant);
            }
            $c->setNom($reqVar["cr_name"]);
            $c->setHoraire($hours);
            $c->setCode($reqVar["cr_code"]);
            $c->setCoefficient((int) $reqVar["cr_rate"]);
            $c->setNiveau((int) $reqVar["cr_grade"]);
            $c->setSession((int) $reqVar["cr_session"]);

            $msg = $c->save();
            $result["message"] = $msg;
            if ($msg == null) {
                $result["error"] = false;
                $result["message"] = "Success !";
                $result["code"] = 1;
                try {
                    $result["data"] = Ressource::data("/course");
                } catch (Exception $e) {
                    Log::printStackTrace($e);
                }
            }
        }
    }
}
//@Course wiping
else if($reqVar = CheckIf::isRequest($_POST, ["cr_del_id"])){
    if(!$client->hasPrivilege(19)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $c = Course::getById((int) $reqVar["cr_del_id"]);
    if($c == null){
        $result["message"] = "The course doesn't exist in the system";
    }
    else{
        if($c->delete()){
            $result["error"] = false;
            $result["code"] = 1;
            $result["message"] = "Success !";
            try{
                $result["data"] = Ressource::data("/course");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
        else{
            $result["message"] = "An error occured during operation";
        }
    }
}
//@Course state
else if(($reqVar = CheckIf::isRequest($_POST, ["cr_state", "cr_id"])) != null){
    if(!$client->hasPrivilege(20)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $c = Course::getById((int) $reqVar["cr_id"]);
    if($c == null){
        $result["message"] = "The course doesn't exist in the system";
    }
    else{
        $msg = $c->setEtat($reqVar["cr_state"]);
        $result["message"] = $msg;
        if($msg == null){
            $result["message"] = "Success !";
            $result["error"] = false;
            $result["code"] = 1;
            try{
                $result["data"] = Ressource::data("/course");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
    }
}
//@Note evaluation
else if(($reqVar = CheckIf::isRequest($_POST, ["nt_course", "nt_studnote", "nt_session"])) != null){
    if(!$client->hasPrivilege(41)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $ok = true;
    $notes = $reqVar["nt_studnote"];
    if(!is_array($notes)) {
        $result["message"] = "Error from parsing data !";
        $ok = false;
    }
    if($ok){
        $msg = Notes::evaluate((int) $reqVar["nt_course"],(int) $reqVar["nt_session"],$notes);
        $result["message"] = $msg;
        if($msg == null){
            $result["error"] = false;
            $result["message"] = "Success !";
            $result["code"] = 1;
            try{
                $result["data"] = Ressource::data("/notes");
            }catch(Exception $e){
                Log::printStackTrace($e);
            }
        }
    }
}
//@User registration /edition
else if(($reqVar = CheckIf::isRequest($_POST, ["usr_nom", "usr_prenom", "usr_pseudo","usr_access"])) != null){
    $id = CheckIf::set($_POST["usr_id"]);
    $poste = CheckIf::set($_POST["usr_hierarchy"]);
    $avatar = CheckIf::set($_POST["usr_avatar"]);
    $avatar = $ths->isUploaded($avatar) ? $avatar : null;
    $password = CheckIf::set($_POST["usr_passcode"]);
    $new_passcode = CheckIf::set($_POST["usr_new_passcode"]);
    $new_passcode = $new_passcode == null ? "" : $new_passcode;
    $password = $password == null ? "" : $password;
    $myself = $client->getId() == (int) $id;
    if(($id == null && strlen($password) == 0) ||
        ($id != null && !CheckIf::isNumber($id)) ||
        ($poste != null && strlen($poste) > 0 && !CheckIf::isNumber($poste))
    ){
        $result["message"] = "Invalid data submitted !";
    }
    else{
        if($id != null){
            if($client->getId() != (int) $id && !$client->hasPrivilege(38)){
                $result["message"] = "Access denied !";
                echo json_encode($result);
                die;
            }
            if((strlen($new_passcode) > 0 && strlen($password) == 0) || (strlen($new_passcode) == 0 && strlen($password) > 0)){
                $result["message"] = "passcode or new passcode empty !";
                echo json_encode($result);
                die;
            }
            if(strlen($password) > 0){
                try{
                    if(User::isMatch($client->getPseudo(), $password, true) == null){
                        $result["message"] = "invalid passcode authentification given !";
                        echo json_encode($result);
                        die;
                    }
                }catch(Exception $e){
                    Log::printStackTrace($e);
                }
                $password = $new_passcode;
            }
        }else if(!$client->hasPrivilege(37)){
            $result["message"] = "Access denied !";
            echo json_encode($result);
            die;
        }
        $usr = $id != null ? User::getById((int) $id) : new User();
        if($usr == null){
            $result["message"] = "User doesn't exist in the system !";
        }
        else{
            $privilegies = null;
            $ok = true;
            try{
                $privilegies = json_decode($_POST["usr_access"], true);
            }catch(Exception $e){Log::printStackTrace($e);}
            if(!$myself && !is_array($privilegies)){
                $ok = false;
                Log::println("[ accesss ] " . $privilegies);
                $result["message"] = "Invalid privilegies data submitted !";
            }
            if($ok) {
                $msg = $myself ? null : $usr->setPrivileges($privilegies);
                $result["message"] = $msg;
                if($msg == null) {
                    $usr->setPseudo($reqVar["usr_pseudo"]);
                    $usr->setPrenom($reqVar["usr_prenom"]);
                    $usr->uploadPhoto($avatar);
                    $usr->setNom($reqVar["usr_nom"]);
                    if ($poste != null && strlen($poste) > 0) {
                        $usr->setPoste((int) $poste);
                    }
                    $password = $password == null || strlen($password) == 0 ? null : $password;
                    $msg = $usr->save($password);
                    $result["message"] = $msg;
                    if ($msg == null) {
                        $result["error"] = false;
                        $result["code"] = 1;
                        $ths->move($avatar, realpath("../assets/avatars/"));
                        $result["message"] = "Success !";
                        if($usr->getId() != $client->getId() && $id != null){
                            Storage::disconnectUser($usr);
                        }
                        $result["data"] = Ressource::data("/users");
                    }
                    else{
                        $ths->flush($avatar);
                    }
                }
            }
        }
    }
}
//@User wiping
else if(($reqVar = CheckIf::isRequest($_POST, ["usr_del_id"])) != null){
    if(!$client->hasPrivilege(39)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    if(!CheckIf::isNumber($reqVar["usr_del_id"])){
        $result["message"] = "Invalid data given";
    }
    else{
        $t = User::getById((int) $reqVar["usr_del_id"]);
        if($t == null){
            $result["message"] = "Unrecognized id given !";
        }
        else{
            $result["message"] = "Unable to execute operation required";
            if($t->delete()){
                $result["message"] = "Success !";
                $result["error"] = false;
                $result["code"] = 1;
                try{
                    $result["data"] = Ressource::data("/users");
                }catch(Exception $e){
                    Log::printStackTrace($e);
                }
            }
        }
    }
}
//@User status
else if(($reqVar = CheckIf::isRequest($_POST, ["usr_id", "usr_state"])) != null){
    if(!$client->hasPrivilege(40)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    $state = CheckIf::set($reqVar["usr_state"]);
    if(!CheckIf::isNumber($reqVar["usr_id"]) && CheckIf::inArray($state->toLowerCase(), ["actif","inactif"])){
        $result["message"] = "Invalid data given";
    }
    else{
        $t = User::getById((int) $reqVar["usr_id"]);
        if($t == null){
            $result["message"] = "Unrecognized id given !";
        }else{
            $result["message"] = "Unable to execute operation required";
            $msg = $t->setActif($state->equalsIgnoreCase("actif"));
            $result["message"] = $msg;
            if($msg == null){
                $result["message"] = "Success";
                $result["code"] = 1;
                $result["error"] = false;
                try{
                    $result["data"] = Ressource::data("/users");
                }catch(Exception $e){
                    Log::printStackTrace($e);
                }
            }
        }
    }
}
//@User teacher linking
else if(($reqVar = CheckIf::isRequest($_POST, ["th_id", "th_pseudo", "th_passcode"])) != null){
    if(!$client->hasPrivilege(37)){
        $result["message"] = "Access denied !";
        echo json_encode($result);
        die;
    }
    if(!CheckIf::isNumber($reqVar["th_id"])){
        $result["message"] = "Invalid id submitted !";
        echo json_encode($result);
        die;
    }

    $teacher = Teacher::getById((int) $reqVar["th_id"]);

    if($teacher == null){
        $result["message"] = "Unrecognized teacher id !";
        echo json_encode($result);
        die;
    }
    $teacherAccount = new User();
    $access = [0,2,3,6,7,16,21,22,41,42];
    $teacherAccount->setPseudo($reqVar["th_pseudo"]);
    $teacherAccount->setPrivileges($access);
    $msg = $teacherAccount->save($reqVar["th_passcode"], $teacher);
    $result["message"] = $msg;
    if($msg == null){
        $result["error"] = false;
        $result["code"] = 1;
        $result["message"] = "Success";
        try{
            $result["data"] = Ressource::data("/users");
        }catch(Exception $e){
            Log::printStackTrace($e);
        }
    }
}

echo json_encode($result, JSON_INVALID_UTF8_IGNORE);