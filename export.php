<?php
include "db.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="loan_report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, [
'Customer ID',
'Customer Name',
'Income',
'Loan Amount',
'Loan Duration',
'Dependents',
'Education',
'Employment',
'CIBIL Score',
'Assets',
'EMI',
'Status',
'Risk Level',
'Date'
]);
$result = mysqli_query($conn,"SELECT * FROM loans");

while($row = mysqli_fetch_assoc($result)){
    fputcsv($output, [
        $row['customer_id'],
$row['customer_name'],
$row['income'],
$row['loan_amount'],
$row['loan_duration'],
$row['dependents'],
$row['education'],
$row['employment'],
$row['cibil_score'],
$row['assets'],
$row['emi'],
$row['status'],
$row['risk_level'],
$row['created_at']
    ]);
}

fclose($output);
exit();
?>