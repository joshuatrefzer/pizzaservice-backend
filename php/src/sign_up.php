<?php
require 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Use POST.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = isset($data['username']) ? trim($data['username']) : null;
$password = isset($data['password']) ? trim($data['password']) : null;
$firstname = isset($data['firstname']) ? trim($data['firstname']) : null;
$lastname = isset($data['lastname']) ? trim($data['lastname']) : null;
$street = isset($data['street']) ? trim($data['street']) : null;
$street_nr = isset($data['street_nr']) ? trim($data['street_nr']) : null;
$postal_code = isset($data['postal_code']) ? trim($data['postal_code']) : null;

if (!$username || !$password || !$firstname || !$lastname || !$street || !$street_nr || !$postal_code) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required.'
    ]);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username already exists.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, firstname, lastname, street, street_nr, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $hashed_password, $firstname, $lastname, $street, $street_nr, $postal_code);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User registered successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error occurred while registering the user.'
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

$conn->close();
