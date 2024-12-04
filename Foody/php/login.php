<?php
require_once 'conectbd.php';

function loginUser($email, $password) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT num, hash_password, rol 
            FROM users 
            WHERE email = ?
        ");
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        $user = $result->fetch_assoc();
        
        if (!password_verify($password, $user['hash_password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }
        
        session_start();
        $_SESSION['user_num'] = $user['num'];
        $_SESSION['user_role'] = $user['rol'];
        $_SESSION['user_email'] = $email;
        
        $_SESSION['is_employee'] = ($user['rol'] === 'employee');
        
        if ($user['rol'] === 'employee') {
            $stmt = $conn->prepare("
                SELECT num as employee_num, firstName, lastName
                FROM employee 
                WHERE userNum = ?
            ");
            
            $stmt->bind_param("i", $user['num']);
            $stmt->execute();
            $emp_result = $stmt->get_result();
            
            if ($emp_result->num_rows > 0) {
                $employee = $emp_result->fetch_assoc();
                $_SESSION['employee_num'] = $employee['employee_num'];
                $_SESSION['employee_name'] = $employee['firstName'] . ' ' . $employee['lastName'];
            }
        }
        
        return [
            'success' => true,
            'role' => $user['rol'],
            'user_num' => $user['num']
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function checkSession() {
    if (!isset($_SESSION['user_num']) || !isset($_SESSION['user_role'])) {
        header('Location: ../index.php');
        exit();
    }
    
    if (strpos($_SERVER['PHP_SELF'], 'InterfacesEmpleadohtml') !== false) {
        if (!isset($_SESSION['is_employee']) || !$_SESSION['is_employee']) {
            header('Location: ../index.php');
            exit();
        }
    }
}
?>