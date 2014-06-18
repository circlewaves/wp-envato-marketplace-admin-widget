		<?php // MONTHLY ?>
		
		
		<h3><?php _e('Monthly Earnings',$this->plugin_slug); ?></h3>

		
		<div class="cw-wpemaw-widget-row wpemaw-monthly">

<?php
$defaultTimezone=date_default_timezone_get(); //remember default timezone
date_default_timezone_set("Australia/Melbourne"); // set timezone to Melbourne time (since all sales are based in Australia, Melbourne timezone)



$monthly_earnings=$envatoAPI->earnings_by_month($username);

foreach($monthly_earnings as $k=>$v){
	$month = date('F Y', strtotime($v->month));
	
?>
	<div class="monthly-row">
		<div class="monthly-row-container">
			<div class="monthly-left">
				<div class="monthly-date"><?php echo $month; ?></div>				
			</div>
			<div class="monthly-right">
				<div class="monthly-amount">$<?php echo $v->earnings; ?></div>
				<div class="monthly-type"><?php echo $v->sales; ?> <?php _e('sales',$this->plugin_slug); ?></div>		
			</div>
		</div>
	</div>

<?php }



date_default_timezone_set($defaultTimezone); //restore default timezone
?>		
		
		
		</div>
		
		
		<?php // END MONTHLY ?>