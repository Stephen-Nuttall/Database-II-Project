<!--  8. The admin or instructor can appoint one or two instructors as advisor(s) for PhD
students, including a start date, and optional end date. The advisor will be able to view
the course history of their advisees, and update their advisees’ information. -->

<?php
// Establishing MYSQL connection 
$host = "localhost";
$username = "root";
$password = "";
$database = "db2";
$myconnection = new mysqli($host, $username, $password, $database);

// Check if connection is estblished
if ($myconnection->connect_error) {
    die("Connection failed: " . $myconnection->connect_error);
}

$email = $_POST["email"];
$password_attempt = $_POST["password"];
$sid = $_POST["student_id"];

if (empty($email) or empty($password_attempt) or empty($sid)) {
    echo "Please fill out all the fields!";
    exit;
}

// Check if the email exists and belongs to an instructor
$email_query =
    "SELECT *
    FROM account
    WHERE email = ? AND type = 'instructor'";

$stmt = $myconnection->prepare($email_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "This is not a valid email.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Check if password is correct
$password_query =
    "SELECT password
    FROM account
    WHERE email = ?";

$stmt = $myconnection->prepare($password_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($actual_password);
$stmt->fetch();
$stmt->close();
if ($password_attempt != $actual_password) {
    echo "The password entered is incorrect.";
    $myconnection->close();
    exit;
}

// Check if instructor has this student as an advisee
$student_id_query =
    "SELECT *
    FROM advise, instructor
    WHERE student_id = ? AND advise.instructor_id = instructor.instructor_id AND email = ?";

$stmt = $myconnection->prepare($student_id_query);
$stmt->bind_param("ss", $sid, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "This instructor has no advisee with this ID.";
    $myconnection->close();
    exit;
}
$stmt->close();

$course_history_query =
    "SELECT course_id, section_id, semester, year, grade
    FROM take
    WHERE student_id = ?";
$stmt = $myconnection->prepare($course_history_query);

$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($course_id, $section_id, $semester, $year, $grade);
echo "Course ID &emsp; &emsp; Section ID &emsp; &emsp; Semester &emsp; &emsp; Year &emsp; &emsp; Grade<br>";
while ($stmt->fetch()) {
    echo htmlspecialchars($course_id) . " &emsp; &emsp; " . htmlspecialchars($section_id) . " &emsp; &emsp; " . htmlspecialchars($semester) . " &emsp; &emsp; " . htmlspecialchars($year) . " &emsp; &emsp; &emsp;" . htmlspecialchars($grade) . "<br>";
}
$stmt->close();
$myconnection->close();