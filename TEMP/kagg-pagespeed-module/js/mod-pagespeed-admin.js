jQuery(document).ready(function ($) {
	var msgTimer;

	function clearMessages() {
		$('#ps-success').html('').hide();
		$('#ps-error').html('').hide();
		clearTimeout(msgTimer);
	}
	
	function showMessage( el, message) {
		$(el).html(message).slideDown('slow');
		msgTimer = setTimeout( function() {
			$(el).html('').slideUp('slow');
		}, 5000 );
	}
	
	function ajaxCall( data ) {
		clearMessages();
		$.ajax({
			url: mod_pagespeed.ajax_url,
			type: 'post',
			data: data,
			success: function (response) {
				if (response.success) {
					showMessage('#ps-success', response.data);
				} else {
					showMessage('#ps-error', response.data);
				}
			},
			error: function (response) {
				showMessage('#ps-error', response.data);
			}
		});
	}

	$('#purge_styles, #purge_entire_cache').on('click', function () {
		var data = {
			action: 'mod_pagespeed',
			id: $(this).attr('id'),
			nonce: mod_pagespeed.nonce
		};
		ajaxCall(data);
	});

	$('#dev_mode').on('change', function() {
		var checked = $(this).is(':checked');
		if (checked) {
			$(this).parent().addClass('active');
		} else {
			$(this).parent().removeClass('active');
		}
		var data = {
			action: 'mod_pagespeed',
			id: $(this).attr('id'),
			checked: checked,
			nonce: mod_pagespeed.nonce
		};
		ajaxCall(data);
	});

});
