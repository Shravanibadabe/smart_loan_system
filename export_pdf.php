<?php
require('fpdf/fpdf.php');
include "db.php";

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = "SELECT * FROM loans WHERE id='$id'";
} else {
    $query = "SELECT * FROM loans";
}

$result = mysqli_query($conn,$query);

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();

/* ================= HEADER DESIGN ================= */
$pdf->SetFillColor(36,59,85); // Dark blue
$pdf->Rect(0,0,297,25,'F');

$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',20);
$pdf->SetXY(0,8);
$pdf->Cell(297,10,'SMART LOAN AI - REPORT',0,1,'C');

$pdf->SetTextColor(0,0,0);
$pdf->Ln(15);

/* ================= REPORT INFO ================= */
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,8,'Generated On: '.date("d-m-Y h:i A"),0,1,'R');
$pdf->Ln(3);

/* ================= TABLE HEADER ================= */
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(220,220,220);

$headers = [
'CustomerID','Name','Income','Loan','Dur',
'Dep','Edu','Emp',
'CIBIL','Assets','EMI',
'Status','Risk','Date'
];

$widths = [
25,40,20,25,15,
15,25,25,
20,20,20,
25,20,32
];

for($i=0;$i<count($headers);$i++){
    $pdf->Cell($widths[$i],8,$headers[$i],1,0,'C',true);
}
$pdf->Ln();

/* ================= TABLE BODY ================= */
$pdf->SetFont('Arial','',7);

$fill = false;

while($row = mysqli_fetch_assoc($result)){

    if($fill){
        $pdf->SetFillColor(245,245,245);
    } else {
        $pdf->SetFillColor(255,255,255);
    }

   $pdf->Cell(25,8,$row['customer_id'],1,0,'C',true);
$pdf->Cell(40,8,$row['customer_name'],1,0,'C',true);
$pdf->Cell(20,8,$row['income'],1,0,'C',true);
    $pdf->Cell(25,8,$row['loan_amount'],1,0,'C',true);
    $pdf->Cell(15,8,$row['loan_duration'],1,0,'C',true);
    $pdf->Cell(15,8,$row['dependents'],1,0,'C',true);
    $pdf->Cell(25,8,$row['education'],1,0,'C',true);
    $pdf->Cell(25,8,$row['employment'],1,0,'C',true);
    $pdf->Cell(20,8,$row['cibil_score'],1,0,'C',true);
    $pdf->Cell(20,8,$row['assets'],1,0,'C',true);
    $pdf->Cell(20,8,$row['emi'],1,0,'C',true);
    $pdf->Cell(25,8,$row['status'],1,0,'C',true);
    $pdf->Cell(20,8,$row['risk_level'],1,0,'C',true);
    $pdf->Cell(32,8,$row['created_at'],1,0,'C',true);
    $pdf->Ln();

    $fill = !$fill;
}

/* ================= FOOTER ================= */
$pdf->Ln(5);
$pdf->SetFont('Arial','I',9);
$pdf->Cell(0,8,'This is a system generated report from Smart Loan AI.',0,1,'C');

$pdf->Output('D','Smart_Loan_AI_Report.pdf');
exit();
?>