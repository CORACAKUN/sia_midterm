<?php
include "../../config/db_connection.php";
include "../utils/response.php";
include "../utils/json_validator.php";

// Step 1: Fetch students from DB
$result = $conn->query("SELECT * FROM student ORDER BY id DESC");

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Step 2: Validate each student object against schema
$singleSchema = load_schema("student_response.json");

foreach ($students as &$s) {
    list($ok, $errs) = validate_against_schema($s, $singleSchema);
    if (!$ok) {
        $s["validation_errors"] = $errs; // optional
    }
}

// Step 3: Build final response
$response = [
    "status" => "success",
    "data" => $students
];

// Step 4: Send response once
send_response($response);
?>
