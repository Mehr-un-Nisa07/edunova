<?php
// User.php — OOP User class
require_once 'db.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "This email is already registered."];
        }
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed]);
        return ["success" => true, "message" => "Registration successful! Please log in."];
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return ["success" => true, "user" => $user];
        }
        return ["success" => false, "message" => "Invalid email or password."];
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $name, $email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "That email is already used by another account."];
        }
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
        return ["success" => true, "message" => "Profile updated successfully."];
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($currentPassword, $user['password'])) {
            return ["success" => false, "message" => "Current password is incorrect."];
        }
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $id]);
        return ["success" => true, "message" => "Password changed successfully."];
    }

    // APPLICATION METHODS

    public function applyForProgram($userId, $program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement) {
        $stmt = $this->pdo->prepare("SELECT id FROM applications WHERE user_id = ?");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "You already have an application. Please use Edit to update it."];
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO applications (user_id, program, degree, dob, phone, address, prev_school, prev_grade, statement)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement]);
        return ["success" => true, "message" => "Application submitted successfully! We will be in touch soon."];
    }

    public function getApplication($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM applications WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateApplication($userId, $program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement) {
        $stmt = $this->pdo->prepare(
            "UPDATE applications
             SET program=?, degree=?, dob=?, phone=?, address=?, prev_school=?, prev_grade=?, statement=?, updated_at=NOW()
             WHERE user_id=?"
        );
        $stmt->execute([$program, $degree, $dob, $phone, $address, $prevSchool, $prevGrade, $statement, $userId]);
        return ["success" => true, "message" => "Application updated successfully."];
    }

    public function withdrawApplication($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM applications WHERE user_id = ?");
        $stmt->execute([$userId]);
        return ["success" => true, "message" => "Application withdrawn."];
    }
}
?>
