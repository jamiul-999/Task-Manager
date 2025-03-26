<?php
    // Define the database
    class Database
    {
        private $host = 'localhost';
        private $db_name = 'task_manager';
        private $username = 'root';
        private $password = '';
        private $conn; // Database connection using PDO

        public function connect() {
            $this->conn = null;

            try {
                // PDO connects to database
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name}",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection error: " . $e->getMessage();
            }

            return $this->conn;
        }
    }
?>