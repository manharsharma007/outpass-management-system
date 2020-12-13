<div class="row content">
    <div class="row-fixed">

        <div class="row page-heading">
            <h4>Dashboard</h4>
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
				<h5>Today's Reports</h5>
			</div>
			<div class="row">


				<div class="col-2 card">
					<div class="card-box bg-green">
						<div class="card-content">
							<h3><?= (isset($today_issued)) ? $today_issued : 0; ?></h3>
							<p>Issued Pass</p>
							<div class="card-icon">
								<i class="fa fa-book" aria-hidden="true"></i>
							</div>
						</div>
						
					</div>
				</div>


				<div class="col-2 card">
					<div class="card-box bg-yellow">
						<div class="card-content">
							<h3><?= (isset($today_returned)) ? $today_returned : 0; ?></h3>
							<p>Pass Returned</p>
							<div class="card-icon">
								<i class="fa fa-book" aria-hidden="true"></i>
							</div>
						</div>
						
					</div>
				</div>

			</div>
		</div>

		<div class="row clearfix graph_box">
			<div class="row clearfix dashboard-heading">
				<h5>Month's Reports</h5>
			</div>
			<div class="row">

				<div class="col-2 card">
					<div class="card-box bg-green">
						<div class="card-content">
							<h3><?= (isset($month_issued)) ? $month_issued : 0; ?></h3>
							<p>Issued Pass</p>
							<div class="card-icon">
								<i class="fa fa-book" aria-hidden="true"></i>
							</div>
						</div>
						
					</div>
				</div>


				<div class="col-2 card">
					<div class="card-box bg-yellow">
						<div class="card-content">
							<h3><?= (isset($month_returned)) ? $month_returned : 0; ?></h3>
							<p>Pass Returned</p>
							<div class="card-icon">
								<i class="fa fa-book" aria-hidden="true"></i>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>


		<div class="row clearfix graph_box">
			<div class="row clearfix dashboard-heading">
				<h5>Total Record till date</h5>
			</div>

					<?php 
						if($total_students == 0 && $total_issued == 0 && $total_returned == 0)

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
								<div id="year" style="width: 1000px; height: 300px; margin:0 auto;"></div>
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
										<tr><td>Total Students</td><td><?= $total_students ?></td></tr>
										<tr><td>Pass Issued</td><td><?= $total_issued ?></td></tr>
										<tr><td>Pass Returned</td><td><?= $total_returned ?></td></tr>
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
var year =  document.getElementById('year');
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

if (typeof(year) != 'undefined' && year != null)
{
	google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = google.visualization.arrayToDataTable([
        ['Record', 'Total Record',],
        ["Total Students",<?= $total_students ?>],

          ["Pass Issued", <?= $total_issued ?>],

          ["Pass Returned", <?= $total_returned ?>]
      ]);

      var options = {
        title: 'Total Record till date',
        chartArea: {width: '60%'},
        hAxis: {
          title: 'Total Record',
          minValue: 0
        },
        vAxis: {
          title: ''
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('year'));

      chart.draw(data, options);
    }
}
</script>


					