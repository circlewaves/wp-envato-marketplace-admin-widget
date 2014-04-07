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
	$cw_wpemaw_option_username = esc_attr( get_option( $cw_wpemaw_pluginSettings[0]['name'] ) ); // Get username
	$cw_wpemaw_option_api = esc_attr( get_option( $cw_wpemaw_pluginSettings[1]['name'] ) ); // Get API
	
	if(!$cw_wpemaw_option_username || !$cw_wpemaw_option_api){?>
		<div class="error below-h2 form-invalid"><p><?php _e('Please <a href="'.admin_url( 'options-general.php?page=' . $cw_wpemaw_plugin_slug ) . '">configure</a> this widget',$cw_wpemaw_plugin_slug);?></p></div>
	<?php }else{

	require_once plugin_dir_path(dirname(__FILE__) ).'includes/Envato_marketplaces.php';
	
	$cw_wpemaw_Envato = new Envato_marketplaces($cw_wpemaw_option_api);
	$cw_wpemaw_Envato->clear_cache();

	//Get Balance
	$cw_wpemaw_user_balance=$cw_wpemaw_Envato->balance($cw_wpemaw_option_username);


	if(!$cw_wpemaw_user_balance){?>
		<div class="error below-h2 form-invalid"><p><?php _e('Incorrect username and/or API key. Please, check widget <a href="'.admin_url( 'options-general.php?page=' . $cw_wpemaw_plugin_slug ) . '">options</a>',$cw_wpemaw_plugin_slug);?></p></div>
	<?php }else{?>
		<div class="cw-wpemaw-widget-row cw-wpemaw-widget-header">
			<div class="cw-wpemaw-widget-left">
				<?php _e('Welcome',$cw_wpemaw_plugin_slug); echo ', <strong>'.$cw_wpemaw_option_username.'</strong>';?> 
			</div>
			<div class="cw-wpemaw-widget-right">
				<?php _e('Your balance',$cw_wpemaw_plugin_slug); echo ': <strong>$'.$cw_wpemaw_user_balance.'</strong>';?>
			</div>
		</div>
		<h3><?php _e('Verify Purchase Code',$cw_wpemaw_plugin_slug); ?></h3>
		<?php 
		//handle form		
		if(('POST' == $_SERVER['REQUEST_METHOD']) && (isset($_POST['cw_wpemaw_verify_purchase_nonce'])) && (wp_verify_nonce( $_POST['cw_wpemaw_verify_purchase_nonce'], 'cw_wpemaw_verify_purchase' )) ) {

			$cw_wpemaw_verify_purchase_code_value=esc_attr($_POST['cw_wpemaw_verify_purchase_code']);
			if(!$cw_wpemaw_verify_purchase_code_value){
				echo '<p class="form-invalid">'.__('Please enter purchase code',$cw_wpemaw_plugin_slug).'</p>';
			}else{
				echo '<p class="alternate">'.__('Verification result for code').' <strong>'.$cw_wpemaw_verify_purchase_code_value.'</strong></p>';
				
				$verify = $cw_wpemaw_Envato->verify_purchase($cw_wpemaw_option_username, $cw_wpemaw_verify_purchase_code_value);
				if ( isset($verify->buyer) ){
					echo '<p class="alternate">'.__('Purchase code is valid',$cw_wpemaw_plugin_slug).'</p>';
					echo '<p class="alternate">';
					echo '<span>'.__('Item Name',$cw_wpemaw_plugin_slug).': '.$verify->item_name.'</span><br />';
					echo '<span>'.__('Purchase Date',$cw_wpemaw_plugin_slug).': '.$verify->created_at.'</span><br />';
					echo '<span>'.__('Buyer',$cw_wpemaw_plugin_slug).': '.$verify->buyer.'</span><br />';
					echo '<span>'.__('License',$cw_wpemaw_plugin_slug).': '.$verify->licence.'</span>';
					echo '</p>';
				}else{
				 echo '<p class="form-invalid">'.__('Purchase code is not valid',$cw_wpemaw_plugin_slug).'</p>';
				}	
				
			}

			$_POST=null;

		}?>		

		<div class="cw-wpemaw-widget-row">
			<form method="POST">
				<?php wp_nonce_field( 'cw_wpemaw_verify_purchase', 'cw_wpemaw_verify_purchase_nonce' );?>
				<p>
					<input type="text" class="all-options" id="cw_wpemaw_verify_purchase_code" name="cw_wpemaw_verify_purchase_code" value="" placeholder="<?php _e('Enter purchase code here',$cw_wpemaw_plugin_slug); ?>" /> 	<input class="button-primary" type="submit" value="<?php _e( 'Verify',$cw_wpemaw_plugin_slug); ?>" />
				</p>
			</form>			
		</div>

	<?php }?>


					
						
<?php } ?>
	
</div>