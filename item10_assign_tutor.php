<?php

    //After adding a new tutor,the admin can now assign hours and update hours for tutors based on multiple checks
    //The user can save a tutor assignment (insert or update) based on the following : 
    // Is the tutor active?
    // Did the tutor get a good grade (A- or better )in the course?
    // Is there any time conflict with tutor classes?
    // Is the tutor working more than the allowed weekly hours (Assumed to be 22 hours in this case)?
    // Does the new time overlap with another tutoring session?
    // If one of the requirments above are not met the user cannot assign new hours or update


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

//Cnstants 
const MAX_HOURS_PER_WEEK = 22;

//Get tutor names
$query_get_tutor_names="SELECT student.name FROM tutor,student WHERE tutor.student_id=student.student_id ORDER BY student.name";
$result_get_tutor_names=$conn->query($query_get_tutor_names);

//Load the courses into the courses drop down menu 
$tutor_names = "";
$tutor_names .= '<option value="">-- Select A Tutor--</option>';
if ($result_get_tutor_names->num_rows > 0) {
    while ($row = $result_get_tutor_names->fetch_assoc()) {
        $tutor_names .= '<option value="' . htmlspecialchars($row["name"]) . '">' . htmlspecialchars($row["name"]) . '</option>';
    }
} else {
    $tutor_names = '<option value="">No Tutors available</option>';
}

//Get all courses name
$query_get_all_courses_name = "SELECT course_name FROM course ORDER BY course_name";
$result_get_all_courses_name=$conn->query($query_get_all_courses_name);

//Load the courses into the courses drop down menu 
$course_names = "";
$course_names .= '<option value="">-- Select A Course--</option>';
if ($result_get_all_courses_name->num_rows > 0) {
    while ($row = $result_get_all_courses_name->fetch_assoc()) {
        $course_names .= '<option value="' . htmlspecialchars($row["course_name"]) . '">' . htmlspecialchars($row["course_name"]) . '</option>';
    }
} else {
    $course_names = '<option value="">No courses available</option>';
}

// Get all tutoring locations 
$query_get_all_locations = "SELECT building, room_number FROM classroom WHERE building LIKE '%Tutoring%' ORDER BY building, CAST(room_number AS UNSIGNED) ASC";
$result_query_get_all_locations = $conn->query($query_get_all_locations);

//Load the locations into the locations drop down menu 
$locations = '<option value="">-- Select a Location --</option>';
if ($result_query_get_all_locations->num_rows > 0) {
    while ($row = $result_query_get_all_locations->fetch_assoc()) {
        $combined = htmlspecialchars($row["building"]) . '-' . htmlspecialchars($row["room_number"]);
        $locations .= '<option value="' . $combined . '">' . $combined . '</option>';
    }
} else {
    $locations = '<option value="">No Location available</option>';
}


// Get all days
$query_get_all_days = "SELECT * FROM days ORDER BY day_name";
$result_get_all_days = $conn->query($query_get_all_days);

//Load the days into the days drop down menu 
$days = '<option value="">-- Select a Day --</option>';
if ($result_get_all_days->num_rows > 0) {
    while ($row = $result_get_all_days->fetch_assoc()) {
      $days .= '<option value="' . htmlspecialchars($row["day_name"]) . '">' . htmlspecialchars($row["day_name"]) . '</option>';

    }
} else {
    $days = '<option value="">No Days available</option>';
}

//Get all starting times 
$query_get_all_start_times = "SELECT * 
                                FROM hours
                                WHERE hour_label >= '08:00:00' AND hour_label < '24:00:00'
                                ORDER BY hour_label";
                              
$result_get_all_start_times = $conn->query($query_get_all_start_times);
//Load the start times into the start time drop down menu 
$start_times = '<option value="">-- Select a Starting Time --</option>';
if ($result_get_all_start_times->num_rows > 0) {
  while ($row = $result_get_all_start_times->fetch_assoc()) {
      
      $start_time_full = date('H:i:s', strtotime($row["hour_label"]));

      $start_time_display = date('H:i', strtotime($row["hour_label"]));


      $start_times .= '<option value="' . $start_time_full . '">' . $start_time_display . '</option>';
  }
} else {
  $start_times = '<option value="">No Starting Time available</option>';
}

// Get all ending times 
$query_get_all_end_times = "SELECT * 
                              FROM hours
                              WHERE hour_label > '08:00:00' AND hour_label <= '23:59:59'
                              ORDER BY hour_label";

$result_get_all_end_times = $conn->query($query_get_all_end_times);

// Load the end times into the end time drop down menu 
$end_times = '<option value="">-- Select an Ending Time --</option>';
if ($result_get_all_end_times->num_rows > 0) {
  while ($row = $result_get_all_end_times->fetch_assoc()) {
      
      $end_time_full = date('H:i:s', strtotime($row["hour_label"]));

      $end_time_display = date('H:i', strtotime($row["hour_label"]));

      $end_times .= '<option value="' . $end_time_full . '">' . $end_time_display . '</option>';
  }
} else {
  $end_times = '<option value="">No Ending Time available</option>';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}


//Get User Inputs
$input_tutor = $_POST["tutor_names"];
$input_course=$_POST["course_names"];
$input_location = $_POST["locations"];
$input_year = $_POST["year"];
$input_semester = $_POST["semester"];
$input_day = $_POST["days"];
$input_start_time = $_POST["start_times"];
$input_end_time = $_POST["end_times"];

if ($input_start_time >= $input_end_time) {
    echo "start and end time range is not valid, start time should be less than end time";
    return;
}

//Get objects Details

//Get tutor details
//First We need to get student by student name
function get_student_by_student_name($conn,$student_name){
  $query = "SELECT * FROM student WHERE name = ?";
  
  $stmt = $conn->prepare($query);
  if (!$stmt) {
      echo "Prepare failed: " . $conn->error;
      return null;
  }

  $stmt->bind_param("s", $student_name);
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

//Tutor Details
function get_tutor_details_by_tutor_name($conn,$tutor_name){
  $student = get_student_by_student_name($conn,$tutor_name);
  if(!$student){
    echo ("student is not found");
    return ;
  }
  $student_id = $student["student_id"];
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

//Get Course Details 
function get_course_by_course_name($conn,$course_name){
  $query = "SELECT * FROM course WHERE course_name = ?";
  
  $stmt = $conn->prepare($query);
  if (!$stmt) {
      echo "Prepare failed: " . $conn->error;
      return null;
  }

  $stmt->bind_param("s", $course_name);
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

//Get Location Details
$parts = explode("-", $input_location);
$building_name = $parts[0];
$room_number = $parts[1];
function get_location_details_by_building_name_and_room_number($conn,$building_name,$room_number){
    $query = "SELECT * FROM classroom WHERE building=? AND room_number=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return null;
    }
    $stmt->bind_param("ss", $building_name,$room_number);
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

// $input_day = $_POST["days"];
//Get Day Details 
function get_day_details_by_day_name($conn,$day_name){
    $query = "SELECT * FROM days WHERE day_name=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return null;
    }
    $stmt->bind_param("s", $day_name);
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


//Get Start/End time details
function get_hour($conn,$hour){
    $query = "SELECT * FROM hours WHERE hour_label=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return null;
    }
    $stmt->bind_param("s", $hour);
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

//Check if tutor has been assigned with same hours,day,semester and year 
function tutor_assignment_exists($conn, $tutor_id, $course_id, $classroom_id, $year, $semester, $day_id, $start_hour_id, $end_hour_id) {
    $query = "SELECT* FROM tutor_assignments 
              WHERE tutor_id = ? 
                AND course_id = ? 
                AND classroom_id = ? 
                AND year = ? 
                AND semester = ? 
                AND day_id = ? 
                AND (start_hour_id = ? 
                OR end_hour_id = ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssii", $tutor_id, $course_id, $classroom_id, $year, $semester, $day_id, $start_hour_id, $end_hour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

    $tutor_assignment_exists = tutor_assignment_exists($conn, 
    get_tutor_details_by_tutor_name($conn,$input_tutor)['tutor_id'], 
    get_course_by_course_name($conn,$input_course)['course_id'], 
    get_location_details_by_building_name_and_room_number($conn,$building_name,$room_number)['classroom_id'],
    (int)$input_year,
    $input_semester,
    get_day_details_by_day_name($conn,$input_day)['day_id'], 
    get_hour($conn,$input_start_time)['hour_id'],
    get_hour($conn,$input_end_time)['hour_id']
);

//Check if tutor is active
$tutor = get_tutor_details_by_tutor_name($conn,$input_tutor);
$is_tutor_active = ($tutor['is_active']===1);

//Check if the tutor passes the class with a grade of A- or better 
function did_get_required_grade_for_course($conn, $student_id, $course_id) {
    $query = "SELECT * FROM take 
            WHERE student_id = ? 
            AND course_id = ? 
            AND grade IN ('A-', 'A', 'A+')";
            

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

//Check grade for tutoring
$did_get_required_grade_for_course = did_get_required_grade_for_course($conn,get_tutor_details_by_tutor_name($conn,$input_tutor)['student_id'],
get_course_by_course_name($conn,$input_course)['course_id']);

//Check is there is an overlap
function is_there_an_overlaping_time_between_tutoring_and_classes($conn,$student_id,$semester,$year,$day,$tutoring_start_time,$tutoring_end_time){
    $query = "SELECT time_slot.start_time, time_slot.end_time 
        FROM 
          (SELECT * 
           FROM take 
           WHERE student_id = ? AND semester = ? AND year = ?) AS taken_by_student,
          (SELECT * 
           FROM section 
           WHERE semester = ? AND year = ? ) AS sections,
          time_slot
        WHERE 
          taken_by_student.section_id = sections.section_id
          AND sections.time_slot_id = time_slot.time_slot_id
          AND time_slot.day LIKE ?
          AND (? <= time_slot.end_time AND ? >= time_slot.start_time)";
          $stmt = $conn->prepare($query);
    
          if (!$stmt) {
              echo "Prepare failed: " . $conn->error;
              return false;
          }
      
          $like_day = "%" . substr($day, 0, 2) . "%";
          

      
          $stmt->bind_param(
              "ssisisss", 
              $student_id, 
              $semester, 
              $year, 
              $semester, 
              $year, 
              $like_day, 
              $tutoring_start_time, 
              $tutoring_end_time
          );
      
          $stmt->execute();
          $result = $stmt->get_result();
      
          return $result->num_rows > 0;
}

$is_there_an_overlaping_time_between_tutoring_and_classes = is_there_an_overlaping_time_between_tutoring_and_classes($conn,
get_tutor_details_by_tutor_name($conn,$input_tutor)['student_id'],$input_semester,(int)$input_year,$input_day,$input_start_time,$input_end_time);

//Get the sum of work hours for a tutor to check (Tutor can work for max of 22 hours)
//First get how many hours a tutot is working 
function get_total_working_hours($conn,$tutor_id, $semester, $year){
    $query = "SELECT 
    ROUND(SUM(TIME_TO_SEC(TIMEDIFF(end_hour.hour_label, start_hour.hour_label))) / 3600, 2) AS total_hours 
    FROM tutor_assignments
    JOIN hours AS start_hour ON tutor_assignments.start_hour_id = start_hour.hour_id
    JOIN hours AS end_hour ON tutor_assignments.end_hour_id = end_hour.hour_id
    WHERE tutor_id = ? AND semester = ? AND year = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $tutor_id, $semester, $year);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$current_hours = $row['total_hours'] ?? 0;
return $current_hours;
}

//Get sum of hours between two hours
function get_hours_between_times($start_time, $end_time) {
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->i / 60);
    $hours += ($interval->s / 3600);
    return round($hours, 2);
}
$total_working_hours = get_total_working_hours($conn,get_tutor_details_by_tutor_name($conn,$input_tutor)['tutor_id'],$input_semester,$input_year);
$hours_trying_to_add = get_hours_between_times($input_start_time,$input_end_time);
$can_add_hours = ($total_working_hours + $hours_trying_to_add) <= MAX_HOURS_PER_WEEK;

//Check if there is an overlap between two tutoring sessions for the same tutor
function is_there_an_overlaping_time_between_tutoring_sessions(
    $conn,
    $year,
    $semester,
    $day,
    $tutor_id,
    $tutoring_start_time,
    $tutoring_end_time,
    $assignment_id = null
) {
    $query = "SELECT * 
              FROM tutor_assignments
              JOIN hours AS start_hour ON tutor_assignments.start_hour_id = start_hour.hour_id
              JOIN hours AS end_hour ON tutor_assignments.end_hour_id = end_hour.hour_id
              JOIN days ON tutor_assignments.day_id = days.day_id
              WHERE tutor_assignments.year = ?
                AND tutor_assignments.semester = ?
                AND days.day_name = ?
                AND tutor_id = ? 
                AND (? <= end_hour.hour_label AND ? >= start_hour.hour_label)";
              
            if ($assignment_id !== null) {
                $query .= " AND assignment_id != ?";
            }
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    if ($assignment_id !== null) {
        $stmt->bind_param(
            "ississi", 
            $year,
            $semester,
            $day,
            $tutor_id,
            $tutoring_start_time,
            $tutoring_end_time,
            $assignment_id
        );
    } else {
        $stmt->bind_param(
            "ississ", 
            $year,
            $semester,
            $day,
            $tutor_id,
            $tutoring_start_time,
            $tutoring_end_time
        );
    }
    

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}
$assignment_id = $tutor_assignment_exists['assignment_id'] ?? null;
$is_there_an_overlaping_time_between_tutoring_sessions = is_there_an_overlaping_time_between_tutoring_sessions(
    $conn,
    (int)$input_year,
    $input_semester,
    $input_day,
    get_tutor_details_by_tutor_name($conn,$input_tutor)['tutor_id'],
    get_hour($conn,$input_start_time)['hour_label'],
    get_hour($conn,$input_end_time)['hour_label'],
    $assignment_id
);







//Assign hours to tutor
function insert_tutor_assignment($conn, $tutor_id, $course_id, $classroom_id, $year, $semester, $day_id, $start_hour_id, $end_hour_id) 
{
    $query = "INSERT INTO tutor_assignments (
                tutor_id, course_id, classroom_id, year, semester,
                day_id, start_hour_id, end_hour_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    $stmt->bind_param("isssssii", 
        $tutor_id, 
        $course_id, 
        $classroom_id, 
        $year, 
        $semester, 
        $day_id, 
        $start_hour_id, 
        $end_hour_id
    );

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Error: " . $stmt->error;
        return false;
    }
}




function update_tutor_assignment(
    $conn,
    $assignment_id,
    $tutor_id,
    $course_id,
    $classroom_id,
    $day_id,
    $start_hour_id,
    $end_hour_id,
    $semester,
    $year
) {
    $query = "UPDATE tutor_assignments
              SET tutor_id = ?, 
                  course_id = ?, 
                  classroom_id = ?, 
                  day_id = ?, 
                  start_hour_id = ?, 
                  end_hour_id = ?, 
                  semester = ?, 
                  year = ?
              WHERE assignment_id = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return false;
    }

    $stmt->bind_param(
        "issiiissi",
        $tutor_id,
        $course_id,
        $classroom_id,
        $day_id,
        $start_hour_id,
        $end_hour_id,
        $semester,
        $year,
        $assignment_id
    );

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Update failed: " . $stmt->error;
        return false;
    }
}

function can_save($conn,$is_tutor_active,$did_get_required_grade_for_course,$is_there_an_overlaping_time_between_tutoring_and_classes,$can_add_hours,$is_there_an_overlaping_time_between_tutoring_sessions,$tutor_assignment_exists=null){
    if(!$is_tutor_active){
        echo (".Tutor is not active.");
        return false;
    }
    if(!$did_get_required_grade_for_course){
        echo (".Tutor did not get A- or better for this course.");
        return false;
    }
    if($is_there_an_overlaping_time_between_tutoring_and_classes){
        echo (".There is an overlap between tutoring hours and classes schedule.");
        return false;
    }
    if(!$can_add_hours){
        echo (".Maximum Hours is 20 per week.");
        return false;
    }
    if($is_there_an_overlaping_time_between_tutoring_sessions){
        echo (".There is an overlap between tutoring sessions for this tutor.");
        return false;
    }
    

    return true;
}


if(!$tutor_assignment_exists){
    if(can_save($conn,$is_tutor_active,$did_get_required_grade_for_course,$is_there_an_overlaping_time_between_tutoring_and_classes,$can_add_hours,$is_there_an_overlaping_time_between_tutoring_sessions)){
        if(insert_tutor_assignment(
            $conn, 
            get_tutor_details_by_tutor_name($conn,$input_tutor)['tutor_id'],
            get_course_by_course_name($conn,$input_course)['course_id'],
            get_location_details_by_building_name_and_room_number($conn,$building_name,$room_number)['classroom_id'],
            (int)$input_year,
            $input_semester, 
            get_day_details_by_day_name($conn,$input_day)['day_id'], 
            get_hour($conn,$input_start_time)['hour_id'],
            get_hour($conn,$input_end_time)['hour_id']
        )){
            echo (".Hours inserted successfully.");
        } 
        else{
            echo (".Failed Insertion.");
        }
    }
    else{
        echo ".Cannot insert.";
    }
}
else{
    if(can_save($conn,$is_tutor_active,$did_get_required_grade_for_course,$is_there_an_overlaping_time_between_tutoring_and_classes,$can_add_hours,$is_there_an_overlaping_time_between_tutoring_sessions,$tutor_assignment_exists['assignment_id'])){
        if(update_tutor_assignment(
            $conn,
            $tutor_assignment_exists['assignment_id'],
            get_tutor_details_by_tutor_name($conn,$input_tutor)['tutor_id'],
            get_course_by_course_name($conn,$input_course)['course_id'],
            get_location_details_by_building_name_and_room_number($conn,$building_name,$room_number)['classroom_id'],
            get_day_details_by_day_name($conn,$input_day)['day_id'],
            get_hour($conn,$input_start_time)['hour_id'],
            get_hour($conn,$input_end_time)['hour_id'],
            $input_semester, 
            (int)$input_year
            )
            ){
            echo (".Hours Updated successfully.");
        } 
        else{
            echo (".Failed Updating.");
        }
    }
    else{
        echo ".Cannot Update.";
    }
}




//Close Connection
$conn->close();


?>