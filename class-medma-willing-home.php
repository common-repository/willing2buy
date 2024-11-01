<?php 

class Willing_home{
	//
	const VERSION = '1.0';

	
	protected $plugin_slug = 'willing_home';


	protected static $instance = null;


	/**
	 * Initialize the plugin by loading public scripts and styels or admin page
	 *
	 */
	public function __construct() {
		
		if ( is_admin() ) {
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'my-style-css', plugins_url('css/my-style.css', __FILE__ ));
			add_action( 'admin_menu', array( $this, 'plugin_admin_menu' ) );
			
			//wp_enqueue_script( 'my-script-handle', plugins_url('js/my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
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
		add_submenu_page( 'willingto-buy', __( 'Home Page' ), __( 'Home Page' ), 8,'willingtobuy_home', array( $this, 'medma_menu_options' ) );
	}
	
	
	public function medma_menu_options() {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		//wp_redirect( admin_url( 'admin.php?page='.$_GET['page'].'&updated=1' ) );
		
		?>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			
			.container
			{
			margin-top:25px;
			width:100%;
			}
			.header_title
			{
			width:45%;
			text-align:center;
			float:left;
			}
			.logo
			{
			width:15%;	
			}
			#company
			{
			padding: 0;
			font-size: 40px;
			letter-spacing: 0;
			line-height: 1.3em;
			margin-top:0 !important;
			}
			#company p
			{
			  font-size: 40px;
			  margin:0;
			}
			.header_div
			{
			width:40%;
			float:left;
			}
			.header_box
			{
			width: 100%;
			padding: 2px 15px 2px 15px;
			border: 1px solid #DADADA;
			margin: 0;
			height: auto;
			}
			.news_heading
			{
				border-bottom: 1px solid #E5E5E5;
				margin: 0px;
				padding: 8px 0 6px 0 !important;
				margin-bottom: 8px;
			}
			.main-section
			{
			width:99%;
			float:left;
			height:100px;
			border:2px solid gray;
			margin-top:20px;
			}
			.footer-section
			{
			width:99%;
			float:left;
			height:150px;
			border:2px solid gray;
			margin-top:20px;
			}
			.tabs {
				width:100%;
				display:inline-block;
			}
			 /*----- Tab Links -----*/
			  
			.tab-links:after {
				display:block;
				clear:both;
				content:'';
			}

			.tab-links li {
				margin:0px 5px;
				float:left;
				list-style:none;
			}
			 
			.tab-links a {
				padding:9px 15px;
				display:inline-block;
				border-radius:3px 3px 0px 0px;
				background:#E5E5E5;
				font-size:16px;
				font-weight:600;
				color:#777;
				transition:all linear 0.15s;
				 text-decoration:none;
			}
			 
			.tab-links a:hover {
				background:#D4D4D4;
				text-decoration:none;
			}
			 
			li.active a, li.active a:hover {
				background:#B3B3B3;
				color:#4c4c4c;
			}

			/*----- Content of Tabs -----*/
			.tab-content {
				padding:15px;
				border-radius:3px;
				background:#fff;
				border-top: 1px solid #E5E5E5;
			}

			.tab {
				display:none;
			}

			.tab.active {
				display:block;
			}

			.row {
				margin-right: 0 !important;
				margin-left: 0 !important;
			}
			.tab-links
			{
				padding-left: 0 !important;
				margin-top:40px !important;
				margin-bottom:0 !important;
			}	

			.footer-block:after {
				 display: inline-block;
				clear:both;
				content:'';
				 margin: 0;
				 padding: 0;
			   
			}
			.footer-block li {
				margin:20px 20px;
				float:left;
				list-style:none;
				display:inline;

			}
			.footer-link
			{
				text-align:center;
			}
			.rate_widget {
				overflow:   visible;
				padding:    10px;
				position:   relative;
				width:      180px;
				height:     32px;
			}
			.star1 {
				background: url('histar.png') no-repeat;
				float:      left;
				height:     28px;
				padding:    2px;
				width:      32px;
				display:none;
			}
			.star2 {
				background: url('histar.png') no-repeat;
				float:      left;
				height:     28px;
				padding:    2px;
				width:      32px;
				display:none;
			}
			.star3 {
				background: url('histar.png') no-repeat;
				float:      left;
				height:     28px;
				padding:    2px;
				width:      32px;
				display:none;
			}
			.star4 {
				background: url('histar.png') no-repeat;
				float:      left;
				height:     28px;
				padding:    2px;
				width:      32px;
				display:none;
			}
			.star5 {
				background: url('histar.png') no-repeat;
				float:      left;
				height:     28px;
				padding:    2px;
				width:      32px;
				display:none;
			}
			.ratings_vote {
				background: url('histar.png') no-repeat;
			}
			.ratings_over {
				background: url('histar.png') no-repeat;
			}

			.about-block
			{
				padding: 30px 5px;
			}
			.about-block a
			{
				font-size:22px !important;
				color: #555;
				text-decoration: none
			}
			.about-block a:hover
			{
				color:#23527c;
				text-decoration:none !important;
			}
			.icon
			{
				text-align:center !important;
				font-size: 34px;
			}
			.extension-block
			{
			 padding :30px 5px;
			 text-align: center;
			vertical-align: middle;
			}
			.pay_text_row
			{
				padding: 30px 5px;
				vertical-align: middle;
				text-align: center;
			}
			.free_text_row
			{
				padding: 30px 5px;
				vertical-align: middle;
				text-align: center;
			}
			.offers_text_row
			{
				padding: 30px 5px;
				vertical-align: middle;
				text-align: center;
			}
			#new_version
			{
				display:none;
			}
			#ext_name
			{
				float: left;
			}
			#ext_version
			{
				font-size: 17px !important;
				color: #C1C1C1;
				float: left;
				line-height: 22px;
				font-style:italic;
			}
			#new_version a
			{
				font-size:17px !important;
				color:#FF0000;
				line-height: 22px;
				margin-left: 5px;
			}
			#ext_desc
			{
				font-size:16px !important;
				color:#AEACAC;
				float: left;
				width: 100%;
				margin:0;
			}
			#pay_tab img, #free_tab img, #offers_tab img{max-width: 240px;width: 100%;}
			.rating{color: #ff5501;}
			#pay_tab .col-md-3, #free_tab .col-md-3, #offers_tab .col-md-3{height:400px;}
			.BuyButton {background: #337ab7;
				color: #FFFFFF;
				padding: 5px 20px;
				border: 1px solid #25649A;
				border-radius: 4px;
			}
			.BuyButton:hover {color: #FFFFFF;text-decoration:none;background-color:#1B466B;}
			.tab-content p {
				margin: 11px 0 10px;
			}
			.copyright {border-top:1px solid #EBEBEB;padding: 10px 0px;}
			.copyright, .copyright a{color:#888787;}
		</style>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				<!--div class="col-md-2">
					<div class="logo"><img src="http://www.medma.net/home/images/medma_infomatix_logo.png" alt="logo" id="logo_img"/></div>
				</div-->
					<div class="col-md-8">
						<div class="row">
							<h1 id="company">
								<p id="ext_name">[Extension Name]</p>  
							</h1>
						</div>
						<div class="row">
							<span id="ext_version">Version <span id="ver_text">1.0.0</span></span>
							<span id="new_version">
								<p>&nbsp;<a href="" id="new_vers"/>New Version <span id="new_ver_no"></span> available!!</a></p>
							</span>
						</div>
						
						<p id="ext_desc"></p>
					</div>
					<div class="col-md-4">
						<div class="header_box">
							<h2 class="news_heading">Latest News</h2>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="tabs">
						<ul class="tab-links">
							<li class="active"><a href="#about_tab">About</a></li>
							<li><a href="#offers_tab">Special Offers </a></li>
							<li><a href="#pay_tab">Paid Extensions</a></li>
							<li><a href="#free_tab">Free Extensions</a></li>
						</ul>
						<div class="tab-content">
							<div id="about_tab" class="tab active">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-6">
											<div class="row">
												
												
												<div class="col-md-4 about-block">
													<p style="text-align:center;"><i class="fa fa-rocket icon" aria-hidden="true"></i><br/></p>
													<p style="text-align:center;"><a href="" id="demo_link"/>Demo</a></p>
												</div>												
												<div class="col-md-4 about-block">
													<p style="text-align:center;"><i class="fa fa-file-video-o icon" aria-hidden="true"></i><br/></p>
													<p style="text-align:center;"><a href="" id="video_demo_link"/>Video Demo</a></p>
												</div>
												<div class="col-md-4 about-block">
													<p style="text-align:center;"><i class="fa fa-book icon" aria-hidden="true"></i><br/></p>
													<p style="text-align:center;"><a href="" id="manual_link"/>Manual</a></p>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6 about-block">
													<p style="text-align:center;"><i class="fa fa-ticket icon" aria-hidden="true"></i><br/></p>
													<p style="text-align:center;"><a href="" id="support_tick_link"/>Create Support Ticket</a></p>
												</div>
												<div class="col-md-6 about-block">
													<p style="text-align:center;"><i class="fa fa-question-circle-o icon" aria-hidden="true"></i><br/></p>
													<p style="text-align:center;"><a href="" id="knowl_link"/>KnowledgeBase</a></p>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="extension-block">
												<img src="http://www.medma.net/home/images/medma_infomatix_logo.png" alt="logo" id="ext_logo"/>
											</div>
										</div>
									</div>
									
								</div>
							</div>
							<div id="pay_tab" class="tab">
								<div class="row pay_text_row">
								</div>
							</div>
							<div id="free_tab" class="tab">
								<div class="row free_text_row">
								</div>
							</div>
							<div id="offers_tab" class="tab">
								<div class="row offers_text_row">
								</div>
							</div>							
						</div>
						<div class="col-md-12 copyright">
							Copyright <?php echo date('Y');?> <a href="" id="company_url">Medma Infomatix</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	<script>
	//script for tabs//
	jQuery(document).ready(function() {
		jQuery('.tabs .tab-links a').on('click', function(e)  {
			 var currentAttrValue = jQuery(this).attr('href');
			 jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
			jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
			e.preventDefault();
	 });
	 //script for fetch data//
	  var id= '15';
	  var version= '1.0';
	  var siteProtocol = "https";
	  //Ajax Call
	   $.ajax({
				url:siteProtocol+'://www.medma.in/apps/public/extension',
				type: 'POST', 
				data:{'id':id,'version':version} ,
				success: function (response) {
					 console.log(response);
					   var i;
					   for(i=0;i<response.news.length;i++)
					   {
							  var newsId=response.news[i].id;
							  var newsLink=response.news[i].link; 
							  var news=response.news[i].news;
							  $('.header_box').append('<p class="news_cont'+i+'"><span id="news_id'+i+'" style="display:none;"></span><a href="" id="news_link'+i+'"><span id="news'+i+'"></span></a></p>');
							  $('#news_id'+i).text(newsId); 
							  $('#news'+i).html(news); 
							  $('#news_link'+i).attr('href',newsLink);
					   }
					   //console.log(response.data);
					   for(i=0;i<response.data.length;i++)
					   {
							   var company=response.data[i].company_name;
							   var logo=response.data[i].company_logo;
							   var new_version=response.data[i].new_version;
							   var ver_link=response.data[i].link;
							   var new_ver_name=response.data[i].name;
							   var version=response.data[i].version;
							 $('#support_tick_link').attr('href',response.data[i].support_link);
							  $('#knowl_link').attr('href',response.data[i].knowledgebase_link);
							  $('#demo_link').attr('href',response.data[i].demo_link);
							  $('#manual_link').attr('href',response.data[i].manual_link);
							  $('#video_demo_link').attr('href',response.data[i].video_demo_link);
							  $('#ext_logo').attr('src',response.data[i].extension_logo);
							  $('#ext_name').text(response.data[i].name);
							  //$('#ext_version').text(response.data[i].version);
							  $('#ext_desc').text(response.data[i].description);
							  $('#company_url').attr('href',response.data[i].company_url);
							  
						}
						//alert($('#ver_text').text());alert(version);
						if($('#ver_text').text() != version)
						{
							$('#new_version').css('display','block');
							$('#new_vers').attr('href',ver_link);
							$('#new_ver_no').text(version);
						}
						//console.log(response.extensions.free);
						for(i=0;i<response.extensions.free.length;i++)
					   {
							var freeImg=response.extensions.free[i].image;
							var freeName=response.extensions.free[i].name;
							var freeRating=response.extensions.free[i].rating;
							var freeRatingElm = RatingElm(freeRating);
							var freePrice=response.extensions.free[i].price;
							var freeLink=response.extensions.free[i].link;
							//$('.free_text_row').append('<div class="col-md-3"><img id="freeext_img'+i+'" src=""/><p id="freeext_desc'+i+'"></p><p id="freeext_rating'+i+'"></p><p id="freeext_price'+i+'"></p><a href="" id="freeext_link'+i+'" class="BuyButton">Buy</a></div>');
							$('.free_text_row').append('<div class="col-md-3"><img id="freeext_img'+i+'" src=""/><p id="freeext_desc'+i+'"></p><p id="freeext_rating'+i+'"></p><a href="" id="freeext_link'+i+'" class="BuyButton">BUY</a></div>');
							$('#freeext_img'+i).attr('src',freeImg);
							$('#freeext_desc'+i).text(freeName);
							$('#freeext_rating'+i).html(freeRatingElm);
							$('#freeext_price'+i).text("$"+freePrice);
							$('#freeext_link'+i).attr('href',freeLink);
						 }
						for(i=0;i<response.extensions.paid.length;i++)
					   {
								var paidImg=response.extensions.paid[i].image;
								var paidDesc=response.extensions.paid[i].name;
								var paidRating=response.extensions.paid[i].rating;
								var paidRatingElm = RatingElm(paidRating);
								var paidPrice=response.extensions.paid[i].price;
								var paidLink=response.extensions.paid[i].link;
								$('.pay_text_row').append('<div class="col-md-3"><img id="payext_img'+i+'" src=""/><p id="payext_desc'+i+'"></p><p id="payext_rating'+i+'"></p><p id="payext_price'+i+'"></p><a href="" id="payext_link'+i+'" class="BuyButton">BUY</a></div>');
								$('#payext_img'+i).attr('src',paidImg);
								$('#payext_desc'+i).text(paidDesc);
								$('#payext_rating'+i).html(paidRatingElm);
								$('#payext_price'+i).text("$"+paidPrice);
								$('#payext_link'+i).attr('href',paidLink);
						}
						for(i=0;i<response.extensions.offers.length;i++)
					   {
								var offersImg=response.extensions.offers[i].image;
								var offersDesc=response.extensions.offers[i].name;
								var offersRating=response.extensions.offers[i].rating;
								var offersRatingElm = RatingElm(offersRating);
								var offersPrice=response.extensions.offers[i].price;
								var offersLink=response.extensions.offers[i].link;
								$('.offers_text_row').append('<div class="col-md-3"><img id="offerext_img'+i+'" src=""/><p id="offerext_desc'+i+'"></p><p id="offerext_rating'+i+'"></p><p id="offerext_price'+i+'"></p><a href="" id="offerext_link'+i+'" class="BuyButton">BUY</a></div>');
								$('#offerext_img'+i).attr('src',offersImg);
								$('#offerext_desc'+i).text(offersDesc);
								$('#offerext_rating'+i).html(offersRatingElm);
								$('#offerext_price'+i).text("$"+offersPrice);
								$('#offerext_link'+i).attr('href',offersLink);
								}
						},
				   
				});
		});
		function RatingElm(Rating) {
			var rateElm = '';
			for(var i=1;i<=5;i++) {
				if(i<=Rating)
					rateElm += '<i class="fa fa-star rating" aria-hidden="true"></i> ';
				else
					rateElm += '<i class="fa fa-star-o" aria-hidden="true"></i> ';
			}
			return rateElm;
		}
	</script>
		<?php
	}
	
	
	
}
?>
