<?php
/*
 * SETUP
 */
// get HTML form info
$id = $_POST['student_id'];
$GPA = 0.0;

// establish mysql connection
$myconnection = mysqli_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
$mydb = mysqli_select_db($myconnection, 'db2') or die('Could not select database');

/*
 * FETCHING AND ECHOING RESULTS
 */
// fetch the courses the student takes
$take_query =
    "SELECT *
    FROM take
    WHERE student_id = '$id'";
$take_result = mysqli_query($myconnection, $take_query) or die("Query failed: " . mysqli_error($myconnection));

// print heading
echo 'Course &emsp; &emsp; &emsp;
      Semester &emsp; &emsp;
      Year &emsp; &emsp; &emsp;
      Grade<br>';

// for each row in take_result...
while ($row = mysqli_fetch_array($take_result, MYSQLI_ASSOC)) {
    // get course name and credits
    $course_fetch = $row["course_id"];
    $course_query =
        "SELECT course_name, credits
        FROM course
        WHERE course_id = '$course_fetch'";
    $course_result = mysqli_query($myconnection, $course_query) or die("Query failed: " . mysqli_error($myconnection));
    $course_info = mysqli_fetch_array($course_result, MYSQLI_ASSOC);

    // add to GPA calculation
    $GPA += $course_info["credits"] * match ($row['grade']) {
        'A+' => 4.0,
        'A' => 4.0,
        'A-' => 3.7,
        'B+' => 3.3,
        'B' => 3.0,
        'B-' => 2.7,
        'C+' => 2.3,
        'C' => 2.0,
        'C-' => 1.7,
        'D+' => 1.3,
        'D' => 1.0,
        default => 0.0
    };

    // echo course details
    echo $course_info["course_name"];
    echo "&emsp; &emsp; &emsp;";
    echo $row["semester"];
    echo "&emsp; &emsp; &emsp;";
    echo $row["year"];
    echo "&emsp; &emsp; &emsp;";
    echo $row["grade"];
    echo '<br>';
}

degreeCredits('undergraduate', $id, $GPA, $myconnection);
degreeCredits('master', $id, $GPA, $myconnection);

// close mysql connection
mysqli_close($myconnection);

function degreeCredits($degree, $id, $GPA, $connection)
{
    // fetch the student's total credits
    $totalCredits_query =
        "SELECT total_credits
        FROM $degree
        WHERE student_id = '$id'";
    $totalCredits_result = mysqli_query($connection, $totalCredits_query) or die("Query failed: " . mysqli_error($connection));
    $totalCredits_fetch = mysqli_fetch_array($totalCredits_result, MYSQLI_ASSOC);
    $totalCredits = $totalCredits_fetch ? $totalCredits_fetch['total_credits'] : 0;

    // calculate cumulative GPA
    $cumulativeGPA = $totalCredits > 0 ? $GPA / $totalCredits : 0;

    echo '<br>';
    echo "For $degree degree:";
    echo '<br>';
    echo "Total Credits: $totalCredits";
    echo '<br>';
    echo "Cumulative GPA: $cumulativeGPA";
    echo '<br>';
}