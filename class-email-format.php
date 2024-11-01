<?php 

class Email_format{
	//echo "test";exit;
	const VERSION = '1.0';

	
	protected $plugin_slug = 'email_format';


	protected static $instance = null;
	
	
	/**
	 * Initialize the plugin by loading public scripts and styels or admin page
	 *
	 */
	public function __construct() {
		$this->suggest_subject = get_option( 'sp_suggest_subject' );
		$this->suggest_message = stripslashes(get_option( 'sp_suggest_message' ));
		$this->suggest_status = get_option( 'sp_suggest_status' );
		$this->cron_event = get_option( 'sp_cron_event' );
		$this->custom_time = get_option( 'sp_custom_time' );
		
		if ( is_admin() ) {
			if($this->suggest_message == ''){
				$message = '<p>Hello,<br><br>We have changed the price of [product_name] as suggested by you and other customers. We have changed the price from [old_price] to [new_price] now. Please click link to buy [product_name] [product_link]<br><br>Thanks!</p>';
				update_option( 'sp_suggest_message', $message );
				$this->suggest_message = stripslashes(get_option( 'sp_suggest_message' ));
			}
			
			if($this->suggest_subject == ''){
				$subject = 'Price of [product_name] has been updated';
				update_option( 'suggest_subject', $subject );
				$this->suggest_subject = get_option( 'sp_suggest_subject' );
			}
			
			add_action( 'admin_menu', array( $this, 'plugin_admin_menu' ) );
			wp_enqueue_script( 'view-price-detail-script', plugins_url('js/admin_suggest_price.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		}
	}
	
	
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	public function plugin_admin_menu() {
		//add_options_page( __( 'Willing2Buy Settings', 'email-format' ), __( 'Willing2Buy Settings', 'email-format' ), 'manage_options', $this->plugin_slug, array( $this, 'suggest_email_format' ) );
		add_submenu_page( 'willingto-buy', __( 'Wiiling2Buy Settings' ), __( 'Wiiling2Buy Settings' ), 8,'Wiiling2Buy_Settings', array( $this, 'suggest_email_format' ) );
	}
	
	public function suggest_email_format() {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		if ( ! empty( $_POST ) && check_admin_referer( 'email_format', 'save_email_format' ) ) {
			if ( $this->suggest_subject !== false ) {
				update_option( 'sp_suggest_subject', $_POST['suggest_subject'] );
			} else {
				add_option( 'sp_suggest_subject', $_POST['suggest_subject'], null, 'no' );
			}
			
			//2add or update Medma strip details
			if ( $this->suggest_message !== false ) {
				update_option( 'sp_suggest_message', $_POST['suggest_message'] );
			} else {
				add_option( 'sp_suggest_message',  $_POST['suggest_message'] , null, 'no' );
			}
			
			//3add or update Medma strip text colour
			if ( $this->suggest_status !== false ) {
				update_option( 'sp_suggest_status', $_POST['suggest_status'] );
			} else {
				add_option( 'sp_suggest_status', $_POST['suggest_status'], null, 'no' );
			}
			
			if ( $this->cron_event !== false ) {
				update_option( 'sp_cron_event', $_POST['cron_event'] );
			} else {
				add_option( 'sp_cron_event', $_POST['cron_event'], null, 'no' );
			}
			
			if ( $this->custom_time !== false ) {
				update_option( 'sp_custom_time', $_POST['custom_time'] );
			} else {
				add_option( 'sp_custom_time', $_POST['custom_time'], null, 'no' );
			}
			
			wp_redirect( admin_url( 'admin.php?page='.$_GET['page'].'&updated=1' ) );
		}
		?>
		<div class="wrap" style=" margin:10px; padding:35px;">
			<h2 class="bottom-line"><?php _e( 'Willing2Buy Settings', 'email-format' );?></h2>
			
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page='.$_GET['page'].'&noheader=true' ) ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( 'email_format', 'save_email_format' ); ?>
				<div class="email_format_form">
					<table class="form-table" width="100%">
						<tr>
							<th scope="row" >Status </th>
							<td>
								<select name="suggest_status" id="suggest_status" class="email-input">
									<?php foreach ( $this->get_activeStatus() as $key => $value ): ?>
										<option value="<?php esc_attr_e( $key ); ?>"<?php esc_attr_e( $key == $this->suggest_status ? ' selected="selected"' : '' ); ?>><?php esc_attr_e( $value ); ?></option>
									<?php endforeach;?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row" >Subject </th>
							<td>
								<input class="input_height email-input" type="text" name="suggest_subject" placeholder="Subject" value="<?php echo $this->suggest_subject ?>">
							</td>
						</tr>
						<tr>
							<th scope="row" >Message </th>
							<td>
								<?php wp_editor( $this->suggest_message, 'suggest_message' ); ?> 
								<div style="margin-top: 10px;">Note : You can change the above default notification email.Please do not change the variable names. </div>
							</td>
						</tr>
						<tr>
							<th scope="row" >Cron Event </th>
							<td>
								<select name="cron_event" id="cron_event" class="email-input">
									<?php foreach ( $this->get_cronEvent() as $key => $value ): ?>
										<option value="<?php esc_attr_e( $key ); ?>"<?php esc_attr_e( $key == $this->cron_event ? ' selected="selected"' : '' ); ?>><?php esc_attr_e( $value ); ?></option>
									<?php endforeach;?>
								</select>
								<?php if($this->cron_event == 'custom'): ?>
									<input class="input_height email-input" type="number" name="custom_time" placeholder="(In days)" value="<?php echo $this->custom_time ?>">
								<?php else : ?>
									<input class="input_height email-input" type="number" name="custom_time" placeholder="(In days)" value="" style="display:none;">
								<?php endif; ?>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input id="email_format_submit" type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Submit' ) ?>" />
					</p>
				</div>
			</form>
		</div>
		<?php 
	}
	
	public function get_activeStatus() {
		return array(
				'disable' => 'Disable',	
				'enable' => 'Enable',
			);
	}
	
	public function get_cronEvent() {
		return array(
				'daily' => 'Daily',	
				'twicedaily' => 'Twice in a day',
				'hourly' => 'Hourly',
				'weekly' => 'Weekly',
				'everyminute' => 'Every Minute',
				'custom' => 'Custom (in days)',
			);
	}
	
}
