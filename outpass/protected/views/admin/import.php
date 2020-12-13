<div class="row content">
    <div class="row-fixed">

        <div class="row page-heading">
            <h4>Import Students</h4>
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
                <form action="#" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="security_token" value = "<?= $token ?>"/>
                    <div class="form">
                        <div class="form-element clearfix">
                            <label for="">Import<span>*</span></label>
                            <input type="file" name="csv_file">
                        </div>
                        <div class="form-element clearfix">
                            <input type="submit" class="__btn-round" name="submit">
                        </div>
                    </div>
                </form>
            </div>