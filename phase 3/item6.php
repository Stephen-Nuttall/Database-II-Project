<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Establishing MYSQL connection 
$host = "localhost";
$username = "root";
$password = "";
$database = "db2";
$myconnection = new mysqli($host, $username, $password, $database);

// Check if connection is established
if ($myconnection->connect_error) {
    $response['message'] = "Database connection failed.";
    echo json_encode($response);
    exit;
}

//Get Current year
$this_year=-1;
$query_get_current_year = "SELECT YEAR(CURDATE()) AS current_year";
$result = $myconnection->query($query_get_current_year);

if ($result && $row = $result->fetch_assoc()) {
    $this_year = $row['current_year'];
}

//Get current semester 
$this_semester = "";
$query_get_current_semester = "SELECT
    CASE
        WHEN MONTH(CURDATE()) BETWEEN 1 AND 5 THEN 'Spring'
        WHEN MONTH(CURDATE()) BETWEEN 9 AND 12 THEN 'Fall'
        ELSE 'Summer'
    END AS current_semester";

$result = $myconnection->query($query_get_current_semester);
if ($result && $row = $result->fetch_assoc()) {
    $this_semester = $row['current_semester'];
}

$action = $_POST['action'] ?? '';

// Handle section list dropdown
if ($action === 'get_sections') {
    // Get all sections offered this year and semester 
    $query_get_all_sections = "SELECT course_id , section_id 
        FROM section
        WHERE year = ? AND semester = ?
        ORDER BY course_id, section_id";

    $stmt = $myconnection->prepare($query_get_all_sections);
    $stmt->bind_param("ss", $this_year, $this_semester);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        // Construct the section information string
        $section_info = $row['course_id'] . "-" . $row['section_id'];
        $sections[] = $section_info;
    }
    $stmt->close();
    $myconnection->close();
    echo json_encode(["success" => true, "sections" => $sections, "semester" => $this_semester, "year" => $this_year]);
    exit;
}

$email = $_POST["email"];
$password_attempt = $_POST["password"];
$sid = $_POST["student_id"];
$selected_course_id_and_section_id = $_POST["section"];
$parts = explode("-", $selected_course_id_and_section_id);
$course_id = $parts[0];     
$section_id = $parts[1];

if (empty($email) or empty($password_attempt) or empty($sid)) {
    $response['message'] = "Please fill out all the fields!";
    echo json_encode($response);
    exit;
}

$admin_email_query =
    "SELECT *
    FROM account
    WHERE email = ? AND type = 'admin'";

$stmt = $myconnection->prepare($admin_email_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    $response['message'] = "This is not a valid email.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}
$stmt->close();

$admin_password_query =
    "SELECT password
    FROM account
    WHERE email = ?";

$stmt = $myconnection->prepare($admin_password_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($actual_password);
$stmt->fetch();
$stmt->close();
if ($password_attempt != $actual_password) {
    $response['message'] = "The password entered is incorrect.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}

$student_id_query =
    "SELECT *
    FROM phd
    WHERE student_id = ?";

$stmt = $myconnection->prepare($student_id_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    $response['message'] = "There is no PhD student with this ID.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}
$stmt->close();

$phd_availablity_query =
    "SELECT *
    FROM ta
    WHERE student_id = ?";

$stmt = $myconnection->prepare($phd_availablity_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 0) {
    $response['message'] = "This PhD student is already a TA for a section.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}
$stmt->close();

$section_open_query =
    "SELECT *
    FROM ta
    WHERE course_id = ? AND section_id = ? AND semester = ? AND year = ?";

$stmt = $myconnection->prepare($section_open_query);
$stmt->bind_param("sssd", $course_id, $section_id, $this_semester, $this_year);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 0) {
    $response['message'] = "This section already has a TA.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}
$stmt->close();

$section_size_query =
    "SELECT COUNT(*)
    FROM take
    where course_id = ? AND section_id = ? AND semester = ? AND year = ?";

$stmt = $myconnection->prepare($section_size_query);
$stmt->bind_param("sssd", $course_id, $section_id, $this_semester, $this_year);
$stmt->execute();
$stmt->bind_result($size);
$stmt->fetch();
$stmt->close();

if ($size < 10) {
    $response['message'] = "There need to be at least 10 students in a section to get a TA.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}

$ta_insert =
    "INSERT INTO ta
    VALUES (?, ?, ?, ?, ?)";
    
$stmt = $myconnection->prepare($ta_insert);
$stmt->bind_param("ssssd", $sid, $course_id, $section_id, $this_semester, $this_year);
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "The student has been added as a TA to the chosen section.";
} else {
    $response['message'] = "Insert failed, student could not be added.";
}
$myconnection->close();
echo json_encode($response);
