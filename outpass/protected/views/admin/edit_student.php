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
            <h4>Edit Student</h4>
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
                            <label for="">Name<span>*</span></label>
                            <input type="text" name="fname" value="<?= isset($fname) ? $fname : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Email</label>
                            <input type="text" name="email" value="<?= isset($email) ? $email : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Primary number<span>*</span></label>
                            <input type="text" name="p_no" value="<?= isset($p_no) ? $p_no : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Secondary number</label>
                            <input type="text" name="s_no" value="<?= isset($s_no) ? $s_no : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Registration number<span>*</span></label>
                            <input type="text" name="reg_no" value="<?= isset($reg_no) ? $reg_no : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Roll no<span>*</span></label>
                            <input type="text" name="roll_no" value="<?= isset($roll_no) ? $roll_no : '' ; ?>">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Meal Type<span>*</span></label>
                            <select name="meal_type">
                                <option value="VEG <?php echo (isset($meal_type) && $meal_type == 'VEG') ? 'selected' : '' ?>">Vegeterian</option>
                                <option value="NONVEG" <?php echo (isset($meal_type) && $meal_type == 'NONVEG') ? 'selected' : '' ?>>Non Vegetarian</option>
                            </select>
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Room no<span>*</span></label>
                            <input type="number" name="room_no" value="<?= isset($room_no) ? $room_no : '' ; ?>">
                        </div>
                        <div class="row clearfix">                            
                            <div class="form-element-half">
                                <label for="">Bed no<span>*</span></label>
                                <input type="number" name="bed_no" value="<?= isset($bed_no) ? $bed_no : '' ; ?>">
                            </div>                            
                            <div class="form-element-half">
                                <label for="">Rack no<span>*</span></label>
                                <input type="number" name="rack_no" value="<?= isset($rack_no) ? $rack_no : '' ; ?>">
                            </div>
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