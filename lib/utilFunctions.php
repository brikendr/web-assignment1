<?php
require_once('../classes/Student.php');
require_once('../classes/Course.php');
require_once('../classes/StudentCourses.php');

/**
 * A function that reads the students.csv file and returns an object with the header names and the data.
 *
 * @param string $filePath  The path to the students.csv file.
 * @return array            The array object that contains the data and headers read from the csv file.
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

/**
 * Similar function as getStudentsFromCSV, except that this one reads the courses.
 *
 * @param string $filePath  The path to the courses.csv file.
 * @return array            The array object that contains the data and headers read from the csv file.
 */
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

/**
 * A function that reads the student_courses.csv file and returns all the lines in the csv files as a list of constructed StudentCourses objects.
 *
 * @param string $pathToStudentCoursesDataFile  The path to the student_courses.csv file.
 * @return array                                The array object that contains the data read from the csv file.
 */
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

/**
 * This function calculates the number of courses passed and failed for a specific student (identified by its "studentNr")
 *
 * @param integer $studentNr          A numeric value that uniquely identifies a studnet.
 * @param [type] $studentCourseData   The list of student_courses Objects for all students registered in the database.
 * @return array                      The array object that contains the number of courses passed and failed for that student.
 */
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
/**
 * A function that generate a number of course statistics succh as nr of students passed, failed, registered and the average grade.
 *
 * @param string $courseCode          The course code for which the stats will be genereated.
 * @param array $studentCourseData    The list of student_courses Objects for all students registered in the database.
 * @return array                      The array object that contains the course statistics.
 */
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

/**
 * A function that calculates a student's GPA.
 *
 * @param integer $studentNr  The student number for which the GPA will be calculated.
 * @param array $courseData   The list of student_courses Objects for getting the grades and course credits.
 * @param array $courses      The list of courses registered in the database.
 * @return float              The calculated GPA.
 */
function calculateStudentGPA($studentNr, $courseData, $courses) {
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

/**
 * A function that iterates through a list of courses and finds the course with the given course code as a parameter.
 *
 * @param string $courseCode  The course code to find on the list.
 * @param array $courses      The list of courses.
 * @return object             The course object found, null if not found.
 */
function getCourseObjByCode($courseCode, $courses) {
    foreach ($courses as &$courseObj) {
        $code = $courseObj->getCourseCode();
        if($courseCode == $code) {
            return $courseObj;
        }
    }
    return null;
}

/**
 * A function that generates a student status string value based on the $gpa input parameter.
 *
 * @param integer $gpa      The gpa to be analyzed.
 * @return string           A string value representing the status of the student based on the gpa.
 */
function getStatusBasedOnGPA($gpa) {
    $statusLabel = "";
    switch ($gpa) {
        case ($gpa >= 0 && $gpa < 2):
            $statusLabel = "unsatisfactory";
            break;
        case ($gpa >= 2 && $gpa < 3):
            $statusLabel = "satisfactory";
            break;
        case ($gpa >= 3 && $gpa < 4):
            $statusLabel = "honour";
            break;
        case ($gpa >= 4):
            $statusLabel = "high honour";
            break;
    }
    return $statusLabel;
}

/**
 * A function that checks whether a student is part of the give studentList.
 *
 * @param integer $studentNr  The numeric value uniquely representing a student.
 * @param array $studentList  The list of all students registered.
 * @return object             The student object if it is found, null if otherwise.
 */
function checkIfStudentExists($studentNr, $studentList) {
    foreach ($studentList as $student) {
        if($studentNr == $student->getStudentNumber()) {
           return $student;
        }
    }
    return null;
}

/**
 * A function that checks whether a course is part of the given course list.
 *
 * @param string $courseCode  A string value uniquely representing a course.
 * @param array $courseList   The list of all courses registered.
 * @return object             The course object if it is found, null otherwise.
 */
function checkIfCourseExists($courseCode, $courseList) {
    foreach ($courseList as &$course) {
      if($courseCode == $course->getCourseCode()) {
        return $course;
      }
    }
    return null;
}

/**
 * A function that checks whether a student and course entry exists in the database.
 *
 * @param object $studentCourseObj    A student - course mapping object to be checked.
 * @param array $studentCoursesList   The list of all student - course mappings.
 * @return boolean                    A boolean value whether the entry was found in the list.                     
 */
function checkIfStudentCourseEntryExists($studentCourseObj, $studentCoursesList) {
  foreach ($studentCoursesList as &$studCourseObj) {
    if($studentCourseObj->getStudentNr() == $studCourseObj->getStudentNr()
    && $studentCourseObj->getCourseCode() == $studCourseObj->getCourseCode()) {
      return true;
    }
  }
  return false;
}

function saveNewStudent($studentObject) {
  $inputData = $studentObject->attributesToCSVFormat();
  file_put_contents("../data/students.csv", $inputData, FILE_APPEND);
}

/**
 * A function that validates a date format.
 *
 * @param string $date  The date string to be validated.
 * @param string $format  The format to validate the date against.
 * @return boolean  True if the validation passes, and false otherwise.
 */
function validateDate($date, $format = 'd.m.Y'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * A function that checks whether the given student object exists in the database, and if so, 
 * it checks that the data matches to the one on the database. It saves the student if it does
 * not exists in the students database.
 *
 * @param object $studentObj    The student object to be analyzed.
 * @param array $studentList    The list of all students registered in the database.
 * @return string               A string that tells whether the saving was successful or not.
 */
function validateStudentData($studentObj, $studentList) {
  $savedStudent = checkIfStudentExists($studentObj->getStudentNumber(), $studentList);
  $failureReason = "";
  if($savedStudent != null) {
    if($studentObj->getName() != $savedStudent->getName()) {
      $failureReason .= "Name for student with id: [".$studentObj->getStudentNumber()."] doesn't match!<br>";
    } 
    if($studentObj->getLastname() != $savedStudent->getLastname()) {
      $failureReason .= "Lastname for student with id: [".$studentObj->getStudentNumber()."] doesn't match!<br>";
    }
    if($studentObj->getBirthdate() != $savedStudent->getBirthdate()) {
      $failureReason .= "Birthdate for student with id: [".$studentObj->getStudentNumber()."] doesn't match!<br>";
    }

    if(!validateDate($studentObj->getBirthdate())) {
      $failureReason .= "Birthdate format for student with id: [".$studentObj->getStudentNumber()."] is wrong!<br>";
    }
  } else {
    // Save the new student
    saveNewStudent($studentObj);
  }

  return $failureReason;
}

/**
 * A function that checks whether the given course object exists in the database, and if so, 
 * it checks that the data matches to the one on the database.
 *
 * @param object $courseObj     The course object to be analyzed.
 * @param array $courseList     The list of all courses registered in the database.
 * @return string               A string that tells whether the saving was successful or not.
 */
function validateCourseData($courseObj, $courseList) {
  $savedCourse = checkIfCourseExists($courseObj->getCourseCode(), $courseList);
  $failureReason = "";

  if($savedCourse != null) {
    if($courseObj->getCourseYear() != $savedCourse->getCourseYear()) {
      $failureReason .= "Course Year for course with id: [".$courseObj->getCourseCode()."] doesn't match!<br>";
    }

    if($courseObj->getCourseSemester() != $savedCourse->getCourseSemester()) {
      $failureReason .= "Course Semester for course with id: [".$courseObj->getCourseCode()."] doesn't match!<br>";
    }

    if($courseObj->getCourseInstructor() != $savedCourse->getCourseInstructor()) {
      $failureReason .= "Course Instructor for course with id: [".$courseObj->getCourseCode()."] doesn't match!<br>";
    }

    if($courseObj->getCourseCredits() != $savedCourse->getCourseCredits()) {
      $failureReason .= "Course Credits for course with id: [".$courseObj->getCourseCode()."] doesn't match!<br>";
    }
  } else {
    $failureReason = "No saved course with course code: [".$courseObj->getCourseCode()."]!<br>";;
  }

  return $failureReason;
}

/**
 * A function that checks if the student-course mapping exists. If it doesn't, it will persist the new data.
 *
 * @param object $studentCourseEntry  The student-course object to be persisted.
 * @param list $studentCoursesList    The list of existing student-course mappings.
 * @return string                     A string value that represents the persistance status.
 */
function persistStudentCourseEntry($studentCourseEntry, $studentCoursesList) {
  $status = "Unchanged!";
  if(!checkIfStudentCourseEntryExists($studentCourseEntry, $studentCoursesList)) {
    $inputData = $studentCourseEntry->attributesToCSVFormat();
    echo "PERSISTING: <br>";
    echo $inputData . "<br>";
    file_put_contents("../data/student_courses.csv", $inputData, FILE_APPEND);
    $status = "Persisted!";
  }

  return $status;
}
?>