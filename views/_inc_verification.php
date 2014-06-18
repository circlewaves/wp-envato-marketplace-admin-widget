		<?php // VERIFY PURCHASE CODE ?>
		
		<h3><?php _e('Verify Purchase Code',$this->plugin_slug); ?></h3>

		
		<div class="cw-wpemaw-widget-row  wpemaw-verification">
		
		<?php 
		//handle form		
		if(('POST' == $_SERVER['REQUEST_METHOD']) && (isset($_POST['cw_wpemaw_verify_purchase_nonce'])) && (wp_verify_nonce( $_POST['cw_wpemaw_verify_purchase_nonce'], 'cw_wpemaw_verify_purchase' )) ) {

			$cw_wpemaw_verify_purchase_code_value=esc_attr($_POST['cw_wpemaw_verify_purchase_code']);
			if(!$cw_wpemaw_verify_purchase_code_value){
				echo '<div class="verification-error">'.__('Please enter purchase code',$this->plugin_slug).'</div>';
			}else{
				echo '<div class="verification-result-wrapper">';
				echo '<div class="verification-code">'.__('Verification result for code').' <em>'.$cw_wpemaw_verify_purchase_code_value.'</em></div>';
				
				$verify = $envatoAPI->verify_purchase($username, $cw_wpemaw_verify_purchase_code_value);
				if ( isset($verify->buyer) ){
					echo '<div class="verification-valid">'.__('Purchase code is valid',$this->plugin_slug).'</div>';
					echo '<div class="verification-details">';
					echo '<div>'.__('Item Name',$this->plugin_slug).': '.$verify->item_name.'</div>';
					echo '<div>'.__('Purchase Date',$this->plugin_slug).': '.$verify->created_at.'</div>';
					echo '<div>'.__('Buyer',$this->plugin_slug).': '.$verify->buyer.'</div>';
					echo '<div>'.__('License',$this->plugin_slug).': '.$verify->licence.'</div>';
					echo '</div>';
				}else{
				 echo '<div class="verification-error">'.__('Purchase code is not valid',$this->plugin_slug).'</div>';
				}	
				echo '</div>';
			}

			$_POST=null;

		}?>		
		
		
		
		<div class="cw-wpemaw-widget-row verification-form">
			<form method="POST">
				<?php wp_nonce_field( 'cw_wpemaw_verify_purchase', 'cw_wpemaw_verify_purchase_nonce' );?>
				<p>
					<input type="text" class="all-options" id="cw_wpemaw_verify_purchase_code" name="cw_wpemaw_verify_purchase_code" value="" placeholder="<?php _e('Enter purchase code here',$this->plugin_slug); ?>" /> 	<input class="button-primary" type="submit" value="<?php _e( 'Verify',$this->plugin_slug); ?>" />
				</p>
			</form>			
		</div>
		
</div>		
		
		
		<?php // END VERIFY PURCHASE CODE ?>