;
(function ($) {

    $.fn.cs_tooltip = function(options){
        let defaults = {
            hover_duration:3000,
            bg_width:200,
            bg_height:305,
            hover_alpha:1,
            easing:"easeInOutSine",
            style_object: null,
            tooltip_resource_attribute:"title",
            use_tooltip:true,
            add_click_evt:false

        };
        let main        =   $(this);
        let config      =   $.extend({}, defaults, options);
        let title_attr  =   null;
        let relX        =   null;
        let relY        =   null;


        let cTip = {
            init: function(){
                main.hover(this.overFX, this.outFX);
                main.mousemove(this.mouseMoveAction);
                if(config.add_click_evt){
                    main.click( this.outFX);

                }
            },

            mouseMoveAction:function(e){
                let lbl_div = $("div.label_div");
                relX        = e.pageX-( lbl_div.outerWidth()/2);
                relY        = e.pageY+15;

                lbl_div.css({
                    top:relY + "px",
                    left:relX + "px"
                });
            },

            overFX: function(e){
                let dis     = $(this);
                if( !dis.attr('data-blind')){ 
                    title_attr  = $(dis).attr(config.tooltip_resource_attribute);
                    let lbl_div = $('div.label_div');
                    relX        = e.pageX-( lbl_div.outerWidth()/2);
                    relY        = e.pageY+15;
                    title_attr  = title_attr.replace("\n", "<br />");

                    dis.data('title', title_attr );
                    dis.attr('title', '');
	
	                lbl_div = $('.label_div').remove();
                    $("<div />", {
                        html: dis.data('title'),
                        "class": "label_div"
                    }).appendTo("body");

                    if( dis.attr('pix_data') ){
                        $("<img />", {
                            "src": dis.attr('pix_data'),
                            "class": "tooltip_pix"
                        }).css({
                                "float":"left",
                                "clear":"both",
                                width:"50px"
                            }).appendTo("div.label_div");
                    }
                    if(config.style_object != null){
                        lbl_div.css(
                            config.style_object
                        );
                    }else{
                        lbl_div.css({
                            padding:"10px",
                            background:"rgba(10, 18, 200, 0.5)",
                            borderRadius:"5px",
                            textAlign:"center"
                        })
                    }

                    lbl_div.css({
                        position:"absolute",
                        top:relY + "px",
                        left:relX + "px",
                        zIndex:999999,
                        backgroundPosition: "50% 50%"
                    }).hide();
                    lbl_div.fadeIn({"duration":250, "easing":"easeInOutSine"});
                }
            },

            outFX: function(evt){
                let dis = $(this);
                if( !dis.attr('data-blind')){ 
                    $("div.label_div").fadeOut({"duration":150, "easing":"easeInOutSine"});
                }
            }
        };
        cTip.init();
    };

}(jQuery) );