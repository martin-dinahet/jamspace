<?php
class Post {
    public $id;
    public $image;
    public $description;
    public $user_id;

    // Function to create a new post in the database
    public static function createPost($pdo, $userId, $image, $description) {
        $sql = "INSERT INTO posts (user_id, image, description) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $image, $description]);
    }

    // Function to get all posts
    public static function getAllPosts($pdo) {
        $sql = "SELECT * FROM posts";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Function to find a post by id
    public static function findPostById($pdo, $postId) {
        $sql = "SELECT * FROM posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$postId]);
        return $stmt->fetch();
    }

    // Function to update a post
    public static function updatePost($pdo, $postId, $image, $description) {
        $sql = "UPDATE posts SET image = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$image, $description, $postId]);
    }

    // Function to delete a post
    public static function deletePost($pdo, $postId) {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$postId]);
    }
}
?>
