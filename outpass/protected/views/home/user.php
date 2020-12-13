
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
<div class="ajax_response"></div>
								<div class="row result-table-wrapper">
									<table class="layout">
											<tr>
												<td>User ID</td>
												<td>Name</td>
												<td>Registration no</td>
												<td>Out time</td>
												<td>Expected time</td>
												<td>Issued Date</td>
												<td></td>
											</tr>

											<?php
												if(isset($pass))
													foreach ($pass as $key => $value) {

														$value->expected_time = (ValidateDateTime($value->expected_time, DATE_FORMAT)) ? date_format(date_create($value->expected_time), 'M d, Y h:i A') : '------';
														$value->out_time = (ValidateDateTime($value->out_time, DATE_FORMAT)) ? date_format(date_create($value->out_time), 'M d, Y h:i A') : '------';
														$value->date = (ValidateDateTime($value->date, DATE_FORMAT)) ? date_format(date_create($value->date), 'M d, Y h:i A') : '------';



														echo '<tr class="edit_tr" id="tr_'.$value->id.'"><td>'.$value->stu_id.'</td><td>'.$value->name.'</td>
														<td>'.$value->reg_no.'</td><td>'.$value->out_time.'</td>
														<td>'.$value->expected_time.'</td><td>'.$value->date.'</td>
														<td><a href="#" class"ret" id="'.$value->id.'"><i class="fa fa-trash-o" aria-hidden="true"></i> Return</a></td></tr>';
													}
											?>
									</table>
								</div>
				</div>
<script>
	
jQuery(document).ready(function($)
{
	$(".edit_tr a").on('click', function(e)
	{
		e.preventDefault();
	var ID=$(this).attr('id');
	var table_row = $('#tr_'+ID);


	$.ajax({ //ajax form submit
                url : "<?= SITE_URL ?>home/processreturn",
                type: "GET",
                data : 'id='+ID,
                dataType : "json",
                contentType: false,
                cache: false,
                processData:false
            }).done(function(res){
                    $(".ajax_response").html( res.text );
                    if(res.code == 1)
                    	table_row.remove();
            });
     });

});
</script>