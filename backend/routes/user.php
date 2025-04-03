<?php
// Get all users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/users') {
    $sql = "SELECT * FROM users";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();
    echo json_encode($users);
}

// Get user by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/\/users\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
  $userId = $matches[1];
  
  // Find user by ID
  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userId]);
  $user = $stmt->fetch();
  
  if (!$user) {
      echo json_encode(["message" => "User not found"]);
      http_response_code(404);
  } else {
      echo json_encode($user);
  }
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && preg_match('/\/users\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
  $userId = $matches[1];
  $data = json_decode(file_get_contents('php://input'), true);
  $username = $data['username'] ?? null;
  $email = $data['email'] ?? null;
  $password = $data['password'] ?? null;

  // Find user by ID
  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userId]);
  $user = $stmt->fetch();

  if (!$user) {
      echo json_encode(["message" => "User not found"]);
      http_response_code(404);
      exit();
  }

  // Update user data
  if ($username) {
      $user['username'] = $username;
  }
  if ($email) {
      $user['email'] = $email;
  }
  if ($password) {
      $user['password'] = User::hashPassword($password); // Hash new password
  }

  // Update the user in the database
  $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$user['username'], $user['email'], $user['password'], $userId]);

  echo json_encode($user);
  http_response_code(200);
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('/\/users\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
  $userId = $matches[1];

  // Find user by ID
  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userId]);
  $user = $stmt->fetch();

  if (!$user) {
      echo json_encode(["message" => "User not found"]);
      http_response_code(404);
      exit();
  }

  // Delete the user from the database
  $sql = "DELETE FROM users WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userId]);

  echo json_encode(["message" => "User deleted successfully"]);
  http_response_code(204);
}