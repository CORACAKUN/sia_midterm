<?php
include "../../config/db_connection.php";
include "../utils/response.php";
include "../utils/json_validator.php";

// 1. Validate ID param
if (!isset($_GET["id"])) {
    send_response(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET["id"]);

// 2. Fetch ONE student
$stmt = $conn->prepare("SELECT * FROM student WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    send_response(["status" => "error", "message" => "Student not found"]);
    exit;
}

$student = $res->fetch_assoc();

// 3. Validate response with schema
$schema = load_schema("student_response.json");
list($ok, $errs) = validate_against_schema($student, $schema);

if (!$ok) {
    $student["validation_errors"] = $errs; // optional for grading
}

// 4. Send response
send_response([
    "status" => "success",
    "data" => $student
]);
?>
