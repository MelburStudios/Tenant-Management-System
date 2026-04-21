<?php include('db_connect.php');?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
<div class="container-fluid">
	
	<div class="col-lg-12 mt-5">
		<div class="row">
			<!-- FORM Panel -->
			
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>House List</b>
					</div>
					<div class="card-body">
						<table id="example" class="display table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">House</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$house = $conn->query("SELECT h.*,c.name as cname FROM houses h inner join categories c on c.id = h.category_id order by id asc");
								while($row=$house->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p>House #: <?php echo $row['house_no'] ?></p>
										<p><small>House Type: <?php echo $row['cname'] ?></small></p>
										<p><small>Description: <?php echo $row['description'] ?></small></p>
										<p><small>Price: <?php echo number_format($row['price'],2) ?></small></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_house" type="button" data-id="<?php echo $row['id'] ?>"  data-house_no="<?php echo $row['house_no'] ?>" data-description="<?php echo $row['description'] ?>" data-category_id="<?php echo $row['category_id'] ?>" data-price="<?php echo $row['price'] ?>" >Edit</button>
										<button class="btn btn-sm btn-danger delete_house" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<!-- EDIT HOUSE MODAL -->
<div id="house_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
  <div style="background:#fff; width:400px; margin:80px auto; padding:20px; border-radius:8px; position:relative;">
    
    <h4>Edit House</h4>

    <form id="manage-house">
      <input type="hidden" name="id">

      <div id="msg"></div>

      <div class="form-group">
        <label>House Number</label>
        <input type="text" name="house_no" class="form-control" required>
      </div>

      <div class="form-group">
        <label>House Type</label>
        <select name="category_id" class="form-control" required>
          <?php 
          $cat = $conn->query("SELECT * FROM categories");
          while($row=$cat->fetch_assoc()):
          ?>
          <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
      </div>

      <div class="form-group">
        <label>Price</label>
        <input type="number" name="price" class="form-control" required>
      </div>

      <br>
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" id="close_modal">Cancel</button>
    </form>
  </div>
</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p {
		margin: unset;
		padding: unset;
		line-height: 1em;
	}
</style>
<script>
	$('#manage-house').on('reset',function(e){
		$('#msg').html('')
	})
	$('#manage-house').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_house',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					$('#msg').html('<div class="alert alert-danger">House number already exist.</div>')
					end_load()
				}
			}
		})
	})
	$(document).on('click', '.edit_house', function(){
    var form = $('#manage-house')[0];
    form.reset();

    $('#manage-house [name="id"]').val($(this).attr('data-id'));
    $('#manage-house [name="house_no"]').val($(this).attr('data-house_no'));
    $('#manage-house [name="description"]').val($(this).attr('data-description'));
    $('#manage-house [name="price"]').val($(this).attr('data-price'));
    $('#manage-house [name="category_id"]').val($(this).attr('data-category_id'));

    $('#house_modal').fadeIn();
});
	$(document).on('click', '.delete_house', function(){
    var id = $(this).attr('data-id');

    if(confirm("Are you sure you want to delete this house?")){
        delete_house(id);
    }
});
	function delete_house($id){
	start_load()
	$.ajax({
		url:'ajax.php?action=delete_house',
		method:'POST',
		data:{id:$id},
		success:function(resp){
			if(resp==1){
				alert_toast("Data successfully deleted",'success')
				setTimeout(function(){
					location.reload()
				},1500)
			}
		}
	})
}
	// $('table').dataTable()
	$(document).on('click', '#close_modal', function(){
    $('#house_modal').fadeOut();
});
</script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
	new DataTable('#example', {
    layout: {
        topStart: {
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5']
        }
    }
});
</script><footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
   <p class="text-muted mb-1 mb-md-0">Developed at <a href="https://melbur.co.ke" target="_blank">Melbur Studios</a></p>
   
</footer>