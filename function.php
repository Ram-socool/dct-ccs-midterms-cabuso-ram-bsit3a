<?php
function checkUserSessionIsActive() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['email'])) {
        header("Location: dashboard.php");
        exit;
    }
}

function validateLoginCredentials($email, $password) {
    $errors = [];
    if (empty(trim($email))) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty(trim($password))) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    return $errors;
}

function getUsers() {
    $users = [
        ['email' => 'user1@email.com', 'password' => password_hash('password1', PASSWORD_DEFAULT)],
        ['email' => 'user2@email.com', 'password' => password_hash('password2', PASSWORD_DEFAULT)],
        ['email' => 'user3@email.com', 'password' => password_hash('password3', PASSWORD_DEFAULT)],
        ['email' => 'user4@email.com', 'password' => password_hash('password4', PASSWORD_DEFAULT)],
        ['email' => 'user5@email.com', 'password' => password_hash('password5', PASSWORD_DEFAULT)],
    ];
    return $users;
}

function checkLoginCredentials($email, $password, $users) {
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            if (password_verify($password, $user['password'])) {
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}

function displayErrors($errors) {
    if (empty($errors)) {
        return '';
    }
    $html = '<div class="alert alert-danger"><strong>System Errors:</strong><ul>';
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    $html .= '</ul></div>';
    return $html;
}

function renderErrorsToView($error) {
    if (empty($error)) {
        return '';
    }
    $html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $html .= htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';
    return $html;
}

function guard() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['email'])) {
        header("Location: index.php");
        exit;
    }
}

function validateStudentData(array $student_data): array {
    $errors = [];
    if (empty($student_data['student_id'])) {
        $errors[] = "Student ID is required.";
    } elseif (!preg_match('/^\d{5,10}$/', $student_data['student_id'])) {
        $errors[] = "Student ID must be between 5 and 10 digits.";
    }
    if (empty($student_data['first_name'])) {
        $errors[] = "First Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $student_data['first_name'])) {
        $errors[] = "First Name must contain only letters, spaces, or hyphens.";
    }
    if (empty($student_data['last_name'])) {
        $errors[] = "Last Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $student_data['last_name'])) {
        $errors[] = "Last Name must contain only letters, spaces, or hyphens.";
    }
    return $errors;
}

function checkDuplicateStudentData(array $student_data): string {
    foreach ($_SESSION['students'] as $student) {
        if (strcasecmp($student['student_id'], $student_data['student_id']) === 0) {
            return "Duplicate Student ID found.";
        }
    }
    return "";
}

function getSelectedStudentIndex(string $student_id): ?int {
    foreach ($_SESSION['students'] as $index => $student) {
        if (isset($student['student_id']) && $student['student_id'] === $student_id) {
            return $index;
        }
    }
    return null;
}

function getSelectedStudentData(int $index): ?array {
    return $_SESSION['students'][$index] ?? null;
}

function validateSubjectData(array $subject_data): array {
    $errors = [];
    if (empty($subject_data['subject_code'])) {
        $errors[] = "Subject Code is required.";
    } elseif (!preg_match('/^[A-Z0-9]{3,10}$/', $subject_data['subject_code'])) {
        $errors[] = "Subject Code must be between 3 and 10 uppercase alphanumeric characters.";
    }
    if (empty($subject_data['subject_name'])) {
        $errors[] = "Subject Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $subject_data['subject_name'])) {
        $errors[] = "Subject Name must contain only letters and spaces.";
    }
    return $errors;
}

function checkDuplicateSubjectData(array $subject_data): ?string {
    if (!isset($_SESSION['subjects'])) {
        return null;
    }
    foreach ($_SESSION['subjects'] as $subject) {
        if (strcasecmp($subject['subject_code'], $subject_data['subject_code']) === 0) {
            return "Duplicate Subject Code: " . htmlspecialchars($subject_data['subject_code'], ENT_QUOTES, 'UTF-8') . " already exists.";
        }
        if (strcasecmp($subject['subject_name'], $subject_data['subject_name']) === 0) {
            return "Duplicate Subject Name: " . htmlspecialchars($subject_data['subject_name'], ENT_QUOTES, 'UTF-8') . " already exists.";
        }
    }
    return null;
}

function validateAttachedSubject(array $subject_data): array {
    if (empty($subject_data)) {
        return ["At least one subject must be selected."];
    }
    return [];
}

function getSelectedSubjectIndex(string $subject_code): ?int {
    foreach ($_SESSION['subjects'] as $index => $subject) {
        if ($subject['subject_code'] === $subject_code) {
            return $index;
        }
    }
    return null;
}

function getSelectedSubjectData(int $index): ?array {
    return $_SESSION['subjects'][$index] ?? null;
}
?>
