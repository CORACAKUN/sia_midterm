<?php
include __DIR__ . "/../../config/db_connection.php";
include __DIR__ . "/../utils/response.php";
include __DIR__ . "/../utils/json_validator.php";

    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);

    if (!$input) {
        send_response(["status"=>"error","message"=>"Invalid JSON"]);
        exit;
    }

    //Validate body structure
    $schema = load_schema("student_create_request.json");
    list($ok, $errs) = validate_against_schema($input, $schema);
    if (!$ok) {
        send_response(["status"=>"error","message"=>"Schema validation failed","errors"=>$errs]);
        exit;
    }

    //Prevent duplicate student_id
    $check1 = $conn->prepare("SELECT id FROM student WHERE student_id = ?");
    $check1->bind_param("s", $input["student_id"]);
    $check1->execute();
    if ($check1->get_result()->num_rows > 0) {
        send_response(["status"=>"error","message"=>"Student ID already exists"]);
        exit;
    }

    //Prevent duplicate RFID
    if (!empty($input["rfid_uid"])) {
        $check2 = $conn->prepare("SELECT id FROM student WHERE rfid_uid = ?");
        $check2->bind_param("s", $input["rfid_uid"]);
        $check2->execute();
        if ($check2->get_result()->num_rows > 0) {
            send_response(["status"=>"error","message"=>"RFID UID already exists"]);
            exit;
        }
    }

    //Insert new student
    $stmt = $conn->prepare("
        INSERT INTO student (student_id, full_name, course, year_level, rfid_uid)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $input["student_id"],
        $input["full_name"],
        $input["course"],
        $input["year_level"],
        $input["rfid_uid"]
    );

    if (!$stmt->execute()) {
        send_response(["status"=>"error","message"=>$conn->error]);
        exit;
    }

    //Success response
    $inserted_id = $conn->insert_id;

    $resData = [
        "status" => "success",
        "data" => [
            "id" => (int)$inserted_id,
            "student_id" => $input["student_id"],
            "full_name" => $input["full_name"],
            "course" => $input["course"],
            "year_level" => $input["year_level"],
            "rfid_uid" => $input["rfid_uid"],
            "created_at" => date("Y-m-d H:i:s")
        ]
    ];

    send_response($resData);
?>
