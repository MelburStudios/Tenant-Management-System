<?php include 'db_connect.php'; ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <b>Water Billing</b>
            </div>
            <div class="card-body">
                
                <form id="manage-water">
<input type="hidden" name="id">
                    <div class="form-group">
                        <label>House Number</label>
<select name="house_id" class="form-control select2" required>
    <option value=""></option>
    <?php 
    $houses = $conn->query("SELECT * FROM houses ORDER BY house_no ASC");
    while($row=$houses->fetch_assoc()):
    ?>
    <option value="<?php echo $row['id'] ?>"><?php echo $row['house_no'] ?></option>
    <?php endwhile; ?>
</select>

<div class="form-group mt-2">
    <label>Tenant</label>
    <input type="text" id="tenant_name" class="form-control" readonly>
    <input type="hidden" name="tenant_id">
</div>
                    </div>

                    <div class="form-group">
                        <label>Previous Reading</label>
                        <input type="number" step="any" name="prev_reading" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Current Reading</label>
                        <input type="number" step="any" name="curr_reading" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Units Used</label>
                        <input type="number" step="any" name="units_used" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Rate (KES per unit)</label>
                        <input type="number" step="any" name="rate" value="130" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="any" name="amount" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Billing Month</label>
                        <input type="month" name="billing_month" class="form-control" required>
                    </div>

                    <button class="btn btn-primary">Save Water Record</button>

                </form>

                <hr>

                <h5>Water Records</h5>

                <table class="table table-bordered" id="water_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tenant</th>
                            <th>Units</th>
                            <th>Amount</th>
                            <th>Month</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=1;
                        $qry = $conn->query("SELECT w.*, concat(t.lastname,', ',t.firstname,' ',t.middlename) as name FROM water_readings w INNER JOIN tenants t ON t.id=w.tenant_id ORDER BY w.id DESC");
                        while($row=$qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $row['name'] ?></td>
                            <td><?php echo $row['units_used'] ?></td>
                            <td><?php echo number_format($row['amount'],2) ?></td>
                            <td><?php echo $row['billing_month'] ?></td>
                            <td><button class="btn btn-sm btn-danger delete_water" data-id="<?php echo $row['id'] ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
$('.select2').select2({
    placeholder:"Select Tenant",
    width:"100%"
});
$(document).on('change', '[name="house_id"]', function(){

    var house_id = $(this).val();

    if(house_id){

        $.ajax({
            url:'ajax.php?action=get_house_tenant',
            method:'POST',
            data:{id:house_id},
            success:function(resp){

                var data = JSON.parse(resp);

                if(data.id){
                    $('#tenant_name').val(data.name);
                    $('[name="tenant_id"]').val(data.id);

                    // 🔥 ALSO LOAD LAST READING
                    $.ajax({
                        url:'ajax.php?action=get_last_reading',
                        method:'POST',
                        data:{id:data.id},
                        success:function(r){

                            r = r.trim();

                            if(r !== ''){
                                $('[name="prev_reading"]').val(r);
                                $('[name="prev_reading"]').prop('readonly', true);
                            }else{
                                $('[name="prev_reading"]').val('');
                                $('[name="prev_reading"]').prop('readonly', false);
                            }
                        }
                    });

                }else{
                    $('#tenant_name').val('');
                    $('[name="tenant_id"]').val('');
                    $('[name="prev_reading"]').val('');
                }
            }
        });

    }

});

$(document).on('input', '[name="prev_reading"], [name="curr_reading"], [name="rate"]', function(){
    var prev = parseFloat($('[name="prev_reading"]').val()) || 0;
    var curr = parseFloat($('[name="curr_reading"]').val()) || 0;
    var rate = parseFloat($('[name="rate"]').val()) || 0;

    var units = curr - prev;
    if(units < 0) units = 0;

    var amount = units * rate;

    $('[name="units_used"]').val(units);
    $('[name="amount"]').val(amount);
});

$(document).off('submit', '#manage-water').on('submit', '#manage-water', function(e){
    e.preventDefault();

    var form = $(this);

    if(form.hasClass('submitting')) return false;
    form.addClass('submitting');

    start_load();

    $.ajax({
        url:'ajax.php?action=save_water',
        method:'POST',
        data:form.serialize(),
        success:function(resp){

            if(resp.trim() == "1"){
                alert_toast("Water record saved",'success');
                setTimeout(function(){
                    location.reload();
                },1000);
            }else{
                console.log(resp);
                alert("Error: " + resp);
            }

            form.removeClass('submitting');
            end_load();
        }
    });
});
$(document).on('click', '.delete_water', function(){
    var id = $(this).attr('data-id');

    if(confirm("Delete this water record?")){
        $.ajax({
            url:'ajax.php?action=delete_water',
            method:'POST',
            data:{id:id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Deleted successfully",'success');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }
            }
        });
    }
});
</script>
<footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
   <p class="text-muted mb-1 mb-md-0">Developed at <a href="https://melbur.co.ke" target="_blank">Melbur Studios</a></p>
</footer>