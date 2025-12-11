<?php
include "../../config/db_connection.php";
include "../utils/response.php";

if (!isset($_GET["id"])) {
    send_response(["status" => "error", "message" => "Missing ID"]);
    exit;
}

$id = intval($_GET["id"]);

// Prepared delete statement
$stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Check if student existed and was deleted
if ($stmt->affected_rows === 1) {
    send_response(["status" => "success", "message" => "Student deleted"]);
} else {
    // No rows deleted → student does NOT exist
    send_response([
        "status" => "error",
        "message" => "Student not found or already deleted"
    ]);
}
?>
