<?php
include "db.php";

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = "";
$where = "";

if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = $_GET['search'];
    $where = "WHERE customer_id LIKE '%$search%'
          OR customer_name LIKE '%$search%'
          OR income LIKE '%$search%' 
          OR status LIKE '%$search%' 
          OR risk_level LIKE '%$search%'
          OR education LIKE '%$search%'
          OR employment LIKE '%$search%'
          OR cibil_score LIKE '%$search%'
          OR dependents LIKE '%$search%'
          OR loan_duration LIKE '%$search%'
          OR created_at LIKE '%$search%'";
}

$total_records_query = mysqli_query($conn,"SELECT COUNT(*) as total FROM loans $where");
$total_records = mysqli_fetch_assoc($total_records_query)['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn,"
SELECT * FROM loans 
$where
ORDER BY id DESC 
LIMIT $start,$limit
");

$output = "";

while($row = mysqli_fetch_assoc($result)){
$output .= "<tr>
<td>{$row['customer_id']}</td>
<td>{$row['customer_name']}</td>
<td>{$row['income']}</td>
<td>{$row['loan_amount']}</td>
<td>{$row['loan_duration']}</td>
<td>{$row['dependents']}</td>
<td>{$row['education']}</td>
<td>{$row['employment']}</td>
<td>{$row['cibil_score']}</td>
<td>{$row['assets']}</td>
<td>{$row['emi']}</td>
<td>{$row['status']}</td>
<td>{$row['risk_level']}</td>
<td>{$row['created_at']}</td>
</tr>";
}

$pagination = "";
for($i=1;$i<=$total_pages;$i++){
$pagination .= "<button class='page-btn btn btn-sm btn-info m-1' data-page='$i'>$i</button>";
}

echo json_encode([
"table"=>$output,
"pagination"=>$pagination
]);
?>