<div class="row content">
    <div class="row-fixed">



        <div class="row">
			<div class="row-fixed">
				<div class="secondary-menu">
					<ul>
						<li><a href="<?= SITE_URL.'home/settings' ?>">Personal</a></li>
						<li><a href="<?= SITE_URL.'home/change_password' ?>">Password</a></li>
					</ul>
				</div>
			</div>
		</div>

        <div class="row page-heading">
            <h4>Settings</h4>
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

                    <div class="form">

                        <fieldset>
                        	<legend>Change Password</legend>

	                        <div class="form-element clearfix">
	                            <label for="">Old Password</label>
	                            <input type="text" name="old_pass">
	                        </div>

	                        <div class="form-element clearfix">
	                            <label for="">New Password</label>
	                            <input type="text" name="new_pass">
	                        </div>

	                        <div class="form-element clearfix">
	                            <label for="">Confirm Password</label>
	                            <input type="text" name="confirm_pass">
	                        </div>

	                        <div class="form-element clearfix">
	                            <input type="submit" class="__btn-round" name="change_pass">
	                        </div>

                        </fieldset>
                    </div>
                </form>
            </div>