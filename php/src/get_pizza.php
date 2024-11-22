<?php 

header('Content-Type: application/json');

require 'db_connection.php';

$sql = "SELECT id , name, description , price, image_url FROM pizza";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pizzas = [];

    while($row = $result->fetch_assoc()) {
        $pizzas[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'image_url' => $row['image_url']
        ];
    }

    $data = [
        'status' => 'success',
        'data' => $pizzas
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