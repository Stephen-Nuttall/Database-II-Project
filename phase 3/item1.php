<?php

/*
 * SETUP
 */
// get HTML form info
$id = $_POST['student_id'];
$email = $_POST['email'];
$old_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$name = $_POST['name'];
$degree = $_POST['degree'];
$dept = $_POST['dept'];

// establish mysql connection
$myconnection = mysqli_connect('localhost', 'root', '') or die('Could not connect: ' . mysqli_error($myconnection));
$mydb = mysqli_select_db($myconnection, 'db2') or die('Could not select database');

/*
 * CREATING QUERY STRINGS
 * This is only possible to do all up front because none of the information inserted into these strings will change before they're used.
 */
// create query strings for account table
$account_query =
    "SELECT *
    FROM account
    WHERE email = '$email'";

$account_insert =
    "INSERT into account (email, password, type)
    values ('$email', '$new_password', 'student')";

$account_update =
    "UPDATE account
    SET password = '$new_password'
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

// search queries for undergrad, masters, and phd tables
$undergrad_query =
    "SELECT *
    FROM undergraduate
    WHERE student_id = '$id'";

$master_query =
    "SELECT *
    FROM master
    WHERE student_id = '$id'";

$phd_query =
    "SELECT *
    FROM phd
    WHERE student_id = '$id'";

// delete queries for undergrad, masters, and phd tables
$undergrad_delete =
    "DELETE FROM undergraduate
    WHERE student_id = '$id'";

$master_delete =
    "DELETE FROM master
    WHERE student_id = '$id'";

$phd_delete =
    "DELETE FROM phd
    WHERE student_id = '$id'";

/*
 * INSERT OR UPDATE ACCOUNT TABLE 
 */
// see if there is already an account with the same email
$account_result = mysqli_query($myconnection, $account_query) or die('Query failed: ' . mysqli_error($myconnection));

// if there isn't already an account with this email, insert a new entry for this email and password.
// else, update the account's password to match the user input.
$account_fetch = mysqli_fetch_array($account_result, MYSQLI_ASSOC);
if (is_null($account_fetch)) {
    mysqli_query($myconnection, $account_insert) or die('Query failed: ' . mysqli_error($myconnection));
    echo "Account created successfully!\n";
} else {
    if ($account_fetch["password"] == $old_password) {
        mysqli_query($myconnection, $account_update) or die('Query failed: ' . mysqli_error($myconnection));
        echo "Account updated successfully!\n";
    } else {
        echo "Account could not be updated. Incorrect password.\n";
        return;
    }
}

/*
 * INSERT OR UPDATE STUDENT TABLE (and undergrad/master/phd)
 */
// see if there is already an account with the same id
$student_found = mysqli_query($myconnection, $student_query) or die('Query failed: ' . mysqli_error($myconnection));
$undergraduate_found = mysqli_query($myconnection, $undergrad_query) or die('Query failed: ' . mysqli_error($myconnection));
$master_found = mysqli_query($myconnection, $master_query) or die('Query failed: ' . mysqli_error($myconnection));
$phd_found = mysqli_query($myconnection, $phd_query) or die('Query failed: ' . mysqli_error($myconnection));

// if there isn't already a student with this id, insert a new entry for this information.
// else, update the student information to match the user input.
if (is_null(mysqli_fetch_array($student_found, MYSQLI_ASSOC))) {
    mysqli_query($myconnection, $student_insert) or die('Query failed: ' . mysqli_error($myconnection));

    if ($degree == 'undergraduate') {
        mysqli_query($myconnection, $undergrad_insert) or die('Query failed: ' . mysqli_error($myconnection));
        echo "Undergraduate Student information created successfully!\n";
    } else if ($degree == 'master') {
        mysqli_query($myconnection, $master_insert) or die('Query failed: ' . mysqli_error($myconnection));
        echo "Master Student information created successfully!\n";
    } else if ($degree == 'phd') {
        mysqli_query($myconnection, $phd_insert) or die('Query failed: ' . mysqli_error($myconnection));
        echo "PhD Student information created successfully!\n";
    } else {
        echo "ERROR: could not add to undergraduate, master, or phd table.\n";
    }

} else {
    mysqli_query($myconnection, $student_update) or die('Query failed: ' . mysqli_error($myconnection));
    if ($degree == 'undergraduate') {
        if (is_null(mysqli_fetch_array($undergraduate_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $undergrad_insert) or die('Query failed: ' . mysqli_error($myconnection));
        }

        if (!is_null(mysqli_fetch_array($master_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $master_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }
        if (!is_null(mysqli_fetch_array($phd_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $phd_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }

        echo "Undergraduate Student information updated successfully!\n";
    } else if ($degree == 'master') {
        if (is_null(mysqli_fetch_array($master_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $master_insert) or die('Query failed: ' . mysqli_error($myconnection));
        }

        if (!is_null(mysqli_fetch_array($undergraduate_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $undergrad_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }
        if (!is_null(mysqli_fetch_array($phd_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $phd_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }

        echo "Master Student information updated successfully!\n";
    } else if ($degree == 'phd') {
        if (is_null(mysqli_fetch_array($phd_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $phd_insert) or die('Query failed: ' . mysqli_error($myconnection));
        }

        if (!is_null(mysqli_fetch_array($undergraduate_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $undergrad_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }
        if (!is_null(mysqli_fetch_array($master_found, MYSQLI_ASSOC))) {
            mysqli_query($myconnection, $master_delete) or die('Query failed: ' . mysqli_error($myconnection));
        }

        echo "PhD Student information updated successfully!\n";
    } else {
        echo "ERROR: could not add to undergraduate, master, or phd table.\n";
    }

    echo "Student information updated successfully!\n";
}

// close mysql connection
mysqli_close($myconnection);