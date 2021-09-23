(function ($) {
	$(function () {
		$('#wp-admin-bar-surecache-autopurge-manual-purge a').click(function (e) {
			e.preventDefault();
			var container = $('#surecache-autopurge-admin-notices');
			// ugly hack to create a container div for our notices in the right location
			container.removeClass('notice').show();
			$.post(
				ajaxurl,
				{
					action: 'surecache_autopurge_manual_purge',
					wp_nonce: $.trim($('#surecache-autopurge-purge-wp-nonce').text())
				},
				function (r) {
					try {
						var response = JSON.parse(r);
					} catch (error) {
						var response = {success: false, message: error}
					}
					var noticeClass = 'notice-success';
					if (!response.success) {
						noticeClass = 'notice-error';
					}
					var notice = $('<div class="notice ' + noticeClass + '"><p>' + response.message + '</p></div>');
					container.append(notice);
					notice.on('click', function () {
						$(this).remove();
					});
					notice.delay(3000).fadeOut();
				}
			);
		});
	});
}(jQuery));
