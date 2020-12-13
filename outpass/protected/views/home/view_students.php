<div class="row content">
	<div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'home/menus/secondary_menu/_view_user.php'); ?>
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

		<div class="row">
			<table class="layout">
				<tr>
					<td>Student ID</td>
					<td>Name</td>
					<td>Email</td>
					<td>Primary_number</td>
					<td>Registration no</td>
					<td>Roll no</td>
					<td>Status</td>
				</tr>
				<?php
					foreach ($students as $key => $value) {

						$status = '<span class="btn btn-wide btn-round bg-green">Active</span>';
						
						if($value->active <= 0)
		                {
		                    $status = '<span class="btn btn-wide btn-round bg-red">Deactive</span>';
		                }



						$tr =  '<tr class="edit_tr">';

						echo $tr.'
						<td>'.$value->stu_id.'</td>
								<td>'.$value->name.'</td>
								<td>'.$value->email.'</td>

								<td>'.$value->primary_no.'</td>
								<td>'.$value->reg_no.'</td>
								<td>'.$value->roll_no.'</td>
								<td>'.$status.'</td></tr>';
					}
				?>
			</table>
		</div>

		<div class="row pagination">
			<?= $pagination_link; ?>
		</div>