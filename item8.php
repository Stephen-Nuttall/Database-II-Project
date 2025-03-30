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
$iid = $_POST["instructor_id"];
$iid_2 = $_POST["instructor_id_2"];
$start = $_POST["start_date"];
$end = $_POST["end_date"];
if (empty($end)) {
    $end = NULL;
}

if (empty($email) or empty($password_attempt) or empty($sid) or empty($iid) or empty($start)) {
    echo "Please fill out all the required fields!";
    exit;
}

// Check if the email exists and belongs to an admin or instructor
$email_query =
    "SELECT *
    FROM account
    WHERE email = ? AND (type = 'admin' OR type = 'instructor')";

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

// Check if PhD student exists
$student_id_query =
    "SELECT *
    FROM phd
    WHERE student_id = ?";

$stmt = $myconnection->prepare($student_id_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "There is no PhD student with this ID.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Check if PhD student has advisors 
$phd_advisors_query =
    "SELECT *
    FROM advise
    WHERE student_id = ?";

$stmt = $myconnection->prepare($phd_advisors_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 1 ) {
    echo "This PhD student already has two advisors.";
    $myconnection->close();
    exit;
} else if (!empty($iid_2) && $stmt->num_rows > 0) {
    echo "This PhD student already has an advisor, you may only add one more.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Check if advisor is already an advisor to this student
$phd_advisors_availability_query =
    "SELECT *
    FROM advise
    WHERE student_id = ? AND (instructor_id = ?)";

$stmt = $myconnection->prepare($phd_advisors_availability_query);
$stmt->bind_param("ss", $sid, $iid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 0 ) {
    echo "This instructor is already an advisor for this student.";
    $myconnection->close();
    exit;
}
$stmt->close();

// If applicable, check that the end date is after the start date
if (!empty($end) && $end <= $start) {
    echo 'The end date must be after the start date.';
    $myconnection->close();
    exit;
}

// Insert advisor(s)
$advisor_insert =
"INSERT INTO advise
VALUES (?, ?, ?, ?)";

$stmt = $myconnection->prepare($advisor_insert);
$stmt->bind_param("ssss", $iid, $sid, $start, $end);
if (!$stmt->execute()) {
    'Insert failed, student could not be added.';
    $myconnection->close();
    exit;
}

if (!empty($iid_2)) {
    $stmt->close();
    $stmt = $myconnection->prepare($advisor_insert);
    $stmt->bind_param("ssss", $iid_2, $sid, $start, $end);
    if (!$stmt->execute()) {
        'Insert failed, student could not be added.';
        $myconnection->close();
        exit;
    }
}
echo 'Advisor(s) inserted.';
$myconnection->close();

