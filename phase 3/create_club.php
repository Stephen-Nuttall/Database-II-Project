<?php




header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$response = [
    "success" => false,
    "message" => "Unknown error occurred."
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instructor_id = $_POST['instructor_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $club_name = $_POST['club_name'] ?? '';
    $president_id = $_POST['president_id'] ?? '';

    $conn = mysqli_connect('localhost', 'root', '', 'db2');
    if (!$conn) {
        $response['message'] = "Database connection failed.";
        echo json_encode($response);
        exit;
    }

    $password_query = "
        SELECT password
        FROM account
        WHERE email IN (
            SELECT email FROM instructor WHERE instructor_id = '$instructor_id'
        )";

    $result = mysqli_query($conn, $password_query);
    $row = mysqli_fetch_assoc($result);
    $correct_password = $row['password'] ?? null;

    if ($password !== $correct_password) {
        $response['message'] = "Incorrect instructor ID or password.";
        echo json_encode($response);
        exit;
    }

    // Check for duplicates and get new club_id
    $club_id = 0;
    $club_check_query = "SELECT * FROM club";
    $club_check_result = mysqli_query($conn, $club_check_query);

    while ($row = mysqli_fetch_assoc($club_check_result)) {
        if ($row["advisor_id"] == $instructor_id) {
            $response['message'] = "Instructor is already an advisor of another club.";
            echo json_encode($response);
            exit;
        }
        if ($row["president_id"] == $president_id) {
            $response['message'] = "President is already leading another club.";
            echo json_encode($response);
            exit;
        }
        if ($row["name"] == $club_name) {
            $response['message'] = "Club name already exists.";
            echo json_encode($response);
            exit;
        }
        $club_id++;
    }

    // Verify president exists
    $president_check = mysqli_query($conn, "SELECT * FROM student WHERE student_id = '$president_id'");
    if (mysqli_num_rows($president_check) === 0) {
        $response['message'] = "President student ID not found.";
        echo json_encode($response);
        exit;
    }

    // Insert new club
    $insert_club = "
        INSERT INTO club (name, club_id, advisor_id, president_id)
        VALUES ('$club_name', '$club_id', '$instructor_id', '$president_id')";

    $insert_president = "
        INSERT INTO clubparticipants (student_id, club_id)
        VALUES ('$president_id', '$club_id')";

    if (mysqli_query($conn, $insert_club) && mysqli_query($conn, $insert_president)) {
        $response['success'] = true;
        $response['message'] = "$club_name successfully created. President added as member.";
    } else {
        $response['message'] = "Failed to insert club or participant.";
    }

    mysqli_close($conn);
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
