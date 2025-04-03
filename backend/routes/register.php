<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/register') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];

    // Check if username or email exists
    $existingUser = User::findByUsername($pdo, $username);
    if ($existingUser) {
        echo json_encode(["message" => "username already exists"]);
        http_response_code(400);
        exit();
    }

    // Create the new user
    User::createUser($pdo, $username, $email, $password);
    echo json_encode(["message" => "User created successfully"]);
    http_response_code(201);
}
?>
