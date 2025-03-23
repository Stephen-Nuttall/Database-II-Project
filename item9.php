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
 * VERIFY PASSWORD
 */

// get the password tied to the account for this instructor
$password_query =
    "SELECT password
    FROM account
    WHERE email IN (
        SELECT email
        FROM instructor
        WHERE instructor_id = '$instructor_id'
    )";

$password_result = mysqli_query($myconnection, $password_query) or die("Query failed:" . mysqli_error($myconnection));
$password_fetch = mysqli_fetch_array($password_result, MYSQLI_ASSOC);
$correct_password = $password_fetch ? $password_fetch['password'] : null;

// check if the given password matches the correct password
if ($password == $correct_password) {
    /* 
     * CREATE CLUB ID AND CHECK DUPLICATE ADVISOR/PRESIDENT
     */

    // query the club table
    $clubId_query =
        "SELECT *
        FROM club";
    $clubId_result = mysqli_query($myconnection, $clubId_query) or die("Query failed:" . mysqli_error($myconnection));

    // do 3 things here:
    // make the club id the amount of entries currently in the table.
    // check if the president is already president of another club.
    // check if the instructor is already the advisor of another club.
    $club_id = 0;
    while ($clubId_fetch = mysqli_fetch_array($clubId_result, MYSQLI_ASSOC)) {
        if ($clubId_fetch["advisor_id"] == $instructor_id) {
            echo "Could not create club. The instructor with the ID $instructor_id is already the advisor of another club.";
            return;
        } else if ($clubId_fetch["president_id"] == $president_id) {
            echo "Could not create club. The student with the ID $president_id is already president of another club.";
            return;
        }

        $club_id++;
    }

    /* 
     * INSERT QUERY
     */

    // insert the new tuple into club table
    $insert_query =
        "INSERT INTO club (name, club_id, advisor_id, president_id)
        values ('$club_name', '$club_id', '$instructor_id', '$president_id')";
    mysqli_query($myconnection, $insert_query) or die("Query failed:" . mysqli_error($myconnection));

    echo "$club_name successfully created";
} else {
    // incorrect password
    echo 'Could not create club. Incorrect instructor id or password.';
}