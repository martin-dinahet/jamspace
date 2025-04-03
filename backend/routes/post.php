<?php
// Get all posts
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/posts') {
    $posts = Post::getAllPosts($pdo);
    echo json_encode($posts);
}

// Create a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/posts') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'];
    $image = $data['image'];
    $description = $data['description'];

    Post::createPost($pdo, $userId, $image, $description);
    echo json_encode(["message" => "Post created successfully"]);
    http_response_code(201);
}

// Update a post
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && preg_match('/\/posts\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $postId = $matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    $image = $data['image'];
    $description = $data['description'];

    Post::updatePost($pdo, $postId, $image, $description);
    echo json_encode(["message" => "Post updated successfully"]);
    http_response_code(200);
}

// Delete a post
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('/\/posts\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $postId = $matches[1];

    Post::deletePost($pdo, $postId);
    echo json_encode(["message" => "Post deleted successfully"]);
    http_response_code(204);
}
?>
