<?php
include __DIR__ . "/../../config/db_connection.php";
include __DIR__ . "/../utils/response.php";
include __DIR__ . "/../utils/json_validator.php";

if (!isset($_GET["id"])) {
    send_response(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET["id"]);

// Get JSON body
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    send_response(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

// ---------------------------
// Validate input schema
// ---------------------------
$schema = load_schema("student_update_request.json");
list($ok, $errs) = validate_against_schema($input, $schema);

if (!$ok) {
    send_response([
        "status" => "error",
        "message" => "Schema validation failed",
        "errors" => $errs
    ]);
    exit;
}

// ---------------------------
// Prevent duplicate student_id
// ---------------------------
$check1 = $conn->prepare("
    SELECT id FROM student 
    WHERE student_id = ? AND id != ?
");
$check1->bind_param("si", $input["student_id"], $id);
$check1->execute();
$res1 = $check1->get_result();

if ($res1->num_rows > 0) {
    send_response([
        "status" => "error",
        "message" => "Student ID already exists"
    ]);
    exit;
}

// ---------------------------
// Prevent duplicate RFID UID
// ---------------------------
if (!empty($input["rfid_uid"])) {
    $check2 = $conn->prepare("
        SELECT id FROM student 
        WHERE rfid_uid = ? AND id != ?
    ");
    $check2->bind_param("si", $input["rfid_uid"], $id);
    $check2->execute();
    $res2 = $check2->get_result();

    if ($res2->num_rows > 0) {
        send_response([
            "status" => "error",
            "message" => "RFID UID already exists"
        ]);
        exit;
    }
}

// ---------------------------
// Perform update
// ---------------------------
$stmt = $conn->prepare("
    UPDATE student
    SET student_id=?, full_name=?, course=?, year_level=?, rfid_uid=?
    WHERE id=?
");

$stmt->bind_param(
    "sssssi",
    $input["student_id"],
    $input["full_name"],
    $input["course"],
    $input["year_level"],
    $input["rfid_uid"],
    $id
);

if ($stmt->execute()) {
    send_response(["status" => "success", "message" => "Student updated"]);
} else {
    send_response(["status" => "error", "message" => $conn->error]);
}
?>
