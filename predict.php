<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

/* ================================
   PDF DOWNLOAD SECTION
================================ */
if(isset($_POST['download_pdf'])){

require_once 'dompdf/vendor/autoload.php';

extract($_POST);

$html = "
<html>
<head>
<style>
body{font-family:Arial;background:#f4f6f9;padding:30px;}
.card{background:#fff;border-radius:15px;padding:30px;}
.title{text-align:center;font-size:24px;font-weight:bold;margin-bottom:20px;}
.label{font-weight:bold;color:#34495e;}
.section{margin-bottom:12px;font-size:14px;}
</style>
</head>
<body>
<div class='card'>
<div class='title'>Smart Loan AI - Detailed Report</div>
<div class='section'><span class='label'>Customer ID:</span> $customer_id_hidden</div>
<div class='section'><span class='label'>Customer Name:</span> $customer_name_hidden</div>
<div class='section'><span class='label'>Status:</span> $status_hidden</div>
<div class='section'><span class='label'>Confidence:</span> $confidence_hidden%</div>
<div class='section'><span class='label'>Accuracy:</span> $accuracy_hidden%</div>
<div class='section'><span class='label'>Risk:</span> $risk_hidden</div>

<hr>

<div class='section'><span class='label'>Income:</span> $income_hidden</div>
<div class='section'><span class='label'>Loan Amount:</span> $loan_hidden</div>
<div class='section'><span class='label'>Loan Duration:</span> $loan_duration_hidden</div>
<div class='section'><span class='label'>Dependents:</span> $dependents_hidden</div>
<div class='section'><span class='label'>Education:</span> $education_hidden</div>
<div class='section'><span class='label'>Employment:</span> $employment_hidden</div>
<div class='section'><span class='label'>CIBIL:</span> $cibil_hidden</div>
<div class='section'><span class='label'>Assets:</span> $assets_hidden</div>
<div class='section'><span class='label'>EMI:</span> $emi_hidden</div>

<hr>

<div class='section'><span class='label'>AI Explanation:</span><br>$reason_hidden</div>

</div>
</body>
</html>";

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("Prediction_Report.pdf", ["Attachment"=>true]);
exit();
}

/* ================================
   NORMAL PREDICTION FLOW
================================ */

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: dashboard.php");
    exit();
}

/* ===== GET ALL NEW INPUTS ===== */
$customer_name = $_POST['customer_name'];
$income = floatval($_POST['income']);
$loan = floatval($_POST['loan']);
$loan_duration = intval($_POST['loan_duration']);
$dependents = intval($_POST['dependents']);
$education = $_POST['education'];
$employment = $_POST['employment'];
$cibil = intval($_POST['cibil_score']);
$assets = floatval($_POST['assets']);
$credit = intval($_POST['credit']);
$emi = floatval($_POST['emi']);

if($income <= 0 || $loan <= 0){
    die("Invalid Input");
}

/* ================================
   CALL PYTHON MODEL (CORRECT 9 FEATURES)
================================ */

// Convert categorical values first
$education_val = ($education == "Graduate") ? 1 : 0;
$employment_val = ($employment == "Salaried") ? 1 : 0;

$python = "C:\\Program Files\\Python313\\python.exe";
$script = "C:\\xampp\\htdocs\\loan_ai_project\\model.py";

// CORRECT ORDER (Must match model.py exactly)
$command = "\"$python\" \"$script\" "
    . "$income $loan $credit $dependents "
    . "$education_val $employment_val "
    . "$loan_duration $cibil $assets";

$output = shell_exec($command);
$data = json_decode($output, true);
if(!$data){
    $status = "Prediction Error";
    $confidence = 0;
    $accuracy = 0;
} else {
    $status = $data['status'];
    $confidence = $data['confidence'];
    $accuracy = $data['accuracy'];
}

/* ================================
   IMPROVED RISK LOGIC
================================ */

if($cibil >= 750 && $income > 5000 && $credit == 1)
    $risk = "Low";
elseif($cibil >= 650)
    $risk = "Medium";
else
    $risk = "High";

/* ================================
   AI EXPLANATION
================================ */

$reason = "";

if($cibil >= 750) $reason .= "✔ Excellent CIBIL Score<br>";
elseif($cibil >= 650) $reason .= "✔ Good CIBIL Score<br>";
else $reason .= "✖ Low CIBIL Score<br>";

if($income > 5000) $reason .= "✔ High Income<br>";
else $reason .= "⚠ Moderate/Low Income<br>";

if($assets > 5000) $reason .= "✔ Strong Asset Backup<br>";
else $reason .= "⚠ Limited Assets<br>";

if($loan_duration > 60) $reason .= "⚠ Long Loan Duration<br>";

/* ================================
   INSERT INTO DATABASE
================================ */
$customer_id = "CUST" . rand(1000,9999);
mysqli_query($conn,"
INSERT INTO loans 
(customer_id,customer_name,income,loan_amount,loan_duration,dependents,
education,employment,cibil_score,assets,emi,credit_history,status,risk_level,created_at)
VALUES 
('$customer_id','$customer_name','$income','$loan','$loan_duration','$dependents','$education',
'$employment','$cibil','$assets','$emi','$credit',
'$status ($confidence%)','$risk',NOW())
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Prediction Result</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#0d6efd">
<style>
body{background:linear-gradient(135deg,#e3f2fd,#f8f9fa);}
.card{border-radius:20px;}
.progress-bar{transition:width 1.5s ease-in-out;}
</style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow-lg text-center" style="width:520px">

<h3 class="mb-3">AI Prediction Result</h3>

<div class="alert alert-info">
<b><?php echo $status; ?></b><br>
Confidence: <?php echo $confidence; ?>% <br>
Accuracy: <?php echo $accuracy; ?>%
</div>

<div class="progress mb-3" style="height:25px;">
<div class="progress-bar bg-success" 
style="width:<?php echo $confidence; ?>%">
<?php echo $confidence; ?>%
</div>
</div>

<div class="alert alert-warning">
Risk Level: <b><?php echo $risk; ?></b>
</div>

<div class="alert alert-secondary text-start">
<b>AI Explanation:</b><br>
<?php echo $reason; ?>
</div>

<a href="dashboard.php" class="btn btn-primary w-100 mt-2">
Back to Dashboard
</a>

<form method="POST" class="mt-2">
<input type="hidden" name="customer_id_hidden" value="<?php echo $customer_id; ?>">
<input type="hidden" name="customer_name_hidden" value="<?php echo $customer_name; ?>">
<input type="hidden" name="income_hidden" value="<?php echo $income; ?>">
<input type="hidden" name="loan_hidden" value="<?php echo $loan; ?>">
<input type="hidden" name="loan_duration_hidden" value="<?php echo $loan_duration; ?>">
<input type="hidden" name="dependents_hidden" value="<?php echo $dependents; ?>">
<input type="hidden" name="education_hidden" value="<?php echo $education; ?>">
<input type="hidden" name="employment_hidden" value="<?php echo $employment; ?>">
<input type="hidden" name="cibil_hidden" value="<?php echo $cibil; ?>">
<input type="hidden" name="assets_hidden" value="<?php echo $assets; ?>">
<input type="hidden" name="emi_hidden" value="<?php echo $emi; ?>">
<input type="hidden" name="status_hidden" value="<?php echo $status; ?>">
<input type="hidden" name="confidence_hidden" value="<?php echo $confidence; ?>">
<input type="hidden" name="accuracy_hidden" value="<?php echo $accuracy; ?>">
<input type="hidden" name="risk_hidden" value="<?php echo $risk; ?>">
<input type="hidden" name="reason_hidden" value="<?php echo strip_tags($reason); ?>">

<button type="submit" name="download_pdf" 
class="btn btn-success w-100 mt-2">
Download PDF Report
</button>

</form>

</div>
</body>
</html>