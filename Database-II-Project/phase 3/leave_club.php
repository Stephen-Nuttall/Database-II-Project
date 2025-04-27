<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$response = [
    "success" => false,
    "message" => "An unknown error occurred."
];

// Get input data
$club_name = $_POST['club_name'] ?? '';
$student_id = $_POST['student_id'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($club_name) || empty($student_id) || empty($password)) {
    $response['message'] = "All fields are required.";
    echo json_encode($response);
    exit;
}

// Connect to DB
$conn = mysqli_connect('localhost', 'root', '', 'db2');
if (!$conn) {
    $response['message'] = "Database connection failed.";
    echo json_encode($response);
    exit;
}

// Escape input
$club_name = mysqli_real_escape_string($conn, $club_name);
$student_id = mysqli_real_escape_string($conn, $student_id);
$password = mysqli_real_escape_string($conn, $password);

// Verify password
$password_query = "
    SELECT password
    FROM account
    WHERE email IN (
        SELECT email FROM student WHERE student_id = '$student_id'
    )
";
$pass_result = mysqli_query($conn, $password_query);
$pass_row = mysqli_fetch_assoc($pass_result);
$correct_password = $pass_row['password'] ?? null;

if ($password !== $correct_password) {
    $response['message'] = "Incorrect student ID or password.";
    echo json_encode($response);
    exit;
}

// Check if club exists
$club_query = "SELECT * FROM club WHERE name = '$club_name'";
$club_result = mysqli_query($conn, $club_query);
if (mysqli_num_rows($club_result) === 0) {
    $response['message'] = "No club found by the name $club_name.";
    echo json_encode($response);
    exit;
}

$club = mysqli_fetch_assoc($club_result);
$club_id = $club['club_id'];

// Check if student is in club
$participant_query = "
    SELECT * FROM clubparticipants 
    WHERE club_id = '$club_id' AND student_id = '$student_id'
";
$participant_result = mysqli_query($conn, $participant_query);
if (mysqli_num_rows($participant_result) === 0) {
    $response['message'] = "You are not a member of $club_name.";
    echo json_encode($response);
    exit;
}

// Prevent president from leaving
if ($club['president_id'] == $student_id) {
    $response['message'] = "Cannot leave $club_name. You are the club president.";
    echo json_encode($response);
    exit;
}

// Remove from club
$delete_query = "
    DELETE FROM clubparticipants 
    WHERE club_id = '$club_id' AND student_id = '$student_id'
";
if (mysqli_query($conn, $delete_query)) {
    $response['success'] = true;
    $response['message'] = "Successfully left $club_name.";
} else {
    $response['message'] = "Failed to leave the club.";
}

mysqli_close($conn);
echo json_encode($response);
