<?php
require_once('../lib/utilFunctions.php');
/*
1. Read file students.csv
1.2 When reading the students construct an array of Student Object
2.Count all students in students.csv and display the count
3. Read student_curses.csv file and construct an array of StudentCourses object

4. For each student in the students array, we find all matches on the student courses, and calcualte nr of corses
passed, failed, gpa and status

5. Create an array of mappings between student object and student courses object which will be displayed on the table.
6. Sort this array based on the GPA (Descending Order)

Functions needed
1. A function that calculates the number of courses passed
2. A function that calculates the number of courses failed
3. A function that calculates the GPA of a student
4. A function that calculates the Status based on the GPA
*/

$studentData = getStudentsFromCSV("../data/students.csv");
$courseData = getCoursesFromCSV("../data/courses.csv");
$students = $studentData['students'];
$headers = $studentData['headers'];
$studentCourseData = readStudentCourseRecords("../data/student_courses.csv")
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
      var studentsTable = $('#students').DataTable();

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
            <div class="col-md-12">
                <h3>Total Unique Students: <?=count($students);?> </h3>   
			</div>
        </div>
		<div class="row">
			<div class="col-md-12">
				<table id="students" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <?php
                                foreach ($headers as &$value) {
                                    echo "<th>" . $value . "</th>";
                                }
                                //Add the Courses passed, failed, gpa and status headers
                            ?>
                            <th> Courses Passed</th>
                            <th> Courses Failed</th>
                            <th> GPA </th>
                            <th> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($students as &$student) {
                                $passFailCounts = studentCoursePassFailRatio($student->getStudentNumber(), $studentCourseData);
                                $gpa = calculateStudentGPA($student->getStudentNumber(), $studentCourseData, $courseData['courses']);
                                $tableRow = "<tr>";
                                $tableRow .= "<td>" . $student->getStudentNumber() . "</td>";
                                $tableRow .= "<td>" . $student->getName() . "</td>";
                                $tableRow .= "<td>" . $student->getLastname() . "</td>";
                                $tableRow .= "<td>" . $student->getBirthdate() . "</td>";
                                $tableRow .= "<td>" . $passFailCounts['passed'] ."</td>";
                                $tableRow .= "<td>" . $passFailCounts['failed'] ."</td>";
                                $tableRow .= "<td>" . $gpa . "</td>";
                                $tableRow .= "<td>" . getStatusBasedOnGPA($gpa) . "</td>";
                                $tableRow .= "</tr>";
                                echo $tableRow;
                            }
                        ?>
                    </tbody>
                </table>
			</div>	
		</div>
	</div>
</body>
</html>