const ajaxFetch = {
	$               : $,
	log             : console.log,
	
	PoizAlert       : function PoizAlert(msgText, delay){
		msgText         = (msgText !== undefined) ? msgText : "Das Bild wurde kopiert!";
		delay           = (delay !== undefined) ? delay: 2000;
		let objDim      = this.getWindowParams();
		let message     = "<span " +
			"style='display:block;padding:30px;margin:0 auto;width:350px;max-width:350px;color:#000000;" +
			"text-align:center;background:rgba(189,189,189,0.9);border-radius:7px;border:double 3px rgba(255,255,255,0.4);" +
			"font-size:14px;font-weight:500;letter-spacing:0.01em;'>" + msgText +
			"</span>";
		
		//CREATE WRAPPER OVERLAY-DIV
		let alertBox    = $("<div />", {
			id: "pz-alert",
			"class": "pz-alert"
		}).css( {
			position    : "fixed",
			width       : "100%",
			height      : "80px",
			background  : "transparent",
			display     : "none",
			margin      : 0,
			padding     : 0,
			left        : 0,
			zIndex      : 9999,
			top         : ((objDim.winHeight - 140)/2) + "px"
		} ).append($(message));
		$("body").append(alertBox);
		alertBox.fadeIn(500, function(){
			setTimeout(function(){
				alertBox.fadeOut(1000, function(){alertBox.remove();});
			}, delay);
		});
	},
	
	getWindowParams : function getWindowParams(){
		let config              = {};
		config.winWidth         = $(window).width();
		config.winHeight        = $(window).height();
		config.docWidth         = $(document).width();
		config.docHeight        = $(document).height();
		config.halfDocHeight    = (config.winHeight - config.popHeight)/2;
		return config;
	},
	
	number_format   : function number_format(number, decimals, dec_point, thousands_sep) {
		number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
		let n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function(n, prec) {
				let k = Math.pow(10, prec);
				return '' + (Math.round(n * k) / k)
				.toFixed(prec);
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '')
			.length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1)
			.join('0');
		}
		return s.join(dec);
	},
};
