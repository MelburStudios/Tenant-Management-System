<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM payments where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
    $$k=$val;
}
}
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
<div class="container-fluid">
    <form action="" id="manage-payment">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg"></div>
        <div class="form-group">
    <label class="control-label">House</label>
    <select id="house_id" class="custom-select select2">
        <option value=""></option>
        <?php 
        $house = $conn->query("SELECT * FROM houses ORDER BY house_no ASC");
        while($row=$house->fetch_assoc()):
        ?>
        <option value="<?php echo $row['id'] ?>">
            <?php echo $row['house_no'] ?>
        </option>
        <?php endwhile; ?>
    </select>
    <input type="hidden" name="tenant_id" id="tenant_id">
</div>
        
        <div class="form-group" id="details">
            
        </div>

        <div class="form-group">
            <label for="" class="control-label">Invoice: </label>
            <input type="text" class="form-control" name="invoice"  value="<?php echo isset($invoice) ? $invoice :'' ?>" >
        </div>
        <div class="form-group">
    <label class="control-label">Payment Date</label>
    <input type="date" class="form-control" name="date_created" value="<?php echo date('Y-m-d') ?>" required>
</div>
        <div class="form-group">
            <label for="" class="control-label">Amount Paid: </label>
            <input type="number" class="form-control text-right" step="any" name="amount"  value="<?php echo isset($amount) ? $amount :'' ?>" >
        </div>
</div>
    </form>
</div>
<div id="details_clone" style="display: none">
    <div class='d'>
        <large><b>Details</b></large>
        <hr>
        <p>Tenant: <span class="tenant_name"></span></p><br>
        <p>Monthly Rental Rate: <span class="price"></span></p>
        <p>Water Bill: <span class="water"></span></p>
        <p>Outstanding Balance: <span class="outstanding"></span></p>
        <p>Total Paid: <span class="total_paid"></span></p><br>
        <p>Rent Started: <span class='rent_started'></span></p>
        <p>Payable Months: <span class="payable_months"></span></p>
        <hr>
    </div>
</div>

<script>
$('#house_id').change(function(){

    var house_id = $(this).val();

    if(house_id == '')
        return false;

    start_load();

    $.ajax({
        url:'ajax.php?action=get_house_tenant',
        method:'POST',
        data:{id:house_id},
        success:function(resp){

            var data = JSON.parse(resp);

            if(data.id){

                // ✅ SET tenant_id (IMPORTANT)
                $('#tenant_id').val(data.id);

                // ✅ LOAD DETAILS DIRECTLY
                $.ajax({
                    url:'ajax.php?action=get_tdetails',
                    method:'POST',
                    data:{
                        id: data.id,
                        pid: '<?php echo isset($id) ? $id : '' ?>'
                    },
                    success:function(resp2){

                        if(resp2){
                            resp2 = JSON.parse(resp2);

                            var details = $('#details_clone .d').clone();

                            details.find('.tenant_name').text(resp2.name);
                            details.find('.price').text(resp2.price);
                            details.find('.outstanding').text(resp2.outstanding);
                            details.find('.water').text(resp2.water);
                            details.find('.total_paid').text(resp2.paid);
                            details.find('.rent_started').text(resp2.rent_started);
                            details.find('.payable_months').text(resp2.months);

                            $('#details').html(details);
                        }
                    }
                });

            }else{
                $('#tenant_id').val('');
                $('#details').html('');
            }

        },
        complete:function(){
            end_load();
        }
    });

});
$('.modal-footer .btn-primary').click(function(){

    var form = $('#manage-payment');

    start_load();

    $.ajax({
        url:'ajax.php?action=save_payment',
        method:'POST',
        data:form.serialize(),
        success:function(resp){

            console.log("SAVE RESPONSE:", resp);

            if(resp == 1){
                alert_toast("Payment successfully saved",'success');
                setTimeout(function(){
                    location.reload();
                },1000);
            }else{
                alert("Error saving payment: " + resp);
                end_load();
            }

        }
    });

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