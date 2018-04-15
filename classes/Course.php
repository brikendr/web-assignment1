<?php
class Course {
    private $code;
    private $name;
    private $year;
    private $semester;
    private $instructor;
    private $credits;

    function setCourseCode($courseCode) {
        $this->code = $courseCode;
    }
    function getCourseCode() {
        return $this->code;
    }

    function setCourseName($courseName) {
        $this->name = $courseName;
    }
    function getCourseName() {
        return $this->name;
    }

    function setCourseYear($courseYear) {
        $this->year = $courseYear;
    }
    function getCourseYear() {
        return $this->year;
    }

    function setCourseSemester($courseSemester) {
        $this->semester = $courseSemester;
    }
    function getCourseSemester() {
        return $this->semester;
    }

    function setCourseInstructor($courseInstrutor) {
        $this->instructor = $courseInstrutor;
    }
    function getCourseInstructor() {
        return $this->instructor;
    }

    function setCourseCredits($courseCredits) {
        $this->credits = $courseCredits;
    }
    function getCourseCredits() {
        return $this->credits;
    }

    function toString() {
        $toString = "CourseData: ";
        $toString .= "Code: " . $this->code;
        $toString .= "Name: " . $this->name;
        $toString .= "Year: " . $this->year;
        $toString .= "Semester: " . $this->semester;
        $toString .= "Instructor: " . $this->instructor;
        $toString .= "Credits: " . $this->credits;
        return toString;
    }

    function equals($courseObj) {
        return $this->code == $courseObj->getCourseCode();
    }
}
?>