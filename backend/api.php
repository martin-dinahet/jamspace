<?php

// inclusion des fichiers nécessaires
require_once "db.php"; // connexion à la base de données
require_once "vendor/autoload.php"; // chargement des dépendances

// importation de firebase jwt pour la gestion des tokens
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// configuration des en-têtes pour les requêtes http
header("Access-Control-Allow-Origin: *"); // autorise toutes les origines
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS"); // méthodes autorisées
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // en-têtes autorisés
header("Content-Type: application/json"); // type de contenu json

// gestion des requêtes options (pré-vol)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit();
}

// récupération du chemin de l'url et de la méthode http
$path = array_values(array_filter(explode("/", trim($_SERVER["REQUEST_URI"], "/"))));
$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);

// clé secrète utilisée pour le jwt
$key = "super_secret_key";

// fonction pour générer un jwt
function generate_jwt($user_id)
{
  global $key;
  $payload = ["sub" => $user_id, "iat" => time(), "exp" => time() + (60 * 60 * 24)];
  return JWT::encode($payload, $key, "HS256");
}

// fonction pour vérifier un jwt
function verify_jwt($token)
{
  global $key;
  try {
    return JWT::decode($token, new Key($key, "HS256"));
  } catch (Exception $e) {
    return null;
  }
}

// fonction pour récupérer le token dans l'en-tête authorization
function get_bearer_token()
{
  $headers = getallheaders();
  return isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;
}

// fonction pour authentifier un utilisateur via son jwt
function authenticate()
{
  $token = get_bearer_token();
  if (!$token) respond(["error" => "unauthorized"], 401);
  $decoded = verify_jwt($token);
  if (!$decoded) respond(["error" => "invalid token"], 401);
  return $decoded->sub;
}

// fonction pour envoyer une réponse json avec un code http
function respond($data, $status = 200)
{
  http_response_code($status);
  echo json_encode($data);
  exit;
}

/////////////////////

// gestion des endpoints d'authentification
if ($path[0] === "auth") {
  // endpoint pour la connexion
  if ($path[1] === "login" && $method === "POST") {
    if (!$input || !isset($input["email"], $input["password"])) {
      respond(["error" => "missing email or password"], 400);
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input["email"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($input["password"], $user["password"])) {
      $token = generate_jwt($user["id"]);
      respond(["token" => $token]);
    } else {
      respond(["error" => "invalid credentials"], 401);
    }
  }

  // endpoint pour l'inscription
  if ($path[1] === "register" && $method === "POST") {
    if (!$input || !isset($input["username"], $input["email"], $input["password"])) {
      respond(["error" => "missing username, email, or password"], 400);
    }
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input["email"]]);
    if ($stmt->fetch()) {
      respond(["error" => "email already in use"], 400);
    }
    $hashed_password = password_hash($input["password"], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, posts) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input["username"], $input["email"], $hashed_password, '[]']);
    respond(["message" => "user registered successfully"], 201);
  }
}

// endpoint pour récupérer les infos de l'utilisateur connecté
if ($path[0] === "users" && isset($path[1]) && $path[1] === "me" && $method === "GET") {
  $userId = authenticate(); // vérifie que l'utilisateur est bien connecté
  $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user) {
    respond($user);
  } else {
    respond(["error" => "user not found"], 404);
  }
}

// gestion des endpoints utilisateurs
if ($path[0] === "users") {
  $userId = authenticate(); // vérifie l'authentification
  // récupération de tous les utilisateurs
  if ($method === "GET" && count($path) === 1) {
    $stmt = $pdo->query("SELECT id, username, email, posts FROM users");
    respond($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
  // récupération d'un utilisateur par id
  if ($method === "GET" && count($path) === 2) {
    $stmt = $pdo->prepare("SELECT id, username, email, posts FROM users WHERE id = ?");
    $stmt->execute([$path[1]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
      respond($user);
    } else {
      respond(["error" => "user not found"], 404);
    }
  }
  // mise à jour d'un utilisateur
  if ($method === "PUT" && count($path) === 2) {
    if (!$input || !isset($input["username"], $input["email"], $input["password"])) {
      respond(["error" => "missing username, email, or password"], 400);
    }
    $hashed_password = password_hash($input["password"], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->execute([$input["username"], $input["email"], $hashed_password, $path[1]]);
    respond(["message" => "user updated successfully"]);
  }
}

// réponse par défaut en cas de requête invalide
respond(["error" => "invalid request"], 400);
