<?php
require '../vendor/autoload.php';
use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'FPDF is installed!');
$pdf->Output();
?>
