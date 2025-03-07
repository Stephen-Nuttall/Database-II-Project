<?php

/*
 * SETUP
 */
// get HTML form info
$id = $_POST['student_id'];
$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];
$degree = $_POST['degree'];
$dept = $_POST['dept'];

// establish mysql connection
$myconnection = mysqli_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
$mydb = mysqli_select_db($myconnection, 'db2') or die('Could not select database');

/*
 * CREATING QUERY STRINGS
 */
// create query strings for account table
$account_query =
    "SELECT *
    FROM account
    WHERE email = '$email'";

$account_insert =
    "INSERT into account (email, password, type)
    values ('$email', '$password', 'student')";

$account_update =
    "UPDATE account
    SET password = '$password'
    WHERE email = '$email'";

// create query strings for student table
$student_query =
    "SELECT *
    FROM student
    WHERE student_id = '$id'";

$student_insert =
    "INSERT into student (student_id, name, email, dept_name)
values ('$id', '$name', '$email', '$dept')";

$student_update =
    "UPDATE student
    SET name = '$name', email = '$email', dept_name = '$dept'
    WHERE student_id = '$id'";

// create queries strings for inserting into the undergrad, masters, and phd tables
$undergrad_insert =
    "INSERT into undergraduate (student_id, total_credits, class_standing)
    values ('$id', 0, 'freshman')";
$master_insert =
    "INSERT into master (student_id, total_credits)
    values ('$id', 0)";
$phd_insert =
    "INSERT into phd (student_id, qualifier, proposal_defence_date, dissertation_defence_date)
    values ('$id', '', '', '')";

/*
 * INSERT OR UPDATE ACCOUNT TABLE 
 */
// see if there is already an account with the same email
$account_found = mysqli_query($myconnection, $account_query) or die('Query failed: ' . mysql_error());

// if there isn't already an account with this email, insert a new entry for this email and password.
// else, update the account's password to match the user input.
if (is_null(mysqli_fetch_array($account_found, MYSQLI_ASSOC))) {
    mysqli_query($myconnection, $account_insert) or die('Query failed: ' . mysql_error());
    echo "Account created successfully!\n";
} else {
    mysqli_query($myconnection, $account_update) or die('Query failed: ' . mysql_error());
    echo "Account updated successfully!\n";
}

/*
 * INSERT OR UPDATE STUDENT TABLE (and undergrad/master/phd)
 */
// see if there is already an account with the same id
$student_found = mysqli_query($myconnection, $student_query) or die('Query failed: ' . mysql_error());

// if there isn't already a student with this id, insert a new entry for this information.
// else, update the student information to match the user input.
if (is_null(mysqli_fetch_array($student_found, MYSQLI_ASSOC))) {
    mysqli_query($myconnection, $student_insert) or die('Query failed: ' . mysql_error());

    if ($degree == 'undergraduate') {
        mysqli_query($myconnection, $undergrad_insert) or die('Query failed: ' . mysql_error());
        echo "Undergraduate Student information created successfully!\n";
    } else if ($degree == 'master') {
        mysqli_query($myconnection, $master_insert) or die('Query failed: ' . mysql_error());
        echo "Master Student information created successfully!\n";
    } else if ($degree == 'phd') {
        mysqli_query($myconnection, $phd_insert) or die('Query failed: ' . mysql_error());
        echo "PhD Student information created successfully!\n";
    } else {
        echo "ERROR: could not add to undergraduate, master, or phd table.\n";
    }

    echo "!";

} else {
    mysqli_query($myconnection, $student_update) or die('Query failed: ' . mysql_error());
    echo "Student information updated successfully!\n";
}

// close mysql connection
mysqli_close($myconnection);

?>