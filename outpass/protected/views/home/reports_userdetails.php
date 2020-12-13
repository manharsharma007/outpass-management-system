<div class="row content">
	<div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'home/menus/secondary_menu/_reports.php'); ?>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-80 page-heading">
				<h4>User Report</h4>
			</div>
		</div>
        <div class="row-fixed form">
                <form action="#" method="post">
                    <div class="form">
                        <div class="form-element clearfix">
                            <label for="">Registration number</label>
                            <input type="text" name="reg_no_filter">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Roll no</label>
                            <input type="text" name="roll_no_filter">
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Meal Type</label>
                            <select name="meal_type_filter">
                                <option value="">------</option>
                                <option value="VEG">Vegetarian</option>
                                <option value="NONVEG">Non Vegetarian</option>
                            </select>
                        </div>
                        <div class="form-element clearfix">
                            <label for="">Room no</label>
                            <input type="number" name="room_no_filter">
                        </div>
                        <div class="row clearfix">                            
                            <div class="form-element-half">
                                <label for="">Bed no</label>
                                <input type="number" name="bed_no_filter">
                            </div>                            
                            <div class="form-element-half">
                                <label for="">Rack no</label>
                                <input type="number" name="rack_no_filter">
                            </div>
                        </div>
                        <div class="form-element clearfix">
                            <input type="submit" class="__btn-round" name="submit" value="Get Details">
                        </div>
                    </div>
                </form>
            </div>