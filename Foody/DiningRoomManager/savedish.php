<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=foody", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['description']) || 
        !isset($data['price']) || !isset($data['category'])) {
        throw new Exception('Faltan campos requeridos');
    }

    $code = 'D' . substr(uniqid(), -4);

    $sql = "INSERT INTO dish (code, name, description, price, discountPercentage, category, menu) 
            VALUES (:code, :name, :description, :price, :discountPercentage, :category, :menu)";
    
    $stmt = $pdo->prepare($sql);
    
    $success = $stmt->execute([
        ':code' => $code,
        ':name' => $data['name'],
        ':description' => $data['description'],
        ':price' => $data['price'],
        ':discountPercentage' => 0, 
        ':category' => $data['category'],
        ':menu' => 'MEN01' 
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Plato creado exitosamente']);
    } else {
        throw new Exception('Error al crear el plato');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>