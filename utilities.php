<?php
require_once 'config.php';
/*
Function to sanitize user input and prevent SQL injection
@param string $input The user input to sanitize
@return string The sanitized input
 */
function sanitizeInput($input){
    global $conn;
    return mysqli_real_escape_string($conn, $input);
}
/*
Function to retrieve the user's information from the database
@param int $userId The ID of the user
@return array|bool The user's information if found, false otherwise
*/
function getUserInfo($userId){
    global $conn;
    $sql = "SELECT username, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}
/*
Function to check if a user has permission to access a certain hangar
@param int $userId The ID of the user
@param int $hangarId The ID of the hangar
@return bool True if the user has permission, false otherwise
*/
function hasHangarPermission($userId, $hangarId){
    global $conn;
    $sql = "SELECT id FROM hangars WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $hangarId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 1;
}
