(function( $ ) {
	'use strict';

	$( document ).on('ready', () => {
		/**
		*	This function will combine the form data from the two input boxes
		* 	and pass it on to the submission as a single variable because 
		* 	it uses the built in WP password protection which only has one
		* 	password on a post. 
		*/
		var form = document.querySelector("form.dual-password-form");
		
		if (form) {
			form.addEventListener('formdata', (e) => {
			const formData = e.formData; 
			
			formData.set('post_password', e.formData.get('booking_ref') + e.formData.get('surname').toUpperCase());
			formData.delete('booking_ref');
			formData.delete('surname');
		  });
		}

		// Set the lightbox options
		lightbox.option({
			'disableScrolling': true,
			'wrapAround': true
		});

		var postId = $('body').attr('class').match(/postid-(\d+)/)[1];

		$( '.media-delivery-container .media-delivery-dl-btn' ).on('click', event => {
			$('.media-delivery-container #download-error-'+postId).text('');
			$('.media-delivery-container #download-error-'+postId).addClass('hidden');
			
			$(event.target).prop('disabled', true);
			$(event.target).text(translatedStrings.loading);

			$.ajax({
				url: ajaxurl,
				method: "POST",
				data: {
					post_id: postId,
					action: 'download_images_zip'
				}		
			}).done(response => {
				if(response.success) {
					document.location = response.data.url;
				} else {
					$('.media-delivery-container #download-error-'+postId).text(response.data.message);
					$('.media-delivery-container #download-error-'+postId).removeClass('hidden');
				}
				$(event.target).text(translatedStrings.download);
				$(event.target).prop('disabled', false);
			});
		});
	});

})( jQuery );
