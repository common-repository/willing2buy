<?php
/**
Plugin Name: Willing2Buy Price Suggestion
Author: Medma Technologies
Author URI: http://www.medma.in/
Description: The extension helps Admin to collect price suggestions from customers for any product listed on Woocommerce store
Version: 1.0
*/


wp_enqueue_style( 'admin-mycss-suggest-price', plugins_url('css/suggest_mycss.css', __FILE__ ));
//Willing2Buy Menu

function createMainMenuWilling(){
	add_menu_page( __('Willing2Buy'), __('Willing2Buy') , 'willingto_buy', 'willingto-buy' );
}

add_action('admin_menu','createMainMenuWilling');

//echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';
////////////////////////////////////////////
/*         CRON DEMO STARTS HERE           */
/////////////////////////////////////////////


// unschedule event upon plugin deactivation
function cronstarter_deactivate() {	
	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('mycronjob');
	// unschedule previous event if any
	wp_unschedule_event ($timestamp, 'mycronjob');
} 
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');


// create a scheduled event (if it does not exist already)
function cronstarter_activation() {
	if( !wp_next_scheduled( 'mycronjob' ) ) { 
	   $cron_event = get_option( 'sp_cron_event' ); 
	   wp_schedule_event( time(), $cron_event, 'mycronjob' );  
	}
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'cronstarter_activation');


// here's the function we'd like to call with our cron job
function my_repeat_function() {
	
	global $wpdb,$product;
	
	$cronDetails = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix .'cron_email WHERE status = 0', OBJECT );
	$currency = get_woocommerce_currency_symbol();
	
	foreach($cronDetails as $detail){
		
		$product_id = $detail->product_id;
		$old_pice = $currency." ".$detail->old_price;
		$product_name = get_the_title( $product_id );
		$product_link = get_permalink( $product_id );
		$new_price = $currency." ".get_post_meta( $product_id, '_regular_price', true);
		
		if(!empty($detail->email)){
			// components for our email
			$recepients = $detail->email;
			$subject = getSession(get_option( 'sp_suggest_subject' ), $old_pice, $new_price, $product_name, $product_link);
			$message = getSession(wpautop( get_option( 'sp_suggest_message' ) ), $old_pice, $new_price, $product_name, $product_link);
			
			//$message = nl2br($message);
			add_filter( 'wp_mail_content_type','set_html_content_type' );
			$status = wp_mail( $recepients, $subject, $message );
			// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type','set_html_content_type' );
			
			if ($status) {
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix ."cron_email SET status = 1  WHERE id = ".$detail->id));
			}
		}
	}
}

// hook that function onto our scheduled event:
add_action ('mycronjob', 'my_repeat_function'); 

// CUSTOM INTERVALS
function cron_add_weekly( $schedules ) {
	// Adds once weekly to the existing schedules.
    $schedules['weekly'] = array(
	    'interval' => 604800,
	    'display' => __( 'Once Weekly' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_weekly' );


// add another interval
function cron_add_minute( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
	    'interval' => 60,
	    'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_minute' );


// add another interval
function cron_add_custom_seconds( $schedules ) {
	// Adds once every minute to the existing schedules.
	$time = get_option( 'sp_custom_time' ); 
	$t_seconds = $time * 24 * 60 * 60;
    $schedules['custom'] = array(
	    'interval' => $t_seconds,
	    'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_custom_seconds' );


function set_html_content_type() {
	return 'text/html';
}
	
	
function getSession($text, $old_pice, $new_price, $product_name, $product_link){
	preg_match_all("/\[[^\]]*\]/", $text, $matches);
	for($i=0;$i<count($matches[0]);$i++){
		$sess_value = str_replace(array( '[', ']' ), '', $matches[0][$i]);
		//$session = $_SESSION[$sess_value];//$_SESSION[$sess_value];
		if($sess_value == 'old_price'){
			$session = $old_pice;
		}
		else if($sess_value == 'new_price'){
			$session = $new_price;
		}
		else if($sess_value == 'product_name'){
			$session = $product_name;
		}
		else if($sess_value == 'product_link'){
			$session = $product_link;
		}
		
		$text = str_replace($matches[0][$i],$session,$text);
	}
	return $text;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////

$suggest_status = get_option( 'sp_suggest_status' );

if($suggest_status == 'enable'){
	add_action( 'woocommerce_single_product_summary', 'add_custom_field',20 );
}
//wp_enqueue_script( 'suggest-custom', plugins_url('js/suggest_custom.js', __FILE__ ));
wp_enqueue_style( 'custom-css-suggest-price', plugins_url('css/suggest_custom.css', __FILE__ ));
wp_enqueue_style( 'font-awesome-css-suggest', plugins_url('css/font-awesome_4.1.0/css/font-awesome.min.css', __FILE__ ));


function add_custom_field() {
    global $post, $product;
    $product_id =  $product->id ;
    //echo $product_id;exit;
	echo "<a href='#' id='suggestPriceLink' title='Suggest Your Price'><i class='fa fa-comment' aria-hidden='true'></i> Suggest W2B Price </a>";
	echo "<div id='suggest-product-id' data-suggest-id='".$product_id."'></div>";
	//echo get_post_meta( $post->ID, 'Product Code', true );

    return true;
}

function head_popup_suggest_price(){
	wp_enqueue_script( 'suggest-custom', plugins_url('js/suggest_custom.js', __FILE__ ));
	//wp_enqueue_style( 'custom-css-suggest-price', plugins_url('css/suggest_custom.css', __FILE__ ));
	//wp_enqueue_style( 'font-awesome-css-suggest', plugins_url('css/font-awesome_4.1.0/css/font-awesome.min.css', __FILE__ ));
	echo "<div data-value='".admin_url( 'admin-ajax.php' )."' id='ajax_url'></div>";
}

	
//~ function content_product_page($content){
	//~ global $product;
	//~ $product_id =  $product->id ;
	//~ $sugest = "<script>jQuery('.price').append('<a href=\'#\' id=\'suggestPriceLink\' title=\'Suggest Your Price\'>Suggest Price</a>');</script>";
	//~ $product_div = "<div id='suggest-product-id' data-suggest-id='".$product_id."'></div>";
	//~ //'.get_permalink($product_id).'
	//~ if(is_product())
		//~ return $content.''.$sugest.''.$product_div;
	//~ else
		//~ return $content;
//}

function foot_popup_suggest_price(){
	global $product;
	$product_id =  $product->id ;
	$product_name =  get_the_title( $product->id ); 
	//wp_enqueue_style( 'custom-css', plugins_url('css/custom.css', __FILE__ ));
	$content = '<form id="suggestPriceForm" action="javascript:void(0);">
					<input type="hidden" class="suggest-input-type" name="product_id" value="'.$product_id.'">
					<input type="hidden" class="suggest-input-type" name="product_name" value="'.$product_name.'">
					<input type="text" class="suggest-input-type" name="user_price" required="required" placeholder="Price">
					<div class="err-txt">Invalid Price Format!</div>
					<button id="submit-price">Submit</button> 
				</form>
				<form id="nameEmailForm" action="javascript:void(0);">
					<div class="notification">If we get similar suggestions from others, we will change price.</div>
					<input type="hidden" class="suggest-input-type" name="last_insert_id" value="">
					<label>Enter email to get notified when price changes</label>
					<input type="text" class="suggest-input-type" name="user_email" placeholder="Email">
					<div class="err-txt">Invalid Email Address!</div>
					<input type="text" class="suggest-input-type" name="user_name" placeholder="Name">
					<button id="submit-nameEmail">Submit</button> 
				</form>';
				
	echo '<div id="contactModal" class="suggest-modal"><div class="suggest-modal-content">
			<span class="suggest-close"><i class="fa fa-times-circle-o" aria-hidden="true"></i></span>
		  <div class="suggest-modal-header">
			<p style="margin-bottom: 0;"></p>
		  </div>
		  <div class="suggest-modal-body" id="suggest-modal-input">
			'.$content.'
		  </div>
		</div></div>';
		
}

function submitSuggestedPrice(){
	global $wpdb;
	$product_id = $_POST['product_id'];
	$product_name = $_POST['product_name'];
	$price = $_POST['price'];
	$time = current_time( 'mysql' );
	//echo $time;exit;
	$wpdb->insert( 
		$wpdb->prefix.'suggested_pice', 
		array( 
			'product_id' => $product_id, 
			'product_name' => $product_name,
			'price' => $price,
			'time' => $time,
		), 
		array( 
			'%d', 
			'%s',
			'%f',
			'%s',
		) 
	);
	$lastid = $wpdb->insert_id;
	echo $lastid;	
	wp_die();

}

function updateUserNameEmail(){
	//echo "test";exit;
	global $wpdb;
	
	$last_insert_id = $_POST['last_insert_id'];
	$user_name = $_POST['user_name'];
	$user_email = $_POST['user_email'];
	$table_name = $wpdb->prefix.'suggested_pice';
	//print_r($_POST);exit;
	$wpdb->query($wpdb->prepare("UPDATE $table_name SET name='$user_name',email='$user_email' WHERE id=$last_insert_id"));
	
	wp_die();
}


//add_filter('the_content', 'content_product_page');
add_action('wp_footer', 'foot_popup_suggest_price');
add_action('wp_head', 'head_popup_suggest_price');


//Submit Price
add_action('wp_ajax_submit_price', 'submitSuggestedPrice');
add_action('wp_ajax_nopriv_submit_price', 'submitSuggestedPrice');


//Update user name email
add_action('wp_ajax_update_user_name_email', 'updateUserNameEmail');
add_action('wp_ajax_update_user_name_email', 'updateUserNameEmail');


//Code to create DB in database
register_activation_hook(__FILE__,'suggestPriceTable');

function suggestPriceTable() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'suggested_pice';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		product_id mediumint(9) NOT NULL,
		product_name varchar(150) NOT NULL,
		price float(10,2) NOT NULL,
		name varchar(150) NULL,
		email varchar(150) NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

//Code to create DB in database
register_activation_hook(__FILE__,'cronEmail');

function cronEmail(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'cron_email';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			product_id mediumint(9) NOT NULL,
			old_price float(10,2) NOT NULL,
			email varchar(150) NOT NULL,
			status tinyint(4) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}



/****************************************Medma Home****************************************/
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'class-medma-willing-home.php';

add_action( 'plugins_loaded', array( 'Willing_home', 'get_instance' ) );

/***********************************WP Admin*****************************/
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'class-suggest-price.php';

add_action( 'plugins_loaded', array( 'Suggest_price', 'get_instance' ) );


/***********************************WP Email*****************************/
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'class-email-format.php';

add_action( 'plugins_loaded', array( 'Email_format', 'get_instance' ) );






