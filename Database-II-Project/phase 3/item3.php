<?php
// Let students see their past sections they have their password
$sid = $_POST['student_id'];
$pid = $_POST['password'];

if (empty($sid) or empty($pid)) {
    $response['message'] = "Please fill out all the fields!";
    echo json_encode($response);
    exit;
}

$myconnection = mysqli_connect('localhost', 'root', '', 'db2');
if (!$myconnection) {
    $response['message'] = 'Could not connect: ' . mysqli_connect_error();
    echo json_encode($response);
    exit;
}

$student_id_query =
    "SELECT *
    FROM student
    WHERE student_id = ?";
$stmt = mysqli_prepare($myconnection, $student_id_query);
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt); 

if (mysqli_stmt_num_rows($stmt) == 0) {
    $response['message'] = "This student doesn't exist!";
    mysqli_close($myconnection);
    echo json_encode($response);
    exit;
}
mysqli_stmt_close($stmt);

// Check that the person is authorized to perform this query
$password_query =
    "SELECT password
    FROM account
    WHERE email IN (
        SELECT email
        FROM student
        WHERE student_id = ?)";
$stmt = mysqli_prepare($myconnection, $password_query);
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $password);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($pid != $password) {
    $response['message'] = "The password entered is incorrect.";
    mysqli_close($myconnection);
    echo json_encode($response);
    exit;
}

$courses_query =
    "SELECT course_id, section_id, semester, year, grade
    FROM take
    WHERE student_id = '$sid'";
$courses_result = mysqli_query($myconnection, $courses_query);
if (!$courses_result) {
    $response['message'] = "Query failed: " . mysqli_error($myconnection);
    echo json_encode($response);
    mysqli_close($myconnection);
    exit;
}

$response['message'] = "Course ID   Section ID  Semester  Year  Grade
";
// Loop through all the sections
while ($row = mysqli_fetch_array($courses_result, MYSQLI_ASSOC)) {
    $course_fetch = $row["course_id"];
    $section_fetch = $row["section_id"];
    $semester_fetch = $row["semester"];
    $year_fetch = $row["year"];
    $grade_fetch = $row["grade"];
    $response['message'] .= "
    $course_fetch  $section_fetch   $semester_fetch   $year_fetch   $grade_fetch";
}

$response['reply'] = true;
echo json_encode($response);
mysqli_close($myconnection);
?>