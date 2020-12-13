<div class="row content">
    <div class="row-fixed">
        
        <div class="row">
            <div class="row-fixed">
                <div class="secondary-menu">
                    <?php include_once(VIEWS.DS.'home/menus/secondary_menu/_reports.php'); ?>
                </div>
            </div>
        </div>

        <div class="row page-heading">
            <h4>Reports</h4>
        </div>
					

		<div class="row clearfix">
			<div class="row graph_box">

				<div class="row clearfix dashboard-heading">
					<h5>Total Pass Reports</h5>
				</div>

				<?php 
					if($total_students == 0 && $total_issued == 0 && $total_returned == 0 )

					{
						?>
							<div class="row">
								<div class="msg notice"><p>No details available</p></div>
							</div>
						<?php
					}
					else
					{
						if($internet == true)
						{
						?>

							<div class="row">
								<noscript>
									<div class="msg notice"><p>Javascript is required to properly run the application.</p></div>
								</noscript>
							</div>
							<div id="overview" style="width: 600px; height: 400px; margin:0 auto;"></div>
						<?php
						}
						else
						{
							?>
							<div class="row">
								<div class="msg notice"><p>Graphs will be available if your are connected to the internet</p></div>
							</div>
							<table style="width:400px;" class="layout">
								<thead>
									<tr><td>Data</td><td>Value</td></tr>
								</thead>
								<tbody>
									<tr><td>Total students</td><td><?= $total_students ?></td></tr>
									<tr><td>Total Pass Issued</td><td><?= $total_issued ?></td></tr>
									<tr><td>Total Pass Lost</td><td><?= $total_returned ?></td></tr>
								</tbody>
								
							</table>
							<?php
						}
					}
				?>
			</div>
		</div>

		<div class="row clearfix graph_box">
			<div class="row clearfix dashboard-heading">
				<h5>Today's Report</h5>
			</div>

					<?php 
						if($today_late_not_returned == 0 && $today_late_returned == 0 && $today_issued == 0 && $today_pending == 0 && $today_returned == 0)

						{
							?>
								<div class="row">
									<div class="msg notice"><p>No details available</p></div>
								</div>
							<?php
						}
						else
						{
							if($internet == true)
							{
							?>
							
								<div class="row">
									<noscript>
										<div class="msg notice"><p>Javascript is required to properly run the application.</p></div>
									</noscript>
								</div>
								<div id="today" style="width: 1000px; height: 300px; margin:0 auto;"></div>
							<?php
							}
							else
							{
								?>
								<div class="row">
									<div class="msg notice"><p>Graphs will be available if your are connected to the internet</p></div>
								</div>
								<table style="width:400px;" class="layout">
									<thead>
										<tr><td>Data</td><td>Value</td></tr>
									</thead>
									<tbody>
										<tr><td>Today Issued</td><td><?= $today_issued ?></td></tr>
										<tr><td>Today Returned</td><td><?= $today_returned ?></td></tr>
										<tr><td>Today Pending</td><td><?= $today_pending ?></td></tr>
										<tr><td>Late (Not returned)</td><td><?= $today_late_not_returned ?></td></tr>
										<tr><td>Late (Returned)</td><td><?= $today_late_returned ?></td></tr>
									</tbody>
									
								</table>
								<?php
							}
						}
					?>
		</div>

		<div class="row clearfix graph_box">
			<div class="row clearfix dashboard-heading">
				<h5>Current Month Report</h5>
			</div>

					<?php 
						if($month_late_not_returned == 0 && $month_late_returned == 0 && $month_issued == 0 && $month_pending == 0 && $month_returned == 0)

						{
							?>
								<div class="row">
									<div class="msg notice"><p>No details available</p>
								</div>
							<?php
						}
						else
						{
							if($internet == true)
							{
							?>
							
								<div class="row">
									<noscript>
										<div class="msg notice"><p>Javascript is required to properly run the application.</p></div>
									</noscript>
								</div>
								<div id="month" style="width: 1000px; height: 300px; margin:0 auto;"></div>
							<?php
							}
							else
							{
								?>
								<div class="row">
									<div class="msg notice"><p>Graphs will be available if your are connected to the internet</p></div>
								</div>
								<table style="width:400px;" class="layout">
									<thead>
										<tr><td>Data</td><td>Value</td></tr>
									</thead>
									<tbody>
										<tr><td>Issued</td><td><?= $month_issued ?></td></tr>
										<tr><td>Returned</td><td><?= $month_returned ?></td></tr>
										<tr><td>Pending</td><td><?= $month_pending ?></td></tr>
										<tr><td>Late (Not returned)</td><td><?= $month_late_not_returned ?></td></tr>
										<tr><td>Late (Returned)</td><td><?= $month_late_returned ?></td></tr>
									</tbody>
									
								</table>
								<?php
							}
						}
					?>
		</div>

<?php Loader::addScript('charts.js'); ?>
<script type="text/javascript">
var element =  document.getElementById('overview');
var today =  document.getElementById('today');
var month =  document.getElementById('month');
if (typeof(element) != 'undefined' && element != null)
{
	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Total Record'],
          ["Total students",<?= $total_students ?>],

          ["Pass Issued", <?= $total_issued ?>],

          ["Pass Returned", <?= $total_returned ?>]
        ]);

        var options = {
          title: ''
        };

        var chart = new google.visualization.PieChart(document.getElementById('overview'));

        chart.draw(data, options);
      }

}

if (typeof(today) != 'undefined' && today != null)
{
	google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = google.visualization.arrayToDataTable([
        ['Record', 'Today\'s Record',],
        ["Today Issued",<?= $today_issued ?>],

          ["Today Returned", <?= $today_returned ?>],

          ["Today Pending", <?= $today_pending ?>],

          ["Late (Not returned)", <?= $today_late_not_returned ?>],

          ["Late (Returned)", <?= $today_late_returned ?>]
      ]);

      var options = {
        title: 'Today\'s Record',
        chartArea: {width: '60%'},
        hAxis: {
          title: 'Today\s Record',
          minValue: 0
        },
        vAxis: {
          title: ''
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('today'));

      chart.draw(data, options);
    }
}

if (typeof(month) != 'undefined' && month != null)
{
	google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = google.visualization.arrayToDataTable([
        ['Record', 'This Month\'s Record',],
        ["Issued",<?= $month_issued ?>],

          ["Returned", <?= $month_returned ?>],

          ["Pending", <?= $month_pending ?>],

          ["Late (Not returned)", <?= $month_late_not_returned ?>],

          ["Late (Returned)", <?= $month_late_returned ?>]
      ]);

      var options = {
        title: 'Today\'s Record',
        chartArea: {width: '60%'},
        hAxis: {
          title: 'Today\s Record',
          minValue: 0
        },
        vAxis: {
          title: ''
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('month'));

      chart.draw(data, options);
    }
}
</script>