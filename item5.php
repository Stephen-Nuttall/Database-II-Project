<?php

$iid = $_POST['instructor_id'];
$pid = $_POST['password'];

if (empty($iid) or empty($pid)) {
    echo "Please fill out all the fields!";
    exit;
}

$myconnection = mysqli_connect('localhost', 'root', '', 'db2') or die('Could not connect: ' . mysqli_connect_error());

$instructor_id_query =
    "SELECT *
    FROM instructor
    WHERE instructor_id = ?";
$stmt = mysqli_prepare($myconnection, $instructor_id_query);
mysqli_stmt_bind_param($stmt, "s", $iid);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt); 

if (mysqli_stmt_num_rows($stmt) == 0) {
    echo "This instructor doesn't exist!";
    mysqli_stmt_close($stmt);
    mysqli_close($myconnection);
    exit;
}
mysqli_stmt_close($stmt);

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
    echo "The password entered is incorrect.";
    mysqli_close($myconnection);
    exit;
}


$sections_query =
    "SELECT course_id, section_id, semester, year
    FROM section
    WHERE instructor_id = '$iid'";
$sections_result = mysqli_query($myconnection, $sections_query) or die("Query failed: " . mysqli_error($myconnection));

while ($row = mysqli_fetch_array($sections_result, MYSQLI_ASSOC)) {
    // get course name and credits
    $course_fetch = $row["course_id"];
    $section_fetch = $row["section_id"];
    $semester_fetch = $row["semester"];
    $year_fetch = $row["year"];
    $course_section_query =
        "SELECT student_id, grade
        FROM take
        WHERE course_id = '$course_fetch' AND section_id = '$section_fetch' AND semester = '$semester_fetch' AND year = '$year_fetch'";
    $course_section_result = mysqli_query($myconnection, $course_section_query) or die("Query failed: " . mysqli_error($myconnection));

    echo '<br>';
    echo '<b>' . $course_fetch . '</b>';
    echo "&emsp; &emsp; &emsp;";
    echo '<b>' . $section_fetch . '</b>';
    echo "&emsp; &emsp; &emsp;";
    echo '<b>' . $semester_fetch . '</b>';
    echo "&emsp; &emsp; &emsp;";
    echo '<b>' . $year_fetch . '</b>';
    echo '<br>';
    echo '<br>';

    echo 'Student ID';
    echo "&emsp; &emsp; &emsp;";
    echo 'Student Name';
    echo "&emsp; &emsp; &emsp;";
    echo 'Grade';
    echo '<br>';

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

        echo $student_fetch;
        echo "&emsp; &emsp; &emsp;";
        echo $student_name_result;
        echo "&emsp; &emsp; &emsp;";
        echo $grade_fetch;
        echo '<br>';
    }
    echo '<br>';
    echo '<br>';
}
?>