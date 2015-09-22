jQuery(document).ready(function($){

	//vars
	var ajaxurl			= feature_request.ajaxurl,
		results         = $('#avfr-entry-form-results p'),
		thanks_voting   = feature_request.thanks_voting,
		already_voted   = feature_request.already_voted,
		error_message 	= feature_request.error_message,
		thanks_flag     = feature_request.thanks_flag,
		already_flagged = feature_request.already_flagged,
		form 			= $('#avfr-entry-form'),
		captcha_src     = $('#imgCaptcha').attr('src'),
		reached_limit 	= feature_request.reached_limit;

	var options = { 
        target:        '#avfr-entry-form-results p',        
        success:       showResponse,    
        beforeSubmit:  showRequest,    
        url:    ajaxurl                     
    }; 

    form.ajaxForm(options);

	// entry handler
  	function showRequest(formData, jqForm, options) {

  		var $this =form;

	   	if ( $.trim( $('#avfr-entryform-title').val() ) === '' || $.trim( $('#avfr-entryform-description').val() ) === '' )  
	    {
	        $(results).html('Title and description are required.');
	        
	        $this.find('input[name="idea-title"]').css('border-color','#d9534f');
	        $this.find('textarea[name="idea-description"]').css('border-color','#d9534f');
	        return false;
	    }
			$this.find(':submit').attr( 'disabled','disabled' );
			$('#avfr-entry-form-results').show();
			$('#avfr-entry-form-results').css({'display':'inline-block','background-color':'#afafaf'});
			$('#avfr-entry-form-results p').html('Sending data ...');
	}

   function showResponse(responseJson, statusText, xhr, $form)  {

   	var json = $.parseJSON(responseJson);

   		$(results).html(json.message);
   		$('#avfr-entry-form-results').css({'display':'inline-block','background-color':'#DD6D5A'});

   		if ( 'false' == json.success ) {

   			$('.avfr-modal-footer input').removeAttr( 'disabled' );
   			
   		} else {

   			$('#avfr-entry-form-results').css('background-color','#53d96f');
   			setTimeout(function(){
   				location.reload();
   			}, 1000);

   		}
	}

	$('.avfr-wrap').on('click','.avfr-like', function(e) {
		e.preventDefault();

		var $this = $(this);

		$this.nextAll('.avfr-tooltip').show();
		e.stopPropagation();

		var voteClass = $this.hasClass('avfr-vote-up') ? 'avfr-set-vote-up' : 'iavfr-set-vote-down';
		$this.nextAll('.avfr-tooltip').find('.avfr-submit').removeClass( 'avfr-set-vote-up avfr-set-vote-down' );
		$this.nextAll('.avfr-tooltip').find('.avfr-submit').addClass( voteClass );

		var data      = {
			action:    'calc_remaining_votes',
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     feature_request.nonce
		};

		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			$this.nextAll('.avfr-tooltip').find('span').html( json.response );
			if ( null != localStorage.getItem('email') ) {
				$('.voting-buttons-title').hide();
			};
		})

	});

	// When user like / dislike / vote up 1
	$( '.avfr-wrap' ).on('click', '.avfr-submit', function(e) {

		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    $this.hasClass('avfr-set-vote-up') ? 'process_vote_up' : 'process_vote_down',
			user_id:   $this.data('user-id'),
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     feature_request.nonce
		};
		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			if ( 'success' == json.response ) {

				$('#' + data['post_id'] ).find('.avfr-tooltip').css({'margin-top':'5px'});
				$('#' + data['post_id'] ).find('.avfr-like').hide();
				$('#' + data['post_id'] ).find('.avfr-tooltip .voting-buttons').html( thanks_voting );
				$('#' + data['post_id'] ).find('.avfr-tooltip span').html( json.remaining );
				$('#' + $this.data('post-id') + ' .avfr-totals_num').html( json.total_vote );
				localStorage.email = data['voter_email'];

			} else if ( 'already-voted' == json.response ) {

				alert( already_voted );

			} else if ( 'reached-limit' == json.response ) {

				alert( reached_limit );

			} else {

				alert( error_message );

			}

		});

	});

	$('#imgCaptcha').on('load', function() {
		$('#reload').removeClass('if-reload-animation');
	});

	$('#reload').click( function (e) {
		e.preventDefault();
		$('#imgCaptcha').attr('src',captcha_src+'?'+Math.random());
		$(this).addClass('if-reload-animation');
	});


	// When user report (flag)

	$( '.avfr-flag' ).click ( function(e) {
		var r = confirm('Are you sure to report this idea as inappropriate ?');
		if ( r == false ) {

		} else {

			e.preventDefault();

			var $this = $(this);

			var data      = {
				action:    'process_flag',
				user_id:   $this.data('user-id'),
				post_id:   $this.data('post-id'),
				cig: 	   $this.data('current-group'), // cig = Current Idea Group
				nonce:     feature_request.nonce
			};

			$.post( ajaxurl, data, function(response) {

				if ( response == 'success' ) {

					$this.parent().addClass('avfr-flagged');
					$this.parent().html(thanks_flag);
				} else if ( 'already-flagged' == response ){
					alert( already_flagged );
				}

			});
		}

	});

	// When user vote multiple
	$( '.avfr-wrap' ).on('click', '.avfr-votes-value', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    'process_multi_vote',
			user_id:   $this.data('user-id'),
			post_id:   $this.data('post-id'),
			votes:     $this.data('vote'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     feature_request.nonce
		};
		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};
			$.post( ajaxurl, data, function(response) {
				var json = $.parseJSON(response);
				if ( 'success' == json.response ) {
					
					$this.parent().parent().css({'margin-top':'-5px'});
					$this.parent().addClass('avfr-voted');
					$this.parent().nextAll('.small-text').find('span').html( json.remaining );
					$this.parent().html( thanks_voting );
					$('#' + $this.data('post-id') + ' .iavfr-totals_num').html( json.total_votes );
					localStorage.email = data['voter_email'];
					$('#' + $this.data('post-id') + ' .avfr-vote-now').addClass('voted');
					$('.voted').hide();

				} else if ( 'remaining-limit' == json.response ) {

					alert( remaining_limit );

				} else if ( 'already-voted' == json.response ) {

					alert( already_voted );

				} else if ( 'email-warning' == json.response ) {

					alert( json.warning );

				} else {

					alert( 'Your remainig votes are '+ json.response );

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

				alert( 'Idea status changed.' );

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

	// When click button to see multivote dropdown
	$( document ).on('click', '.avfr-vote-now', function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.nextAll('.avfr-tooltip').show();
		e.stopPropagation();

		var data      = {
			action:    'calc_remaining_votes',
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     feature_request.nonce
		};

		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			$this.nextAll('.avfr-tooltip').find('span').html( json.response );
			if ( null != localStorage.getItem('email') ) {
				$('.voting-buttons-title').hide();
			};
		})
		
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
