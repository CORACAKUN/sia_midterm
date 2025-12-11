<?php
include __DIR__ . "/xml.php";

function send_response($data) {
    $accept = $_SERVER["HTTP_ACCEPT"] ?? "application/json";

    // XML response
    if (strpos($accept, "xml") !== false) {
        header("Content-Type: application/xml");

        $xml = new SimpleXMLElement("<response/>");
        array_to_xml($data, $xml);

        echo $xml->asXML();
        return;
    }

    // JSON response
    header("Content-Type: application/json");
    echo json_encode($data);
}
?>
