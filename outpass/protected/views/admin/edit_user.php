<div class="row content">
    <div class="row-fixed">

        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'admin/menus/secondary_menu/_view_user.php'); ?>
                </div>
            </div>
        </div>

        <div class="row page-heading">
            <h4>Add Student</h4>
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
        	<div class="row-fixed form">
                <form action="#" method="post">
						<input type="hidden" name="security_token" value = "<?= $token ?>"/>
						<input type="hidden" value="<?= $_GET['id'] ?>" name="id">
						<div class="form-element clearfix">
							<a href="<?php echo SITE_URL.'admin/edituser/true?id='.$_GET['id'].'&token='.$token; ?>" class="btn btn-red btn-wide btn-round">Click to regenerate password?</a>
						</div>
					     <div class="form-element clearfix">
							<label for="name">Name <sup>*</sup></label>
							<input type="text" id="name" name="name" value="<?= isset($name) ? $name : '' ; ?>">
						</div>
						<div class="form-element clearfix">
							<label for="name">Email <sup>*</sup></label>
							<input type="text" id="name" name="email" value="<?= isset($email) ? $email : '' ; ?>">
						</div>
						<div class="form-element clearfix">
							<label for="username">Username <sup>*</sup></label>
							<input type="text" id="username" name="username" value="<?= isset($username) ? $username : '' ; ?>">
						</div>
						<div class="form-element clearfix">
							<label for="name">Role</label>
							<select name="role">
								<option value="super_user" <?php echo (isset($role) && $role == 'SA') ? 'selected' : '' ?>>Super User</option>
								<option value="admin" <?php echo (isset($role) && $role == 'A') ? 'selected' : '' ?>>Admin</option>
								<option value="manager" <?php echo (isset($role) && $role == 'M') ? 'selected' : '' ?>>Manager</option>
							</select>
						</div>
						<div class="form-element clearfix">
							<label for="name">Status <sup>*</sup></label>
							<select name="status">
								<option value="yes" <?php echo (isset($status) && $status == '1') ? 'selected' : '' ?>>Active</option>
								<option value="no" <?php echo (isset($status) && $status == '0') ? 'selected' : '' ?>>Deactive</option>
							</select>
						</div>
						
	                    <div class="form-element clearfix">
	                        <input type="submit" class="__btn-round" name="submit">
	                    </div>
				</form>
				
			</div>