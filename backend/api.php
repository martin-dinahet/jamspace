<?php

require_once "db.php";
require_once "vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$path = array_values(array_filter(explode("/", trim($_SERVER["REQUEST_URI"], "/"))));
$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);

$key = "super_secret_key";

function generate_jwt($user_id)
{
    global $key;
    $payload = ["sub" => $user_id, "iat" => time(), "exp" => time() + (60 * 60 * 24)];
    return JWT::encode($payload, $key, "HS256");
}

function verify_jwt($token)
{
    global $key;
    try {
        return JWT::decode($token, new Key($key, "HS256"));
    } catch (Exception $e) {
        return null;
    }
}

function get_bearer_token()
{
    $headers = getallheaders();
    return isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;
}

function authenticate()
{
    $token = get_bearer_token();
    if (!$token) respond(["error" => "Unauthorized"], 401);
    $decoded = verify_jwt($token);
    if (!$decoded) respond(["error" => "Invalid token"], 401);
    return $decoded->sub;
}

function respond($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

/////////////////////

// AUTHENTICATION ENDPOINTS
if ($path[0] === "auth") {
  // Login endpoint
  if ($path[1] === "login" && $method === "POST") {
      if (!$input || !isset($input["email"], $input["password"])) {
          respond(["error" => "Missing email or password"], 400);
      }
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$input["email"]]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user && password_verify($input["password"], $user["password"])) {
          $token = generate_jwt($user["id"]);
          respond(["token" => $token]);
      } else {
          respond(["error" => "Invalid credentials"], 401);
      }
  }

  // Register endpoint
  if ($path[1] === "register" && $method === "POST") {
      if (!$input || !isset($input["username"], $input["email"], $input["password"])) {
          respond(["error" => "Missing username, email, or password"], 400);
      }
      $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->execute([$input["email"]]);
      if ($stmt->fetch()) {
          respond(["error" => "Email already in use"], 400);
      }
      $hashed_password = password_hash($input["password"], PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, email, password, posts) VALUES (?, ?, ?, ?)");
      $stmt->execute([$input["username"], $input["email"], $hashed_password, '[]']);
      respond(["message" => "User registered successfully"], 201);
  }
}

if ($path[0] === "users" && isset($path[1]) && $path[1] === "me" && $method === "GET") {
    $userId = authenticate(); // Ensure the user is authenticated
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
      respond($user);
    } else {
      respond(["error" => "User not found"], 404);
    }
  }
  
// USER ENDPOINTS
if ($path[0] === "users") {
    $userId = authenticate();
    // Get all users
    if ($method === "GET" && count($path) === 1) {
        $stmt = $pdo->query("SELECT id, username, email, posts FROM users");
        respond($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    // Get user by ID
    if ($method === "GET" && count($path) === 2) {
        $stmt = $pdo->prepare("SELECT id, username, email, posts FROM users WHERE id = ?");
        $stmt->execute([$path[1]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            respond($user);
        } else {
            respond(["error" => "User not found"], 404);
        }
    }
    // Update user
    if ($method === "PUT" && count($path) === 2) {
        if (!$input || !isset($input["username"], $input["email"], $input["password"])) {
            respond(["error" => "Missing username, email, or password"], 400);
        }
        $hashed_password = password_hash($input["password"], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$input["username"], $input["email"], $hashed_password, $path[1]]);
        respond(["message" => "User updated successfully"]);
    }
}

respond(["error" => "Invalid request"], 400);
