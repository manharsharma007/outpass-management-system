<div class="row content">
    <div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'super/menus/secondary_menu/_pass.php'); ?>
                </div>
            </div>
        </div>

        <div class="row page-heading">
            <h4>Process Return</h4>
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
					<input type="hidden" name="security_token" value="<?= $token ?>">
					<div class="form-element clearfix">
						<label for="name">Registration no / Roll no<sup>*</sup></label>
						<input type="text" id="user_id" name="user_id" value="<?= isset($form_var['user_id']) ? $form_var['user_id'] : '' ; ?>">
					</div>
					<div class="form-element clearfix">
						<label for="otp">OTP<sup>*</sup></label>
						<input type="text" id="otp" name="otp" value="<?= isset($form_var['otp']) ? $form_var['otp'] : '' ; ?>">
					</div>
					<div class="form-element clearfix">
						<label for="in_time">Expected IN time<sup>*</sup></label>
						<input type="text" id="in_time" class="datetimepicker" name="in_time" value="<?= isset($form_var['in_time']) ? $form_var['in_time'] : '' ; ?>">
					</div>

					<div class="form-element clearfix">
                        <input type="submit" class="__btn-round" name="submit">
                    </div>
                </form>
            </div>