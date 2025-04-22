<?php
// Let instructors see their past sections and students if they have their password
$iid = $_POST['instructor_id'];
$pid = $_POST['password'];

if (empty($iid) or empty($pid)) {
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

$instructor_id_query =
    "SELECT *
    FROM instructor
    WHERE instructor_id = ?";
$stmt = mysqli_prepare($myconnection, $instructor_id_query);
mysqli_stmt_bind_param($stmt, "s", $iid);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt); 

if (mysqli_stmt_num_rows($stmt) == 0) {
    $response['message'] = "This instructor doesn't exist!";
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
        FROM instructor
        WHERE instructor_id = ?)";
$stmt = mysqli_prepare($myconnection, $password_query);
mysqli_stmt_bind_param($stmt, "s", $iid);
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

$sections_query =
    "SELECT course_id, section_id, semester, year
    FROM section
    WHERE instructor_id = '$iid'";
$sections_result = mysqli_query($myconnection, $sections_query);
if (!$sections_result) {
    $response['message'] = "Query failed: " . mysqli_error($myconnection);
    echo json_encode($response);
    mysqli_close($myconnection);
    exit;
}

$response['message'] = "";
// Loop through all the sections the instructor has taught/is teaching and the student info
while ($row = mysqli_fetch_array($sections_result, MYSQLI_ASSOC)) {
    $course_fetch = $row["course_id"];
    $section_fetch = $row["section_id"];
    $semester_fetch = $row["semester"];
    $year_fetch = $row["year"];
    $course_section_query =
        "SELECT student_id, grade
        FROM take
        WHERE course_id = '$course_fetch' AND section_id = '$section_fetch' AND semester = '$semester_fetch' AND year = '$year_fetch'";
    $course_section_result = mysqli_query($myconnection, $course_section_query);
    if (!$course_section_result) {
        $response['message'] = "Query failed: " . mysqli_error($myconnection);
        echo json_encode($response);
        mysqli_close($myconnection);
        exit;
    }
    $response['message'] .= "$course_fetch       $section_fetch      $semester_fetch     $year_fetch";

    $response['message'] .= "
    Student ID          Student Name                Grade";

    while ($row = mysqli_fetch_array($course_section_result, MYSQLI_ASSOC)) {
        $student_fetch = $row["student_id"];
        $student_name_query =
            "SELECT name
            FROM student
            WHERE student_id=?";

        $stmt = mysqli_prepare($myconnection, $student_name_query);
        mysqli_stmt_bind_param($stmt, "s", $student_fetch);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $student_name_result);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $grade_fetch = $row["grade"];
        
        $response['message'] .= "
        $student_fetch          $student_name_result        $grade_fetch";
    }
    $response['message'] .= "
    
    ";
}

$response['reply'] = true;
echo json_encode($response);
mysqli_close($myconnection);
?>