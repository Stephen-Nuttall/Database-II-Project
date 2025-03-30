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
$proposal = $_POST["proposal_date"];
$dissertation = $_POST["dissertation_date"];

if (empty($email) || empty($password_attempt) || empty($sid)) {
    echo "Please fill out the email, password, and student ID fields!";
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

// Get current dates
$dates_query =
    "SELECT proposal_defence_date, dissertation_defence_date
    FROM phd
    WHERE student_id = ?";

$stmt = $myconnection->prepare($dates_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->bind_result($current_proposal, $current_dissertation);
$stmt->fetch();
$stmt->close();

//if (empty($proposal)) { $proposal = $current_proposal; }
//if (empty($dissertation)) { $dissertation = $current_dissertation; }

// Ensure the dissertation is after the defence (if dates are set)
if ($proposal >= $dissertation && !empty($dissertation)) {
    echo "The proposal defence date must be before the dissertation defence date.";
    $myconnection->close();
    exit;
}

$update_dates =
    "UPDATE phd
    SET proposal_defence_date = ?, dissertation_defence_date = ?
    WHERE student_id = ?";

$stmt = $myconnection->prepare($update_dates);
$stmt->bind_param("sss", $proposal, $dissertation, $sid);
$stmt->execute();
if (!$stmt->execute()) {
    'Update failed, advisee info could not be updated.';
    $stmt->close();
    $myconnection->close();
    exit;
}
$stmt->close();

echo "Old Proposal Defence Date: " . htmlspecialchars($current_proposal) . "<br>";
echo "Old Dissertation Defence Date: ". htmlspecialchars($current_dissertation) . "<br><br>";
echo "New Proposal Defence Date: " . htmlspecialchars($proposal) . "<br>";
echo "New Dissertation Defence Date: ". htmlspecialchars($dissertation) . "<br>";
echo "(Blank if none) <br>";

$myconnection->close();