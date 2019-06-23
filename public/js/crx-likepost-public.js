jQuery(function ($) {
	'use strict';

	$('.crx-like-button').on("click", function(e){
		e.preventDefault();
		let post_id = $(this).attr('id').split('-')[1];
		jQuery.post(like_ajax_obj.ajax_url, {'action': 'liked_action', 'post_id':post_id}, function(response) {
			//<i class="fa fa-thumbs-up" aria-hidden="true"></i>
			//<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			var r = JSON.parse(response);
			
			if ( r.type === 'n' ) {
				$('#like-'+post_id).find('i').removeAttr('class').addClass('fa fa-thumbs-o-up');
			} else if ( r.type === 'p' ) {
				$('#like-'+post_id).find('i').removeAttr('class').addClass('fa fa-thumbs-up');
			} else {}

			$('article#post-'+post_id).find('.like-count-notification').html(r.new_count);
			
		});
	});
});
