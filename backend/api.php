<?php
require_once "db.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];
$path = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
$input = json_decode(file_get_contents("php://input"), true);

if ($path[0] === "hello") {
  switch ($method) {
    case "GET":
      echo json_encode(["message" => "Hello, World!"]);
      exit;
      break;

    default:
      echo json_encode(["error" => "Invalid request"]);
  }
} else {
  echo json_encode(["error" => "Invalid endpoint"]);
}
