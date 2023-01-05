<!-- Crée par SAID BOUSSIF DEV101 2021/2022 -->
<?php require "config.php"; ?>
<html>
	<head>
		<title>BOUSSIF SUPERMARKET</title>
		
		<link rel="stylesheet" href="css/bootstrap.min.css">
		
		<script src="js/jquery-3.6.0.min.js"></script>
		
		<script src="js/popper.min.js"></script>
		<script src="js/popper.min.js" ></script>
		
		<link rel='stylesheet' href='css/jquery-ui.css'>
		<script src="js/jquery-ui.min.js"></script>
		
	</head>
	<body>
		<div class='container pt-5'>
			<h1 class='text-center text-primary'>BOUSSIF SUPERMARKET</h1><hr>
			<?php
				if(isset($_POST["submit"])){
					$invoice_no=$_POST["invoice_no"];
					$invoice_date=date("Y-m-d",strtotime($_POST["invoice_date"]));
					$cname=mysqli_real_escape_string($con,$_POST["cname"]);
					$caddress=mysqli_real_escape_string($con,$_POST["caddress"]);
					$ccity=mysqli_real_escape_string($con,$_POST["ccity"]);
					$grand_total=mysqli_real_escape_string($con,$_POST["grand_total"]);
					
					$sql="insert into invoice (INVOICE_NO,INVOICE_DATE,CNAME,CADDRESS,CCITY,GRAND_TOTAL) values ('{$invoice_no}','{$invoice_date}','{$cname}','{$caddress}','{$ccity}','{$grand_total}') ";
					if($con->query($sql)){
						$sid=$con->insert_id;
						
						$sql2="insert into invoice_products (SID,PNAME,PRICE,QTY,TOTAL) values ";
						$rows=[];
						for($i=0;$i<count($_POST["pname"]);$i++)
						{
							$pname=mysqli_real_escape_string($con,$_POST["pname"][$i]);
							$price=mysqli_real_escape_string($con,$_POST["price"][$i]);
							$qty=mysqli_real_escape_string($con,$_POST["qty"][$i]);
							$total=mysqli_real_escape_string($con,$_POST["total"][$i]);
							$rows[]="('{$sid}','{$pname}','{$price}','{$qty}','{$total}')";
						}
						$sql2.=implode(",",$rows);
						if($con->query($sql2)){
							echo "<div class='alert alert-success'>Invoice Added. <a href='print.php?id={$sid}' target='_BLANK'>Click</a> here to Print Invoice</div>";
						}else{
							echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
						}
					}else{
						echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
					}
				}

				//  Crée par SAID BOUSSIF DEV101 2021/2022 

			?>
			<form method='post' action='index.php' autocomplete='off'>
				<div class='row'>
					<div class='col-md-4'>
						<h5 class='text-success'>Invoice Details</h5>
						<div class='form-group'>
							<label>Invoice No</label>
							<input type='text' name='invoice_no' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>Invoice Date</label>
							<input type='text' name='invoice_date' id='date' required class='form-control'>
						</div>
					</div>
					<div class='col-md-8'>
						<h5 class='text-success'>Customer Details</h5>
						<div class='form-group'>
							<label>Name</label>
							<input type='text' name='cname' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>Address</label>
							<input type='text' name='caddress' required class='form-control'>
						</div>
						<div class='form-group'>
							<label>City</label>
							<input type='text' name='ccity' required class='form-control'>
						</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12'>
						<h5 class='text-success'>Product Details</h5>
						<table class='table table-bordered'>
							<thead>
								<tr>
									<th>Product</th>
									<th>Price</th>
									<th>Qty</th>
									<th>Total</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id='product_tbody'>
								<tr>
									<td><input type='text' required name='pname[]' class='form-control'></td>
									<td><input type='text' required name='price[]' class='form-control price'></td>
									<td><input type='text' required name='qty[]' class='form-control qty'></td>
									<td><input type='text' required name='total[]' class='form-control total'></td>
									<td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td><input type='button' value='+ Add Row' class='btn btn-primary btn-sm' id='btn-add-row'></td>
									<td colspan='2' class='text-right'>Total</td>
									<td><input type='text' name='grand_total' id='grand_total' class='form-control' required></td>
								</tr>
							</tfoot>
						</table>
						<input type='submit' name='submit' value='Save Invoice' class='btn btn-success float-right'>
					</div>
				</div>
			</form>
		</div>
		<script>
			$(document).ready(function(){
				$("#date").datepicker({
					dateFormat:"dd-mm-yy"
				});
				
				$("#btn-add-row").click(function(){
					var row="<tr> <td><input type='text' required name='pname[]' class='form-control'></td> <td><input type='text' required name='price[]' class='form-control price'></td> <td><input type='text' required name='qty[]' class='form-control qty'></td> <td><input type='text' required name='total[]' class='form-control total'></td> <td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td> </tr>";
					$("#product_tbody").append(row);
				});
				
				$("body").on("click",".btn-row-remove",function(){
					if(confirm("Are You Sure?")){
						$(this).closest("tr").remove();
						grand_total();
					}
				});

				$("body").on("keyup",".price",function(){
					var price=Number($(this).val());
					var qty=Number($(this).closest("tr").find(".qty").val());
					$(this).closest("tr").find(".total").val(price*qty);
					grand_total();
				});
				
				$("body").on("keyup",".qty",function(){
					var qty=Number($(this).val());
					var price=Number($(this).closest("tr").find(".price").val());
					$(this).closest("tr").find(".total").val(price*qty);
					grand_total();
				});			
				
				function grand_total(){
					var tot=0;
					$(".total").each(function(){
						tot+=Number($(this).val());
					});
					$("#grand_total").val(tot);
				}
			});
		</script>
	</body>
</html>
<!-- Crée par SAID BOUSSIF DEV101 2021/2022 -->
