(function ($) {

	if ($('.ihumbak-anchor-menu-wrapper').length) {

		var $anchors = $('.ihumbak-anchor');

		var $menu = $('<ul class="ihumbak-anchor-menu"></ul>');
		var beforeElementType = null
		var $h2 = null
		var str = ''
		var $headers = $anchors.map(function(index, elem) {
			var currentElementType = $(elem).prop('tagName').toLowerCase()
			var $currentElement = $(elem)

			var anchor=$(this).text();
			anchor = $(this).text().replace(/\s/g,'-');
			anchor = anchor.replace(/[^a-zA-Z0-9\-]/g, '');
			$(this).attr('id', anchor);

			var $a = '<a href="#' + anchor + '" class="ihumbak-anchor-link">' + $(this).text() + '</a>'

			if (currentElementType === 'h2' && beforeElementType === null) {
				str += '<li>'+$a
			} else if (currentElementType === 'h2' && beforeElementType === 'h3') {
				str += '</li></ul><li>' + $a
			} else if (currentElementType === 'h2' && beforeElementType === 'h2') {
				str += '</li><li>' + $a
			}

			if (currentElementType === 'h3' && beforeElementType === 'h2') {
				str += '<ul><li>' + $a
			} else if (currentElementType === 'h3' && beforeElementType === 'h3') {
				str += '</li><li>' + $a
			}



			beforeElementType = $(elem).prop('tagName').toLowerCase()


		})

		str =  (str + '</li>')


		$('.ihumbak-anchor-menu-wrapper').append($('<ul class="ihumbak-anchor-menu">' + str + '</ul>'));


		$(document).on('click', '.ihumbak-anchor-link', function (e) {
			var href = $(e.target).attr('href');
			var scrollTop = $(href).offset().top;
			$('html, body').animate({
				scrollTop: scrollTop - 168
			}, 300);
		});
		var $hide = $('.ihumbak-anchor-menu-hide');

		$hide.on('click', function(e) {
			e.preventDefault();
			$('.ihumbak-anchor-menu-wrapper ul').hide(200);
			$(this).addClass('ihumbak-anchor-d-none');
			$('.ihumbak-anchor-menu-show').removeClass('ihumbak-anchor-d-none');
		});
		var $show = $('.ihumbak-anchor-menu-show');

		$show.on('click', function(e) {
			e.preventDefault();
			$('.ihumbak-anchor-menu-wrapper ul').show(200);
			$(this).addClass('ihumbak-anchor-d-none');
			$('.ihumbak-anchor-menu-hide').removeClass('ihumbak-anchor-d-none');
		});
	}
})(jQuery)
