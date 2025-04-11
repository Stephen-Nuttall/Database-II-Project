<?php

// get HTML form info
$club_name = $_POST['club_name'];
$student_id = $_POST['student_id'];
$password = $_POST['password'];
$club_id = 0;

// establish mysql connection
$myconnection = mysqli_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
$mydb = mysqli_select_db($myconnection, 'db2') or die('Could not select database');

$password_query =
    "SELECT password
    FROM account
    WHERE email IN (
        SELECT email
        FROM student
        WHERE student_id = '$student_id'
    )";

// get the password tied to the account for this instructor
$password_result = mysqli_query($myconnection, $password_query) or die("Query failed:" . mysqli_error($myconnection));
$password_fetch = mysqli_fetch_array($password_result, MYSQLI_ASSOC);
$correct_password = $password_fetch ? $password_fetch['password'] : null;

// check if the given password matches the correct password
if ($password == $correct_password) {
    // check if a club with this name exists
    $club_query =
        "SELECT *
        FROM club
        WHERE name = '$club_name'";
    $club_result = mysqli_query($myconnection, $club_query) or die("Query failed: " . mysqli_error($myconnection));
    if (mysqli_num_rows($club_result) > 0) {
        // get club ID
        $club_fetch = mysqli_fetch_array($club_result, MYSQLI_ASSOC);
        $club_id = $club_fetch["club_id"];

        // check if student is already in this club
        $participant_query =
            "SELECT *
            FROM clubparticipants
            WHERE club_id = $club_id";
        $participant_result = mysqli_query($myconnection, $participant_query) or die("Query failed: " . mysqli_error($myconnection));
        while ($participant_fetch = mysqli_fetch_array($participant_result, MYSQLI_ASSOC)) {
            if ($participant_fetch["student_id"] == $student_id) {
                echo "Could not join $club_name. This student is already a member of this club.";
                return;
            }
        }

        // insert to club participants table
        $insert_query =
            "INSERT INTO clubparticipants (student_id, club_id)
            values ('$student_id', '$club_id')";
        mysqli_query($myconnection, $insert_query) or die("Query failed: " . mysqli_error($myconnection));
        echo "Successfully joined $club_name";
    } else {
        echo "No club found by the name $club_name.";
    }
} else {
    echo "Incorrect student ID or password.";
}


// close mysql connection
mysqli_close($myconnection);