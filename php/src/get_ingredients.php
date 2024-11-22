<?php 

header('Content-Type: application/json');

require 'db_connection.php';

$sql = "SELECT id , name, price  FROM pizza";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ingredients = [];

    while($row = $result->fetch_assoc()) {
        $ingredients[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
        ];
    }

    $data = [
        'status' => 'success',
        'data' => $ingredients
    ];

echo json_encode($data);

} else {
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Keine Pizzen gefunden'
    ]);
}

$conn->close();
?>