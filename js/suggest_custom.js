var ajaxurl = jQuery('#ajax_url').attr('data-value');

(function ($) {
  $(function () {
	  //Show or Hide suggest price link
	    var getSuggestPriceCookie = getCookie('productSuggestedPrice');
	   // alert(getSuggestPriceCookie);
	    if(getSuggestPriceCookie !== ''){
			var p_id = $('#suggest-product-id').attr('data-suggest-id');
			var str_array = getSuggestPriceCookie.split(',');
			for(var i = 0; i < str_array.length; i++) {
				if(str_array[i] != p_id){
					$('#suggestPriceLink').css('display','inline-block');
					//break;
				}else{
					$('#suggestPriceLink').css('display','none');
				}
			}
		}else{
			$('#suggestPriceLink').css('display','inline-block');
		}
	  
	  // When the user clicks on <span> (x), close the modal
	  var modal = document.getElementById('contactModal');
	  var span = document.getElementsByClassName("suggest-close")[0];
		span.onclick = function() {
			modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
  });
}(jQuery));


(function ($) {
	$(document).on('click','#suggestPriceLink',function(){
		//alert("sugest");
		var modal = document.getElementById('contactModal');
		modal.style.display = "block";
		$('.suggest-modal-header').children().text('How much you want to pay for this item?');
		$('#suggestPriceForm').show();
		$('#nameEmailForm').hide();
	});
}(jQuery));


(function ($) {
	$(document).on('click','#submit-price',function(){
		
		var product_id = $('input[name="product_id"]').val();
		var product_name = $('input[name="product_name"]').val();
		var price = $('input[name="user_price"]').val();
		
		if($.isNumeric(price)){
		
			$.ajax({
				method: "POST",
				url: ajaxurl,
				data: {
					'product_id':product_id,
					'product_name':product_name,
					'price':price,
					'action':'submit_price'
				},
				success:function(data) {
					$('.suggest-modal-header').children().text('Thanks!');
					$('input[name="last_insert_id"]').val(data);
					$('#suggestPriceForm').hide();
					$('#nameEmailForm').show();
					$('#suggestPriceLink').hide();
					setSuggestCookie(product_id);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		}else{
			$('input[name="user_price"]').next().show();
			$('input[name="user_price"]').css('border','1px solid red');
			return false;
		}
	});
}(jQuery));


function setSuggestCookie(id){
	var old_arr = getCookie('productSuggestedPrice');
	var arr = [];
	if(old_arr == ''){
		arr.push( id );
	}
	else{
		var str_array = old_arr.split(',');
		for(var i = 0; i < str_array.length; i++) {
			if(str_array[i] != id){
				arr.push( str_array[i] );
			}
		}
		arr.push( id );
	}
	
	var date = new Date();
	date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
	var expires = date.toUTCString();
	document.cookie = "productSuggestedPrice="+arr+";expires=" + expires;
}


(function ($) {
	$(document).on('click','#submit-nameEmail',function(){
		var modal = document.getElementById('contactModal');
		var last_insert_id = $('input[name="last_insert_id"]').val();
		var user_name = $('input[name="user_name"]').val();
		var user_email = $('input[name="user_email"]').val();
		
		var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		if(pattern.test(user_email)){
			$.ajax({
				method: "POST",
				url: ajaxurl,
				data: {
					'last_insert_id':last_insert_id,
					'user_name':user_name,
					'user_email':user_email,
					'action':'update_user_name_email'
				},
				success:function(data) {
					modal.style.display = "none";
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
			
		}else{
			$('input[name="user_email"]').next().show();
			$('input[name="user_email"]').css('border','1px solid red');
			return false;	
		}
	});
}(jQuery));


function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length,c.length);
		}
	}
	return "";
}




