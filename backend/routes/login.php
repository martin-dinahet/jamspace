<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/login') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'];
    $password = $data['password'];

    // Find user by username
    $user = User::findByUsername($pdo, $username);
    if (!$user) {
        echo json_encode(["message" => "Invalid username"]);
        http_response_code(401);
        exit();
    }

    // Validate the password
    if (!User::validatePassword($password, $user['password'])) {
        echo json_encode(["message" => "Invalid password"]);
        http_response_code(401);
        exit();
    }

    // Generate JWT token
    $jwt = generateJwt($user['id'], $user['username']);
    echo json_encode(["token" => $jwt]);
    http_response_code(200);
}

function generateJwt($id, $username) {
    $secretKey = "secret";
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour from the issued time
    $payload = [
        'id' => $id,
        'username' => $username,
        'iat' => $issuedAt,
        'exp' => $expirationTime,
    ];

    return JWT::encode($payload, $secretKey);
}
?>
