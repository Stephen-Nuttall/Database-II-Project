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

//Get User Inputs
$student_id = trim($_POST['student_id']);
$hourly_rate= $_POST['hourly_rate'];
$notes= $_POST['notes'];
$is_active= $_POST['is_active'];

//Check if students exist 
$query_check_if_student_exist = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($query_check_if_student_exist);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_exist = ($result->num_rows > 0);

if(!$student_exist){
  echo ("Student with id : $student_id does not exist");
  return;
}

//Check if tutor exists 
$query_check_if_tutor_exists = "SELECT * FROM tutor WHERE student_id = ?";
$stmt = $conn->prepare($query_check_if_tutor_exists);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$tutor_exist = ($result->num_rows > 0);

//Check if user passed at least one class with A- or better 
$query_check_grade_for_at_least_one_class = "SELECT * FROM take WHERE student_id = ? AND grade IN ('A-','A','A+')";
$stmt = $conn->prepare($query_check_grade_for_at_least_one_class);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$passed_at_least_one_class_with_required_grade = ($result->num_rows > 0);

// Check if we can insert
function can_insert($tutor_exist, $passed_at_least_one_class_with_required_grade, $student_id) {
  if ($tutor_exist) {
    echo "Tutor with student ID: $student_id already exists.";
    return false;
  }
  if (!$passed_at_least_one_class_with_required_grade) {
    echo "Student must pass at least one class with A- or better to be a tutor.";
    return false;
  }
  return true;
}


function insert($conn, $student_id, $hourly_rate = null, $notes = null, $is_active = 0) {
  $query_sql_insert = "INSERT INTO tutor (student_id, notes, hourly_rate, is_active)
                       VALUES (?, ?, ?, ?)";
  
  $stmt = $conn->prepare($query_sql_insert);
  if (!$stmt) {
      echo "Prepare failed: " . $conn->error;
      return false;
  }

  $stmt->bind_param("ssdi", $student_id, $notes, $hourly_rate, $is_active);

  if ($stmt->execute()) {
      $stmt->close();
      return true;
  } else {
      echo "Insert failed: " . $stmt->error;
      $stmt->close();
      return false;
  }
}

//Find tutor by student id 
function find_tutor_by_student_id($conn, $student_id) {
  $query = "SELECT * FROM tutor WHERE student_id = ?";
  
  $stmt = $conn->prepare($query);
  if (!$stmt) {
      echo "Prepare failed: " . $conn->error;
      return null;
  }

  $stmt->bind_param("s", $student_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
      $stmt->close();
      return $row;  
  } else {
      $stmt->close();
      return null;  
  }
}


//Update tutor
function update($conn,$student_id,$notes,$hourly_rate,$is_active){
  $tutor=find_tutor_by_student_id($conn,$student_id);
  if(!$tutor){
    echo(".Tutor is not found");
    return;
  }
  $tutor_id=$tutor['tutor_id'];
  $query="UPDATE tutor SET student_id=?,notes=?,hourly_rate=?,is_active=? WHERE tutor_id =?";
  $stmt = $conn->prepare($query);
  if (!$stmt) {
      echo "Prepare failed: " . $conn->error;
      return null;
  }

  $stmt->bind_param("ssdii", $student_id,$notes,$hourly_rate,$is_active,$tutor_id);
  if ($stmt->execute()) {
    $stmt->close();
    return true;
} else {
    echo "Update failed: " . $stmt->error;
    $stmt->close();
    return false;
}
}



if(!$tutor_exist){
  if(can_insert($tutor_exist, $passed_at_least_one_class_with_required_grade, $student_id)){
    if(insert($conn, $student_id, $hourly_rate, $notes, $is_active))
    {
      echo(".Tutor added successfully.");
    }
    else
    {
      echo(".Failed Adding tutor.");
    }
  }
  else{
    echo (".Cannot Insert.");
  }

}
else{
  if(update($conn,$student_id,$notes,$hourly_rate,$is_active)){
    echo(".Tutor Updated successfully.");
  }
  else{
    echo(".Failed Updating Tutor.");
  }
}

//Close Connection
$conn->close();


?>