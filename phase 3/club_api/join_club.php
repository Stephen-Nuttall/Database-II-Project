<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$response = [
    "success" => false,
    "message" => "Unknown error occurred."
];

// Get POST data
$club_name = $_POST['club_name'] ?? '';
$student_id = $_POST['student_id'] ?? '';
$password = $_POST['password'] ?? '';

$conn = mysqli_connect('localhost', 'root', '', 'db2');
if (!$conn) {
    $response['message'] = "Database connection failed.";
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';

// Handle club list dropdown
if ($action === 'get_clubs') {
    $query = "SELECT name FROM club";
    $result = mysqli_query($conn, $query);

    $clubs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clubs[] = $row['name'];
    }

    echo json_encode(["success" => true, "clubs" => $clubs]);
    mysqli_close($conn);
    exit;
}

// Get student password
$password_query = "
    SELECT password
    FROM account
    WHERE email IN (
        SELECT email
        FROM student
        WHERE student_id = '$student_id'
    )";
$pass_result = mysqli_query($conn, $password_query);
$pass_row = mysqli_fetch_assoc($pass_result);
$correct_password = $pass_row['password'] ?? null;

if ($password !== $correct_password) {
    $response['message'] = "Incorrect student ID or password.";
    echo json_encode($response);
    exit;
}

// Check if club exists
$club_result = mysqli_query($conn, "SELECT * FROM club WHERE name = '$club_name'");
if (mysqli_num_rows($club_result) === 0) {
    $response['message'] = "No club found by the name $club_name.";
    echo json_encode($response);
    exit;
}

$club_data = mysqli_fetch_assoc($club_result);
$club_id = $club_data['club_id'];

// Check if already a member
$check_query = "SELECT * FROM clubparticipants WHERE club_id = '$club_id' AND student_id = '$student_id'";
$check_result = mysqli_query($conn, $check_query);
if (mysqli_num_rows($check_result) > 0) {
    $response['message'] = "Student is already a member of $club_name.";
    echo json_encode($response);
    exit;
}

// Insert membership
$insert_query = "INSERT INTO clubparticipants (student_id, club_id) VALUES ('$student_id', '$club_id')";
if (mysqli_query($conn, $insert_query)) {
    $response['success'] = true;
    $response['message'] = "Successfully joined $club_name.";
} else {
    $response['message'] = "Failed to join club.";
}

mysqli_close($conn);
echo json_encode($response);
