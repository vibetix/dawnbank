<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Return a JSON response for the frontend
echo json_encode([
    "success" => true,
    "redirect" => "index.html?logged_out=1"
]);
exit();
?>
