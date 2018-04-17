<?php
class Student {
    private $number;
    private $name;
    private $lastname;
    private $birthdate;
    
    function setStudentNumber($studentNumber) {
        $this->number = $studentNumber;
    }
    function getStudentNumber() {
        return $this->number;
    }

    function setName($studentName) {
        $this->name = $studentName;
    }
    function getName() {
        return $this->name;
    }

    function setLastname($studentLastname) {
        $this->lastname = $studentLastname;
    }
    function getLastname() {
        return $this->lastname;
    }

    function setBirthdate($studentBirthdate) {
        $this->birthdate = $studentBirthdate;
    }
    function getBirthdate() {
        return $this->birthdate;
    }

    function toString() {
        $toString = "Student: <br>";
        $toString .= "Number: " . $this->number;
        $toString .= "Name: " . $this->name . ' ' . $this->lastname;
        $toString .= "Birthdate: " . $this->birthdate;
        return $toString;
    }

    function equals($studentObj) {
        return $this->number == $studentObj->getStudentNumber();
    }

    function attributesToCSVFormat() {
      return "\xA" . $this->number . "," . $this->name . "," . $this->lastname . "," . $this->birthdate;
    }
}
?>