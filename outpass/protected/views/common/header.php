<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Productive</title>
	<?php Loader::addStyle('style.css'); ?>
	<?php Loader::addStyle('css/font-awesome.min.css'); ?>
	<?php Loader::addStyle('jquery.datetimepicker.css'); ?>
	<?php Loader::addScript('jquery.js'); ?>
</head>
<body>
	<div class="container">
		<div class="row header">
			<div class="row-fixed">
				<div class="col logo"><h1><a href="<?= SITE_URL ?>">Outpass Management <sup>beta</sup></a></h1></div>
			</div>
		</div>

		<div class="row">
			<div class="row-fixed">
				<div class="primary-menu">
					<?php
						if(session::get('user_role') == 'SA')
						{
							include_once(VIEWS.DS.'common/menus/primary_menu/_superadmin.php');
						}
						elseif(session::get('user_role') == 'A')
						{
							include_once(VIEWS.DS.'common/menus/primary_menu/_admin.php');
						}	
						else
						{
							include_once(VIEWS.DS.'common/menus/primary_menu/_home.php');							
						}
					?>
				</div>
			</div>
		</div>