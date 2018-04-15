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

$courseData = getCoursesFromCSV("../data/courses.csv");
$courses = $courseData['courses'];
$headers = $courseData['headers'];
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
            var coursesTable = $('#courses').DataTable();

            /* Sort the students table on the GPA columin in descending order*/
            coursesTable.order( [ 6, 'asc' ] )
            .draw();
        });
    </script>
</head>

<body>
    <h1 class="myHeader">NTNU data</h1>
    
	<div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>Total Unique Courses: <?=count($courses);?> </h3>   
			</div>
        </div>
		<div class="row">
			<div class="col-md-12">
				<table id="courses" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <?php
                                foreach ($headers as &$value) {
                                    echo "<th>" . $value . "</th>";
                                }
                                //Add the Courses passed, failed, gpa and status headers
                            ?>
                            <th> Nr Student Registered</th>
                            <th> Nr Students Passed</th>
                            <th> Nr Students Failed </th>
                            <th> Avg Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($courses as &$course) {
                                $courseStats = getCourseStats($course->getCourseCode(), $studentCourseData);
                                $tableRow = "<tr>";
                                $tableRow .= "<td>" . $course->getCourseCode() . "</td>";
                                $tableRow .= "<td>" . $course->getCourseName() . "</td>";
                                $tableRow .= "<td>" . $course->getCourseYear() . "</td>";
                                $tableRow .= "<td>" . $course->getCourseSemester() . "</td>";
                                $tableRow .= "<td>" . $course->getCourseInstructor() . "</td>";
                                $tableRow .= "<td>" . $course->getCourseCredits() . "</td>";
                                $tableRow .= "<td>" . $courseStats['registered'] ."</td>";
                                $tableRow .= "<td>" . $courseStats['passed'] ."</td>";
                                $tableRow .= "<td>" . $courseStats['failed'] ."</td>";
                                $tableRow .= "<td>" . $courseStats['avg_grade'] ."</td>";
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