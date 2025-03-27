<?php
class Task {
    private $db;
    private $id;
    private $user_id;
    private $title;
    private $description;
    private $due_date;
    private $is_completed;
    private $created_at;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Create a new task
    public function create($user_id, $title, $description, $due_date) {
        $query = 'INSERT INTO tasks (user_id, title, description, due_date)
                VALUES (:user_id, :title, :description, :due_date)';
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);

        return $stmt->execute();
    }

    // Retrieve all tasks for a user
    public function getAll($user_id) {
    $query = 'SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date ASC';
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Retrieve a single task
    public function getById($id) {
        $query = 'SELECT * FROM tasks WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a task
    public function update($id, $title, $description, $due_date, $is_completed) {
        $query = 'UPDATE tasks 
                  SET title = :title, 
                      description = :description, 
                      due_date = :due_date, 
                      is_completed = :is_completed 
                  WHERE id = :id';
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':is_completed', $is_completed, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Delete a task
    public function delete($id) {
        $query = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Mark task as completed
    public function markAsCompleted($id) {
        $query = 'UPDATE tasks SET is_completed = TRUE WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

}
?>