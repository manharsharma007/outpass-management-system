<div class="row content">
	<div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'admin/menus/secondary_menu/_view_user.php'); ?>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-80 page-heading">
				<h4>Students</h4>
			</div>
		</div>
		<?php
                if(isset($error['error_code']) && isset($error['message'])) {

                    if($error['error_code'] == SUCCESS)
                        echo '<div class="msg success"><p>'.$error["message"].'</p>';
                    elseif($error['error_code'] == WARNING)
                        echo '<div class="msg warning"><p>'.$error["message"].'</p>';
                    elseif($error['error_code'] == ERROR)
                        echo '<div class="msg fail"><p>'.$error["message"].'</p>';

                    echo'</div>';
                }
        ?>

		<div class="ajax_response"></div>

		<div class="row filters">
						<form action="#" method="post">							
							<div class="col-5">
								<div class="field-row">
									<label>Reg no
									</label>
									<input type="text" name="reg_no_filter" placeholder="Enter Reg no." autofocus>
								</div>
							</div>

							
							<div class="col-5">
								<div class="field-row">
									<label>Meal type 
									</label>
		                            <select name="meal_type_filter">
		                                <option value="VEG">Vegetarian</option>
		                                <option value="NONVEG">Non Vegetarian</option>
		                            </select>
									
								</div>
							</div>		

										
							<div class="col-5">
								<div class="field-row">
									<label>Room no
									</label>
									<input type="text" name="room_no_filter" placeholder="Ex. 4486" autofocus>
								</div>
							</div>
							<div class="col-5">
								<div class="field-row">
									<label>Bed no
									</label>
									<input type="email" name="bed_no_filter" placeholder="Enter bed no">
								</div>
							</div>
							
							<div class="col-5">
								<input type="submit" name="filter" value="Filter Results" class="button2">
							</div>
							</form>
					</div>


		<form action="<?= SITE_URL ?>admin/delete_students" method="post">
		<div class="row helper-menu">
			<ul>
				<li><a class="__btn __btn-link-small __btn-grey __btn-round" href="?export"><i class="fa fa-download" aria-hidden="true"></i> Export</a></li>
				<li><a class="__btn __btn-link-small __btn-grey __btn-round" href="<?= SITE_URL ?>admin/import"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Import</a></li>
				<li><button type="submit" name="submit" class="__btn __btn-link-small __btn-grey __btn-round">Delete</button></li>
			</ul>
		</div>
		<div class="row">
			<table class="layout">
				<tr>
					<td><input type="checkbox"  class='item_check_all' onclick="select_all_item()"></td>
					<td>Student ID</td>
					<td>Name</td>
					<td>Email</td>
					<td>Primary_number</td>
					<td>Registration no</td>
					<td>Roll no</td>
					<td>Status</td>
					<td></td>
				</tr>
				<?php
					foreach ($students as $key => $value) {

						$status = '<span class="btn btn-wide btn-round bg-green">Active</span>';
						
						if($value->active <= 0)
		                {
		                    $status = '<span class="btn btn-wide btn-round bg-red">Deactive</span>';
		                }



						$tr =  '<tr class="edit_tr" id="tr_'.$value->stu_id.'" data-id="'.$value->stu_id.'">';

						echo $tr.'<td><input type="checkbox" value="'.$value->stu_id.'" name="item[]" class="item_case"></td>
						<td>'.$value->stu_id.'</td>
								<td>'.$value->name.'</td>
								<td>'.$value->email.'</td>

								<td>'.$value->primary_no.'</td>
								<td>'.$value->reg_no.'</td>
								<td>'.$value->roll_no.'</td>
								<td>'.$status.'</td>
								<td><a href="'.SITE_URL.'admin/editstudent/?id='.$value->stu_id.'">Edit</a></td></tr>';
					}
				?>
			</table>
		</div>
</form>

		<div class="row pagination">
			<?= $pagination_link; ?>
		</div>
    <script>


function select_all_item() {
    $('input[class=item_case]:checkbox').each(function(){ 
        if($('input[class=item_check_all]:checkbox:checked').length == 0){ 
            $(this).prop("checked", false); 
        } else {
            $(this).prop("checked", true); 
        } 
    });
}
					</script>