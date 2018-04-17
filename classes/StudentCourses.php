<?php
class StudentCourses {
    private $student_nr;
    private $course_code;
    private $grade;

    function setStudentNr($studentNr) {
        $this->student_nr = $studentNr;
    }
    function getStudentNr() {
        return $this->student_nr;
    }

    function setCourseCode($courseCode) {
        $this->course_code = $courseCode;
    }
    function getCourseCode() {
        return $this->course_code;
    }

    function setGrade($grade) {
        $this->grade = $grade;
    }
    function getGrade() {
        return $this->grade;
    }

    function toString() {
        return "Student Nr: " . $this->student_nr . ", Course Code: " . $this->course_code . ", Grade: " . $this->grade;
    }

    function equals($studentCourseObj) {
        return $this->student_nr == $studentCourseObj->getStudentNr() && $this->course_code == $studentCourseObj->getCourseCode();
    }

    function attributesToCSVFormat() {
      return "\xA" . $this->student_nr . "," . $this->course_code . "," . $this->grade;
    }
}
?>