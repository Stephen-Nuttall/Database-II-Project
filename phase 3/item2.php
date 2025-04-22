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

$password_attempt = $_POST["password"];
$sid = $_POST["student_id"];
$selected_course_id_and_section_id = $_POST["section"];
$parts = explode("-", $selected_course_id_and_section_id);
$course_id = $parts[0];     
$section_id = $parts[1];

if (empty($password_attempt) or empty($sid)) {
    $response['message'] = "Please fill out all the fields!";
    echo json_encode($response);
    exit;
}

$student_id_query =
    "SELECT *
    FROM student
    WHERE student_id = ?";

$stmt = $myconnection->prepare($student_id_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    $response['message'] = "There is no student with this ID.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}
$stmt->close();


$student_password_query =
    "SELECT password
    FROM account
    WHERE email = (
    SELECT email
    FROM student
    WHERE student_id = ?)";

$stmt = $myconnection->prepare($student_password_query);
$stmt->bind_param("s", $sid);
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

// Check if student already registered for the section 
$query_check_registration = "
    SELECT *
    FROM take
    WHERE student_id = ?
      AND course_id = ?
      AND section_id = ?
      AND semester = ?
      AND year = ?
";
$stmt = $myconnection->prepare($query_check_registration);
$stmt->bind_param("ssssd", $sid, $course_id, $section_id, $this_semester, $this_year);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $response['message'] = "You have already registered for this section.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}

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

if ($size > 14) {
    $response['message'] = "There are already 15 students taking this section.";
    $myconnection->close();
    echo json_encode($response);
    exit;
}

$take_insert =
    "INSERT INTO take
    VALUES (?, ?, ?, ?, ?, ?)";
    
$stmt = $myconnection->prepare($take_insert);
$nullValue = NULL;
$stmt->bind_param("ssssds", $sid, $course_id, $section_id, $this_semester, $this_year, $nullValue);
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "You have enrolled in the chosen section.";
} else {
    $response['message'] = "Insert failed, student could not be added.";
}
$myconnection->close();
echo json_encode($response);
