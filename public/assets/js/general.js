jQuery(document).ready(function($){

	//vars
	var ajaxurl			= feature_request.ajaxurl,
		results         = $('#avfr-entry-form-results p'),
		thanks_voting   = feature_request.thanks_voting,
		already_voted   = feature_request.already_voted,
		error_message 	= feature_request.error_message,
		already_flagged = feature_request.already_flagged,
		form 			= $('#avfr-entry-form'),
		captcha_src     = $('#imgCaptcha').attr('src'),
		user_email 		= feature_request.user_email,
		reached_limit 	= feature_request.reached_limit,
		confirm_flag 	= feature_request.confirm_flag;

	var options = { 
        target:        '#avfr-entry-form-results p',        
        success:       showResponse,    
        beforeSubmit:  showRequest,    
        url: 		   ajaxurl                     
    };

    form.ajaxForm(options);

	// entry handler
  	function showRequest(formData, jqForm, options) {

  		var $this =form;

	   	if ( $.trim( $('#avfr-entryform-title').val() ) === '' ) {
	        $this.find("#avfr-entryform-title").css('border-color','#d9534f');
	        var _return = false;
	    }
	    if ( $.trim( $('#avfr-entryform-description').val() ) === '' ) {
	        $this.find("textarea[name='avfr-description']").css('border-color','#d9534f');
	        
	        var _return = false;
	    }
	    if ( _return === false ) {
	    	return false;
	    }
			$this.find(':submit').attr( 'disabled','disabled' );
			$('#avfr-entry-form-results').show();
			$('#avfr-entry-form-results').css({'display':'inline-block','background-color':'#afafaf'});
			$('#avfr-entry-form-results p').html('Sending data ...');
	}

   function showResponse(responseJson, statusText, xhr, $form)  {

   		$(results).html(responseJson.message);
   		$('#avfr-entry-form-results').css({'display':'inline-block','background-color':'#DD6D5A'});

   		if ( 'false' == responseJson.success ) {

   			$('.avfr-modal-footer input').removeAttr( 'disabled' );
   			
   		} else {

   			$('#avfr-entry-form-results').css('background-color','#53d96f');
   			setTimeout(function(){
   				window.location = window.location.pathname;
   			}, 1000);

   		}
	}

	// When user like / dislike / vote up 1
	$( '.avfr-wrap' ).on('click', '.avfr-submit', function(e) {

		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    'avfr_vote',
			post_id:   $this.data('post-id'),
			cfg: 	   $this.data('current-group'), // cfg = Current-Feature's Group
			votes:     $this.hasClass('avfr-set-vote-up') ? "+1" : "-1",
			nonce:     feature_request.nonce
		};
		if ( null === localStorage.getItem('email') || 'undefined' == localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = ( '' != user_email ) ? user_email : localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {

			if ( 'success' == response.response ) {

				$('#avfr-' + data['post_id'] ).find('.avfr-tooltip').css({'margin-top':'5px'});
				$('#avfr-' + data['post_id'] ).find('.avfr-like').hide();
				$('#avfr-' + data['post_id'] ).find('.avfr-tooltip .voting-buttons').html( thanks_voting );
				$('#avfr-' + data['post_id'] ).find('.avfr-tooltip span').html( response.remaining );
				$('#avfr-' + $this.data('post-id') + ' .avfr-totals-num').html(response.total_votes);
				localStorage.email = data['voter_email'];

			} else if ( 'already-voted' == response.response ) {

				alert( already_voted );

			} else if ( 'reached-limit' == response.response ) {

				alert( reached_limit );

			} else if ( 'email-warning' == response.response ) {

				alert( response.warning );

			} else {

				alert( 'Your remainig votes are '+ response.response );

			}

		});

	});

	// When user vote multiple
	$( '.avfr-wrap' ).on('click', '.avfr-votes-value', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    'avfr_vote',
			post_id:   $this.data('post-id'),
			votes:     $this.data('vote'),
			cfg: 	   $this.data('current-group'), // cfg = Current-Feature's Group
			nonce:     feature_request.nonce
		};
		if ( null === localStorage.getItem('email') || 'undefined' == localStorage.getItem('email')) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = ( '' != user_email ) ? user_email : localStorage.email;
		};
			$.post( ajaxurl, data, function(response) {

				if ( 'success' == response.response ) {
					
					$this.parent().parent().css({'margin-top':'-5px'});
					$this.parent().addClass('avfr-voted');
					$this.parent().nextAll('.small-text').find('span').html( response.remaining );
					$this.parent().html( thanks_voting );
					$('#avfr-' + $this.data('post-id') + ' .avfr-totals-num').html( response.total_votes );
					localStorage.email = data['voter_email'];
					$('#avfr-' + $this.data('post-id') + ' .avfr-vote-calc').addClass('voted');
					$('.voted').hide();

				} else if ( 'remaining-limit' == response.response ) {

					alert( remaining_limit );

				} else if ( 'already-voted' == response.response ) {

					alert( already_voted );

				} else if ( 'email-warning' == response.response ) {

					alert( response.warning );

				} else {

					alert( 'Your remainig votes are '+ response.response );

				}

			});

	});

	// When status chenged from fornt-end
	$( document ).on('click', '.avfr-change-status', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:     'process_change_status',
			post_id:    $this.data('post-id'),
			new_status: $this.data('val'),
			nonce:      feature_request.nonce
		};

		$.post( ajaxurl, data, function(response) {

			if ( response == 'success' ) {

				alert( 'Status changed.' );

			}

		});

	});

	// Whene user submit status change
	$( '.change-status-select' ).click( function (e) {
		e.preventDefault();

		var $this = $(this);

		$this.change(function(){

			$this.parent().nextAll(".avfr-change-status").attr('data-val', $this.find('option:selected').val());
		
		})

	});

	//calc remaining votes like/dislike/votes
	$('.avfr-wrap').on('click','.avfr-vote-calc', function(e) {
		e.preventDefault();

		var $this = $(this);

		$this.nextAll('.avfr-tooltip').show();
		e.stopPropagation();

		var voteClass = $this.hasClass('avfr-vote-up') ? 'avfr-set-vote-up' : 'avfr-set-vote-down';
		$this.nextAll('.avfr-tooltip').find('.avfr-submit').removeClass( 'avfr-set-vote-up avfr-set-vote-down' );
		$this.nextAll('.avfr-tooltip').find('.avfr-submit').addClass( voteClass );

		var data      = {
			action:    'avfr_calc_remaining_votes',
			post_id:   $this.data('post-id'),
			cfg: 	   $this.data('current-group'),
			nonce:     feature_request.nonce
		};

		if ( null === localStorage.getItem('email') || 'undefined' == localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = ( '' != user_email ) ? user_email : localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			$this.nextAll('.avfr-tooltip').find('span').html( response.response );
			if ( !( null == localStorage.getItem('email') || 'undefined' == localStorage.getItem('email') ) ) {
				$('.voting-buttons-title').hide();
			};
		})

	});

	$('#imgCaptcha').on('load', function() {
		$('#reload').removeClass('avfr-reload-animation');
	});

	$('#reload').click( function (e) {
		e.preventDefault();
		$('#imgCaptcha').attr('src',captcha_src+'?'+Math.random());
		$(this).addClass('avfr-reload-animation');
	});

	// When user report (flag)
	$( '.avfr-flag' ).click ( function(e) {
		var r = confirm(confirm_flag);
		if ( r == true ) {

			e.preventDefault();

			var $this = $(this);

			var data      = {
				action:    'avfr_add_flag',
				post_id:   $this.data('post-id'),
				cfg: 	   $this.data('current-group'), // cfg = Current Feature Group
				nonce:     feature_request.nonce
			};

			$.post( ajaxurl, data, function(response) {

				if ( response.response == 'success' ) {

					$this.addClass('avfr-flagged');
					$this.html(response.message);
				} else if ( 'already-flagged' == response.response ){
					alert( response.message );
				}

			});
		}

	});


	$( document ).on('click', '.avfr-tooltip', function(e) {
		$(this).nextAll('.avfr-tooltip').show();
		e.stopPropagation();
	});

	$( document ).click( function(e) {
		e.stopPropagation();
		$('.avfr-tooltip').hide();
	});

	//Filter buttons current link
	$(function(){
		$('.avfr-filter-control-item a').each(function() {
			if ($(this).prop('href') == window.location.href) {
				$(this).addClass('current');
			}
		});
	});

});
