<div class="row content">
    <div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'home/menus/secondary_menu/_create_pass.php'); ?>
                </div>
            </div>
        </div>

        <div class="row page-heading">
            <h4>Single Pass</h4>
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

                    <?php if(isset($stage) && !empty($stage) && $stage == 'CREATE' && session::get('otp_data') != null)
                    {
                        ?>
                        <input type="hidden" name="security_token" value = "<?= $token ?>"/>
                        <div class="form">
                            <div class="form-element clearfix">
                                <label for="">Enter OTP<span>*</span></label>
                                <input type="text" name="otp" value="">
                            </div>
                            <div class="form-element clearfix">
                                <input type="submit" class="__btn-round" name="create">
                            </div>
                        </div>
                        <?php
                    }
                    elseif(isset($stage) && !empty($stage) && $stage == 'OTP' && session::get('otp_data') != null)
                    {
                        ?>
                        <input type="hidden" name="security_token" value = "<?= $token ?>"/>
                        <div class="form">
                            <div class="form-element clearfix">
                                <label for="">Reg no./Roll no.<span>*</span></label>
                                <input type="text" name="reg_no" value="<?php echo $reg_no; ?>">
                            </div>
                            <div class="form-element clearfix">
                                <label for="">Out time<span>*</span></label>
                                <input type="text" class="datetimepicker" name="out_time" value="<?php echo $curr_time; ?>">
                            </div>
                            <div class="form-element clearfix">
                                <label for="">Expected IN time<span>*</span></label>
                                <input type="text" class="datetimepicker" name="in_time">
                            </div>
                            <div class="form-element clearfix">
                                <input type="submit" class="__btn-round" name="otp">
                            </div>
                        </div>
                        <?php
                    }
                    else
                    {
                    ?>
                        <input type="hidden" name="security_token" value = "<?= $token ?>"/>
                        <div class="form">
                            <div class="form-element clearfix">
                                <label for="">Reg no./Roll no.<span>*</span></label>
                                <input type="text" name="reg_no">
                            </div>
                            <div class="form-element clearfix">
                                <input type="submit" class="__btn-round" name="search">
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </form>
            </div>