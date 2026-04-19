<?php
// Check if FPDF class exists
if (!class_exists('FPDF')) {
    require_once __DIR__ . "/fpdf.php"; // If directly in includes folder
    require_once __DIR__ . "/fpdf/fpdf.php"; // If inside /fpdf subfolder
}
?>
