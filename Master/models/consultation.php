<?php


// Consultation Class
class Consultation {
    public $consultationId;
    public $consultationDate;
    public $duration;
    public $subject;
    public $subjectLocked;
    public $descriptionFromTeacher;
    public $descriptionFromStudent;
    public $ownerId;
    public $ownerName;
    public $studentId;
    public $studentName;

    public function __construct($consultationId, $consultationDate, $duration, $subject, $subjectLocked, $descriptionFromTeacher, $descriptionFromStudent, $ownerId, $ownerName, $studentId = null, $studentName = null) {
        $this->consultationId = $consultationId;
        $this->consultationDate = $consultationDate;
        $this->duration = $duration;
        $this->subject = $subject;
        $this->subjectLocked = $subjectLocked;
        $this->descriptionFromTeacher = $descriptionFromTeacher;
        $this->descriptionFromStudent = $descriptionFromStudent;
        $this->ownerId = $ownerId;
        $this->ownerName = $ownerName;
        $this->studentId = $studentId;
        $this->studentName = $studentName;
    }
}


?>
