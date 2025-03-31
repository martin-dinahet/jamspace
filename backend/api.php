<?php

require_once "db.php";
require_once "vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit();
}

$path = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
$path = array_filter($path);
$path = array_values($path);

$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);

$key = "super_secret_key";

if (empty($path)) {
  respond(["error" => "Invalid endpoint"], 400);
}

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
  if (!isset($headers["Authorization"])) return null;
  $token = str_replace("Bearer ", "", $headers["Authorization"]);
  return $token;
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

// Authentication endpoints
if ($path[0] === "auth" && isset($path[1]) && $path[1] === "login") {
  if ($method !== "POST") {
    respond(["error" => "Invalid request method"], 405);
  }
  // Check if input exists
  if (!$input || !isset($input["email"], $input["password"])) {
    respond(["error" => "Missing email or password"], 400);
  }
  // Fetch user
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

// User endpoints
if ($path[0] === "users") {
  if ($method === "GET") {
    if ($path[1] === "me") {
      $user_id = authenticate();
      $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
      $stmt->execute([$user_id]);
      respond($stmt->fetch(PDO::FETCH_ASSOC));
    } elseif (isset($path[1])) {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
      $stmt->execute([$path[1]]);
      respond($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
      $stmt = $pdo->query("SELECT * FROM users");
      respond($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
  } elseif ($method === "POST") {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$input["username"], $input["email"], password_hash($input["password"], PASSWORD_DEFAULT)]);
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Post endpoints (Protected)
if ($path[0] === "posts") {
  if ($method === "POST") {
    $userId = authenticate();
    $stmt = $pdo->prepare("INSERT INTO post (title, content, imgurl, author) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input["title"], $input["content"], $input["imgurl"] ?? null, $userId]);
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Message endpoints (Protected)
if ($path[0] === "messages" && $method === "POST") {
  $userId = authenticate();
  $stmt = $pdo->prepare("INSERT INTO message (content, sender, receiver, chat_id) VALUES (?, ?, ?, ?)");
  $stmt->execute([$input["content"], $userId, $input["receiver"], $input["chat_id"]]);
  respond(["id" => $pdo->lastInsertId()], 201);
}

// Chat endpoints (Protected)
if ($path[0] === "chats" && $method === "POST") {
  authenticate();
  $stmt = $pdo->prepare("INSERT INTO chat DEFAULT VALUES");
  $stmt->execute();
  respond(["id" => $pdo->lastInsertId()], 201);
}

// Contact endpoints (Protected)
if ($path[0] === "contacts") {
  $userId = authenticate();
  $stmt = $pdo->prepare("SELECT users.* FROM users JOIN user_contacts ON users.id = user_contacts.contact_id WHERE user_contacts.user_id = ?");
  $stmt->execute([$userId]);
  respond($stmt->fetchAll(PDO::FETCH_ASSOC));
}

respond(["error" => "Invalid request"], 400);
