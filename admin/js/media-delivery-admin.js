(function( $ ) {
	'use strict';

	$(document).ready(function() {
		const postId = $('#post_ID').val();

		// Colour picker
		$('.color-picker').iris({
			change: (event, ui) => {
				$('#'+event.target.id+'-preview').css("background-color", ui.color.toString());
			}
		});

		// Notice dismissal
		$( document ).on( 'click', '.activation-notice .notice-dismiss', event => {
			$.ajax( ajaxurl,
			  {
				type: 'POST',
				data: {
				  action: 'dismiss_activation_notice',
				  notice: event.target.parentElement.id
				}
			});
		});

		// This funcation will add together the booking reference and name and show the 
		// error in the DOM if applicable.
		function validatePassLength() {
			var ref = $('#meta-fields-meta-box #meta-fields-booking-ref').val()
			var name = $('#meta-fields-meta-box #meta-fields-customer-surname').val()
			if(ref.length + name.length > 20) {
				$('#meta-fields-meta-box #meta-fields-error-msg').text(translatedStrings.passwordLengthError);
				$('#meta-fields-meta-box #meta-fields-error-msg').removeClass( 'hidden' );
			} else {
				$('#meta-fields-meta-box #meta-fields-error-msg').text('');
				$('#meta-fields-meta-box #meta-fields-error-msg').addClass( 'hidden' );
			}
		}

		$('#meta-fields-meta-box #meta-fields-booking-ref').on('change', () => {
			validatePassLength();
		});

		$('#meta-fields-meta-box #meta-fields-customer-surname').on('change', () => {
			validatePassLength();
		});

		// Improve this by subscribing to when the images or video is changed.
		$('#publish-media-box input#publish').on( 'click', event => {
			if ( $('.img-list li').length === 0 && !vidEmbed.html().trim() ) {
				event.preventDefault();
				$('#publish-media-box #publish-error-msg').text(translatedStrings.mediaMissingError);
				$('#publish-media-box #publish-error-msg').removeClass( 'hidden' );
			} else {
				$('#publish-media-box #publish-error-msg').text('')
				$('#publish-media-box #publish-error-msg').addClass( 'hidden' );
			}
		});

		/******************************************************************/
		/*                            IMAGES                              */
		/* See: https://codex.wordpress.org/Javascript_Reference/wp.media */
		/******************************************************************/
		// Set all variables to be used in scope
		var imgsFrame,
			metaBox = $('#images-meta-box.postbox'),
			imgList = metaBox.find( '.img-list'),
			addImgLink = metaBox.find('.upload-img'),
			delImgLink = imgList.find( '.delete-img');

		function addClickHandlers() {
			// DELETE IMAGE LINK
			delImgLink.on( 'click', (event) => {

				event.preventDefault();

				// Instead of working our way up, select by data attribute?
				$(event.target).parent().parent().parent().remove();
			});
		}

		// TODO: Running this manually is not ideal.
		// Better to subscribe to delImgLink changes to refresh
		// click handlers.
		addClickHandlers();

		// ADD IMAGE LINK
		addImgLink.on( 'click', event => {
			
			event.preventDefault();
			
			// If the media frame already exists, reopen it.
			if ( imgsFrame ) {
				imgsFrame.open();
				return;
			}
			
			// Create a new media frame
			imgsFrame = wp.media({
				title: translatedStrings.imgFrameTitle,
				button: {
					text: translatedStrings.imgFrameButton
				},
				library: {
					type: 'image',
					uploadedTo: postId,
					search: null,
				},
				frame: 'select',
				multiple: true,
			});
			
			$.extend( wp.Uploader.prototype, {
				success : (attachment) => {
					// this forces a refresh of the content
					imgsFrame.content.get().collection._requery(true);
				}
			});

			// When an image is selected in the media frame...
			imgsFrame.on( 'select', () => {
			
				// Get media attachment details from the frame state
				imgsFrame.state().get('selection').models.map(a => a.toJSON()).forEach((attachment, i) => {
					// Ignore anything that isn't an image
					if( attachment.mime.substr(0,attachment.mime.indexOf('/')) === 'image') {
						// Send the attachment URL to our img field to show user.
						// Send the attachment id to our hidden field for saving.
						var liField = '';
						var attachmentUrl = attachment.sizes.hasOwnProperty('thumbnail') ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;
						liField = liField.concat( '<li class="image" data-attachment_id="'+attachment.id+'">' );
						liField = liField.concat( '<img src="'+attachmentUrl+'" alt="" />' );
						liField = liField.concat( '<input class="img-id" name="img_ids['+i+']" type="hidden" value="'+attachment.id+'" />' );
						liField = liField.concat( '<ul class="actions">' );
						liField = liField.concat( '<li>' );
						liField = liField.concat( '<a class="delete-img" href="#" title="'+translatedStrings.deleteButton+'">' );
						liField = liField.concat( 'Delete' );
						liField = liField.concat( '</a>' );
						liField = liField.concat( '</li>' );
						liField = liField.concat( '</ul>' );
						liField = liField.concat( '</li>' );

						imgList.append( liField );

						// TODO: Remove manual running. See above.
						// Refresh the list of delete links
						delImgLink = imgList.find( '.delete-img');
						addClickHandlers();
					}
				});
			});

			// Finally, open the modal on click
			imgsFrame.open();
		});
		

		/******************************************************************/
		/*                            VIDEO                               */
		/* See: https://codex.wordpress.org/Javascript_Reference/wp.media */
		/******************************************************************/
		
		// Set all variables to be used in scope
		var videoFrame,
			metaBox = $('#video-meta-box.postbox'), // Your meta box id here
			addVidLink = metaBox.find( '.upload-vid' ),
			delVidLink = metaBox.find( '.delete-vid' ),
			vidShortcodeLoading = metaBox.find( '#video-loading' ),
			vidEmbed = metaBox.find( '#vid-embed' ),
			vidIdInput = metaBox.find( '.vid-id' );
		
		addVidLink.on( 'click', event => {
		
			event.preventDefault();
			
			// If the media frame already exists, reopen it.
			if ( videoFrame ) {
				videoFrame.open();
				return;
			}
			
			// Create a new media frame
			videoFrame = wp.media({
				title: translatedStrings.videoFrameTitle,
				button: {
					text: translatedStrings.videoFrameButton
				},
				library: {
					type: 'video',
					uploadedTo: postId,
					search: null,
				},
				multiple: false,
				frame: 'select',
			});
			
			// When an video is selected in the media frame...
			videoFrame.on( 'select', () => {
				// Get media attachment details from the frame state
				var attachment = videoFrame.state().get('selection').first().toJSON();

				// Ignore anything tha isn't a video
				if( attachment.mime.substr(0,attachment.mime.indexOf('/')) === 'video') {
				
					// Un-hide the add video link
					vidShortcodeLoading.removeClass( 'hidden' );

					$.ajax({
						url: ajaxurl,
						method: "POST",
						data: {
							src: attachment.url,
							action: 'fetch_video_shortcode'
						}				
					}).done(response => {
						vidEmbed.html(response.data);
				
						// Hide the delete video link
						vidShortcodeLoading.addClass( 'hidden' );	
					});
			
					// Send the attachment id to our hidden input
					vidIdInput.val( attachment.id );
			
					// Hide the add video link
					addVidLink.addClass( 'hidden' );
			
					// Unhide the remove video link
					delVidLink.removeClass( 'hidden' );
				}
			});
		
			// Finally, open the modal on click
			videoFrame.open();
		});
			
			
		delVidLink.on( 'click', event => {

			event.preventDefault();
		
			// Clear out the preview video
			vidEmbed.html( '' );
		
			// Un-hide the add video link
			addVidLink.removeClass( 'hidden' );
		
			// Hide the delete video link
			delVidLink.addClass( 'hidden' );
		
			// Delete the video id from the hidden input
			vidIdInput.val( '' );

		});

		$(".img-list").each((i, e) => {
			$(e).sortable();
		}); // Activate jQuery UI sortable feature

	});
})( jQuery );