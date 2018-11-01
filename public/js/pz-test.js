;

(function($) {
    /** jQuery Document Ready */
    $(document).ready(function(){
        const variantPix        = $(".pz-variant-pix");
        const productWrapper    = $(".pz-product-wrapper");
        const btnAddToCart      = $(".pz-add-to-cart-icon");
        const btnDelFromCart    = $(".pz-delete-from-cart-icon");
        const btnCartIcon       = $(".pz-cart-icon.pz-mini-cart-icon");
        let orderedItemPix      = $(".ordered_item_minipix");
        let hidAble_cart_boxes  = null;
        const toolTippedLinks   = $("*[data-tip]");
	    let cart_open           = true;
        const GLOBAL_DATA       = {INITIATOR:null, TRASHED_AID:null,  BLOCK_ROOT:null, PROCESSOR: null, RESET_DATA: false};

        const cs_tool_tip_config  = {
            padding:"5px 7px",
            borderRadius:"7px 0 7px 0",
            textAlign:"center",
            letterSpacing:"1px",
            "box-shadow":"1px -1px 2px rgb(24, 24, 24)",
            fontSize:"12px",
            fontWeight:"300",
            background: "rgba(211, 211, 211, 0.95)",
            color: "rgba(38, 38, 38, 0.95)"
        };

        const tooltip_configurator= {
            hover_duration:3000,
            bg_width:200,
            bg_height:305,
            hover_alpha:1,
            easing:"easeInOutSine",
            tooltip_div:"div#tooltip",
            tooltip_resource_attribute:"data-tip",
            use_tooltip:true,
            style_object: cs_tool_tip_config
        };


        toolTippedLinks.cs_tooltip(tooltip_configurator);
	    btnAddToCart.on('click',    manipulateViaAjax);
	    btnDelFromCart.on('click',  manipulateViaAjax);
	    btnCartIcon.on('click',     manipulateViaAjax);
	
	    productWrapper.on("mouseover", function(){
	        const variantsBlock = $(this).find('.pz-variants-block');
	        if(variantsBlock.hasClass("pz-hide-flex")){
		        variantsBlock.removeClass("pz-hide-flex");
		        variantsBlock.addClass("pz-show-flex");
            }
        });
	
	    productWrapper.on("mouseout", function(){
		    const variantsBlock = $(this).find('.pz-variants-block');
	        if(variantsBlock.hasClass("pz-show-flex")){
	            variantsBlock.removeClass("pz-show-flex");
	            variantsBlock.addClass("pz-hide-flex");
            }
        });
	
	    orderedItemPix.on("click", function (e) {
		    let targ                = $(this);
		    GLOBAL_DATA.INITIATOR   = $(this);
		    GLOBAL_DATA.BLOCK_ROOT  = $(".prod_pix[data-prod-id=" + targ.attr('data-prod-id') + "]").parentsUntil('.prod_data_box').parent('.prod_data_box');
		    GLOBAL_DATA.TRASHED_AID = targ.attr("data-prod-attrid");
		    $(".label_div").remove();
		
		    manipulateViaAjax(targ, "delete_all_of_same_aid", renderMiniCartPreview);
	    });
	
	    variantPix.on("click", function(){
		    GLOBAL_DATA.INITIATOR   = $(this);
		    let data                = GLOBAL_DATA.INITIATOR.data();
		    let payload             = Object.assign({}, data);
		    GLOBAL_DATA.BLOCK_ROOT  = data.blockRoot;
		    GLOBAL_DATA.PROCESSOR   = data.processor;
		    GLOBAL_DATA.ENDPOINT    = data.endpoint;
		    GLOBAL_DATA.CURRENCY    = $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-add-to-cart-icon').attr('data-currency');
		
		    const ajaxRequest       = $.ajax({
			    url     : GLOBAL_DATA.ENDPOINT,
			    type    : "POST",
			    dataType: "JSON",
		    });
		    ajaxRequest.
		    then(function(data, textStatus, jqXHR){
			    if(data){
				    let randVal = getUniqueKey(null, 6);
				    const addIcon       = $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-add-to-cart-icon');
				    const delIcon       = $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-delete-from-cart-icon');
				    const price         = parseInt(data.onSale) === 1 ? data.salePrice : data.normalPrice;
				    const endPointAdd   = addIcon.attr('data-endpoint').replace(/(\/\d+)(\/\d+)(\/\d+)(\/[\d.]+)$/, '/' + data.productAID +'$2$3/' + price);
				    const endPointDel   = delIcon.attr('data-endpoint').replace(/(\/\d+)(\/\d+)(\/\d+)(\/[\d.]+)$/, '/' + data.productAID +'$2$3/' + price);
				
				    addIcon.attr('data-endPoint', endPointAdd);
				    delIcon.attr('data-endPoint', endPointDel);
				
				    $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-prod-pix').fadeOut(500, function(){
					    $(this).attr('src', '/' + data.productPix + '?nocache=' + randVal).fadeIn(500);
				    });
				    $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-norm-price-box.pz-strike').fadeOut(500);
				    $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-price-box').fadeOut(500, function(){
					    updateSegments(data);
					    $(this).html(data.activeCurrency + ' ' + ajaxFetch.number_format(price, 2, '.', "'")).fadeIn(500);
					    if(parseInt(data.onSale)){
						    $(this).html('<span class="fa fa-tags"></span>&nbsp;' + data.activeCurrency + ' ' + ajaxFetch.number_format(data.salePrice, 2, '.', "'")).fadeIn(500);
						    $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-norm-price-box.pz-strike').text(data.activeCurrency + ' ' + ajaxFetch.number_format(data.normalPrice, 2, '.', "'")).fadeIn(500);
					    }
				    });
			    }
		    }).
		    catch(function(jqXHR, textStatus, errorThrown){
			    console.log('The following error occurred: ' + textStatus, errorThrown);
		    });
	    });
	
	
	    function manipulateViaAjax(initiatorObj, strServerAction, successCallBack) {
		    GLOBAL_DATA.RESET_DATA      = (strServerAction === "delete_all_of_same_aid");
		    if(initiatorObj.currentTarget !== undefined){
			    GLOBAL_DATA.INITIATOR   = $(this);
		    }else{
			    GLOBAL_DATA.INITIATOR   = initiatorObj;
		    }
		    
		    let data                = GLOBAL_DATA.INITIATOR.data();
		    let payload             = Object.assign({}, data);
		   
		    GLOBAL_DATA.BLOCK_ROOT  = undefined !== data.blockRoot ? data.blockRoot : GLOBAL_DATA.BLOCK_ROOT;
		    GLOBAL_DATA.PRELOADER   = data.gif;
		    GLOBAL_DATA.CURRENCY    = data.currency;
		    GLOBAL_DATA.ENDPOINT    = GLOBAL_DATA.INITIATOR.attr('data-endpoint');
		    let quantity            = $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-qty').val();
		    quantity                = quantity ? quantity : (payload.qty !== undefined)? payload.qty : 1;
		    quantity                = quantity < 1 ? 0 : quantity;

		    if(!/delete_item_cluster/.test( GLOBAL_DATA.ENDPOINT) && !/get_cart/.test( GLOBAL_DATA.ENDPOINT)){
			    GLOBAL_DATA.ENDPOINT    = GLOBAL_DATA.ENDPOINT.replace(/(.+)(\/\d+)*?$/, '$1/'+quantity);
		    }
				
			let request = $.ajax({
			    url: GLOBAL_DATA.ENDPOINT,
			    dataType: "json",
			    beforeSend: renderPreLoaderOverlay,
			    type: "POST"
		    });
			
		    if (successCallBack === undefined) {
			    request.done(renderMiniCartPreview);
		    } else {
			    request.done(successCallBack);
		    }
		
		    request.fail(function (jqXHR, textStatus) {
			    ajaxFetch.PoizAlert("Request failed: " + textStatus);
		    });
	    }
	    
	    function renderPreLoaderOverlay(evt) {
		    let full_height = window.outerHeight;
		    let full_width = window.outerWidth;
		    let ldr_hait = 100;
		    let ldr_widt = 100;
		    let top_pos = parseInt((full_height - ldr_hait) / 2);
		    let left_pos = parseInt((full_width - ldr_widt) / 2);
		    //CREATE PRE-LOADER IMAGE
		    let preloader = $("<img />", {
			    'id': "preloader_img",
			    'class': "preloader_img",
			    'src': GLOBAL_DATA.PRELOADER,
			    'style': 'width:36px;height:36px;'
		    }).css({
			    position: "absolute",
			    display: "block",
			    top: top_pos + "px",
			    left: left_pos + "px",
			    zIndex: "10000"
		    });
		    //CREATE WRAPPER OVERLAY-DIV
		    main_overlay = $("<div />", {
			    id: "main_overlay_div",
			    "class": "main_overlay_div"
		    }).css({
			    position: "absolute",
			    width: "100%",
			    height: $("html").outerHeight() + "px", //this should read doc_height + "px" under normal circumstance. req_pos is a workaround in my case.
			    background: "rgba(242, 230, 217, 0.25)",
			    display: "none",
			    cursor: "pointer",
			    left: "0",
			    zIndex: "999",
			    top: "0"
		    }).append(preloader).appendTo("body");
		    main_overlay.fadeIn({ "duration": 250, "easing": "easeOutElastic" });
	    }
	
	    function removePreLoaderOverlay() {
		    $(".main_overlay_div").fadeOut({ "duration": 250, "easing": "easeOutElastic", complete: function (e) {
				    $(this).remove();
			    } });
	    }
	    
	    function renderMiniCartPreview(data) {
		    $(GLOBAL_DATA.BLOCK_ROOT).find('.pz-qty').val('');
		    let cart_module = $("#cs_cart_module");
		    
		    if(((GLOBAL_DATA.RESET_DATA !== undefined) && GLOBAL_DATA.RESET_DATA) || data.total === undefined){
			    updateSegments(null);
		    }
		    if (data) {
			    if (data.cxt_total) {
			    	updateSegments(data)
			    }
			
			    if (data.cart) {
				    cart_module.html(null).html(data.cart).css({ "display": "none" }).fadeIn({ "duration": 250, "easing": "easeOutQuad" }); //, "complete":switch_on_mini_cart_open_flag});
				    let cart_mini_pix       = $(".ordered_item_minipix");
				    let hide_trigger        = cart_module.children(".col-md-12").find(".glyphicon");
				    let checkoutLink        = $(cart_module).find("a#pz-checkout-icon");
				    hidAble_cart_boxes      = cart_module.find(".col-md-12.pad_minimal");
				  
				    checkoutLink.on("click", function(e){
				    	e.preventDefault();
				    	ajaxFetch.PoizAlert("Ideally we should head to «Checkout», but this Functionality was not implemented.", 5000);
				    });
				
				    cart_mini_pix.cs_tooltip(tooltip_configurator);
				    cart_module.find(".prod_cart_header").find("#my_cart_txt").on("click", toggleLeftRightLocation);
				    hide_trigger.on("click", hide_hidAble_cart_boxes);
				
				    if (cart_open) {
					    hidAble_cart_boxes.hide();
					    cart_open = false;
				    }
				    
				    if (GLOBAL_DATA.TRASHED_AID) {
					    let target_global_unit_box = $("img.prod_pix[data-prod-attrid='" +  GLOBAL_DATA.TRASHED_AID + "']").parentsUntil(".prod_data_box").parent(".prod_data_box");
					    target_global_unit_box.find(".quint_qty").text("0");
					    target_global_unit_box.find(".context_qty_bottom").text("0");
					    target_global_unit_box.find(".quint_total_price").text("CHF 0.00");
					    target_global_unit_box.find(".context_price_bottom").text("0.00");
				    }
				
				    /////////////////////////////////////////////////////////////////////
				    cart_mini_pix.on('click', removeProductCollection);
				    $(".cart_view_link").on('click', preventDefaultBehavior);
				    /////////////////////////////////////////////////////////////////////
			    } else {
				    cart_module.html(null).css({ "display": "none" }).fadeOut({ "duration": 250, "easing": "easeOutQuad" });
			    }
		    }
		    removePreLoaderOverlay();
		
		    $("html, body").animate({ scrollTop: 0 }, { "duration": 500, "easing": "easeOutSine" });
	    }
	
	    function removeProductCollection(e) {
	    	e.preventDefault();
		    GLOBAL_DATA.INITIATOR   = $(e.target);
		    GLOBAL_DATA.BLOCK_ROOT  = $("#pz-product-wrapper-" + GLOBAL_DATA.INITIATOR.attr('data-prod-id'));   //$($("#pz-prod-pix-" + GLOBAL_DATA.INITIATOR.attr('data-prod-id')).attr('data-block-root'));
		    GLOBAL_DATA.TRASHED_AID = GLOBAL_DATA.INITIATOR.attr("data-prod-attrid");
		    $(".label_div").remove();
		
		    manipulateViaAjax(GLOBAL_DATA.INITIATOR, "delete_all_of_same_aid", renderMiniCartPreview);
	    }
	
	    function preventDefaultBehavior(e) {
		    e.preventDefault();
	    }
	    
	    function updateSegments(data) {
		    const separator = '&nbsp; | &nbsp;';
		    let price       = '0.00';
		    let quantity    = '0';
		    let priceString = '--';
		    if(data){
			    price       = ajaxFetch.number_format(data.total, 2, ".", "'");
			    quantity    = ((data.qty !== undefined) && parseInt(data.qty) > 0) ? data.qty: 0;
			    priceString = ((data.qty !== undefined) && parseInt(data.qty) > 0) ?
				                data.qty + separator + GLOBAL_DATA.CURRENCY  + " " + price : priceString;
		    }
		    $(GLOBAL_DATA.BLOCK_ROOT).find(".pz-count-slot").html(priceString);
		    $(GLOBAL_DATA.BLOCK_ROOT).find(".pz-qty-total-wrapper-bottom .pz-price-bottom .pz-sub-val-pod").text(price);
		    $(GLOBAL_DATA.BLOCK_ROOT).find(".pz-qty-total-wrapper-bottom .pz-qty-bottom .pz-sub-val-pod").text(quantity);
	    }
	
	    function getUniqueKey(keyID, length) {
		    keyID   = (keyID === undefined || !keyID) ? '' : '-' . keyID;
		    length  = (length === undefined) ? 8 : length;
		    let characters = '0123456789ABCDEF';
		    let randomString = '';
		
		    for (let i = 0; i < length; i++) {
			    randomString += characters[Math.floor((Math.random() * characters.length))];
		    }
		
		    return randomString  + keyID;
	    }
	
	    function toggleLeftRightLocation(evt) {
		    let win_width = $(window).width();
		    let css_left = { "position": "fixed", "right": "auto", "left": 0, "top": 0, "maxWidth": "30%", "display": "none" };
		    let css_right = { "position": "fixed", "right": 0, "top": 0, "left": "auto", "width": "30%", "display": "none" };
		    let animate_box = true;
		
		    if (win_width < 700) {
			    animate_box = false;
			    css_left = { "position": "absolute", "right": "auto", "left": 0, "top": 0, "display": "none", "width": "100%" }; //, "display":"none"
			    css_right = { "position": "absolute", "right": 0, "top": 0, "left": "auto", "width": "100%", "display": "none" };
		    }
		    let dis = $(this).parent(".prod_cart_header");
		    let par_till = dis.parentsUntil("#cart_module_wrapper").parent("div");
		
		    if (animate_box) {
			    if (!to_d_left) {
				    par_till.css(css_left).fadeIn(500);
				    to_d_left = true;
			    } else {
				    par_till.css(css_right).fadeIn(500);
				    to_d_left = false;
			    }
		    } else {
			    par_till.css(css_left).fadeIn(500);
		    }
	    }
	
	    function hide_hidAble_cart_boxes(evt) {
		    let dis = $(this);
		    if (dis.hasClass("glyphicon-minus")) {
			    dis.removeClass("glyphicon-minus");
			    dis.addClass("glyphicon-plus");
		    } else {
			    dis.removeClass("glyphicon-plus");
			    dis.addClass("glyphicon-minus");
		    }
		    hidAble_cart_boxes.fadeToggle({ "duration": 250, "easing": "easeOutQuad" });
	    }

    });
})(jQuery);

