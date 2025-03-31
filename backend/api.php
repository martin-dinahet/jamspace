<?php

require_once "db.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];
$path = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
$input = json_decode(file_get_contents("php://input"), true);

function respond($data, $status = 200)
{
  http_response_code($status);
  echo json_encode($data);
  exit;
}

// User endpoints
if ($path[0] === "users") {
  if ($method === "GET") {
    // Get all users or a specific user by ID
    if (isset($path[1])) {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
      $stmt->execute([$path[1]]);
      respond($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
      $stmt = $pdo->query("SELECT * FROM users");
      respond($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
  } elseif ($method === "POST") {
    // Create a new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$input["username"], $input["email"], password_hash($input["password"], PASSWORD_DEFAULT)]);
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Post endpoints
if ($path[0] === "posts") {
  if ($method === "GET") {
    // Get all posts or posts by a specific user
    if (isset($path[1])) {
      $stmt = $pdo->prepare("SELECT * FROM post WHERE author = ?");
      $stmt->execute([$path[1]]);
      respond($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
      $stmt = $pdo->query("SELECT * FROM post");
      respond($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
  } elseif ($method === "POST") {
    // Create a new post
    $stmt = $pdo->prepare("INSERT INTO post (title, content, imgurl, author) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input["title"], $input["content"], $input["imgurl"] ?? null, $input["author"]]);
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Message endpoints
if ($path[0] === "messages") {
  if ($method === "GET" && isset($path[1])) {
    // Get all messages in a specific chat
    $stmt = $pdo->prepare("SELECT * FROM message WHERE chat_id = ?");
    $stmt->execute([$path[1]]);
    respond($stmt->fetchAll(PDO::FETCH_ASSOC));
  } elseif ($method === "POST") {
    // Send a new message
    $stmt = $pdo->prepare("INSERT INTO message (content, sender, receiver, chat_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input["content"], $input["sender"], $input["receiver"], $input["chat_id"]]);
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Chat endpoints
if ($path[0] === "chats") {
  if ($method === "GET") {
    // Get all chats
    $stmt = $pdo->query("SELECT * FROM chat");
    respond($stmt->fetchAll(PDO::FETCH_ASSOC));
  } elseif ($method === "POST") {
    // Create a new chat
    $stmt = $pdo->prepare("INSERT INTO chat DEFAULT VALUES");
    $stmt->execute();
    respond(["id" => $pdo->lastInsertId()], 201);
  }
}

// Contact endpoints
if ($path[0] === "contacts" && isset($path[1])) {
  if ($method === "GET") {
    // Get a user's contacts
    $stmt = $pdo->prepare("SELECT users.* FROM users JOIN user_contacts ON users.id = user_contacts.contact_id WHERE user_contacts.user_id = ?");
    $stmt->execute([$path[1]]);
    respond($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
}

// Invalid request response
respond(["error" => "Invalid request"], 400);
