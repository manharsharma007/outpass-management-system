<div class="row content">
    <div class="row-fixed">

        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'admin/menus/secondary_menu/_user.php'); ?>
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
                    <div class="form">
                        <div class="form-element clearfix">
                            <label for="">Name<span>*</span></label>
                            <input type="text" name="fname">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Email</label>
                            <input type="text" name="email">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Primary number<span>*</span></label>
                            <input type="text" name="p_no">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Secondary number</label>
                            <input type="text" name="s_no">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Registration number<span>*</span></label>
                            <input type="text" name="reg_no">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Roll no<span>*</span></label>
                            <input type="text" name="roll_no">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Meal Type<span>*</span></label>
                            <select name="meal_type">
                                <option value="VEG">Vegetarian</option>
                                <option value="NONVEG">Non Vegetarian</option>
                            </select>
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Room no<span>*</span></label>
                            <input type="number" name="room_no">
                        </div>
                        <div class="row clearfix">                            
                            <div class="form-element-half">
                                <label for="">Bed no<span>*</span></label>
                                <input type="number" name="bed_no">
                            </div>                            
                            <div class="form-element-half">
                                <label for="">Rack no<span>*</span></label>
                                <input type="number" name="rack_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-element-half clearfix">
                                <label for="">Active</label>
                                <input type="radio" name="active" value="yes">
                            </div>
                            <div class="form-element-half clearfix">
                                <label for="">Deactive</label>
                                <input type="radio" name="active" value="no">
                            </div>                            
                        </div>
                        <div class="form-element clearfix">
                            <input type="submit" class="__btn-round" name="submit">
                        </div>
                    </div>
                </form>
            </div>