(function ( $ ) {
	"use strict";
	$(function () {

        /* Hide Count for 'All' list */
        /*$("li.all").find("span.count").remove(); // not needed, CSS took care of it*/

        /*AJAX - Update Offer Status - Accepted Offer*/
        document.querySelectorAll('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-accept').forEach((object) => {
            object.addEventListener('click', function () {

                var targetID = this.getAttribute('data-target');
                var data = {
                    'action': 'approveOfferFromGrid',
                    'security': 'ofw_tool_enable_auto_accept_declineNonce',
                    'targetID': targetID
                };

                $.post(ajaxurl, data, function (response) {

                    if (typeof (response) !== "undefined" && response !== null && response !== "") {
                        var myObject = JSON.parse(response);

                        var responseStatus = myObject['statusmsg'];
                        var responseStatusDetail = myObject['statusmsgDetail'];

                        if (responseStatus === 'failed-do_capture') {
                            alert(responseStatusDetail);
                            return false;
                        }
                    }

                    document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer').classList.add('status-accepted-offer');
                    document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer').classList.remove('status-publish');

                    var oldColumnDateVal = jQuery('tr.post-' + targetID + '.type-woocommerce_offer td.column-date abbr').attr('title');
                    jQuery('tr.post-' + targetID + '.type-woocommerce_offer .column-date').html("<abbr title='" + oldColumnDateVal + "'></abbr>Accepted");

                    var oldColumnDateVal = document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer td.column-date abbr').getAttribute('title');
                    document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer .column-date').innerHTML = '<abbr title="' + oldColumnDateVal + '"></abbr>Accepted';

                    /*modify post status icon css*/
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').classList.add('accepted');
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').classList.remove('pending', 'trash', 'declined');
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').title = 'Offer Status: Accepted';
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').innerHTML = 'Accepted';

                    /*modify action links on post*/
                    document.querySelector('#woocommerce-offer-post-action-link-manage-id-' + targetID).innerHTML = 'Manage Offer';

                    var previousPendingCountBubbleValue = document.querySelector('#woocommerce-offers-count .pending-count').innerHTML;
                    var newPendingCount = (previousPendingCountBubbleValue - 1);
                    document.querySelector('#woocommerce-offers-count .pending-count').innerHTML = newPendingCount;
                    if (Number(newPendingCount) === 0) {
                        $('#woocommerce-offers-count').fadeOut('slow');
                    }

                    /*remove accept action link*/
                    $('#woocommerce-offer-post-action-link-accept-id-' + targetID + '').parent('span').hide();
                    return true;
                });
                return false;
            });
        });


        /*AJAX - Update Offer Status - Trash Offer*/
        document.querySelectorAll('body.edit-php.post-type-woocommerce_offer .submitdelete').forEach((object) => {
            object.addEventListener('click', function () {
                if (!confirm('are you sure?')) {
                    return false;
                }
            });
        });
        document.querySelectorAll('.woocommerce-offer-post-action-link.woocommerce-offer-post-action-link-decline').forEach((object) => {
            object.addEventListener('click', function () {
                jQuery('.ofw_send_coupon_declineOfferFromGrid').click();
                m7_resize_thickbox();
                var targetID = this.getAttribute('data-target');
                document.getElementById('offer-id').value = targetID;
            });
        });

        jQuery(window).resize(function () {
            m7_resize_thickbox();
        });

        function m7_resize_thickbox() {
            var TB_WIDTH = 547;
            var TB_HEIGHT = 226;
            jQuery(document).find('#TB_window').width(TB_WIDTH).height(TB_HEIGHT).css('margin-left', -TB_WIDTH / 2);
        }

        /*AJAX - Update Offer Status - Declined Offer*/
        document.querySelectorAll('.ofw-decline-popup').forEach((object) => {
            object.addEventListener('click', function () {
                var ofw_current_id = this.getAttribute('id');
                var targetID = document.getElementById('offer-id').value;
                if (ofw_current_id === 'decline_offer') {
                    var data = {
                        'action': 'declineOfferFromGrid',
                        'targetID': targetID
                    };
                } else {
                    var data = {
                        'action': 'declineOfferFromGrid',
                        'targetID': targetID,
                        'coupon_code': document.getElementById('ofw_coupon_list').value
                    };
                }

                /*post it*/
                $.post(ajaxurl, data, function (response) {

                    if (document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer').classList.contains('status-publish')) {
                        var previousPendingCountBubbleValue = document.querySelector('#woocommerce-offers-count .pending-count').innerHTML;
                        var newPendingCount = (previousPendingCountBubbleValue - 1);
                        document.querySelector('#woocommerce-offers-count .pending-count').innerHTML = newPendingCount;
                        if (newPendingCount === 0) {
                            $('#woocommerce-offers-count').fadeOut('slow');
                        }
                    }

                    document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer').classList.add('status-declined-offer');
                    document.querySelector('tr.post-' + targetID + '.type-woocommerce_offer').classList.remove('status-accepted-offer', 'status-publish');

                    /*modify post status icon css*/
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').classList.remove('pending', 'trash', 'accepted');
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').classList.add('declined');
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').title = 'Offer Status: Declined';
                    document.querySelector('#woocommerce-offer-post-status-grid-icon-id-' + targetID + ' i').innerHTML = 'Declined';

                    /*modify action links on post*/
                    document.querySelector('#woocommerce-offer-post-action-link-manage-id-' + targetID).innerHTML = 'Manage Offer';

                    /*remove accept and decline action links*/
                    $('#woocommerce-offer-post-action-link-decline-id-' + targetID + '').parent('span').hide();
                    $('#woocommerce-offer-post-action-link-accept-id-' + targetID + '').parent('span').hide();
                    tb_remove();
                    return true;

                    /*remove the declined post*/
                    /*$('tr.post-'+targetID+'.type-woocommerce_offer').slideToggle('slow');*/
                    return true;
                });
                /*End Post*/
            });
        });
	});

}(jQuery));