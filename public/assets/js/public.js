(function ( $ ) {
	"use strict";
	$(function () {
		// Public-facing JavaScript
				
		$(document).ready(function(){
			$(".offers-for-woocommerce-make-offer-button-single-product").click(function(){
				$(".woocommerce-tabs .tabs li").removeClass("active");
				$(".woocommerce-tabs .tabs li.tab_custom_ofwc_offer_tab").addClass("active");
				$(".woocommerce-tabs div.panel").css("display", "none");
				$(".woocommerce-tabs div#tab-tab_custom_ofwc_offer").css("display", "block");		
		
				var targetTab = $(".tab_custom_ofwc_offer_tab");
				$('html, body').animate({
					scrollTop: $(targetTab).offset().top - '100'
				}, 'slow');
				
				return true;
			});
		});
		
		$(window).load(function(){
			var variantDisplay = $('.single_variation_wrap').css('display');
			if($('body.woocommerce.single-product #content div.product').hasClass('product-type-variable') && variantDisplay != 'block')
			{
				$('#tab_custom_ofwc_offer_tab_inner').hide();
				$('#tab_custom_ofwc_offer_tab_alt_message').show();
			}
		});
		$(window).load(function(){
			var datFunction = function () {
				$('.variations select').change(function() {
					
					$('#tab_custom_ofwc_offer_tab_alt_message_2').hide();
					$('#tab_custom_ofwc_offer_tab_alt_message_success').hide();
					$('#tab_custom_ofwc_offer_tab_inner fieldset').show();
					
					var selectedVariantOption = $('.variations select').val();
					//var variantDisplay = $('.single_variation_wrap.ofwc_offer_tab_form_wrap').css('display');
					
					// Toggle form based on visibility
					if(selectedVariantOption == '')
					{
						$('#tab_custom_ofwc_offer_tab_inner').hide();
						$('#tab_custom_ofwc_offer_tab_alt_message').show();
					}
					else
					{
						$('#tab_custom_ofwc_offer_tab_inner').show();
						$('#tab_custom_ofwc_offer_tab_alt_message').hide();				
					}
				});
			}();
			datFunction;
		});
		
		// Init autoNumeric jquery plugin on 'quantity' field
		$(document).ready(function(){			
			$('#woocommerce-make-offer-form-quantity').autoNumeric('init', 
				{vMin: '0',
				mDec: '0',
				lZero: 'deny',
				aForm: false}
			);
			
			$('#woocommerce-make-offer-form-price-each').autoNumeric('init', 
				{
					mDec: '2', 
					aSign: '$', 
					//wEmpty: 'sign', 
					lZero: 'keep', 
					aForm: false
				}
			);
			
			$('#woocommerce-make-offer-form').find( ':submit' ).attr('value', 'Submit Offer');
			$('#woocommerce-make-offer-form').find( ':submit' ).removeAttr( 'disabled','disabled' );
		});
		
		// offer quantity input keyup
		$('#woocommerce-make-offer-form-quantity').keyup(function() {  
			updateTotal();
		});
		
		// offer price each input keyup
		$('#woocommerce-make-offer-form-price-each').keyup(function() {  
			updateTotal();
		});
		
		// offer price each input keyup
		//$('#woocommerce-make-offer-form-price-each').blur(function() {  
				
		//});
		
		
				
		// Update totals
		var updateTotal = function () {
			var input1 = $('#woocommerce-make-offer-form-quantity').autoNumeric('get');
			var input2 = $('#woocommerce-make-offer-form-price-each').autoNumeric('get');
			if (isNaN(input1) || isNaN(input2)) {
				$('#woocommerce-make-offer-form-total').val('');
			} else {
				var theTotal = (input1 * input2);			  
				$('#woocommerce-make-offer-form-total').val('$' + parseFloat(theTotal, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			}
		};
		
		// Submit offer form		
		$('#woocommerce-make-offer-form').submit(function(){
			
			if($('#woocommerce-make-offer-form-price-each').autoNumeric('get') == '0.00')
			{
				$('#woocommerce-make-offer-form-price-each').autoNumeric('destroy');
				$('#woocommerce-make-offer-form-price-each').val('');
				$('#woocommerce-make-offer-form-price-each').autoNumeric('init', 
					{
						mDec: '2', 
						aSign: '$', 
						//wEmpty: 'sign', 
						lZero: 'keep', 
						aForm: false
					}
				);
				return false;
			}
			
			if($('#woocommerce-make-offer-form-quantity').autoNumeric('get') == '0')
			{
				$('#woocommerce-make-offer-form-quantity').autoNumeric('destroy');
				$('#woocommerce-make-offer-form-quantity').val('');
				$('#woocommerce-make-offer-form-quantity').autoNumeric('init', 
					{vMin: '0',
					mDec: '0',
					lZero: 'deny',
					aForm: false}
				);
				return false;
			}
			
			var offerProductId = '';
			var offerVariationId = '';
			var offerProductId = $("input[name='add-to-cart']").val();
			var offerVariationId = $("input[name='variation_id']").val();
			
			var offerName = $("input[name='offer_name']").val();
			var offerEmail = $("input[name='offer_email']").val();
			var offerNotes = $("input[name='offer_notes']").val();
			
			var offerQuantity = $("input[name='offer_quantity']").autoNumeric('get');
			var offerPriceEach = $("input[name='offer_price_each']").autoNumeric('get');
			
			var offerForm = $('#woocommerce-make-offer-form');
			
			if(offerProductId != '')
			{
				// disable submit button
				$( offerForm ).find( ':submit' ).attr( 'disabled','disabled' );
				
				// hide error divs
				$('#tab_custom_ofwc_offer_tab_alt_message_2').hide();
				
				// show loader image
				$('#offer-submit-loader').show();
				
				var formData = {};
					formData['offer_name'] = offerName;
					formData['offer_email'] = offerEmail;
					formData['offer_quantity'] = offerQuantity;
					formData['offer_price_each'] = offerPriceEach;
					formData['offer_product_id'] = offerProductId;
					formData['offer_variation_id'] = offerVariationId;
				
				// ajax submit offer
				var ajaxtarget = '?woocommerceoffer_post=1';
				
				// abort any pending request
				if (request) {
					request.abort();
				}	
				
				// fire off the request
				var request = $.ajax({
					url: ajaxtarget,
					type: "post",
					data: formData
				});
			
				// callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					if(request.statusText == 'OK'){
						
						var myObject = JSON.parse(request.responseText);
						
						var responseStatus = myObject['statusmsg'];
			
						if(responseStatus == 'failed')
						{
							//console.log('failed');
							// Hide loader image
							$('#offer-submit-loader').hide();
							// Show error message DIV														
							$('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
							$( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );						
						}
						else
						{
							// SUCCESS
							// Hide loader image
							$('#offer-submit-loader').hide();
							$( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );	
							$('#tab_custom_ofwc_offer_tab_inner fieldset').hide();
							$('#tab_custom_ofwc_offer_tab_alt_message_success').slideToggle('fast');							
						}
						
					} else {
						//console.log('error received');				
						//alert('Timeout has likely occured, please refresh this page to reinstate your session');
						// Hide loader image
						$('#offer-submit-loader').hide();
						$('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');
						$( offerForm ).find( ':submit' ).removeAttr( 'disabled','disabled' );
					}					
				});
			
				// callback handler that will be called on failure
				request.fail(function (jqXHR, textStatus, errorThrown){
					// log the error to the console
					// Hide loader image
					$('#offer-submit-loader').hide();
					$('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');					
				});	
			}
			else
			{
				// Hide loader image
				$('#offer-submit-loader').hide();
				$('#tab_custom_ofwc_offer_tab_alt_message_2').slideToggle('fast');						
			}			
			return false;
		});		
		
	});
}(jQuery));
