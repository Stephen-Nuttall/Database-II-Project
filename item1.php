<?php

    $email = $_POST['email']; 
    $password = $_POST['password']; 
    
    $myconnection = mysqli_connect('localhost', 'root', '')  or die ('Could not connect: ' . mysql_error());
    $mydb = mysqli_select_db ($myconnection, 'db2') or die ('Could not select database');

    $query = "SELECT * FROM account WHERE email = '$email'";
    $insert = "INSERT into account (email, password, type) values ('$email', '$password', 'student')";
    $update = "UPDATE account SET password = '$password' WHERE email = '$email'";

    $result = mysqli_query($myconnection, $query) or die ('Query failed: ' . mysql_error());

    if (is_null(mysqli_fetch_array($result, MYSQLI_ASSOC)))
    {
        mysqli_query($myconnection, $insert) or die ('Query failed: ' . mysql_error());
        echo 'Account created successfully!';
    }
    else
    {
        mysqli_query($myconnection, $update) or die ('Query failed: ' . mysql_error());
        echo 'Account updated successfully!';
    }

    mysqli_close($myconnection);

?>