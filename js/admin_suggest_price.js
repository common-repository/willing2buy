var ajaxurl = jQuery('#ajax_url').attr('data-value');
//Script for admin panel

(function ($) {
  $(function () {
	  // When the user clicks on <span> (x), close the modal
	  var modal = document.getElementById('viewDetailModal');
	  //var span = document.getElementsByClassName("suggest-close");
	  var spanClose = $(".suggest-close")[0];
	  if(spanClose){
		spanClose.onclick = function() {
			modal.style.display = "none";
		}
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
	$(document).on('click','#view-suggested-users',function(){
		var modal = document.getElementById('viewDetailModal');
		modal.style.display = "block";
	});
}(jQuery));


(function ($) {
	$(document).on('click','#email_format_submit',function(){
		var return_val = 1;
		var subject = $('input[name="suggest_subject"]').val();
		
		if(subject == ''){
			$('input[name="suggest_subject"]').css('border','1px solid red');
			return_val = 0;
		}
		else{
			$('input[name="suggest_subject"]').css('border','#ddd');	
		}
		
		var cron_event = $('select[name="cron_event"]').val();
		
		if(cron_event == 'custom'){
			var time = $('input[name="custom_time"]').val();
			if(time == ''){
				$('input[name="custom_time"]').css('border','1px solid red');
				return_val = 0;
			}
			else{
				$('input[name="custom_time"]').css('border','#ddd');
			}
		}
		
		if(return_val == 0){
			return false;
		}
		else{
			return true;
		}
	});
}(jQuery));


(function ($) {
	$(document).on('change','#cron_event',function(){
		var cron_event = $('select[name="cron_event"]').val();
		
		if(cron_event == 'custom'){
			$('input[name="custom_time"]').css('display','inline-block');
		}
		else{
			$('input[name="custom_time"]').css('display','none');	
		}
	});
}(jQuery));
