<?php
// naive JSON Schema checker for our limited schemas (draft-07 subset)
// - checks required keys, disallows additionalProperties, checks types (string, integer, array, null)
// - returns array: [bool $ok, array $errors]

function load_schema($file) {
    $path = __DIR__ . "/../schemas/" . $file;
    if (!file_exists($path)) return null;
    return json_decode(file_get_contents($path), true);
}

function check_type($value, $expected) {
    if (is_array($expected)) {
        foreach ($expected as $t) if (check_type($value, $t)) return true;
        return false;
    }
    if ($expected === "string") return is_string($value);
    if ($expected === "integer") return is_int($value) || (is_string($value) && ctype_digit($value));
    if ($expected === "array") return is_array($value);
    if ($expected === "null") return is_null($value);
    if ($expected === "number") return is_int($value) || is_float($value) || (is_string($value) && is_numeric($value));
    if ($expected === "boolean") return is_bool($value);
    return false;
}

function validate_against_schema(array $data, array $schema) {
    $errors = [];

    // additionalProperties
    $additionalAllowed = $schema['additionalProperties'] ?? true;
    if ($additionalAllowed === false && isset($schema['properties'])) {
        foreach ($data as $k => $v) {
            if (!array_key_exists($k, $schema['properties'])) {
                $errors[] = "Unexpected property: $k";
            }
        }
    }

    // required
    $required = $schema['required'] ?? [];
    foreach ($required as $req) {
        if (!array_key_exists($req, $data)) {
            $errors[] = "Missing required property: $req";
        }
    }

    // types
    if (isset($schema['properties'])) {
        foreach ($schema['properties'] as $key => $prop) {
            if (!array_key_exists($key, $data)) continue;
            $expected = $prop['type'] ?? null;
            $value = $data[$key];

            // handle union types like ["string","null"]
            if ($expected !== null) {
                if (!check_type($value, $expected)) {
                    $errors[] = "Property '$key' must be of type " . (is_array($expected) ? implode("|", $expected) : $expected);
                }
            }
            // minLength basic check
            if (isset($prop['minLength']) && is_string($value)) {
                if (strlen($value) < $prop['minLength']) $errors[] = "Property '$key' minLength {$prop['minLength']}";
            }
        }
    }

    return [count($errors) === 0, $errors];
}
