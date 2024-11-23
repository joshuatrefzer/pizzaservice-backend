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

if (!$username || !$password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Username and password are required.'
    ]);
    exit;
}

try {
    
    $stmt = $conn->prepare("SELECT id, password, firstname, lastname, street, street_nr, postal_code FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        
        if (password_verify($password, $user['password'])) {
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful.',
                'user' => [
                    
                    'username' => $username,
                    'id' => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'street' => $user['street'],
                    'street_nr' => $user['street_nr'],
                    'postal_code' => $user['postal_code']
                ]
            ]);
        } else {

            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ]);
        }
    } else {
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password.'
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
