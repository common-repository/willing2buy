<?php 

class Suggest_price{
	
	const VERSION = '1.0';

	
	protected $plugin_slug = 'suggest_pice';


	protected static $instance = null;
	
	
	/**
	 * Initialize the plugin by loading public scripts and styels or admin page
	 *
	 */
	public function __construct() {
		if ( is_admin() ) {
			
			if(!session_id()) {
				session_start();
			}
			
			//Display Field
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'woo_add_custom_general_fields') );
			
			//Save Field
			add_action( 'woocommerce_process_product_meta', array( $this, 'woo_add_custom_general_fields_save' ),20);
			
			//wp_enqueue_style( 'admin-mycss-suggest-price', plugins_url('css/suggest_mycss.css', __FILE__ ));
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
	
	
	public function woo_add_custom_general_fields() {
	
	  echo "<div data-value='".admin_url( 'admin-ajax.php' )."' id='ajax_url'></div>";
	  
	  global $woocommerce, $post, $wpdb, $product, $wp_session;
	  
      $price = get_post_meta( $post->ID, '_regular_price', true);
	  $_SESSION['old_price'] = $price;
	  
	  $poduct_id = $_GET['post'];
	  $currency = get_woocommerce_currency_symbol();
	  $this->no_of_users_suggested = $wpdb->get_results( 'SELECT COUNT(*) as suggestedPriceCount FROM '.$wpdb->prefix .'suggested_pice WHERE product_id = '.$poduct_id, OBJECT );
	  $this->average_of_suggested_price = $wpdb->get_results( 'SELECT AVG(price) AS suggestedAveragePrice FROM '.$wpdb->prefix .'suggested_pice WHERE product_id = '.$poduct_id, OBJECT );
		
	  echo '<div class="options_group">';
	  echo '<p>Number of Users Suggested Price: <span class="edit_suggested_price">'.$this->no_of_users_suggested[0]->suggestedPriceCount.'</span>';
	  if($this->no_of_users_suggested[0]->suggestedPriceCount > 0){
		  echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="button" id="view-suggested-users">View Details</a>';
	  }
	  echo '</p>';
	  echo '<p>Average Suggested Price ('.$currency.'): <span class="edit_suggested_price">'.round($this->average_of_suggested_price[0]->suggestedAveragePrice,2).'</span></p>';
	  
	  // Custom fields will be created here...
	  woocommerce_wp_checkbox( 
		array( 
			'id'            => '_checkbox', 
			'wrapper_class' => 'show_if_simple', 
			//'label'         => __('Notify all price subscribers?', 'woocommerce' ),
			'description'   => __( 'On Price change, notify Users who suggested price', 'woocommerce' ) 
			)
		);
	  
	  echo '<p><b>Note : </b>You can notify Users when you change product prices.</p>';
	  
	  echo '</div>';
	  
	  $details = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix .'suggested_pice WHERE product_id = '.$post->ID, OBJECT );
	 // print_r($details);exit;
	  echo '<div id="viewDetailModal" class="suggest-modal">
				<div class="suggest-modal-content" style="top:10%;">
					<span class="suggest-close" id="suggest-close"><i class="fa fa-times-circle-o suggest-icon-close" aria-hidden="true"></i></span>
					<div class="suggest-modal-header">
						<span style="margin-bottom: 0;">User Details</span>
					</div>
					<div class="suggest-modal-body">
						<table cellpadding="10" class="view-details-table view-table-class">
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Price</th>
							</tr>';
							foreach($details as $detail){
								echo '<tr>
									<td>'.$detail->name.'</td>
									<td class="break-word-cls">'.$detail->email.'</td>
									<td>'.$detail->price.'</td>
								</tr>';
							}
				echo 	'</table>
					</div>
				</div>
			</div>';
	}
	
	public function woo_add_custom_general_fields_save(  $post_id, $post) {
		global $post, $product, $wpdb, $wp_session;
		$woocommerce_checkbox = isset( $_POST['_checkbox'] ) ? 'yes' : 'no';
		//update_post_meta( $post_id, '_checkbox', $woocommerce_checkbox );
		
		
		//echo get_post_meta( $post->ID, '_regular_price', true );exit;
		$currency = get_woocommerce_currency_symbol();
		$_SESSION['new_price'] = get_post_meta( $post->ID, '_regular_price', true );
		$_SESSION['product_link'] = get_permalink( $post );
		$_SESSION['product_name'] = get_the_title( $post->ID );
		$old_price = $_SESSION['old_price'];
		
		
		if($woocommerce_checkbox == 'yes'){
			
			$table_name = $wpdb->prefix.'cron_email';
			//print_r($_POST);exit;
			$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE product_id = ".$post->ID));
			
			
			$details = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix .'suggested_pice WHERE product_id = '.$post->ID, OBJECT );
			foreach($details as $detail){
				if($detail->email != ''){
					$wpdb->insert( 
						$wpdb->prefix.'cron_email', 
						array( 
							'product_id' => $post->ID, 
							'old_price' => $old_price,
							'email' => $detail->email,
							'status' => 0,
							'time' => current_time( 'mysql' ),
						), 
						array( 
							'%d',
							'%f', 
							'%s',
							'%d',
							'%s',
						) 
					);
				}
			}
			
	
			//echo "test";exit;
			//~ $allEmail = $wpdb->get_results( 'SELECT email FROM '.$wpdb->prefix .'suggested_pice WHERE product_id = '.$post->ID, OBJECT );
			//~ $arr_email = [];
			//~ foreach($allEmail as $email){
				//~ if(!empty($email->email)){
					//~ $arr_email[] = $email->email;
				//~ }
			//~ }
			//~ 
			//~ //print_r($_SESSION);exit; 
			//~ 
			//~ if(!empty($arr_email)){
				//~ 
				//~ 
				//~ $to = $arr_email;
				//~ $subject = " Price Updated" ;
				//~ $content = "The price has been updated for following product.<br><br>".
					//~ "Product: <b>" . $product_name . "</b><br>" .
					//~ "Product link: <b><a href=" . get_permalink( $post ) . ">" . get_permalink( $post ) . "</a></b><br>".
					//~ "New Price of Product: <b>" . $currency."&nbsp;".$updated_pro_price . "</b>";
//~ 
				//~ $headers = array(
					//~ 'Reply-To' => 'Monika<monika.kesarwani@medma.in>',
				//~ );
				//~ $header .= "MIME-Version: 1.0\n";
				//~ $header .= "Content-Type: text/html; charset=utf-8\n";
				//~ $header .= "From: monika.kesarwani@medma.in" ;
//~ 
				//~ add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type') );
				//~ $status = wp_mail( $to, $subject, $content);
				//~ // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
				//~ remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ));
				//~ 
				//~ // If status correct then redirect the user to the product page again
				//~ if ( $status ){
					//~ //echo get_permalink( $post );exit;
					//~ wp_redirect( get_permalink( $post ) );
				//~ }
			//~ }
		}
	}
	
	public function set_html_content_type() {
		return 'text/html';
	}
	
	
	
}
?>
