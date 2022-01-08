<?php


class Summary
{
    private $filiereTimeLine = [];
    private $etudiantTimeLine = [];
    private $profTimeLine = [];
    private $courseTimeLine = [];
    private $lastYearPromotedStudents = 0;
    private $lastYearStackStudents = 0;
    private $effectifAugmentation = 0.0;
    private $currentYear = null;

    public function __construct(?AcademicYear $ya){
        $this->currentYear = $ya;
        if($ya != null) {
            $list = $ya->getPrecedentsAndNow();
            $ids = [];
            for ($i = 0, $j = count($list); $i < $j; $i++) {
                $ids[] = $list[$i]->getId();
            }
            $this->setLastYearPromotion($ids);
            $this->setEtudiantTimeLine($list);
            $this->setFiliereTimeLine($list);
            $this->setProfTimeLine($list);
            $this->setCourseTimeLine($list);
        }
    }

    private function setLastYearPromotion(array $academicIds){
        $lastYear = $this->currentYear->previous();
        if($lastYear == null) return;
        foreach(Student::$list as $e){
            if(CheckIf::inArray($e->getAnneeAcademique(), $academicIds)){
                $average = $e->getAverageByAcademic($lastYear->getId());
                if($average >= 65){
                    $this->lastYearPromotedStudents++;
                }else{
                    $this->lastYearStackStudents++;
                }
            }
        }
    }

    private function setFiliereTimeLine(array $list){
        foreach($list as $ya){
            $this->filiereTimeLine[$ya->getAcademie()] = $ya->getNbBranch();
        }
    }

    private function setEtudiantTimeLine(array $list){
        $last_year_nb = 0; $current_nb = 0;
        $saved = false;
        foreach($list as $ya){
            if($ya->getId() == $this->currentYear->getId()){
                $current_nb = $ya->getNbStudent();
                $saved = true;
            }
            $this->etudiantTimeLine[$ya->getAcademie()] = $ya->getNbStudent();
            if(!$saved){
                $last_year_nb = $ya->getNbStudent();
            }
        }
        $this->effectifAugmentation = $last_year_nb == 0 ? 100 : round(($current_nb - $last_year_nb) / $last_year_nb , 2);
    }

    private function setProfTimeLine(array $list){
        foreach($list as  $ya){
            $this->profTimeLine[$ya->getAcademie()] = $ya->getNbTeacher();
        }
    }

    private function setCourseTimeLine(array $list){
        foreach($list as $ya){
            $this->courseTimeLine[$ya->getAcademie()] = $ya->getNbCourse();
        }
    }

    public function data(){
        return [
            "filiereTimeLine" => $this->filiereTimeLine,
            "etudiantTimeLine" => $this->etudiantTimeLine,
            "profTimeLine" => $this->profTimeLine,
            "courseTimeLine" => $this->courseTimeLine,
            "lastYearPromotedStudents" => $this->lastYearPromotedStudents,
            "lastYearStackStudents" => $this->lastYearStackStudents,
            "effectifAugmentation" => $this->effectifAugmentation,
            "currentYear" => $this->currentYear != null ? $this->currentYear->data() : null
        ];
    }

    public function __toString(){return json_encode($this->data());}
}