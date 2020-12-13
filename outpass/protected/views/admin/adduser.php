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
            <h4>Add User</h4>
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
                            <label for="">Username<span>*</span></label>
                            <input type="text" name="user_name">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Password</label>
                            <input type="password" name="pass">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Confirm Password<span>*</span></label>
                            <input type="password" name="confirm_pass">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">User Type<span>*</span></label>
                            <select name="user_type" id="">
                                <option value="super_user">Super User</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                            </select>
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