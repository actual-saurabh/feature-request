jQuery(document).ready(function($){

	//vars
	var ajaxurl			= idea_factory.ajaxurl,
		results         = $('#idea-factory--entry--form-results p'),
		thanks_voting   = idea_factory.thanks_voting,
		already_voted   = idea_factory.already_voted,
		error_message 	= idea_factory.error_message,
		thanks_flag     = idea_factory.thanks_flag,
		already_flagged = idea_factory.already_flagged,
		form 			= $('#idea-factory--entry--form'),
		captcha_src     = $('#imgCaptcha').attr('src'),
		reached_limit 	= idea_factory.reached_limit;

	var options = { 
        target:        '#idea-factory--entry--form-results p',        
        success:       showResponse,    
        beforeSubmit:  showRequest,    
        url:    ajaxurl                     
    }; 

    form.ajaxForm(options);

	// entry handler
  	function showRequest(formData, jqForm, options) {

  		var $this =form;

	   	if ( $.trim( $('#idea-factory--entryform_title').val() ) === '' || $.trim( $('#idea-factory--entryform_description').val() ) === '' )  
	    {
	        $(results).html('Title and description are required.');
	        
	        $this.find('input[name="idea-title"]').css('border-color','#d9534f');
	        $this.find('textarea[name="idea-description"]').css('border-color','#d9534f');
	        return false;
	    }
			$this.find(':submit').attr( 'disabled','disabled' );
			$('#idea-factory--entry--form-results').show();
			$('#idea-factory--entry--form-results').css({'display':'inline-block','background-color':'#afafaf'});
			$('#idea-factory--entry--form-results p').html('Sending data ...');
	}

   function showResponse(responseJson, statusText, xhr, $form)  {

   	var json = $.parseJSON(responseJson);

   		$(results).html(json.message);
   		$('#idea-factory--entry--form-results').css({'display':'inline-block','background-color':'#DD6D5A'});

   		if ( 'false' == json.success ) {

   			$('.idea-factory-modal-footer input').removeAttr( 'disabled' );
   			
   		} else {

   			$('#idea-factory--entry--form-results').css('background-color','#53d96f');
   			setTimeout(function(){
   				location.reload();
   			}, 1000);

   		}
	}

	$('.idea-factory--wrap').on('click','.idea-factory-like', function(e) {
		e.preventDefault();

		var $this = $(this);

		$this.nextAll('.idea-factory-tooltip').show();
		e.stopPropagation();

		var voteClass = $this.hasClass('idea-factory-vote-up') ? 'idea-factory-set-vote-up' : 'idea-factory-set-vote-down';
		$this.nextAll('.idea-factory-tooltip').find('.idea-factory-submit').removeClass( 'idea-factory-set-vote-up idea-factory-set-vote-down' );
		$this.nextAll('.idea-factory-tooltip').find('.idea-factory-submit').addClass( voteClass );

		var data      = {
			action:    'calc_remaining_votes',
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     idea_factory.nonce
		};

		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			$this.nextAll('.idea-factory-tooltip').find('span').html( json.response );
			if ( null != localStorage.getItem('email') ) {
				$('.voting-buttons-title').hide();
			};
		})

	});

	// When user like / dislike / vote up 1
	$( '.idea-factory--wrap' ).on('click', '.idea-factory-submit', function(e) {

		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    $this.hasClass('idea-factory-set-vote-up') ? 'process_vote_up' : 'process_vote_down',
			user_id:   $this.data('user-id'),
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     idea_factory.nonce
		};
		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			if ( 'success' == json.response ) {

				$('#' + data['post_id'] ).find('.idea-factory-tooltip').css({'margin-top':'5px'});
				$('#' + data['post_id'] ).find('.idea-factory-like').hide();
				$('#' + data['post_id'] ).find('.idea-factory-tooltip .voting-buttons').html( thanks_voting );
				$('#' + data['post_id'] ).find('.idea-factory-tooltip span').html( json.remaining );
				$('#' + $this.data('post-id') + ' .idea-factory--totals_num').html( json.total_vote );
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

	$( '.idea-factory-flag' ).click ( function(e) {
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
				nonce:     idea_factory.nonce
			};

			$.post( ajaxurl, data, function(response) {

				if ( response == 'success' ) {

					$this.parent().addClass('idea-factory-flagged');
					$this.parent().html(thanks_flag);
				} else if ( 'already-flagged' == response ){
					alert( already_flagged );
				}

			});
		}

	});

	// When user vote multiple
	$( '.idea-factory--wrap' ).on('click', '.idea-factory-votes-value', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:    'process_multi_vote',
			user_id:   $this.data('user-id'),
			post_id:   $this.data('post-id'),
			votes:     $this.data('vote'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     idea_factory.nonce
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
					$this.parent().addClass('idea-factory-voted');
					$this.parent().nextAll('.small-text').find('span').html( json.remaining );
					$this.parent().html( thanks_voting );
					$('#' + $this.data('post-id') + ' .idea-factory--totals_num').html( json.total_votes );
					localStorage.email = data['voter_email'];
					$('#' + $this.data('post-id') + ' .idea-factory-vote-now').addClass('voted');
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
	$( document ).on('click', '.idea-factory-change-status', function(e) {
		e.preventDefault();

		var $this = $(this);

		var data      = {
			action:     'process_change_status',
			post_id:    $this.data('post-id'),
			new_status: $this.data('val'),
			nonce:      idea_factory.nonce
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

			$this.parent().nextAll(".idea-factory-change-status").attr('data-val', $this.find('option:selected').val());
		
		})

	});

	// When click button to see multivote dropdown
	$( document ).on('click', '.idea-factory-vote-now', function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.nextAll('.idea-factory-tooltip').show();
		e.stopPropagation();

		var data      = {
			action:    'calc_remaining_votes',
			post_id:   $this.data('post-id'),
			cig: 	   $this.data('current-group'), // cig = Current Idea Group
			nonce:     idea_factory.nonce
		};

		if ( null === localStorage.getItem('email') ) {
			data['voter_email'] = $this.parent().find('.voter-email').val();
		} else {
			data['voter_email'] = localStorage.email;
		};

		$.post( ajaxurl, data, function(response) {
			var json = $.parseJSON(response);
			$this.nextAll('.idea-factory-tooltip').find('span').html( json.response );
			if ( null != localStorage.getItem('email') ) {
				$('.voting-buttons-title').hide();
			};
		})
		
	});

	$( document ).on('click', '.idea-factory-tooltip', function(e) {
		$(this).nextAll('.idea-factory-tooltip').show();
		e.stopPropagation();
	});

	$( document ).click( function(e) {
		e.stopPropagation();
		$('.idea-factory-tooltip').hide();
	});

	//Filter buttons current link
	$(function(){
		$('.idea-factory-filter-control-item a').each(function() {
			if ($(this).prop('href') == window.location.href) {
				$(this).addClass('current');
			}
		});
	});

});
