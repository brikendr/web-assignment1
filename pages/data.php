<?php
require_once('../lib/utilFunctions.php');
//$fileContent = file_get_contents($_FILES['upload_file']['tmp_name']);
if(isset($_POST["submit"])) {
    $path_parts = pathinfo($_FILES["fileToUpload"]["name"]);
    $extension = $path_parts['extension'];
    if($extension != "txt" && $extension != "csv") {
        echo "Sorry, only TXT & CSV files are allowed.";
    }
    $fileContent = file_get_contents($_FILES['fileToUpload']['tmp_name']);
    $input_list = explode(PHP_EOL, $fileContent);

    $studentData = getStudentsFromCSV("../data/students.csv");
    $courseData = getCoursesFromCSV("../data/courses.csv");
    $studentCourseData = readStudentCourseRecords("../data/student_courses.csv");
    
    //12, Dafina, Marku, 19.03.1996, CS344, 2018, Spring, Ahmed Whateer, 10, 5
    $processedData = array();
    $reason = "";
    foreach ($input_list as &$inputLine) {
        $inputData = explode(",", $inputLine);
        //echo "Processing: " . $inputData[0] . ", CODE: " . $inputData[4] . "<br>";
        $savedStudent = checkIfStudentExists($inputData[0], $studentData['students']);
        if($savedStudent != null) {
            echo $inputData[1] . " != ". $savedStudent->getName() . " OR " . $inputData[2] . " != ". $savedStudent->getLastname() . " OR " . $inputData[3] . " != ". $savedStudent->getBirthdate();
            if($inputData[1] != $savedStudent->getName() ||
            $inputData[2] != $savedStudent->getLastname() ||
            $inputData[3] != $savedStudent->getBirthdate()) {
                $reason = "Student info don't match with the saved entry!<br>";
            }
        } else {
            //Save new student
            if(validateDate($inputData[3])) {
                $reason = "Date is invalid!<br>";
            }
            saveNewStudent("../data/students.csv", $inputData[0], $inputData[1], $inputData[2], $inputData[3]);
        }

        $savedCourse = checkIfCourseExists($inputData[4], $courseData['courses']);
        if($savedCourse != null) {
            echo $inputData[5] . " != ". $savedCourse->getCourseYear() . " OR " . $inputData[6] . " != ". $savedCourse->getCourseSemester() 
            . " OR " . $inputData[7] . " != ". $savedCourse->getCourseInstructor() . "<br>";
            if($inputData[5] != $savedCourse->getCourseYear() ||
            $inputData[6] != $savedCourse->getCourseSemester() ||
            $inputData[7] != $savedCourse->getCourseInstructor() ||
            $inputData[8] != $savedCourse->getCourseCredits()) {
                $reason = "Course info don't match with the saved entry!<br>";
            }
        }

        echo $reason;
    }
    
    /*4. When checking a line do the following: 
        a) Check if the student exists in the students.csv 
        b) If the student exists, check all data is valid (name, lastname, birthdate)
        c) Check if course exists in courses.cv
        d) if the course exists, check all data is valid
        e) Check if student nr, course code and grade exists on students_courses.csv
        f) if it exists, ignore it
        g) Check if birthdate is a valid date,
        h) check if student number, course year, credits and grade have numberic values

    */
}

/*
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    // Allow certain file formats
    if($imageFileType != "txt" && $imageFileType != "csv") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
*/
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="../assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../assets/css/bootstrap-grid.min.css" rel="stylesheet"/>
    
	<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <script src="../assets/js/jquery-1.12.4.js"></script>
    <title>Students</title>
    <script>
        $(document).ready(function() {
           
        });
    </script>
</head>

<body>
    <h1 class="myHeader">NTNU data</h1>
    
	<div class="container">
        
		<div class="row">
            <div class="col-md-12 text-center">
				<img src="../assets/pic/upload.png" alt="Upload" style="width: 10%; margin-top: 3%" class="img_center">
                <form action="data.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload Image" name="submit">
                </form>
			</div>
		</div>
	</div>
</body>
</html>


