<?php
/*
Busy Server Administration Panel
Plugin URI: https://github.com/gibboncz/busy-server
Description: When the server load is higher than specified, show an error message instead of loading the page.
Version: 0.2.2
Author: Lubos Svoboda
*/
defined( 'ABSPATH' ) or exit();
// Check that the user is allowed to update options
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}
?>
<div class="wrap">
	<h2>Busy Server Options</h2>
	<p><blockquote><strong>WARNING! Wrong settings can easily make your site unusable. Please modify only when you know what you are doing.</strong></blockquote></p>
	<p><blockquote><strong>Only Linux servers supported.</strong></blockquote></p>
	<form method="post" action="options.php">
		<?php settings_fields( 'busy-server-group' ); ?>
		<?php do_settings_sections( 'busy-server-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Maximum load per CPU core</th>
			<td ><input type="number" step="0.01" name="busy_server_max_load" value="<?php echo get_option('busy_server_max_load'); ?>" /></td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Custom Busy Message</th>
			<td title="Displayed when server is busy"><input type="text" name="busy_server_busy_message" size="50" value="<?php echo get_option('busy_server_busy_message'); ?>" /> <i>HTML or plain text</i></td>
			</tr>

		</table>
		
		<?php submit_button(); ?>

	</form>
</div>