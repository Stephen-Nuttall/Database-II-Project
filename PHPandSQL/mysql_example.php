
<?php
$user = "movie";
$bool = false;
$mysqli = new mysqli('localhost', 'root', '', 'movie');
$year = $_POST['year'];

// get the target ID entered
$qGetId = "SELECT id FROM movies WHERE year = '$year'";
$id = $mysqli->query($qGetId);
$targetID = mysqli_fetch_array($id);

// get an array of all movie ID's
$mids = [];
$qmovieIDs = "SELECT id from movies";
$res = $mysqli->query($qmovieIDs);
while($row = mysqli_fetch_assoc($res)){
    foreach($row as $cname => $cvalue){
        array_push($mids,$cvalue);
    }
}

// grab movie info from movies table
$qGetInfo = "SELECT * FROM movies WHERE year = '$year'";
$result = $mysqli->query($qGetInfo);
$testrow = mysqli_fetch_array($result);

// check if target ID is in array of movie ID's
if(empty($targetID)){
    $response["success"] = "false";
    echo json_encode($response);
} else if (!in_array($targetID['id'], $mids)){
    $response["success"] = "false";
    echo json_encode($response);
}else{
    $response["title"] = $testrow['title'];
    $response["year"] = $testrow['year'];
    $response["length"] = $testrow['length'];
    $response["genre"] = $testrow['genre'];

    $response["success"] = "true";
    echo json_encode($response);
}
?>
