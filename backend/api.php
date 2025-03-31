<?php

require_once 'db.php';

$request_uri = $_SERVER['REQUEST_URI'];

if ($request_uri === '/hello') {
  header('Content-Type: application/json');
  echo json_encode(["message" => "Hello, World!"]);
  exit;
}

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
