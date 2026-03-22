<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

/* COUNTS */
$approved = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM loans WHERE status LIKE '%Approved%'"))['total'];

$rejected = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM loans WHERE status LIKE '%Rejected%'"))['total'];

$low = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM loans WHERE risk_level='Low'"))['total'];

$medium = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM loans WHERE risk_level='Medium'"))['total'];

$high = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM loans WHERE risk_level='High'"))['total'];

$total = $approved + $rejected;

/* MONTHLY TREND */
$monthly = mysqli_query($conn,"
SELECT MONTH(created_at) as month, COUNT(*) as total
FROM loans
GROUP BY MONTH(created_at)
");

$months = [];
$counts = [];

while($row = mysqli_fetch_assoc($monthly)){
    $months[] = $row['month'];
    $counts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Smart Loan AI Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#667eea">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ---- YOUR ORIGINAL CSS UNCHANGED ---- */
body{
background: linear-gradient(135deg,#141E30,#243B55);
font-family: 'Segoe UI', sans-serif;
color:white;
padding-bottom:40px;
}

h2,h5{
font-weight:600;
letter-spacing:0.5px;
}
.chart-container{
height:280px;
position:relative;
}

canvas{
width:100% !important;
height:100% !important;
}

.glass-card{
background: rgba(255,255,255,0.08);
backdrop-filter: blur(12px);
border-radius:18px;
padding:25px;
margin-bottom:25px;
box-shadow:0 8px 25px rgba(0,0,0,0.3);
}

.counter{
font-size:28px;
font-weight:bold;
margin-top:10px;
}

.table-container{
max-height:320px;
overflow:auto;
}

.table th{
font-weight:500;
}

.btn{
border-radius:10px;
font-weight:500;
}
.predict-btn{
background: linear-gradient(45deg,#00c6ff,#0072ff);
border: none;
color: white;
font-weight: 600;
letter-spacing: 0.5px;
border-radius: 10px;
transition: 0.3s ease;
}

.predict-btn:hover{
transform: translateY(-2px);
box-shadow: 0 8px 20px rgba(0,114,255,0.4);
background: linear-gradient(45deg,#0072ff,#00c6ff);
}
input,select{
border-radius:10px !important;
padding:10px !important;
}
</style>
</head>

<body class="container py-4">

<!-- HEADER (UNCHANGED) -->
<div class="d-flex justify-content-between align-items-center mb-4">
<h2> Smart Loan AI Dashboard</h2>
<div>
<a href="export.php" class="btn btn-success me-2">Export Report</a>
<a href="logout.php" class="btn btn-danger"> Logout</a>
<button id="installBtn" class="btn btn-warning ms-2" style="display:none;">
 Install App
</button>
</div>
</div>

<!-- COUNTERS (UNCHANGED) -->
<div class="row text-center mb-4">
<div class="col-md-3 mb-3">
<div class="glass-card">
<h6>Total Predictions</h6>
<div class="counter" id="totalCount"><?php echo $total ?></div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="glass-card">
<h6>Approved Loans</h6>
<div class="counter text-success" id="approvedCount"><?php echo $approved ?></div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="glass-card">
<h6>Rejected Loans</h6>
<div class="counter text-danger" id="rejectedCount"><?php echo $rejected ?></div>
</div>
</div>

<div class="col-md-3 mb-3">
<div class="glass-card">
<h6>Low Risk Loans</h6>
<div class="counter text-warning"><?php echo $low ?></div>
</div>
</div>
</div>

<!-- ✅ UPDATED FORM WITH NEW FIELDS -->
<div class="glass-card mb-4">
<h5 class="mb-3">Loan Prediction</h5>
<form action="predict.php" method="post">

<div class="row">

<!-- LEFT SIDE -->
<div class="col-md-6">

<h6 class="mb-3 text-info"> Personal Information</h6>
<div class="mb-3">
<label>Customer Name</label>
<input class="form-control" type="text" name="customer_name" required>
</div>
<div class="mb-3">
<label>No. of Dependents</label>
<input class="form-control" type="number" name="dependents" required>
</div>

<div class="mb-3">
<label>Education</label>
<select class="form-control" name="education" required>
<option value="">Select</option>
<option>Graduate</option>
<option>Not Graduate</option>
</select>
</div>

<div class="mb-3">
<label>Employment Type</label>
<select class="form-control" name="employment" required>
<option value="">Select</option>
<option>Salaried</option>
<option>Self-Employed</option>
</select>
</div>

<div class="mb-3">
<label>CIBIL Score</label>
<input class="form-control" type="number" name="cibil_score" required>
</div>

</div>

<!-- RIGHT SIDE -->
<div class="col-md-6">

<h6 class="mb-3 text-warning">Financial & Loan Details</h6>

<div class="mb-3">
<label>Annual Income</label>
<input class="form-control" type="number" name="income" required>
</div>

<div class="mb-3">
<label>Assets Value</label>
<input class="form-control" type="number" name="assets" required>
</div>

<div class="mb-3">
<label>Loan Amount</label>
<input class="form-control" type="number" id="loanAmount" name="loan" required>
</div>

<div class="mb-3">
<label>Loan Duration (Months)</label>
<input class="form-control" type="number" id="loanDuration" name="loan_duration" required>
</div>

<div class="mb-3">
<label>Credit History</label>
<select class="form-control" name="credit">
<option value="1">Good</option>
<option value="0">Bad</option>
</select>
</div>

</div>
</div>

<!-- EMI RESULT BOX -->
<div class="glass-card mt-4 text-center">
<h6 class="text-success"> Estimated EMI</h6>
<input class="form-control text-center fw-bold" 
       style="font-size:20px;background:#0f2027;color:#00ffcc;border:none;"
       type="text" id="emiField" name="emi" readonly>
</div>

<!-- BUTTON -->
<div class="mt-4">
<button type="submit" class="btn predict-btn w-100 py-3">
 Predict Loan Approval
</button>
</div>

</form>
</div>

<!-- CHARTS -->
<div class="row">

<div class="col-lg-4 mb-4">
<div class="glass-card">
<h5>Approval Analytics</h5>
<div class="chart-container">
<canvas id="loanChart"></canvas>
</div>
</div>
</div>
<div class="col-lg-4 mb-4">
<div class="glass-card">
<h5> Risk Distribution</h5>
<div class="chart-container">
<canvas id="riskChart"></canvas>
</div>
</div>
</div>
<div class="col-lg-4 mb-4">
<div class="glass-card">
<h5> Monthly Trend</h5>
<div class="chart-container">
<canvas id="trendChart"></canvas>
</div>
</div>
</div>

</div>

<!-- HISTORY -->
<div class="glass-card">
<h5>Prediction History</h5>

<input type="text" id="searchBox" class="form-control mb-3" placeholder="Search Income, Status, Risk...">

<div class="table-container">
<table class="table table-dark table-hover table-bordered">
<thead>
<tr>
    <th>Customer ID</th>
<th>Name</th>
<th>Income</th>
<th>Loan</th>
<th>Duration</th>
<th>Dependents</th>
<th>Education</th>
<th>Employment</th>
<th>CIBIL</th>
<th>Assets</th>
<th>EMI</th>
<th>Status</th>
<th>Risk</th>
<th>Date</th>
</tr>
</thead>
<tbody id="historyTable"></tbody>
</table>
</div>

<div id="paginationArea" class="mt-3"></div>

</div>
<script>
let deferredPrompt;

// Detect install availability
window.addEventListener("beforeinstallprompt", (e) => {
  e.preventDefault();
  deferredPrompt = e;

  console.log("Install available ✅");

  // Show button
  document.getElementById("installBtn").style.display = "inline-block";
});

// Button click
document.getElementById("installBtn").addEventListener("click", async () => {
  if (deferredPrompt) {
    deferredPrompt.prompt();

    const choice = await deferredPrompt.userChoice;

    if (choice.outcome === "accepted") {
      console.log("User installed app 🎉");
    } else {
      console.log("User cancelled ❌");
    }

    deferredPrompt = null;
  }
});
</script>
<script>

/* CHARTS WITH SMOOTH ANIMATION */
const loanChart = new Chart(document.getElementById('loanChart'), {
type:'bar',
data:{
labels:['Approved','Rejected'],
datasets:[{
data:[<?php echo $approved ?>,<?php echo $rejected ?>],
backgroundColor:['#00ff99','#ff4b2b']
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
animation:{duration:2000}
}
});

const riskChart = new Chart(document.getElementById('riskChart'), {
type:'doughnut',
data:{
labels:['Low','Medium','High'],
datasets:[{
data:[<?php echo $low ?>,<?php echo $medium ?>,<?php echo $high ?>],
backgroundColor:['#00ccff','#ffcc00','#ff0066']
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
animation:{animateRotate:true,duration:2000}
}
});

const trendChart = new Chart(document.getElementById('trendChart'), {
type:'line',
data:{
labels:<?php echo json_encode($months); ?>,
datasets:[{
label:'Monthly Predictions',
data:<?php echo json_encode($counts); ?>,
borderColor:'#00ffff',
tension:0.4,
fill:false
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
animation:{duration:2000}
}
});

/* LIVE AUTO REFRESH COUNTS */
setInterval(()=>{
fetch("stats_api.php")
.then(res=>res.json())
.then(data=>{
document.getElementById("totalCount").innerText=data.total;
document.getElementById("approvedCount").innerText=data.approved;
document.getElementById("rejectedCount").innerText=data.rejected;
});
},10000);

/* AJAX HISTORY */
function loadData(page=1,search=""){
fetch(`fetch_history.php?page=${page}&search=${search}`)
.then(res=>res.json())
.then(data=>{
document.getElementById("historyTable").innerHTML=data.table;
document.getElementById("paginationArea").innerHTML=data.pagination;

document.querySelectorAll(".page-btn").forEach(btn=>{
btn.addEventListener("click",function(){
loadData(this.dataset.page,document.getElementById("searchBox").value);
});
});
});
}

document.getElementById("searchBox").addEventListener("keyup",function(){
loadData(1,this.value);
});

loadData();
/* ✅ EMI CALCULATOR */
function calculateEMI(){
let P = parseFloat(document.getElementById("loanAmount").value);
let N = parseFloat(document.getElementById("loanDuration").value);
let R = 8.5 / 12 / 100; // fixed 8.5% annual interest

if(P && N){
let emi = (P * R * Math.pow(1+R,N)) / (Math.pow(1+R,N)-1);
document.getElementById("emiField").value = emi.toFixed(2);
}
}

document.getElementById("loanAmount").addEventListener("input",calculateEMI);
document.getElementById("loanDuration").addEventListener("input",calculateEMI);



</script>
<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("service-worker.js")
    .then(() => console.log("Service Worker Registered"))
    .catch(err => console.log(err));
}
</script>
</body>
</html>