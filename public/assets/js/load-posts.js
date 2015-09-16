jQuery(document).ready(function($) {

	var pageNum 	= parseInt(idea_factory.startPage) + 1,
		max 		= parseInt(idea_factory.maxPages),
		nextLink 	= idea_factory.nextLink,
		label    	= idea_factory.label,
		label_loading = idea_factory.label_loading;

	if(pageNum <= max) {
		$('.avfr--wrap')
			.append('<div class="avfr--layout-main clearfix avfr--layout-main-'+ pageNum +'"></div>')
			.append('<p class="avfr--loadmore fix"><a class="avfr--button" href="#">'+label+'</a></p>');

	}

	$('.avfr--loadmore a').click(function() {

		// Are there more posts to load?
		if(pageNum <= max) {

			// Show that we're working.
			$(this).text(label_loading);

			$('.avfr--layout-main-'+ pageNum).load(nextLink + ' .avfr--entry-wrap',
				function() {
					// Update page number and nextLink.
					pageNum++;
					nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);

					// Add a new placeholder, for when user clicks again.
					$('.avfr--loadmore').before('<div class="avfr--layout-main clearfix avfr--layout-main-'+ pageNum +'"></div>')

					// Update the button message.
					if(pageNum <= max) {
						$('.avfr--loadmore a').text(label);
					} else {
						$('.avfr--loadmore a').fadeOut();
					}

				}
			);
		}

		return false;
	});

});