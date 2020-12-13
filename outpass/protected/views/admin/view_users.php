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
				<h4>Users</h4>
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
									<label>Name
									</label>
									<input type="text" name="name_filter" placeholder="Enter Name" autofocus>
								</div>
							</div>

							
							<div class="col-5">
								<div class="field-row">
									<label>Email 
									</label>
									<input type="text" name="email_filter" placeholder="Enter email">
									
								</div>
							</div>		

										
							<div class="col-5">
								<div class="field-row">
									<label>Username
									</label>
									<input type="text" name="username_filter" placeholder="Enter username">
								</div>
							</div>
							<div class="col-5">
								<div class="field-row">
									<label>User Role
									</label>											
		                            <select name="user_role_filter" id="">
		                                <option value="SA">Super User</option>
		                                <option value="A">Admin</option>
		                                <option value="M">Manager</option>
		                            </select>
								</div>
							</div>
							
							<div class="col-5">
								<input type="submit" name="filter" value="Filter Results" class="button2">
							</div>
							</form>
					</div>

		<form action="<?= SITE_URL ?>admin/delete_users" method="post">
		<div class="row helper-menu">
			<ul>
				<li><button type="submit" name="submit" class="__btn __btn-link-small __btn-grey __btn-round">Delete</button></li>
			</ul>
		</div>

		<div class="row">
			<table class="layout">
				<tr>
					<td><input type="checkbox"  class='item_check_all' onclick="select_all_item()"></td>
					<td>User ID</td>
					<td>Name</td>
					<td>Username</td>
					<td>Email</td>
					<td>Status</td>
					<td>Role</td>
					<td></td>
				</tr>
				<?php
					foreach ($users as $key => $value) {

						$status = '<span class="btn btn-wide btn-round bg-green">Active</span>';
						
						if($value->active == 0)
		                {
		                    $status = '<span class="btn btn-wide btn-round bg-red">Deactive</span>';
		                }
		                if($value->user_type == 'SA')
		                {
		                    $value->user_type = '<span class="btn btn-wide btn-round bg-green">Super User</span>';
		                }
		                if($value->user_type == 'A')
		                {
		                    $value->user_type = '<span class="btn btn-wide btn-round bg-yellow">Admin</span>';
		                }
		                if($value->user_type == 'M')
		                {
		                    $value->user_type = '<span class="btn btn-wide btn-round bg-red">Manager</span>';
		                }



						$tr =  '<tr class="edit_tr" id="tr_'.$value->user_id.'" data-id="'.$value->user_id.'">';

						echo $tr.'<td><input type="checkbox" value="'.$value->user_id.'" name="item[]" class="item_case"></td>
						<td>'.$value->user_id.'</td>
								<td>'.$value->full_name.'</td>
								<td>'.$value->user_name.'</td>
								<td>'.$value->user_email.'</td>

								<td>'.$status.'</td>
								<td>'.$value->user_type.'</td>
								<td><a href="'.SITE_URL.'admin/edituser/?id='.$value->user_id.'">Edit</a></td></tr>';
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