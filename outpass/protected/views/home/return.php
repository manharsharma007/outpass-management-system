<div class="row content">
    <div class="row-fixed">

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
					<input type="hidden" name="type" value="">
					<div class="form-element clearfix">
						<label for="name">Registration no / Roll no<span>*</span></label>
						<input type="text" id="user_id" name="user_id" value="<?= isset($form_var['user_id']) ? $form_var['user_id'] : '' ; ?>">
					</div>

					<div class="form-element clearfix">
                        <input type="submit" class="__btn-round" name="return">
                    </div>
                </form>
            </div>