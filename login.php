    <?php
session_start();
include "db.php";

if(isset($_POST['login']))
{
$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result) > 0)
{
$_SESSION['user'] = $username;
header("Location: dashboard.php");
}
else
{
$error = "Invalid Login";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Loan AI Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#667eea">
</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow" style="width:350px">
<h3 class="text-center">Loan AI System</h3>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="post">
<input class="form-control mb-3" name="username" placeholder="Username" required>
<input class="form-control mb-3" name="password" type="password" placeholder="Password" required>
<button class="btn btn-primary w-100" name="login">Login</button>
</form>

</div>
<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("service-worker.js")
    .then(() => console.log("Service Worker Registered"))
    .catch(err => console.log(err));
}
</script>
</body>
</html>