(function ( $ ) {
	"use strict";

	$(function () {

		// Place your administration-specific JavaScript here
		$(document).ready(function() {	
			/* Hide Count for 'All' list */
			//$("li.all").find("span.count").remove(); // not needed, CSS took care of it

            // AJAX - Update Offer Status - Accepted Offer
			$('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-accept').click(function(){
				var targetID = $(this).attr('data-target');
				var data = {
					'action': 'approveOfferFromGrid',
					'targetID': targetID
				};
				
				$.post(ajaxurl, data, function(response) {					
					$('tr.post-'+targetID+'.type-woocommerce_offer').addClass('status-accepted-offer');
					$('tr.post-'+targetID+'.type-woocommerce_offer').removeClass('status-publish');
					
					var oldColumnDateVal = $('tr.post-'+targetID+'.type-woocommerce_offer td.column-date abbr').attr('title');
					$('tr.post-'+targetID+'.type-woocommerce_offer .column-date').html("<abbr title='"+oldColumnDateVal+"'></abbr>Accepted");
					
					// modify post status icon css
					$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').removeClass('pending').removeClass('trash').removeClass('declined');
					$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').addClass('accepted');
					$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').attr('title', 'Offer Status: Accepted');
					$('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').html('Accepted');

					var previousPendingCountBubbleValue = $('#woocommerce-offers-count .pending-count').html();
					var newPendingCount = (previousPendingCountBubbleValue - 1);
					$('#woocommerce-offers-count .pending-count').html(newPendingCount);
					if(newPendingCount == 0)
					{
						$('#woocommerce-offers-count').fadeOut('slow');
					}
					
					// remove accept action link
					$('#woocommerce-offer-post-action-link-accept-id-'+targetID+'').parent('li').hide();
					return true;
				});
				return false;
			});

            // AJAX - Update Offer Status - Declined Offer
            $('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-decline').click(function(){

                if(confirm('are you sure?'))
                {
                    var targetID = $(this).attr('data-target');
                    var data = {
                        'action': 'declineOfferFromGrid',
                        'targetID': targetID
                    };

                    // post it
                    $.post(ajaxurl, data, function(response) {

                        if($('tr.post-'+targetID+'.type-woocommerce_offer').hasClass('status-publish'))
                        {
                            var previousPendingCountBubbleValue = $('#woocommerce-offers-count .pending-count').html();
                            var newPendingCount = (previousPendingCountBubbleValue - 1);
                            $('#woocommerce-offers-count .pending-count').html(newPendingCount);
                            if(newPendingCount == 0)
                            {
                                $('#woocommerce-offers-count').fadeOut('slow');
                            }
                        }

                        $('tr.post-'+targetID+'.type-woocommerce_offer').addClass('status-declined-offer');
                        $('tr.post-'+targetID+'.type-woocommerce_offer').removeClass('status-accepted-offer');
                        $('tr.post-'+targetID+'.type-woocommerce_offer').removeClass('status-publish');

                        // modify post status icon css
                        $('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').removeClass('pending').removeClass('trash').removeClass('accepted');
                        $('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').addClass('declined');
                        $('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').attr('title', 'Offer Status: Declined');
                        $('#woocommerce-offer-post-status-grid-icon-id-'+targetID+' i').html('Declined');

                        // modify action links on post
                        $('#woocommerce-offer-post-action-link-manage-id-'+targetID+'').html('Manage Offer');

                        // remove accept and decline action links
                        $('#woocommerce-offer-post-action-link-decline-id-'+targetID+'').parent('li').hide();
                        $('#woocommerce-offer-post-action-link-accept-id-'+targetID+'').parent('li').hide();
                        return true;

                        // remove the declined post
                        //$('tr.post-'+targetID+'.type-woocommerce_offer').slideToggle('slow');
                        return true;
                    });
                    /*End Post*/
                }
            });

			// AJAX - Update Offer Status - Trash Offer
			$('body.edit-php.post-type-woocommerce_offer .submitdelete').click(function(){
				
				if(!confirm('are you sure?'))
				{
					return false;					
				}
			});
		});
	});

}(jQuery));