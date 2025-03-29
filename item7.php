<!--  7. Grader positions for sections with 5 to 10 students will be assigned by the admin with
        either MS students or undergraduate students who have got A- or A in the course. If
        there are more than one qualified candidates, the admin will choose one as the grader.
        A student may serve as a grader for only one section. -->


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

// Check if student exists
$student_id_query =
    "SELECT *
    FROM student
    WHERE student_id = ?";

$stmt = $myconnection->prepare($student_id_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "There is no student with this ID.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Check if student has an adequate grade
$student_grader_query =
    "SELECT *
    FROM take
    WHERE student_id = ? AND (grade = 'A' OR grade = 'A-')";

$stmt = $myconnection->prepare($student_grader_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "This student does not have the required grade for this class.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Is the section open
$section_open_query =
    "SELECT *
    FROM mastergrader, undergraduategrader
    WHERE (mastergrader.course_id = ? AND mastergrader.section_id = ?
        AND mastergrader.semester = ? AND mastergrader.year = ?)
    OR (undergraduategrader.course_id = ? AND undergraduategrader.section_id = ?
        AND undergraduategrader.semester = ? AND undergraduategrader.year = ?)";

$stmt = $myconnection->prepare($section_open_query);
$stmt->bind_param("sssdsssd",   $course_id, $section_id, $this_semester, $this_year,
                            $course_id, $section_id, $this_semester, $this_year);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 0) {
    echo "This section already has a grader.";
    $myconnection->close();
    exit;
}
$stmt->close();

// Is the section the right size
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

if ($size < 5 && $size > 10) {
    echo "There need to be between 5 and 10 students in a section to get a grader.";
    $myconnection->close();
    exit;
}


// If the student is a master
$master_id_query =
    "SELECT *
    FROM master
    WHERE student_id = ?";

$stmt = $myconnection->prepare($master_id_query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 0) {

    // Is student available
    $master_availablity_query =
        "SELECT *
        FROM mastergrader
        WHERE student_id = ?";

    $stmt = $myconnection->prepare($master_availablity_query);
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows != 0) {
        echo "This student is already serving as a grader for a section.";
        $myconnection->close();
        exit;
    }
    $stmt->close();

    // Insert masters student
    $master_grader_insert =
        "INSERT INTO mastergrader
        VALUES (?, ?, ?, ?, ?)";

    $stmt = $myconnection->prepare($master_grader_insert);
    $stmt->bind_param("ssssd", $sid, $course_id, $section_id, $this_semester, $this_year);
    if ($stmt->execute()) {
        echo 'The student has been added as a grader to the chosen section.';
    } else {
        echo 'Insert failed, student could not be added.';
    }
    $myconnection->close();


// If the student is an undergrad
} else { 
    $undergrad_id_query =
    "SELECT *
    FROM undergraduate
    WHERE student_id = ?";

    $stmt = $myconnection->prepare($undergrad_id_query);
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows != 0) {

        // Is student available
        $undergrad_availablity_query =
            "SELECT *
            FROM undergraduategrader
            WHERE student_id = ?";

        $stmt = $myconnection->prepare($undergrad_availablity_query);
        $stmt->bind_param("s", $sid);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows != 0) {
            echo "This student is already serving as a grader for a section.";
            $myconnection->close();
            exit;
        }
        $stmt->close();

        // Insert undergrad student
        $undergrad_grader_insert =
            "INSERT INTO undergraduategrader
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $myconnection->prepare($undergrad_grader_insert);
        $stmt->bind_param("ssssd", $sid, $course_id, $section_id, $this_semester, $this_year);
        if ($stmt->execute()) {
            echo 'The student has been added as a grader to the chosen section.';
        } else {
            echo 'Insert failed, student could not be added.';
        }
        $myconnection->close();

    // No such student ID
    } else {
        echo "There is no master or undergraduate student with this ID.";
        $myconnection->close();
        exit;
    }
}