<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Destroy the session
    session_unset();
    session_destroy();

    // Send JSON response
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while logging out.']);
}
exit;
?>
