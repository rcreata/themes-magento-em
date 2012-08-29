/**
 * OneStepCheckout by EMThemes
 *
 * Based on Magento 1.7.0.0 source code
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2012 EMThemes. (http://www.emthemes.com)
 * @license     Commercial License. Granted for authorized users only.
 */

/*

- "Checkout Method" change:
 	+ Magento: save Checkout Method

- "Billing" changed:
	+ "Ship to this address" checked:
	 	* Magento: saves Billing & Shipping and updates "Shipping Method"
		* EM: saves "Shipping Method" if validated
	+ "Ship to different address" checked and "Use Billing Address" NOT checked:
		* Magento: saves Billing
		* EM: saves "Payment Method" if validated
	+ "Ship to different address" checked and "Use Billing Address" checked:
		* Magento: saves Billing
		* EM: saves "Shipping"
		
- "Shipping Method" changed:
	+ Magento: saves "Shipping Method" and updates "Payment Method"
	+ EM: saves "Payment Method" if validated
	
- "Payment Method" changed:
	+ Magento: saves "Payment Method" and display "Review"
	

*/



function autoSaveBilling() {
	Form.getElements(billing.form).each(function(el) {
		if (el.id == 'billing:create_account') {
			Event.observe(el, 'click', function(event) {
				onMethodChange.bind(this)(event);
				onBillingChange.bind(this)(event);
			});
		}
		else if (el.tagName == 'SELECT')
			Event.observe(el, 'click', onBillingChange);
		else (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA')
			Event.observe(el, 'change', onBillingChange);
	});
	
	
}
// -------------------------------------------------------------------------
// ValidationEM is a cloned class of Validation with some addition methods
// -------------------------------------------------------------------------

// Static Methods
ValidationEM = Object.extend(Validation, {
	validateSilent: function(elm, options){
		options = Object.extend({
			useTitle : false,
			onElementValidate : function(result, elm) {}
		}, options || {});
		elm = $(elm);

		var cn = $w(elm.className);
		return result = cn.all(function(value) {
			var test = ValidationEM.testSilent(value,elm,options.useTitle);
			options.onElementValidate(test, elm);
			return test;
		});
	},

	testSilent : function(name, elm, useTitle) {
		var v = ValidationEM.get(name);
		try {
			if(ValidationEM.isVisible(elm) && !v.test($F(elm), elm)) {
				this.updateCallback(elm, 'failed');
				return false;
			} else {
				this.updateCallback(elm, 'passed');
				return true;
			}
		} catch(e) {
			throw(e)
		}
	}
});

// Instance Methods
Object.extend(ValidationEM.prototype, {
	validateSilent : function() {
		var result = false;
		var useTitles = this.options.useTitles;
		var callback = this.options.onElementValidate;
		try {
			result = Form.getElements(this.form).all(function(elm) {
				if (elm.hasClassName('local-validation') && !this.isElementInForm(elm, this.form)) {
					return true;
				}
				return ValidationEM.validateSilent(elm,{useTitle : useTitles, onElementValidate : callback});
			}, this);
		} catch (e) {
			throw e;
			console.error(e);
		}
	
	
		this.options.onFormValidate(result, this.form);
		return result;
	}
});



// -------------------------------------------------------------------------
// override class Checkout
// -------------------------------------------------------------------------

(function() {
	var _setStepResponse = Checkout.prototype.setStepResponse;
	var _initialize = Checkout.prototype.initialize;
	var _setLoadWaiting = Checkout.prototype.setLoadWaiting;
	Object.extend(Checkout.prototype, {
		initialize: function(accordion, urls) {
			// disable accordion javascript
			accordion = {
				sections: [],
				openPrevSection: function() {},
				openNextSection: function() {}
			}
			_initialize.bind(this)(accordion, urls);
			this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
		},
		
		resetLoadWaiting: function() {
			checkout.setLoadWaiting(false);
		},
		
		setStepResponse: function(response) {
		
			if (_setStepResponse.bind(this)(response) === false) return false;
	
			if (response.update_section && response.update_section.name == 'shipping-method')
				autoSaveShippingMethod();
	
			else if (response.update_section && response.update_section.name == 'payment-method')
				autoSavePayment();

		},
		
		setMethod: function(){
			if ($('billing:create_account') && !$('billing:create_account').checked) {
				checkout.setLoadWaiting('billing');
				this.method = 'guest';
				var request = new Ajax.Request(
					this.saveMethodUrl,
					{method: 'post', onFailure: this.ajaxFailure.bind(this), onComplete:this.onComplete, parameters: {method:'guest'}}
				);
				Element.hide('register-customer-password');
				this.gotoSection('billing');
			}
			else {
				checkout.setLoadWaiting('billing');
				this.method = 'register';
				var request = new Ajax.Request(
					this.saveMethodUrl,
					{method: 'post', onFailure: this.ajaxFailure.bind(this), onComplete:this.onComplete, parameters: {method:'register'}}
				);
				Element.show('register-customer-password');
				this.gotoSection('billing');
			}
			document.body.fire('login:setMethod', {method : this.method});
		},
		
		gotoSection: function(section) {
			var sectionElement = $('opc-'+section);
			sectionElement.addClassName('allow');
	
			switch (section) {
				case 'billing':
					onBillingChange();
					break;
					
				case 'shipping':
					onShippingChange();
					break;
					
				case 'shipping_method':
					onShippingMethodChange();
					break;
					
				case 'payment':
					onPaymentChange();
					break;
			}
		},
	
		// disable progress block requests
		reloadProgressBlock: function() {
		},
		
		// only show the last waiting indicator
		setLoadWaiting: function(step, keepDisabled) {
			if (step) {
				$('review-please-wait')._loadwating_innerHTML = $('review-please-wait').innerHTML;
				$('review-please-wait').innerHTML = $(step+'-please-wait').innerHTML;
				step = 'review';
			}
			else if (step == 'review') {
				if ($('review-please-wait')._loadwating_innerHTML)
					$('review-please-wait').innerHTML = $('review-please-wait')._loadwating_innerHTML;
			}
			
			return _setLoadWaiting.bind(this)(step, keepDisabled);
		},
		
		showLoginPopup: function() {
			$('opc-login-popup-overlay').show();
			$('opc-login-popup').show();
			$('opc-login-popup').setStyle({
				left: (parseInt(document.viewport.getWidth()) - $('opc-login-popup').getWidth()) / 2 + 'px',
				top: (parseInt(document.viewport.getHeight()) - $('opc-login-popup').getHeight()) / 2 + 'px'
			});
		},
		
		hideLoginPopup: function() {
			$('opc-login-popup-overlay').hide();
			$('opc-login-popup').hide();
		}
	});
})();


// -------------------------------------------------------------------------
// override class Billing
// -------------------------------------------------------------------------

(function() {
	var _nextStep = Billing.prototype.nextStep;
	Billing.prototype.nextStep = function(transport) {
	
		if (_nextStep.bind(this)(transport) === false) return false;
		
		if (transport && transport.responseText){
			try{
				response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				response = {};
			}
		}

		// 1. "ship to this address" checked => already update shipping and jump to payment method
		// 2. "ship to different address" checked and NOT "Use Billing Address" => update payment method
		// 3. "ship to different address" checked and "Use Billing Address" checked => update shipping

		if (typeof response.update_section == 'undefined') {
			if ($('billing:use_for_shipping_no') && $('billing:use_for_shipping_no').checked) {
				if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
					// update shipping if shipping to "different address" and "use billing address" checked
					onShippingChange();
				}
				else {
					onPaymentChange();
				}
			}
		}
	
		
	};

})();

// -------------------------------------------------------------------------
// override class ShippingMethod
// -------------------------------------------------------------------------

(function() {
	var _nextStep = ShippingMethod.prototype.nextStep;
	ShippingMethod.prototype.nextStep = function(transport) {
		
		if (_nextStep.bind(this)(transport) === false) return false;
	
		if (transport && transport.responseText){
			try{
				var response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				var response = {};
			}
		}

		if (response.update_section && response.update_section.name == 'payment-method')
			autoSavePayment();
		payment.initWhatIsCvvListeners();
	};
	
	ShippingMethod.prototype.validateSilent = function() {
		var methods = document.getElementsByName('shipping_method');
		if (methods.length==0) {
			return false;
		}
		for (var i=0; i<methods.length; i++) {
			if (methods[i].checked) {
				return true;
			}
		}
		return false;
		
	};

})();


// -------------------------------------------------------------------------
// override class Payment
// -------------------------------------------------------------------------

(function() {
	Payment.prototype.validateSilent = function() {
	
		var result = this.beforeValidate();
		if (result) {
			return true;
		}
		var methods = document.getElementsByName('payment[method]');
		if (methods.length==0) {
			return false;
		}
		for (var i=0; i<methods.length; i++) {
			if (methods[i].checked) {
				return true;
			}
		}
		result = this.afterValidate();
		if (result) {
			return true;
		}
		return false;
		
	};

})();






function queuedSave(o) {
	
	if (typeof o._queuedSaveInterval != 'undefined' && o._queuedSaveInterval)
		return;
	
	o._queuedSaveInterval = setInterval(function() {
		if (checkout.loadWaiting)
			return true;
		else {
			clearInterval(o._queuedSaveInterval);
			o._queuedSaveInterval = false;
		}
			
		o.save();
	}, 100);
}


function autoSaveLogin() {
	if ($('login:guest')) {
		$('login:guest').observe('click', onMethodChange);
	}
	if ($('login:register')) {
		$('login:register').observe('click', onMethodChange);
	}

}

function onMethodChange() {
	//setTimeout(function() {
		checkout.setMethod();
	//}, 100);
}




function onBillingChange() {
	if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked && shipping) {
		shipping.setSameAsBilling(true);
	}
	
	var validator = new ValidationEM(billing.form);
	if (validator.validateSilent()) queuedSave(billing);
}

function autoSaveShipping() {
	if (typeof shipping == 'undefined') return;
	Form.getElements(shipping.form).each(function(el) {
		if (el.tagName == 'SELECT')
			Event.observe(el, 'click', onShippingChange);
		else (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA')
			Event.observe(el, 'change', onShippingChange);
	});
	
}

function onShippingChange() {
	if (typeof shipping == 'undefined') return;
	var validator = new ValidationEM(shipping.form);
	if ($('opc-shipping').visible() && validator.validateSilent()) queuedSave(shipping);
}

function autoSaveShippingMethod() {
	if (typeof shippingMethod == 'undefined') return;
	Form.getElements(shippingMethod.form).each(function(el) {
		// mark element as binded event
		if (typeof el._autoSaveShippingMethodBinded != 'undefined') return;
		el._autoSaveShippingMethodBinded = true;
		
		if (el.tagName == 'SELECT')
			Event.observe(el, 'click', onShippingMethodChange);
		else (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA')
			Event.observe(el, 'change', onShippingMethodChange);
	});
	
}

function onShippingMethodChange() {
	if (typeof shippingMethod == 'undefined') return;
	
	var validator = new ValidationEM(shippingMethod.form);
	if (shippingMethod.validateSilent() && validator.validateSilent())
		queuedSave(shippingMethod);
}

function autoSavePayment() {
	if (typeof payment == 'undefined') return;
	Form.getElements(payment.form).each(function(el) {
		if (el.tagName == 'SELECT')
			Event.observe(el, 'click', onPaymentChange);
		else (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA')
			Event.observe(el, 'change', onPaymentChange);
	});		

}

function onPaymentChange() {
	if (typeof payment == 'undefined') return;

	var validator = new ValidationEM(payment.form);
	if (payment.validateSilent() && validator.validateSilent())
		queuedSave(payment);
}



Event.observe(window, 'load', function() {
	
	// create popup login form
	if ($('opc-login-popup')) {
		document.body.appendChild($('opc-login-popup'));
		document.body.appendChild($('opc-login-popup-overlay'));
		Event.observe(window, 'resize', function() {
			if ($('opc-login-popup').visible()) {
				$('opc-login-popup').setStyle({
					left: (parseInt(document.viewport.getWidth()) - $('opc-login-popup').getWidth()) / 2 + 'px',
					top: (parseInt(document.viewport.getHeight()) - $('opc-login-popup').getHeight()) / 2 + 'px'
				});
			}
		});
		
	}
	
	// hide shipping section if checked
	if ($('billing:use_for_shipping_yes')) {
		$('billing:use_for_shipping_yes').observe('click', function() {
			$('opc-shipping').hide();
		});
		if ($('billing:use_for_shipping_yes').checked) $('opc-shipping').hide();
	}
		
	// show shipping section if checked
	if ($('billing:use_for_shipping_no')) {
		$('billing:use_for_shipping_no').observe('click', function() {
			$('opc-shipping').show();
		});
		if ($('billing:use_for_shipping_no').checked) $('opc-shipping').show();
	}
	
	// update radio billing:use_for_shipping according to checkbox shipping:same_as_billing 
	if ($('shipping:same_as_billing'))
		$('shipping:same_as_billing').observe('click', function() {
			if (this.checked) {
				if ($('billing:use_for_shipping_yes'))
					$('billing:use_for_shipping_yes').checked = true;
			}
			else {
				if ($('billing:use_for_shipping_no'))
					$('billing:use_for_shipping_no').checked = true;
			}
				
		});
	
	
	
	autoSaveLogin();
	autoSaveBilling();
	autoSaveShipping();
	autoSaveShippingMethod();
	autoSavePayment();
	
	// save Login Method
	if ($('billing:create_account'))
		onMethodChange();
	
	// if user logged then we have to reload shipping or billing to update shipping method and payment method
	if (typeof shipping != 'undefined' && $('opc-shipping').visible())
		onShippingChange();
	else
		onBillingChange();
		
	
	payment.initWhatIsCvvListeners();
});
	

	
