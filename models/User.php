<?php

// Define User class
class User
{
    private $db;
    private $id;
    private $username;
    private $email;
    private $facebook_token;
    private $google_token;


    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    // Login
    public function login($username, $password)
    {   
        // Query to find a user by username
        $query = 'SELECT * FROM users WHERE username = :username LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user details
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->facebook_token = $row['facebook_token'];
                $this->google_token = $row['google_token'];

                // Start session
                $_SESSION['user_id'] = $this->id;
                $_SESSION['username'] = $this->username;

                return true;
            }
        }
        return false;

    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Logout
    public static function logout() {
        session_unset();
        session_destroy();
    }

    // Get current user
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            $db = (new Database())->connect();
            $query = 'SELECT * FROM users WHERE id = :id LIMIT 1';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // Save social media token
    public function saveSocialToken($type, $token) {
        $column = $type . '_token';
        $query = "UPDATE users SET $column = :token WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getFacebookToken() { return $this->facebook_token; }
    public function getGoogleToken() { return $this->google_token; }
}
?>