<?php
require_once('../lib/utilFunctions.php');
require_once('../classes/Student.php');
require_once('../classes/Course.php');
require_once('../classes/StudentCourses.php');

if(isset($_POST["submit"])) {
    $path_parts = pathinfo($_FILES["fileToUpload"]["name"]);
    $extension = $path_parts['extension'];
    if($extension != "txt" && $extension != "csv") {
      echo "Sorry, only TXT & CSV files are allowed.";
    }
    $fileContent = file_get_contents($_FILES['fileToUpload']['tmp_name']);
    $input_list = explode(PHP_EOL, $fileContent);

    $studentList = getStudentsFromCSV("../data/students.csv");
    $courseList = getCoursesFromCSV("../data/courses.csv");
    $studentCourseData = readStudentCourseRecords("../data/student_courses.csv");

    $processedData = array();
    $tableHead = array(
      "stud_nr" => "Student Nr",
      "stud_name" => "Student Name",
      "stud_lastname" => "Student Lastname",
      "stud_birthdate" => "Student Birthdate",
      "course_code" => "Course Code",
      "course_year" => "Course Year",
      "course_semester" => "Course Semester",
      "course_instructor" => "Instructor",
      "course_credits" => "Course Credits",
      "grade" => "Grade",
      "status" => "STATUS"
    );
    array_push($processedData, $tableHead);
    $reason = "";
    $line = 1;
    foreach ($input_list as &$inputLine) {
        $data = explode(",", $inputLine);

        //Create new student from the input data
        $studentObj = new Student();
        $studentObj->setStudentNumber($data[0]);
        $studentObj->setName($data[1]);
        $studentObj->setLastname($data[2]);
        $studentObj->setBirthdate($data[3]);
        //Send it to a function which checks if it exists and if it does, it checks its values if they match (name, lastname, birthdate)
        //If the student does not exist, save it in the csv file (DONE WITH THE STUDENT)

        $studentPersistMsg = validateStudentData($studentObj, $studentList['students']);

        //Create new course entry from the input data
        $courseObj = new Course();
        $courseObj->setCourseCode($data[4]);
        $courseObj->setCourseYear($data[5]);
        $courseObj->setCourseSemester($data[6]);
        $courseObj->setCourseInstructor($data[7]);
        $courseObj->setCourseCredits($data[8]);
        //Send it to a function which checks if it exists, and if it does, it checks that the values match (course year, semester, instructor, credits)
        //If the course does not exist, throw exception (because we cant save a new course from input data, since course name is missing)
        $coursePersistMsg = validateCourseData($courseObj, $courseList['courses']);

        //When all above checks have passed, Create a new student_course object from the input data
        $studentCourseEntry = new StudentCourses();
        $studentCourseEntry->setStudentNr($data[0]);
        $studentCourseEntry->setCourseCode($data[4]);
        $studentCourseEntry->setGrade($data[9]);

        //Send it to a function which checks if this entry has been previously entered
        //If the data exists, do not do anything, otherwise save it
        $persistanceStatus = persistStudentCourseEntry($studentCourseEntry, $studentCourseData);

        $status = $persistanceStatus;
        if($studentPersistMsg != "" || $coursePersistMsg != "") {
          $status = $studentPersistMsg . "<br>" . $coursePersistMsg;
        }

        //12, Dafina, Marku, 19.03.1996, CS344, 2018, Spring, Ahmed Whateer, 10, 5
        $tableRow = array(
          "stud_nr" => $data[0],
          "stud_name" => $data[1],
          "stud_lastname" => $data[2],
          "stud_birthdate" => $data[3],
          "course_code" => $data[4],
          "course_year" => $data[5],
          "course_semester" => $data[6],
          "course_instructor" => $data[7],
          "course_credits" => $data[8],
          "grade" => $data[9],
          "status" => $status
        );
        array_push($processedData, $tableRow);
        //For each line, write a status code, and if the line fails to enter the database then write a reason why it didnt make it through

        //Print the list as a table output.
        $line += 1;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="../assets/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="../assets/css/bootstrap-grid.min.css" rel="stylesheet"/>
  <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"/>
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
  <script src="../assets/js/jquery-1.12.4.js"></script>
  <script src="../assets/js/jquery.dataTables.min.js"></script>
  <title>Students</title>
  <script>
    $(document).ready(function() {
      var studentsTable = $('#processed_input').DataTable();

      /* Sort the students table on the GPA columin in descending order*/
      studentsTable.order( [ 6, 'desc' ] )
      .draw();
    });
  </script>
</head>

<body>
  <h1 class="myHeader">NTNU data</h1>
    
	<div class="container">
        
		<div class="row">
      <div class="col-md-12 text-center">
        <?php if (!isset($processedData)) { ?>
				  <img src="../assets/pic/upload.png" alt="Upload" style="width: 10%; margin-top: 3%" class="img_center">
          <form action="data.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Image" name="submit">
          </form>
          <?php } ?>


          <?php if (isset($processedData)) { ?>
            <table id="processed_input" class="display" style="width:100%">
              <thead>
                <tr>
                  <?php
                  foreach ($processedData[0] as $key => $value) {
                    echo "<th>".$value."</th>";
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                  for ($x = 1; $x < count($processedData); $x++) {
                    $tableRow = "<tr>";
                    $tableRow .= "<td>" . $processedData[$x]["stud_nr"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["stud_name"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["stud_lastname"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["stud_birthdate"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["course_code"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["course_year"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["course_semester"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["course_instructor"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["course_credits"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["grade"] . "</td>";
                    $tableRow .= "<td>" . $processedData[$x]["status"] . "</td>";
                    $tableRow .= "</tr>";
                    echo $tableRow;
                  }
                  ?>
              </tbody>
            </table>
          <?php
          }
          ?>
			</div>
		</div>
	</div>
</body>
</html>


