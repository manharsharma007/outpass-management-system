<div class="row content">
	<div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'home/menus/secondary_menu/_pass.php'); ?>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-80 page-heading">
				<h4>View Pass</h4>
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

		<div class="row filters">
						<form action="#" method="post">							
							<div class="col-4">
								<div class="field-row">
									<label>Type
									</label>
									<select name="type_filter" autofocus>
										<option value="">Select -----</option>
										<option value="ISSUED">Issued</option>
										<option value="COMPLETE">Returned</option>
									</select>
								</div>
							</div>

							
							<div class="col-4">
								<div class="field-row">
									<label>From  
									</label>
									<input type="text" class="datepicker" name="from_filter" placeholder="Enter date">
									
								</div>
							</div>		

										
							<div class="col-4">
								<div class="field-row">
									<label>To
									</label>
									<input type="text" class="datepicker" name="to_filter" placeholder="Enter date">
								</div>
							</div>
							
							<div class="col-4">
								<input type="submit"name="filter" value="Filter Results" class="button2">
							</div>
							</form>
					</div>

		<div class="row">
			<table class="layout">
				<tr>
					<td>Name</td>
					<td>Registration no</td>
					<td>In time</td>
					<td>Expected time</td>
					<td>Out time</td>
					<td>Status</td>
					<td>Issued date</td>
					<td></td>
				</tr>
				<?php
					foreach ($issued as $key => $value) {

						if($value->status == 'ISSUED')
						{
							$status = '<span class="btn btn-wide btn-round bg-yellow">Pending</span>';
						}
						else
						{
							$status = '<span class="btn btn-wide btn-round bg-green">Returned</span>';
						}

						$out_time = strtotime($value->expected_time);
				        $in_time = strtotime($value->in_time);

				        if ($in_time > $out_time)
				        {
				            $status2 = '<span class="btn btn-wide btn-round bg-red">Late</span>';
				        }
				        elseif($in_time < $out_time && $value->status == 'COMPLETE')
				        {
				            $status2 = '<span class="btn btn-wide btn-round bg-green">In Time</span>';
				        }
				        else
				        {
				            $status2 = '<span class="btn btn-wide btn-round bg-green">Pending</span>';
				        }


						$value->in_time = (ValidateDateTime($value->in_time, DATE_FORMAT)) ? date_format(date_create($value->in_time), 'M d, Y h:i A') : '------';
						$value->expected_time = (ValidateDateTime($value->expected_time, DATE_FORMAT)) ? date_format(date_create($value->expected_time), 'M d, Y h:i A') : '------';
						$value->out_time = (ValidateDateTime($value->out_time, DATE_FORMAT)) ? date_format(date_create($value->out_time), 'M d, Y h:i A') : '------';
						$value->issued_date = (ValidateDateTime($value->issued_date, DATE_FORMAT)) ? date_format(date_create($value->issued_date), 'M d, Y h:i A') : '------';


						echo '<tr>
								<td>'.$value->name.'</td>
								<td>'.$value->reg_no.'</td>
								<td>'.$value->in_time.'</td>
								<td>'.$value->expected_time.'</td>
								<td>'.$value->out_time.'</td>
								<td>'.$status.'</td>
								<td>'.$value->issued_date.'</td>
								<td>'.$status2.'</td></tr>';
					}
				?>
			</table>
		</div>

		<div class="row pagination">
			<?= $pagination_link; ?>
		</div>