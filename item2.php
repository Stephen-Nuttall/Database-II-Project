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


//Get all courses name
$query_get_all_courses_name = "SELECT course_name FROM course ORDER BY course_name";
$result_get_all_courses_name=$conn->query($query_get_all_courses_name);

//Load the courses into the courses drop down menu 
$course_names = "";

if ($result_get_all_courses_name->num_rows > 0) {
    while ($row = $result_get_all_courses_name->fetch_assoc()) {
        $course_names .= '<option value="' . htmlspecialchars($row["course_name"]) . '">' . htmlspecialchars($row["course_name"]) . '</option>';
    }
} else {
    $course_names = '<option value="">No courses available</option>';
}

//Get all the instructor names
$query_get_all_instructor_names = "SELECT instructor_name FROM instructor ORDER BY instructor_name";
$result_get_all_instructor_names=$conn->query($query_get_all_instructor_names);
//Load the instructors into the instructors drop down menu 
$instructor_names="";
$instructor_names .= '<option value="">-- Select An Instructor--</option>';
if ($result_get_all_instructor_names->num_rows > 0) {
    while ($row = $result_get_all_instructor_names->fetch_assoc()) {
        $instructor_names .= '<option value="' . htmlspecialchars($row["instructor_name"]) . '">' . htmlspecialchars($row["instructor_name"]) . '</option>';
    }
} else {
    $course_names = '<option value="">No Instructors available</option>';
}

//Get all the building names
$query_get_all_building_names = "SELECT DISTINCT(building) FROM classroom ORDER BY building";
$result_get_all_building_names=$conn->query($query_get_all_building_names);

//Load the buildings names into the buildings drop down menu 
$buildings_names="";
$buildings_names .= '<option value="">-- Select A Building--</option>';
if ($result_get_all_building_names->num_rows > 0) {
    while ($row = $result_get_all_building_names->fetch_assoc()) {
        $buildings_names .= '<option value="' . htmlspecialchars($row["building"]) . '">' . htmlspecialchars($row["building"]) . '</option>';
    }
} else {
    $buildings_names = '<option value="">No Buildings available</option>';
}

// Get all classrooms 
$query_get_all_classrooms = "SELECT building, room_number FROM classroom ORDER BY building, CAST(room_number AS UNSIGNED) ASC";
$result_query_get_all_classrooms = $conn->query($query_get_all_classrooms);

//Load the classrooms into the buildings drop down menu 
$classrooms = '<option value="">-- Select a Classroom --</option>';
if ($result_query_get_all_classrooms->num_rows > 0) {
    while ($row = $result_query_get_all_classrooms->fetch_assoc()) {
        $combined = htmlspecialchars($row["building"]) . '-' . htmlspecialchars($row["room_number"]);
        $classrooms .= '<option value="' . $combined . '">' . $combined . '</option>';
    }
} else {
    $classrooms = '<option value="">No Classroom available</option>';
}

//Get all the timeslots
$query_get_all_timeslots = "SELECT day,start_time,end_time FROM time_slot ORDER BY day,start_time";
$result_query_get_all_timeslots = $conn->query($query_get_all_timeslots);

$timeslots = '<option value="">-- Select a Timeslot--</option>';

if ($result_query_get_all_timeslots->num_rows > 0) {
    while ($row = $result_query_get_all_timeslots->fetch_assoc()) {
        $day = htmlspecialchars($row["day"]);
        
        $start_time_full = date('H:i:s', strtotime($row["start_time"]));
        $end_time_full = date('H:i:s', strtotime($row["end_time"]));

        $start_time_display = date('H:i', strtotime($row["start_time"]));
        $end_time_display = date('H:i', strtotime($row["end_time"]));

        $option_value = "$day $start_time_full-$end_time_full";
        $option_display = "$day $start_time_display - $end_time_display";

        $timeslots .= '<option value="' . $option_value . '">' . $option_display . '</option>';
    }
} else {
    $timeslots = '<option value="">No Timeslot available</option>';
}



if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    return; 
}


//Get User inputs
$course = $_POST["course"];
$section = $_POST["section"];
$semester = $_POST["semester"];
$year = (int) $_POST["year"];
$instructor = $_POST["instructor"];
$classroom = $_POST["classroom"];
$timeslot = $_POST["timeslot"];

//Get Primary Keys 
//Course ID
$course_id="";
$query_get_course_id = "SELECT course_id FROM course WHERE course_name = ? ";
$stmt = $conn->prepare($query_get_course_id);
$stmt->bind_param("s", $course);
$stmt->execute();
$stmt->store_result();  

if ($stmt->num_rows > 0) {
    $stmt->bind_result($course_id);
    $stmt->fetch();
} else {
}
$stmt->close();

//Section ID
$section_id = "Section" . $section;

//Instructor ID 
$instructor_id="";
if(!empty($instructor)){
$query_get_instructor_id = "SELECT instructor_id FROM instructor WHERE instructor_name= ? ";
$stmt = $conn->prepare($query_get_instructor_id);
$stmt->bind_param("s", $instructor);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($instructor_id);
    $stmt->fetch();
} else {
}
$stmt->close();  
}

//Classroom ID 
$classroom_id="";
if (!empty($classroom)){
$parts = explode("-", $classroom);
$building = $parts[0];
$room_number = $parts[1];
$query_get_classroom_id = "SELECT classroom_id FROM classroom WHERE building=? AND room_number=?";
$stmt = $conn->prepare($query_get_classroom_id);
$stmt->bind_param("ss", $building, $room_number);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($classroom_id);
    $stmt->fetch();
} else {
}
$stmt->close();
} 

//TimeSlot ID
$time_slot_id = "";
if(!empty($timeslot)){
$time_parts = explode(" ", $timeslot, 2);
$day = trim($time_parts[0]);

$times = explode("-", $time_parts[1]);
$start_time = trim($times[0]);
$end_time = trim($times[1]);


$query_get_timeslot_id = "SELECT time_slot_id FROM time_slot WHERE day = ? AND start_time = ? AND end_time = ?";
$stmt = $conn->prepare($query_get_timeslot_id);
$stmt->bind_param("sss", $day, $start_time, $end_time);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($time_slot_id);
    $stmt->fetch();
} else {
}
$stmt->close();
}

// 2. The admin will be able to create a new course section and appoint instructor to teach the
// section. Every course section is scheduled to meet at a specific time slot, with a limit of
// two sections per time slot. Each instructor teaches one or two sections per semester.
// Should an instructor be assigned two sections, the two sections must be scheduled in
// consecutive time slots.


$query_check_prof_time_slot = "
    SELECT COUNT(*) 
    FROM section 
    WHERE semester = ? AND year = ? AND time_slot_id = ? AND instructor_id = ?
";
$stmt = $conn->prepare($query_check_prof_time_slot);
$stmt->bind_param("sssi", $semester, $year, $time_slot_id, $instructor_id);
$stmt->execute();
$stmt->bind_result($prof_time_slot_count);
$stmt->fetch();
$stmt->close();

if ($prof_time_slot_count > 0) {
    echo "Instructor is already teaching during this time slot.";
    return;
}

$query_check_classroom_conflict = "
    SELECT COUNT(*) 
    FROM section 
    WHERE semester = ? AND year = ? AND time_slot_id = ? AND classroom_id = ?
";
$stmt = $conn->prepare($query_check_classroom_conflict);
$stmt->bind_param("ssss", $semester, $year, $time_slot_id, $classroom_id);
$stmt->execute();
$stmt->bind_result($classroom_count);
$stmt->fetch();
$stmt->close();

if ($classroom_count > 0) {
    echo "Classroom is already assigned during this time slot.";
    return;
}




//Check if there is a maximum of two sections per time slot
$query_get_section_count_per_time_slot="SELECT COUNT(section_id)
                                        FROM section 
                                        WHERE course_id = ? AND semester = ? AND year = ? AND time_slot_id = ?";
$stmt = $conn->prepare($query_get_section_count_per_time_slot);
$stmt->bind_param("ssss", $course_id,$semester,$year,$time_slot_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($count_per_timeslot);
$stmt->fetch();
if ($count_per_timeslot > 1) {
    echo "Maximum of two sections already assigned to this time slot.";
    return;
} 



//Check if there is a maximum of two sections assigned to each professor
$query_get_section_count_per_professor="SELECT COUNT(section_id)
                                        FROM section
                                        WHERE course_id = ? AND semester = ? AND year = ? AND instructor_id = ?";
$stmt = $conn->prepare($query_get_section_count_per_professor);
$stmt->bind_param("ssss", $course_id,$semester,$year,$instructor_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($section_count_per_professor);
$stmt->fetch();
if ($section_count_per_professor > 1) {
    echo "Maximum of two sections already assigned to this professor.";
    return;
} 


function insert_section($conn, $course_id, $section_id, $semester, $year, $instructor_id, $classroom_id, $time_slot_id) {
    $query = "INSERT INTO section (course_id, section_id, semester, year, instructor_id, classroom_id, time_slot_id)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        return false;
    }

    // Set NULL if values are empty
    $instructor_id = !empty($instructor_id) ? $instructor_id : null;
    $classroom_id  = !empty($classroom_id) ? $classroom_id : null;
    $time_slot_id  = !empty($time_slot_id) ? $time_slot_id : null;

    $stmt->bind_param("sssssss", $course_id, $section_id, $semester, $year, $instructor_id, $classroom_id, $time_slot_id);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}


function update_section($conn, $course_id, $section_id, $semester, $year, $instructor_id, $classroom_id, $time_slot_id) {
    $query = "UPDATE section 
              SET instructor_id = ?, classroom_id = ?, time_slot_id = ?
              WHERE course_id = ? AND section_id = ? AND semester = ? AND year = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        return false;
    }

    $instructor_id = !empty($instructor_id) ? $instructor_id : null;
    $classroom_id  = !empty($classroom_id) ? $classroom_id : null;
    $time_slot_id  = !empty($time_slot_id) ? $time_slot_id : null;

    $stmt->bind_param("sssssss", 
        $instructor_id, $classroom_id, $time_slot_id, 
        $course_id, $section_id, $semester, $year
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        return true;
    } else {
        echo "No changes made.";
        $stmt->close();
        return false;
    }
}


function find_section($conn, $course_id, $section_id, $semester, $year) {

    $query = "SELECT * FROM section 
              WHERE course_id = ? AND section_id = ? AND semester = ? AND year = ?
              LIMIT 1";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return null;
    }

    $stmt->bind_param("ssss", $course_id, $section_id, $semester, $year);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $section = $result->fetch_assoc();
        $stmt->close();
        return $section;
    } else {
        $stmt->close();
        return null;
    }
}

function get_section_info($instructor_id, $time_slot_id, $semester, $year){
    return [
        'instructor_id' => $instructor_id,
        'time_slot_id' => $time_slot_id,
        'semester' => $semester,
        'year' => $year
    ];
}


function get_time_slot_info($conn, $time_slot_id) {
    $query = "SELECT day,start_time,end_time FROM time_slot WHERE time_slot_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $time_slot_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function get_professor_time_slots_ids($conn, $instructor_id, $semester, $year) {
    $query = "
        SELECT time_slot_id
        FROM section
        WHERE semester = ? AND year = ? AND instructor_id = ? AND time_slot_id IS NOT NULL
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("sss", $semester, $year, $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $time_slot_ids = [];
    while ($row = $result->fetch_assoc()) {
        $time_slot_ids[] = $row['time_slot_id'];
    }

    $stmt->close();
    return $time_slot_ids;
}


function is_consecutive($conn, $new_time_slot_id, $prof_time_slot_id) {
    if (empty($new_time_slot_id) || empty($prof_time_slot_id)) {
        return true;
    }

    $new_slot = get_time_slot_info($conn, $new_time_slot_id);
    $prof_slot = get_time_slot_info($conn, $prof_time_slot_id);

    if (!$new_slot || !$prof_slot) {
        return false;
    }

    

    if ($new_slot["day"] !== $prof_slot["day"]) {
        return false;
    }

    $day = $new_slot["day"];
    $new_start = $new_slot["start_time"];
    $prof_start = $prof_slot["start_time"];

    

    $start_range = min($new_start, $prof_start);
    $end_range = max($new_start, $prof_start);

    $query = "
        SELECT COUNT(*) as cnt
        FROM time_slot
        WHERE start_time > ? AND start_time < ? AND day = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $start_range, $end_range, $day);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row["cnt"] == 0; 
}


function is_consecutive_with_at_least_one_professor_section($conn, $instructor_id, $semester, $year, $new_time_slot_id){
    $existing_time_slot_ids = get_professor_time_slots_ids($conn, $instructor_id, $semester, $year);
    if(empty($existing_time_slot_ids)){
        return true;
    }
    
    foreach ($existing_time_slot_ids as $existing_time_slot_id) {
        if (is_consecutive($conn, $new_time_slot_id, $existing_time_slot_id)) {
            return true; 
    }
}
    echo "The sections are not consecutive.";
    return false; 
}

function can_insert_new_section($conn, $instructor_id, $semester, $year, $new_time_slot_id, $section_count_per_professor,$count_per_timeslot) {
    if ($section_count_per_professor == 0) {
        return true;
    } elseif ($section_count_per_professor == 1) {
        if(!is_consecutive_with_at_least_one_professor_section($conn, $instructor_id, $semester, $year, $new_time_slot_id)){
            return false;
        }
        
        return true;
    } else {
        echo "Maximum of two sections already assigned to this professor.";
        return false;
    }
}

function can_update($conn, $instructor_id, $semester, $year, $new_time_slot_id, $section_count_per_professor, $count_per_timeslot) {
    
    if ($section_count_per_professor == 2) {
        return is_consecutive_with_at_least_one_professor_section($conn, $instructor_id, $semester, $year, $new_time_slot_id);
    }
    else{
            return true;
    }
    
}



$existing_section = find_section($conn, $course_id, $section_id, $semester, $year);
//$this_section = get_this_section($instructor_id, $time_slot_id, $semester, $year);
if($existing_section===null){
        if(can_insert_new_section($conn, $instructor_id, $semester, $year, $time_slot_id, $section_count_per_professor,$count_per_timeslot)){
            insert_section($conn, $course_id, $section_id, $semester, $year, $instructor_id, $classroom_id, $time_slot_id);
            echo "Section Inserted Successfully.";
        }
        else{
            echo "Cannot Insert.";
            return;
        }
}
else{
    if(can_update($conn, $instructor_id, $semester, $year, $time_slot_id, $section_count_per_professor, $count_per_timeslot)){
        update_section($conn, $course_id, $section_id, $semester, $year, $instructor_id, $classroom_id, $time_slot_id);
        echo "Section Updated Successfully.";
    }
    else{
        echo "Cannot Update.";
    }
}





?>
