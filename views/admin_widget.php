<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WP Envato Marketplace Admin Widget
 * @author    Circlewaves Team <support@circlewaves.com>
 * @license   GPL-2.0+
 * @link      http://circlewaves.com
 * @copyright 2014 Circlewaves Team <support@circlewaves.com>
 */
?>



<div class="cw-wpemaw-admin-widget">

	<?php

	$username = esc_attr( get_option( self::$pluginSettings[0]['name'] ) ); // Get username
	$api_key = esc_attr( get_option(  self::$pluginSettings[1]['name'] ) ); // Get API
	$show_balance_admin_only= get_option( self::$pluginSettings[2]['name'] ); // If show balance only for admin	
	
	if(!$username || !$api_key){
		if(current_user_can('manage_options')){	?>
			<div class="error below-h2 form-invalid"><p><?php _e('Please <a href="'.admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">configure</a> this widget',$this->plugin_slug);?></p></div>
		<?php }else{?>
			<div class="error below-h2 form-invalid"><p><?php _e('There is some error with widget settings. Please, contact with the website administrator.',$this->plugin_slug);?></p></div>
		<?php }?>
	<?php }else{

	require_once plugin_dir_path(dirname(__FILE__) ).'includes/Envato_marketplaces.php';
	
	$envatoAPI = new Envato_marketplaces($api_key);
	

if(isset($_GET['wpemaw_clear_cache']) && ($_GET['wpemaw_clear_cache']==1)){
	$envatoAPI->clear_cache();
$_GET['wpemaw_clear_cache']=null;
unset($_GET);
}	
	



	$account_info = $envatoAPI->account_information($username);
	if(!isset($account_info)||empty($account_info)||!is_object($account_info)){
		if(current_user_can('manage_options')){	?>
			<div class="error below-h2 form-invalid"><p><?php _e('Incorrect username and/or API key. Please, check the widget <a href="'.admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">options</a> or <a href="?wpemaw_clear_cache=1" title="'.__('Refresh',$this->plugin_slug).'" class="wpemaw-widget-refresh-button dashicons dashicons-update"></a>	',$this->plugin_slug);?></p></div>
		<?php }else{?>
			<div class="error below-h2 form-invalid"><p><?php _e('Incorrect username and/or API key. Please, contact with the website administrator or <a href="?wpemaw_clear_cache=1" title="'.__('Refresh',$this->plugin_slug).'" class="wpemaw-widget-refresh-button dashicons dashicons-update"></a>	',$this->plugin_slug);?></p></div>
		<?php }?>
		
	<?php }else{
	//Get Balance
	$current_balance=$envatoAPI->balance($username);	
	?>
	
		<?php // WIDGET HEADER ?>	
		<div class="wpemaw-widget-row wpemaw-widget-header">
			<div class="wpemaw-left account-image">
				<?php if($account_info->image!=''){echo '<img src="'.$account_info->image.'">';}?>
			</div>
			<div class="wpemaw-left account-user">
				<div class="account-welcome">
					<?php _e('Welcome',$this->plugin_slug); echo ', <strong>'.$username.'</strong>';?>
				</div>
				<?php if((current_user_can('manage_options'))){ ?>
				<div class="wpemaw-widget-settings-button-wrapper">
					<a href="<?php echo admin_url( 'options-general.php?page=' . $this->plugin_slug );?>" title="<?php _e('Widget Settings',$this->plugin_slug); ?>"  class="wpemaw-widget-settings-button dashicons dashicons-admin-generic"></a>
				</div>	
				<?php }?>
			</div>
			<?php if(($show_balance_admin_only && current_user_can('manage_options'))||(!$show_balance_admin_only)){ //hide balance for non-admin users (if option checked)	?>		
			<div class="wpemaw-right account-balance-wrapper">
				<div class="account-balance">
					<?php _e('Your balance',$this->plugin_slug);?>: <?php echo '<strong>$'.$current_balance.'</strong>'; ?> 
					<a href="?wpemaw_clear_cache=1" title="<?php _e('Refresh',$this->plugin_slug); ?>" class="wpemaw-widget-refresh-button dashicons dashicons-update"></a>	
				</div>
			</div>
			<?php } ?>

		</div>
		<?php // END WIDGET HEADER ?>
		




		
<div class="wpemaw-widget-content" id="cw-wpemaw-tabs">
  <ul>
    <li><a href="#cw-wpemaw-tab-verify"><?php _e('Verify Purchase',$this->plugin_slug); ?></a></li>
	<?php if(($show_balance_admin_only && current_user_can('manage_options'))||(!$show_balance_admin_only)){ //hide balance for non-admin users (if option checked)	?>			
    <li><a href="#cw-wpemaw-tab-week_stats"><?php _e('Week Stats',$this->plugin_slug); ?></a></li>
    <li><a href="#cw-wpemaw-tab-statement"><?php _e('Statement',$this->plugin_slug); ?></a></li>
    <li><a href="#cw-wpemaw-tab-monthly"><?php _e('Monthly',$this->plugin_slug); ?></a></li>
	<?php }?>	
  </ul>
  <div id="cw-wpemaw-tab-verify">
		<?php include_once '_inc_verification.php'; ?>
  </div>
	<?php if(($show_balance_admin_only && current_user_can('manage_options'))||(!$show_balance_admin_only)){ //hide balance for non-admin users (if option checked)	?>		
  <div id="cw-wpemaw-tab-week_stats">
		<?php  include_once '_inc_week_stats.php'; ?>
  </div>
  <div id="cw-wpemaw-tab-statement">
		<?php  include_once '_inc_statement.php'; ?>
  </div>
  <div id="cw-wpemaw-tab-monthly">
		<?php include_once '_inc_monthly.php'; ?>
  </div>
	<?php }?>	
</div>

	<?php  }?>


					
						
<?php } ?>
	
</div>