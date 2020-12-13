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
                    	<legend>Change Personal</legend>
	                        <div class="form-element clearfix">
	                            <label for="">Username</label>
	                            <input type="text" name="uname" value="<?= (isset($settings->user_name)) ? $settings->user_name : '' ; ?>">
	                        </div>
	                        <div class="form-element clearfix">
	                            <label for="">Full name</label>
	                            <input type="text" name="name" value="<?= (isset($settings->full_name)) ? $settings->full_name : '' ; ?>">
	                        </div>
	                        <div class="form-element clearfix">
	                            <label for="">Email</label>
	                            <input type="text" name="email" value="<?= (isset($settings->user_email)) ? $settings->user_email : '' ; ?>">
	                        </div>


	                        <div class="form-element clearfix">
	                            <input type="submit" class="__btn-round" name="change_personal">
	                        </div>

	                </fieldset>
                    </div>
                </form>
            </div>