  <!--  3. A student can browse all the courses offered in the current semester and can register for
        a specific section of a course if they satisfy the prerequisite conditions and there is
        available space in the section. (Assume each section is limited to 15 students). -->
<?php
// Establishing MYSQL connection 
$host = "localhost";
$username = "root";
$password = "";
$database = "db2";
$conn = new mysqli($host, $username, $password, $database);

// Check if connection is estblished
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



//Get Current year
$current_year=-1;
$query_get_current_year = "SELECT YEAR(CURDATE()) AS current_year";
$result = $conn->query($query_get_current_year);
if ($result && $row = $result->fetch_assoc()) {
    $current_year = $row['current_year'];
}

//Get current semester 
$current_semester="";
$query_get_current_semester = "SELECT
  CASE
    WHEN MONTH(CURDATE()) BETWEEN 1 AND 5 THEN 'Spring'
    WHEN MONTH(CURDATE()) BETWEEN 9 AND 12 THEN 'Fall'
    ELSE 'Summer'
  END AS current_semester";
  $result = $conn->query($query_get_current_semester);
  if ($result && $row = $result->fetch_assoc()) {
      $current_semester = $row['current_semester'];
  }

  //Get all sections offered this year and semester 
  $query_get_all_sections = "SELECT course.course_name , section.section_id 
  FROM section,course 
  WHERE section.course_id = course.course_id AND year = ? AND semester = ?
  ORDER BY course.course_name , section.section_id";
  $stmt = $conn->prepare($query_get_all_sections);
  $stmt->bind_param("ss", $current_year, $current_semester); 
  $stmt->execute();
  $result_get_all_sections = $stmt->get_result();
//Load the sections into the drop down menu 
$sections = '<option value="">-- Select a Section --</option>';
if ($result_get_all_sections->num_rows > 0) {
    while ($row = $result_get_all_sections->fetch_assoc()) {
        $combined = htmlspecialchars($row["course_name"]) . '-' . htmlspecialchars($row["section_id"]);
        $sections .= '<option value="' . $combined . '">' . $combined . '</option>';
    }
} else {
    $sections = '<option value="">No Section available</option>';
}

//Return if the form is not submitted
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
 return;
}


//Get User inputs 
$student_id = $_POST["student_id"];
$selected_course_name_and_section_id = $_POST["sections"];

$parts = explode("-", $selected_course_name_and_section_id);
$course_name = $parts[0];     
$section_id = $parts[1];     



//Get course id
$course_id="";
$query_get_course_id="SELECT course_id FROM course WHERE course_name = ?";
$stmt = $conn->prepare($query_get_course_id);
$stmt->bind_param("s", $course_name); 
$stmt->execute();
$result_get_course_id = $stmt->get_result();
if ($result_get_course_id && $row = $result_get_course_id->fetch_assoc()) {
  $course_id = $row['course_id'];
}
else{
  echo("course_id $course_id is not found");
}


//Check is student exists
$query_check_if_student_exist = "SELECT student_id FROM student WHERE student_id=?";
$stmt = $conn->prepare($query_check_if_student_exist);
$stmt->bind_param("s", $student_id); 
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
  $student_id = $row['student_id'];
}
else{
  echo ("student id $student_id is not found");
  return;
}


//Check if the student satisfy all the prerequisites for a specefic course query 
$query_are_prerequisites_satisfied="SELECT *
                                    FROM prereq
                                    WHERE course_id = ?
                                    AND prereq_id NOT IN (
                                                  SELECT course_id
                                                  FROM take
                                                   WHERE student_id = ?
                                                  )";
$stmt = $conn->prepare($query_are_prerequisites_satisfied);    
$stmt->bind_param("ss", $course_id,$student_id);     
$stmt->execute();                                         
$result = $stmt->get_result();
$are_prerequisites_satisfied = $result->num_rows===0;

//Check if there is an available space in the section. (Assume each section is limited to 15 students)
$query_num_students_per_section = "SELECT COUNT(student_id) num_students_per_section FROM take WHERE section_id=?";
$stmt = $conn->prepare($query_num_students_per_section);    
$stmt->bind_param("s", $section_id);     
$stmt->execute();                                         
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$num_students = $row['num_students_per_section'];
$is_space_available_in_section = $num_students<15;

//Check if student already registered for the section 
$query_check_registration = "
    SELECT *
    FROM take
    WHERE student_id = ?
      AND course_id = ?
      AND section_id = ?
      AND semester = ?
      AND year = ?
";
$stmt = $conn->prepare($query_check_registration);
$stmt->bind_param("ssssi", $student_id, $course_id, $section_id, $current_semester, $current_year);
$stmt->execute();
$result = $stmt->get_result();

$is_already_registered = $result->num_rows > 0;

function can_register($are_prerequisites_satisfied, $is_space_available_in_section, $is_already_registered){
  if(!$are_prerequisites_satisfied){
    echo ("Prerequisits should be taken before registering for this section.");
    return false;
  }
  if(!$is_space_available_in_section){
    echo("No available position,the section is already full.");
    return false;
  }
  if($is_already_registered){
    echo "Student is already registered.";
    return false;
  }
  return true;
}


  
  function register($conn, $student_id, $course_id, $section_id, $semester, $year, $grade = null) {
    $query_register = "
        INSERT INTO `take` (`student_id`, `course_id`, `section_id`, `semester`, `year`, `grade`)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query_register);

    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    // Use "s" for strings and "i" for integer
    $stmt->bind_param("ssssss", $student_id, $course_id, $section_id, $semester, $year, $grade);

    if ($stmt->execute()) {
        echo "Student successfully registered!";
        return true;
    } else {
        echo "Registration failed: " . $stmt->error;
        return false;
    }
}



if(can_register($are_prerequisites_satisfied, $is_space_available_in_section, $is_already_registered)){
  if(register($conn, $student_id, $course_id, $section_id, $current_semester, $current_year, $grade = null)){
    echo("Successfull Registeration.");
  }
  else{
    echo("Failed Registeration.");
  }
}
else{
  echo("Cannot Register for this section.");
}

?>




