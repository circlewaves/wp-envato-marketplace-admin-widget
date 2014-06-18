		<?php // WEEK STATS (LAST 28 DAYS, 100 EVENTS) ?>
		
		
		<h3><?php _e('Week stats (Last 28 days, 100 events)',$this->plugin_slug); ?></h3>

		
		<div class="cw-wpemaw-widget-row wpemaw-week_stats">

<?php
$defaultTimezone=date_default_timezone_get(); //remember default timezone
date_default_timezone_set("Australia/Melbourne"); // set timezone to Melbourne time (since all sales are based in Australia, Melbourne timezone)

$week_counter=1;
$week_sales=array();


$statement=$envatoAPI->statement($username);

foreach($statement as $k=>$v){
	$day = date('d F Y', strtotime($v->occured_at));
  $week = date('W', strtotime($v->occured_at));

  // create new empty array if it hasn't been created yet
  if( !isset($week_sales[$week]) ) {
    $week_sales[$week] = array('week_sales_count'=>0,'week_sales_amount'=>0,'week_referal_count'=>0,'week_referal_amount'=>0,'day_stat'=>array());
		$week_counter++;
  }
	
	if( !isset($week_sales[$week]['day_stat'][$day]) ){
		$week_sales[$week]['day_stat'][$day]=array('day_sales_count'=>0,'day_sales_amount'=>0,'day_referal_count'=>0,'day_referal_amount'=>0);
	}

	

  // append the post to the array
 // $week_sales[$week][] = $v;
 if($v->kind=='sale'){
	$week_sales[$week]['week_sales_count']++;
	$week_sales[$week]['week_sales_amount']+=$v->amount;
	
	$week_sales[$week]['day_stat'][$day]['day_sales_count']++;
	$week_sales[$week]['day_stat'][$day]['day_sales_amount']+=$v->amount;
}
 if($v->kind=='referral_cut'){
	$week_sales[$week]['week_referal_count']++;
	$week_sales[$week]['week_referal_amount']+=$v->amount;
	
	$week_sales[$week]['day_stat'][$day]['day_referal_count']++;
	$week_sales[$week]['day_stat'][$day]['day_referal_amount']+=$v->amount;
}
	
}


$week_number=1;
foreach($week_sales as $k=>$v){ ?>
	<div class="week-row">
		<div class="week-row-container">

			<div class="week-heading">
				<div class="heading-earnings">Earned this week <strong class="earnings-value">$<?php echo $v['week_sales_amount']+$v['week_referal_amount'];?></strong> </div>
				<div class="heading-earnings-subtitle">(<strong><?php echo $v['week_sales_count'];?></strong> sales, referral cut <strong>$<?php echo $v['week_referal_amount'];?> / <?php echo $v['week_referal_count'];?></strong>)</div>
			</div>
						
			<div class="week-content">		
				<div class="week-content-container">
				<?php
				foreach($v['day_stat'] as $day=>$stat){
					$current_day=date('l',strtotime($day));?>
					
					<div class="week-day day-<?php echo $current_day;?>">	
						<div class="week-day-container">
							<div class="day-heading">
								<div class="day-title"><?php echo $current_day;?></div>
								<div class="day-date"><?php echo date('j M',strtotime($day));?></div>
							</div>
							<div class="day-content">
								<div class="day-total">$<?php echo $stat['day_sales_amount']+$stat['day_referal_amount'];?></div>
								<div class="day-sales"><strong><?php echo $stat['day_sales_count'];?></strong> sales</div>
								<div class="day-ref">
									<div class="day-ref-heading">Referrals</div>
									<div class="day-ref-content"><strong>$<?php echo $stat['day_referal_amount'];?> / <?php echo $stat['day_referal_count'];?></strong></div>
								</div>
							</div>
						</div>		
					</div>	
				<?php  }	?>
				</div>
			</div>

		</div>
	</div>
<?php
$week_number++;	
}

date_default_timezone_set($defaultTimezone); //restore default timezone
?>		
		
		
		</div>
		
		
		<?php // END WEEK STATS(LAST 28 DAYS, 100 EVENTS) ?>