		<?php // STATEMENT(LAST 28 DAYS, 100 EVENTS) ?>
		
		
		<h3><?php _e('Statement (Last 28 days, 100 events)',$this->plugin_slug); ?></h3>

		
		<div class="cw-wpemaw-widget-row wpemaw-statement">

<?php
$defaultTimezone=date_default_timezone_get(); //remember default timezone
date_default_timezone_set("Australia/Melbourne"); // set timezone to Melbourne time (since all sales are based in Australia, Melbourne timezone)



$statement=$envatoAPI->statement($username);

foreach($statement as $k=>$v){
	$statement_date = date('d F Y g:ia (e)', strtotime($v->occured_at));
	
?>
	<div class="statement-row">
		<div class="statement-row-container">
			<div class="statement-left">
				<div class="statement-descr"><?php echo $v->description; ?></div>
				<div class="statement-date"><?php echo $statement_date; ?></div>				
			</div>
			<div class="statement-right">
				<div class="statement-amount">$<?php echo $v->amount; ?></div>
				<div class="statement-type"><?php echo $v->kind; ?></div>		
			</div>
		</div>
	</div>

<?php }



date_default_timezone_set($defaultTimezone); //restore default timezone
?>		
		
		
		</div>
		
		
		<?php // END STATEMENT(LAST 28 DAYS, 100 EVENTS) ?>