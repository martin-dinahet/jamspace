<?php
class User {
    public $id;
    public $username;
    public $email;
    public $password;

    // Function to hash the password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Function to validate the password
    public static function validatePassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Function to create a new user in the database
    public static function createUser($pdo, $username, $email, $password) {
        $hashedPassword = self::hashPassword($password);
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword]);
    }

    // Function to find a user by username
    public static function findByUsername($pdo, $username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}
?>
