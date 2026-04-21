<?php include 'db_connect.php' ?>

<?php 
$tenants =$conn->query("SELECT t.*,concat(t.lastname,', ',t.firstname,' ',t.middlename) as name,h.house_no,h.price FROM tenants t inner join houses h on h.id = t.house_id where t.id = {$_GET['id']} ");
foreach($tenants->fetch_array() as $k => $v){
	if(!is_numeric($k)){
		$$k = $v;
	}
}
$start = new DateTime($date_in);
$today = new DateTime();

$months = ($today->format('Y') - $start->format('Y')) * 12;
$months += ($today->format('m') - $start->format('m'));
$months += 1; // include current month
$payable = $price * $months;
$paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenant_id =".$id);
$last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$id." order by unix_timestamp(date_created) desc limit 1");

$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['date_created'])) : 'N/A';

// 🔥 GET WATER
$water = $conn->query("SELECT amount FROM water_readings WHERE tenant_id = ".$id." ORDER BY billing_month DESC LIMIT 1");
$water_total = $water->num_rows > 0 ? $water->fetch_array()['amount'] : 0;

// 🔥 APPLY PAYMENT TO WATER FIRST
if($paid >= $water_total){
    $remaining_payment = $paid - $water_total;
    $remaining_water = 0;
}else{
    $remaining_payment = 0;
    $remaining_water = $water_total - $paid;
}

// 🔥 APPLY REMAINING TO RENT
if($remaining_payment >= $payable){
    $remaining_rent = 0;
}else{
    $remaining_rent = $payable - $remaining_payment;
}

// FINAL OUTSTANDING
$outstanding = $remaining_water + $remaining_rent;

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-4">
				<div id="details">
					<large><b>Details</b></large>
					<hr>
					<p>Tenant: <b><?php echo ucwords($name) ?></b></p><br>
					<p>Monthly Rental Rate: <b><?php echo number_format($price,2) ?></b></p><br>
					<p>Water Bill: <b><?php echo number_format($water_total,2) ?></b></p>
					<p>Total Outstanding Balance: <b><?php echo number_format($outstanding,2) ?></b></p><br>
					<p>Total All Time Paid: <b><?php echo number_format($paid,2) ?></b></p><br>
					<p>Rent Started: <b><?php echo date("M d, Y",strtotime($date_in)) ?></b></p>
					<p>Payable Months: <b><?php echo $months ?></b></p>
				</div>
			</div>
			<div class="col-md-8">
				<large><b>Payment List</b></large>
					<hr>
				<table class="table table-condensed table-striped">
					<thead>
						<tr>
							<th>Date</th>
							<th>Invoice</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$payments = $conn->query("SELECT * FROM payments where tenant_id = $id");
						if($payments->num_rows > 0):
						while($row=$payments->fetch_assoc()):
						?>
					<tr>
						<td><?php echo date("M d, Y",strtotime($row['date_created'])) ?></td>
						<td><?php echo $row['invoice'] ?></td>
						<td class='text-right'><?php echo number_format($row['amount'],2) ?></td>
					</tr>
					<?php endwhile; ?>
					<?php else: ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<style>
	#details p {
		margin: unset;
		padding: unset;
		line-height: 1.3em;
	}
	td, th{
		padding: 3px !important;
	}
</style><footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
   <p class="text-muted mb-1 mb-md-0">Developed at <a href="https://melbur.co.ke" target="_blank">Melbur Studios</a></p>
   
</footer>