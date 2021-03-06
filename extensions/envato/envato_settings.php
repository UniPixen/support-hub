<?php

$current_account = isset($_REQUEST['shub_account_id']) ? (int)$_REQUEST['shub_account_id'] : false;
$shub_envato = SupportHub::getInstance()->message_managers['envato'];
if($current_account !== false){
    /**
     * @type $shub_envato_account shub_envato_account
     */
	$shub_envato_account = $shub_envato->get_account($current_account);
    if($shub_envato_account->get('shub_extension') != 'envato')die('Wrong extension:' .$shub_envato_account->get('shub_extension'));
	if(isset($_GET['manualrefresh'])){

		$network_key = isset( $_REQUEST['network_key'] ) ? (int) $_REQUEST['network_key'] : 0;
        if(!$network_key){
            // assume we're update the account sales data.
            $shub_envato_account->update_author_sale_history( true, true );
        }else {
            /* @var $items shub_item[] */
            $items = $shub_envato_account->get('items');
            if (!$network_key || !$items || !isset($items[$network_key])) {
                die('No items found to refresh');
            }
            ?>
            Manually refreshing item data... please wait...
            <?php
            $items[$network_key]->run_cron(true);
        }

	}else if(isset($_GET['do_connect'])){
		// connect to envato.
		?>
		<div class="wrap">
			<h2>
				<?php _e( 'Envato Account', 'support_hub' ); ?>
			</h2>
		<?php
		if($shub_envato_account->get('shub_account_id') && $shub_envato_account->get('shub_account_id') == $current_account && $shub_envato_account->get( 'envato_app_id' ) && $shub_envato_account->get( 'envato_app_secret' ) && $shub_envato_account->get( 'envato_token' )) {

            // now we load in a list of envato items to manage and redirect the user back to the 'edit' screen where they can continue managing the account.
//            $shub_envato_account->run_cron(); echo 'done';exit;
            $shub_envato_account->load_available_items();
            $shub_envato_account->confirm_token();
            //$shub_envato_account->run_cron();
            ?>
            <p>You have successfully connected Envato with the Support Hub plugin. Please click the button
                below:</p>
            <?php

            ?>

            <p><a href="<?php echo $shub_envato_account->link_edit(); ?>" class="button">Back</a>
            </p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <?php


		} else {
            // no app / secret defined, use the default Support Hub API ones.
			?>
			Please go back and set an API Secret, Client ID and Personal Token.
			<?php
		}
		?> </div> <?php
	}else {

		?>
		<div class="wrap">
			<h2>
				<?php _e( 'Envato Account', 'support_hub' ); ?>
			</h2>

			<form action="" method="post">
				<input type="hidden" name="_process" value="save_account_details">
				<input type="hidden" name="shub_extension" value="envato">
				<input type="hidden" name="shub_account_id" value="<?php echo (int) $shub_envato_account->get( 'shub_account_id' ); ?>">
				<?php wp_nonce_field( 'save-account' . (int) $shub_envato_account->get( 'shub_account_id' ) ); ?>

                <p>Setup Instructions:</p>
                <ol>
                    <li>Go to <a href="http://build.envato.com/" target="_blank">http://build.envato.com/</a> </li>
                    <li>Click My Apps at the top</li>
                    <li>Login using your existing Envato Account</li>
                    <li>Click Register a new App</li>
                    <li>In the app name put "Item Support" (a buyer might see this name from time to time)</li>
                    <li>Tick "View username", "View email", "View sales history" and "Verify purchase" options</li>
                    <li>In the "Confirmation URL" put <strong><?php echo $shub_envato_account->generate_oauth_redirect_url();?></strong></li>
                    <li>Click Register App</li>
                    <li>Copy your secret key and paste it into the box below</li>
                    <li>Click OK</li>
                    <li>Then look for the "Client ID" for your App and copy that into the box below as well</li>
	                <li>Click the "Create a new Token" button</li>
	                <li>Enter a name again like "SupportHub"</li>
	                <li>Tick the "Verify purchase" option</li>
	                <li>Click "Create Token"</li>
	                <li>Copy this token and paste it into the box below</li>
					<li>Enter the Session Cookie as per the <a href="http://supporthub.co/documentation/envato/" target="_blank">help documentation</a> (required for posting item comments) </li>
                    <li>Click the Save and Connect to Envato button below</li>
                </ol>
				<table class="form-table">
					<tbody>
					<tr>
						<th class="width1">
							<?php _e( 'Envato Username', 'support_hub' ); ?>
						</th>
						<td class="">
							<input type="text" name="account_name" value="<?php echo esc_attr( $shub_envato_account->get( 'account_name' ) ); ?>">

						</td>
					</tr>
                    <tr>
                        <th class="width1">
                            <?php _e( 'App Secret Key', 'support_hub' ); ?>
                        </th>
                        <td class="">
                            <input type="password" name="account_data[envato_app_secret]" value="<?php echo esc_attr( $shub_envato_account->get('envato_app_secret') ? _SUPPORT_HUB_PASSWORD_FIELD_FUZZ : '' ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th class="width1">
                            <?php _e( 'App Client ID', 'support_hub' ); ?>
                        </th>
                        <td class="">
                            <input type="text" name="account_data[envato_app_id]" value="<?php echo esc_attr( $shub_envato_account->get('envato_app_id') ? $shub_envato_account->get('envato_app_id') : '' ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th class="width1">
                            <?php _e( 'Personal Token', 'support_hub' ); ?>
                        </th>
                        <td class="">
                            <input type="password" name="account_data[envato_token]" value="<?php echo esc_attr( $shub_envato_account->get('envato_token') ? _SUPPORT_HUB_PASSWORD_FIELD_FUZZ : '' ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th class="width1">
                            <?php _e( 'Session Cookie', 'support_hub' ); ?>
                        </th>
                        <td class="">
                            <input type="password" name="account_data[envato_cookie]" value="<?php echo esc_attr( $shub_envato_account->get('envato_cookie') ? _SUPPORT_HUB_PASSWORD_FIELD_FUZZ : '' ); ?>">
							<a href="http://supporthub.co/documentation/envato/" target="_blank">(help)</a>
                        </td>
                    </tr>
					<?php if ( $shub_envato_account->get( 'shub_account_id' ) ) { ?>
						<tr>
							<th class="width1">
								<?php _e( 'Last Checked', 'support_hub' ); ?>
							</th>
							<td class="">
								<?php echo $shub_envato_account->get( 'last_checked' ) ? shub_print_date( $shub_envato_account->get( 'last_checked' ), true ) : __( 'N/A', 'support_hub' ); ?>
                                <a href="<?php echo $shub_envato_account->link_refresh();?>" target="_blank">re-load sales data (takes a LONG time)</a>
							</td>
						</tr>
						<tr>
							<th class="width1">
								<?php _e( 'Available Items', 'support_hub' ); ?>
							</th>
							<td class="">
								<input type="hidden" name="save_account_items" value="yep">
								<strong><?php _e( 'Choose which Envato items you would like to manage:', 'support_hub' ); ?></strong><br>
								(note: We haven't yet tested this with more than 50 items, but it should work ok) <br/><br/>
								<?php
								$data = $shub_envato_account->get( 'account_data' );
								if ( $data && isset( $data['items'] ) && is_array( $data['items'] ) && count( $data['items'] ) > 0 ) {
									$items = $shub_envato_account->get('items');
									?>
									<div>
										<input type="checkbox" name="all" value="1" class="envato_check_all"> - check all -
									</div>
									<br/><br/>

									<table class="wp-list-table widefat fixed striped">
										<thead>
										<tr>
											<th>Enabled</th>
											<th>Market</th>
											<th>Envato Item</th>
											<th>Support Hub Product</th>
											<th>Last Checked</th>
											<th>Action</th>
										</tr>
										</thead>
										<tbody>
										<?php
										$products = SupportHub::getInstance()->get_products();
										foreach ( $data['items'] as $item_id => $item_data ) {
											?>
											<tr>
												<td>
													<input type="checkbox" name="item[<?php echo $item_id; ?>]" class="check_item"
													       value="1" <?php echo $shub_envato_account->is_item_active( $item_id ) ? ' checked' : ''; ?>>

                                                    <input type="hidden" name="item_name[<?php echo $item_id;?>]" value="<?php echo esc_attr($item_data['item']);?>">
												</td>
												<td>
													<?php echo htmlspecialchars( $item_data['site'] ); ?>
												</td>
												<td>
													<?php echo htmlspecialchars( $item_data['item'] ); ?>
												</td>
												<td>
													<?php shub_module_form::generate_form_element(array(
														'name' => 'item_product['.$item_id.']',
														'type' => 'select',
														'blank' => __('- None -','support_hub'),
														'value' => $shub_envato_account->is_item_active( $item_id ) ? $items[ $item_id ]->get( 'shub_product_id' ) : $item_data['shub_product_id'],
														'options' => $products,
														'options_array_id' => 'product_name',
														'class' => 'shub_product_dropdown',
													)); ?>
												</td>
												<td>
													<?php echo $shub_envato_account->is_item_active( $item_id ) && $items[ $item_id ]->get( 'last_checked' ) ? shub_print_date( $items[ $item_id ]->get( 'last_checked' ), true ): 'N/A';?>
												</td>
												<td>
													<?php
													if ( $shub_envato_account->is_item_active( $item_id ) ) {
														echo '<a href="' . $items[ $item_id ]->link_refresh() . '" target="_blank">re-load item comments</a>';
													} ?>
												</td>
											</tr>
										<?php
										}
										?>
										</tbody>
									</table>
									<?php
								} else {
									_e( 'No Envato Items Found to Manage, click re-connect button below', 'support_hub' );
								}
								?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

				<p class="submit">
					<?php if ( $shub_envato_account->get( 'shub_account_id' ) ) { ?>
						<input name="butt_save" type="submit" class="button-primary"
						       value="<?php echo esc_attr( __( 'Save', 'support_hub' ) ); ?>"/>
						<input name="butt_save_reconnect" type="submit" class="button"
						       value="<?php echo esc_attr( __( 'Re-Connect to Envato', 'support_hub' ) ); ?>"/>
						<input name="butt_delete" type="submit" class="button"
						       value="<?php echo esc_attr( __( 'Delete', 'support_hub' ) ); ?>"
						       onclick="return confirm('<?php _e( 'Really delete this envato account and all associated data?', 'support_hub' ); ?>');"/>
					<?php } else { ?>
						<input name="butt_save_reconnect" type="submit" class="button-primary"
						       value="<?php echo esc_attr( __( 'Save and Connect to Envato', 'support_hub' ) ); ?>"/>
					<?php } ?>
				</p>


			</form>
		</div>
	<?php
	}
}else{
	// show account overview:
	$myListTable = new SupportHub_Account_Data_List_Table();
	$accounts = $shub_envato->get_accounts();
	foreach($accounts as $account_id => $account){
		$a = new shub_envato_account($account['shub_account_id']);
		$accounts[$account_id]['edit_link'] = $a->link_edit();
		$accounts[$account_id]['title'] = $a->get('account_name');
		$accounts[$account_id]['last_checked'] = $a->get('last_checked') ? shub_print_date( $a->get('last_checked') ) : 'N/A';
	}
	$myListTable->set_data($accounts);
	$myListTable->prepare_items();
	?>
	<div class="wrap">
		<h2>
			<?php _e('Envato Accounts','support_hub');?>
			<a href="?page=<?php echo esc_attr($_GET['page']);?>&tab=<?php echo esc_attr($_GET['tab']);?>&shub_account_id=new" class="add-new-h2"><?php _e('Add New','support_hub');?></a>
		</h2>
	    <?php
	    //$myListTable->search_box( 'search', 'search_id' );
	     $myListTable->display();
		?>
	</div>
	<?php
}

?>
<script type="text/javascript">
	<?php $shub_envato->init_js(); ?>
</script>