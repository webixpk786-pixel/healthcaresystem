<?php
require_once('tcpdf.php');


// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('My Company');
$pdf->SetTitle('Invoice');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Sample HTML content
$html = '<h1 style="text-align:center;">Invoice</h1><p>This is a sample TCPDF-generated invoice.</p>';
$pdf->writeHTML($html, true, false, true, false, '');

// Save PDF to file (inside /web/pdf/invoice_output.pdf)
$pdf->Output(); // 'F' = File