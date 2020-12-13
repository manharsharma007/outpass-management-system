
				<div class="row content">
					

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
			<div class="row result-table-wrapper">
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
							foreach ($history as $key => $value) {

								if($value->status == 'ISSUED')
								{
									$status = '<span class="btn btn-wide btn-round bg-yellow">Issued</span>';
								}
								elseif($value->status == 'PENDING')
								{
									$status = '<span class="btn btn-wide btn-round bg-red">Pending</span>';
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
</div>