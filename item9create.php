<?php

/*
 * SETUP
 */

// get HTML form info
$instructor_id = $_POST['instructor_id'];
$password = $_POST['password'];
$club_name = $_POST['club_name'];
$president_id = $_POST['president_id'];

// establish mysql connection
$myconnection = mysqli_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
$mydb = mysqli_select_db($myconnection, 'db2') or die('Could not select database');

/*
 * CREATING QUERY STRINGS
 */
$password_query =
    "SELECT password
    FROM account
    WHERE email IN (
        SELECT email
        FROM instructor
        WHERE instructor_id = '$instructor_id'
    )";

$clubId_query =
    "SELECT *
    FROM club";

$president_query =
    "SELECT *
    FROM student
    WHERE student_id = '$president_id'";

/* 
 * VERIFY PASSWORD
 */

// get the password tied to the account for this instructor


$password_result = mysqli_query($myconnection, $password_query) or die("Query failed:" . mysqli_error($myconnection));
$password_fetch = mysqli_fetch_array($password_result, MYSQLI_ASSOC);
$correct_password = $password_fetch ? $password_fetch['password'] : null;

// check if the given password matches the correct password
if ($password == $correct_password) {
    /* 
     * CREATE CLUB ID AND CHECK DUPLICATE ADVISOR/PRESIDENT/CLUB NAME
     */

    // query the club table
    $clubId_result = mysqli_query($myconnection, $clubId_query) or die("Query failed:" . mysqli_error($myconnection));

    // do 4 things here:
    // make the club id the amount of entries currently in the table.
    // check if the president is already president of another club.
    // check if the instructor is already the advisor of another club.
    // check if there is already a club with this name.
    $club_id = 0;
    while ($clubId_fetch = mysqli_fetch_array($clubId_result, MYSQLI_ASSOC)) {
        if ($clubId_fetch["advisor_id"] == $instructor_id) {
            echo "Could not create club. The instructor with the ID $instructor_id is already the advisor of another club.";
            return;
        } else if ($clubId_fetch["president_id"] == $president_id) {
            echo "Could not create club. The student with the ID $president_id is already president of another club.";
            return;
        } else if ($clubId_fetch["name"] == $club_name) {
            echo "Could not create club. There is already a club named $club_name.";
        }

        $club_id++;
    }

    $president_result = mysqli_query($myconnection, $president_query) or die("Query failed:" . mysqli_error($myconnection));
    if (mysqli_num_rows($president_result) > 0) {
        /* 
         * CREATE AND RUN INSERT QUERIES
         */

        $insertClub_query =
            "INSERT INTO club (name, club_id, advisor_id, president_id)
            values ('$club_name', '$club_id', '$instructor_id', '$president_id')";

        $insertPresident_query =
            "INSERT INTO clubparticipants (student_id, club_id)
            values ('$president_id', '$club_id')";

        // insert the new tuple into club table
        mysqli_query($myconnection, $insertClub_query) or die("Query failed:" . mysqli_error($myconnection));
        mysqli_query($myconnection, $insertPresident_query) or die("Query failed:" . mysqli_error($myconnection));

        echo "$club_name successfully created. The president was automatically added as a member.";
    } else {
        echo "Could not create club. Could not find the student chosen for president.";
    }
} else {
    // incorrect password
    echo 'Could not create club. Incorrect instructor id or password.';
}

// close mysql connection
mysqli_close($myconnection);