<?php

header('Content-Type: application/json');

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $user_id = $input['user_id'] ?? null;
    $order_items = $input['order_items'] ?? []; 
    $extra_wish = $input['extra_wish'] ?? '';
    $total_price = 0; 


    if (!$user_id || empty($order_items)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ungültige Eingabedaten. Benutzer-ID und Bestellpositionen sind erforderlich.'
        ]);
        exit;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, extra_wish) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $total_price, $extra_wish);
        $stmt->execute();

        $order_id = $stmt->insert_id;

        foreach ($order_items as $item) {
            $pizza_id = $item['pizza_id'] ?? null;
            $quantity = $item['quantity'] ?? 1;
            $extras = $item['extras'] ?? []; 
            $price_per_unit = 0;

            $stmt = $conn->prepare("SELECT price FROM pizza WHERE id = ?");
            $stmt->bind_param("i", $pizza_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $price_per_unit = $result->fetch_assoc()['price'];
            } else {
                throw new Exception("Pizza mit ID $pizza_id nicht gefunden.");
            }

            // Preis für die aktuelle Position berechnen
            $item_total_price = $price_per_unit * $quantity;
            $total_price += $item_total_price;

            // Bestellposition einfügen
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, pizza_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $pizza_id, $quantity, $price_per_unit);
            $stmt->execute();

            // ID der eingefügten Bestellposition
            $order_item_id = $stmt->insert_id;

            // Extras (Zutaten) einfügen
            foreach ($extras as $extra) {
                $ingredient_id = $extra['ingredient_id'] ?? null;
                $extra_quantity = $extra['quantity'] ?? 1;
                $ingredient_price = 0;

                // Preis der Zutat abrufen
                $stmt = $conn->prepare("SELECT price FROM ingredients WHERE id = ?");
                $stmt->bind_param("i", $ingredient_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $ingredient_price = $result->fetch_assoc()['price'];
                } else {
                    throw new Exception("Zutat mit ID $ingredient_id nicht gefunden.");
                }

                // Preis für das Extra berechnen
                $extra_total_price = $ingredient_price * $extra_quantity;
                $total_price += $extra_total_price;

                // Extra in die order_item_extras-Tabelle einfügen
                $stmt = $conn->prepare("INSERT INTO order_item_extras (order_item_id, ingredient_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_item_id, $ingredient_id, $extra_quantity, $ingredient_price);
                $stmt->execute();
            }
        }

        // Gesamtsumme in der Bestellung aktualisieren
        $stmt = $conn->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
        $stmt->bind_param("di", $total_price, $order_id);
        $stmt->execute();

        // Transaktion abschließen
        $conn->commit();

        // Erfolgsmeldung zurückgeben
        echo json_encode([
            'status' => 'success',
            'message' => 'Bestellung erfolgreich aufgegeben.',
            'order_id' => $order_id,
            'total_price' => $total_price
        ]);
    } catch (Exception $e) {
        $conn->rollback();

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ungültige Anfrage. Nur POST-Anfragen sind erlaubt.'
    ]);
}
?>
