<!--  6. Teaching Assistants (TAs), who are PhD students, will be assigned by the admin to
sections with more than 10 students. A PhD student is eligible to be a TA for only one
section. -->

<?php
// Establishing MYSQL connection 
$host = "localhost";
$username = "root";
$password = "";
$database = "db2";
$myconnection = new mysqli($host, $username, $password, $database);

// Check if connection is estblished
if ($myconnection->connect_error) {
    die("Connection failed: " . $myconnection->connect_error);
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

//Get all sections offered this year and semester 
$query_get_all_sections = "SELECT course_id , section_id 
    FROM section
    WHERE year = ? AND semester = ?
    ORDER BY course_id, section_id";

$stmt = $myconnection->prepare($query_get_all_sections);
$stmt->bind_param("ss", $this_year, $this_semester); 
$stmt->execute();
$result_get_all_sections = $stmt->get_result();

//Load the sections into the drop down menu 
$these_sections = '<option value="">-- Select a Section --</option>';
if ($result_get_all_sections->num_rows > 0) {
    while ($row = $result_get_all_sections->fetch_assoc()) {
        $combined = htmlspecialchars($row["course_id"]) . '-' . htmlspecialchars($row["section_id"]);
        $these_sections .= '<option value="' . $combined . '">' . $combined . '</option>';
    }
} else {
    $these_sections = '<option value="">No sections available</option>';
}
  
//Return if the form is not submitted
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
   return;
}

$email = $_POST["email"];
$password_attempt = $_POST["password"];
$sid = $_POST["student_id"];
$selected_course_id_and_section_id = $_POST["section"];
$parts = explode("-", $selected_course_id_and_section_id);
$course_id = $parts[0];     
$section_id = $parts[1];

if (empty($email) or empty($password_attempt) or empty($sid)) {
    echo "Please fill out all the fields!";
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
    echo "This is not a valid email.";
    $myconnection->close();
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
    echo "The password entered is incorrect.";
    $myconnection->close();
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
    echo "There is no PhD student with this ID.";
    $myconnection->close();
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
    echo "This PhD student is already a TA for a section.";
    $myconnection->close();
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
    echo "This section already has a TA.";
    $myconnection->close();
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
    echo "There need to be at least 10 students in a section to get a TA.";
    $myconnection->close();
    exit;
}

$ta_insert =
    "INSERT INTO ta
    VALUES (?, ?, ?, ?, ?)";
    
$stmt = $myconnection->prepare($ta_insert);
$stmt->bind_param("ssssd", $sid, $course_id, $section_id, $this_semester, $this_year);
if ($stmt->execute()) {
    echo 'The student has been added as a TA to the chosen section.';
} else {
    echo 'Insert failed, student could not be added.';
}
$myconnection->close();
