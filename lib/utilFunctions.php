<?php
require_once('../classes/Student.php');
require_once('../classes/Course.php');
require_once('../classes/StudentCourses.php');

/**
 * This function takes as argument the path to the students.csv file, 
 * and returns a json objet with the headers (Student Nr, Name, Last),
 * and an array of Student Object constructed while reading the csv file
 */
function getStudentsFromCSV($filePath) {
    $headers = array();
    $students = array();
    $row = 1;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $cols = count($data);
            if($row == 1){
                $headers = $data;
            } else {
                $student = new Student();
                $student->setStudentNumber($data[0]);
                $student->setName($data[1]);
                $student->setLastname($data[2]);
                $student->setBirthdate($data[3]);
                array_push($students, $student);
            }
            $row++;
        
        }
        fclose($handle);
    }

    $studentData = array(
        "headers" => $headers,
        "students" => $students,
    );

    return $studentData;
}

function getCoursesFromCSV($filePath) {
    $headers = array();
    $courses = array();
    $row = 1;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $cols = count($data);
            if($row == 1){
                $headers = $data;
            } else {
                $course = new Course();
                $course->setCourseCode($data[0]);
                $course->setCourseName($data[1]);
                $course->setCourseYear($data[2]);
                $course->setCourseSemester($data[3]);
                $course->setCourseInstructor($data[4]);
                $course->setCourseCredits($data[5]);
                array_push($courses, $course);
            }
            $row++;
        
        }
        fclose($handle);
    }

    $studentData = array(
        "headers" => $headers,
        "courses" => $courses,
    );

    return $studentData;
}

function readStudentCourseRecords($pathToStudentCoursesDataFile) {
    $studentCourseData = array();
    $row = 1;
    if (($handle = fopen($pathToStudentCoursesDataFile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $cols = count($data);
            if($row > 1){
                $studentCourseEntry = new StudentCourses();
                $studentCourseEntry->setStudentNr($data[0]);
                $studentCourseEntry->setCourseCode($data[1]);
                $studentCourseEntry->setGrade($data[2]);
                
                array_push($studentCourseData, $studentCourseEntry);
            }
            $row++;
        
        }
        fclose($handle);
    }
    return $studentCourseData;
}

function studentCoursePassFailRatio($studentNr, $studentCourseData) {
    $countPassed = 0;
    $countFailed = 0;

    foreach ($studentCourseData as &$course_data) {
        if($studentNr == $course_data->getStudentNr()) {
            $course_data->getGrade() > 0 ? $countPassed += 1 : $countFailed += 1;
        }
    }

    return array(
        "passed" => $countPassed,
        "failed" => $countFailed,
    );
}

function getCourseStats($courseCode, $studentCourseData) {
    $countPassed = 0;
    $countFailed = 0;
    $registered = 0;
    $sumGrade = 0;

    foreach ($studentCourseData as &$course_data) {
        if($courseCode == $course_data->getCourseCode()) {
            $registered += 1;
            $sumGrade += $course_data->getGrade();
            $course_data->getGrade() > 0 ? $countPassed += 1 : $countFailed += 1;
        }
    }

    return array(
        "passed" => $countPassed,
        "failed" => $countFailed,
        "registered" => $registered,
        "avg_grade" => round($sumGrade / $registered, 2)
    );
}

function calculateStudentGPA($studentNr, $courseData, $courses) {
    //sum(course_credit x grade) / sum(credits_taken)
    //(10 * 5 + 7 * 5) /  17 = 50 + 35 / 17
    $gradeCreditMultiplicationSummary = 0;
    $sumCreditsTaken = 0;
    foreach ($courseData as &$course_data) {
        if($studentNr == $course_data->getStudentNr()) {
            $courseCode = $course_data->getCourseCode();
            $grade = $course_data->getGrade();
            $courseObj = getCourseObjByCode($courseCode, $courses);
            $gradeCreditMultiplicationSummary += $grade * $courseObj->getCourseCredits();
            $sumCreditsTaken += $courseObj->getCourseCredits();
        }
    }
    return round($gradeCreditMultiplicationSummary / $sumCreditsTaken, 2);
}

function getCourseObjByCode($courseCode, $courses) {
    foreach ($courses as &$courseObj) {
        $code = $courseObj->getCourseCode();
        if($courseCode == $code) {
            return $courseObj;
        }
    }
    return null;
}

function getStatusBasedOnGrade($grade) {
    $statusLabel = "";
    switch ($grade) {
        case ($grade >= 0 && $grade < 2):
            $statusLabel = "unsatisfactory";
            break;
        case ($grade >= 2 && $grade < 3):
            $statusLabel = "satisfactory";
            break;
        case ($grade >= 3 && $grade < 4):
            $statusLabel = "honour";
            break;
        case ($grade >= 4):
            $statusLabel = "high honour";
            break;
    }
    return $statusLabel;
}

function checkIfStudentExists($studentNr, $studentList) {
    foreach ($studentList as &$student) {
        if($studentNr == $student->getStudentNumber()) {
           return $student;
        }
    }
    return null;
}

function checkIfCourseExists($courseCode, $courseList) {
    foreach ($courseList as &$course) {
        if($courseCode == $course->getCourseCode()) {
           return $course;
        }
    }
    return null;
}

function saveNewStudent($fileToWrite, $studentNr, $studentName, $studentLastname, $studentBirthdate) {
    //TODO
}

function validateDate($date, $format = 'd.m.Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>