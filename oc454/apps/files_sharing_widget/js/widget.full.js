/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
(function() {

	var jQuery;
  	
	if (window.jQuery === undefined || window.jQuery.fn.jquery !== "1.8.2") {
		var script_tag = document.createElement("script");
		script_tag.setAttribute("type", "text/javascript");
		script_tag.setAttribute("src",ownWidgetOptions.path + ownWidgetOptions.appspath+ "/files_sharing_widget/js/jquery-pack.min.js");
		if (script_tag.readyState) {
			
			script_tag.onreadystatechange = function() {
				if (this.readyState == "complete" || this.readyState == "loaded") {
					scriptLoadHandler();
				}
			}
		} else {
			script_tag.onload = scriptLoadHandler;
		}
		(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
	} else {
		jQuery = window.jQuery;
		main();
	}
	
	/******** Called once jQuery has loaded ******/
	function scriptLoadHandler() {
		// Restore $ and window.jQuery to their previous values and store the
		// new jQuery in our local jQuery variable
		jQuery = window.jQuery.noConflict(true);
		// Call our main function
		main();
	}


	function rawurlencode(str) {
		str = (str + "").toString();
		return encodeURIComponent(str).replace(/!/g, "%21").replace(/'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29").replace(/\*/g, "%2A");
	}
 
	function main() {
		jQuery(document).ready(function($) {
		//	if($.browser.msie && $.browser.version<9){
				//widgetContainer.css('opacity',0.3);
		//	}
		 if(ownWidgetOptions.showButtonLink!=undefined){
		    OwnWidget.prepareExternLink(ownWidgetOptions);
		 }else{
			 if($("#ownWidget-container").length>0) OwnWidget.init(ownWidgetOptions);
		 }
		});
	}
	
var OwnWidget={
	
	init:function(options){
		var defaults={ 
			crypt:'',
		 	path:'',
		 	appspath:'apps',
		 	showButtonLink:'',
		 	customThumbHeight:'',
		 	customThumbpPage:'',
		 	display:'',
		 	fbAppid:'',
		 	modal:true,
		 	cssAddWidget:{'width':1000,'height':560},
		 	cssAddButton:{'top':20,'left':20},
		 	buttonlabel:'Fotogalerie'
		}
		this.options=jQuery.extend(true,defaults,options);
		this.loadCssFile();
		this.imageSlide = [];
		this.loadPage='';
		this.widgetContainer=jQuery("#ownWidget-container");
		this.firstShow=true;
		if(this.options.fbAppid!=''){
			this.loginToFacebookApp();
		}
		
		    if (this.options.display == "hidden") {
				this.createShowButton();
			} else {
				this.showWidget();
		   }
	   this.initSupersized();
	    
	},
	prepareExternLink:function(options){
		var self=this;
		
		if(options.showButtonLink!=''){
			jQuery('#'+options.showButtonLink).click(function(){
				clearTimeout(timeout);
                var timeout=setTimeout(function(){self.init(options);}, 500);
				
						
			});
		}
	},
	loadCssFile:function(){
		if(jQuery('#ownWidgetCss').length<1){
		  jQuery("<link>", {id:'ownWidgetCss',rel : "stylesheet",type : "text/css",href :this.options.path + this.options.appspath+ "/files_sharing_widget/css/widget.css"}).appendTo("head");
		}
	},
	
	createShowButton:function () {
		self=this;
		this.widgetContainer.hide();
		if (this.options.modal) {
			this.overlay = jQuery('<div id="ownWidget-overlayWid"></div>').appendTo("body");
		}
		this.addWidgetControls();
		var DisplayButton = jQuery('<div id="ownWidget-displayButton">' + this.options.buttonlabel + "</div>");
		DisplayButton.css(this.options.cssAddButton);
		DisplayButton.appendTo("body");
		DisplayButton.click(function() {
			if (self.options.modal) {
				self.overlay.height(jQuery(window).height());
				self.overlay.width(jQuery(window).width());
				self.overlay.animate({
					opacity : 0.5
				}, 500, function() {
					 self.loadData();
				});
			} else {
				 self.loadData();
			}
		});
	},
	
	showWidget: function (){
		
		this.widgetContainer.hide();
		this.addWidgetControls();
		this.loadData();
		
	},
	
	 addWidgetControls:function() {
		var self=this;
		if (this.options.display == "hidden") {
			var CloseButton = jQuery('<div id="ownWidget-closeButton" style="cursor:pointer;">X</div>');
			this.widgetContainer.prepend(CloseButton);
		}
		this.widgetContainer.prepend('<div id="ownWidget-output" class="jcarousel"></div>');
		var wHeader = jQuery('<div id="ownWidget-header"></div><div id="SlideShowButton">Slideshow</div>');
		this.widgetContainer.prepend(wHeader);
		
		jQuery("#SlideShowButton").click(function() {
			self.widgetContainer.animate({
				opacity : "toggle"
			}, 500, function() {
				jQuery("body").append("<div id='supersized-holder'></div>");
				jQuery("#supersized-loader").remove();
				jQuery("#supersized").remove();
				jQuery("#supersized-holder").append("<div id='supersized-loader'></div><ul id='supersized' style=\"z-index:200;\"></ul>");
				jQuery("#supersized").show();
				jQuery("#slideshow-content").animate({opacity : "toggle"});
				jQuery("a[rel=fancyArea]").each(function(i, el) {
					self.imageSlide.push({
						image : el.href,
						title : el.title.replace(/</, "&lt;").replace(/>/, "&gt;"),
						thumb : el.children[0].src
					});
				});
				self.loadSupersized();
			});
		});
		jQuery("#SlideShowButton").hide();
		if (this.options.display == "hidden") {
			CloseButton.click(function() {
				self.widgetContainer.animate({
					opacity : "toggle"
				}, 500, function() {
					if (self.options.modal) {
						self.overlay.height(0);
						self.overlay.width(0);
					}
					self.firstShow = true;
					self.widgetContainer.hide();
				});
			});
		}
	},
	 initMovingBox:function(){
  		   
  		   var self=this;
  		    var h=this.widgetContainer.height() - 80;
  		    
		    if(jQuery('#albumPics').length > 0) var h=this.widgetContainer.height() -210;
			
			jQuery("#ownWidget-slider").movingBoxes({
				reducedSize : 1,
				startPanel : 1,
				hashTags : false,
				speed : 700,
				fixedHeight : true,
				wrap : false,
				buildNav : true,
				navFormatter : function() {
					return "&#9679;"
				},
				preinit:function(){
				  self.widgetContainer.prepend('<div id="ownWidget-Loader">&nbsp;</div>');
				    jQuery("#ownWidget-output").hide();
					jQuery(".mb-wrapper").css({height : h});
				},
				initialized:function(e, slider, tar){
					jQuery(".mb-scroll").height(h-20);
					jQuery("#ownWidget-slider").height(h-20);
					jQuery("#ownWidget-Loader").remove();	
					jQuery("#ownWidget-output").show();		
											
				}
			});
     },
     
     loadData:function() {
		var self = this;
		var addCustomThumbSize='';
		if(this.options.customThumbHeight!='') addCustomThumbSize='&cTh='+this.options.customThumbHeight;
		
		var addCustomThumbPage='';
		if(this.options.customThumbpPage!='') addCustomThumbPage='&cTpP='+this.options.customThumbpPage;
		//customThumbpPage
		jQuery.ajax({
			dataType : "jsonp",
			jsonp : "jsonp_callback",
			url : self.options.path + "widget.php?iToken=" + rawurlencode(self.options.crypt) + self.loadPage+addCustomThumbSize+addCustomThumbPage,
			
			success : function(data) {
				
				jQuery("#ownWidget-output").html("");
				jQuery("#ownWidget-output").html(data.databack);
				jQuery("#ownWidget-header").html(data.nav + " " + data.folder);
				
				
				if (jQuery("#loginForm").length > 0) {
					jQuery("#loginForm #iSubmit").click(function(event) {
						event.preventDefault();
						self.loadPage = "&password=" + jQuery("#password").val();
						self.loadData();
					});
				}
				jQuery("#ownWidget-header .loadAlbum").each(function(i, el) {
					jQuery(el).click(function() {
						if (jQuery(el).attr("title") == "") {
							self.loadPage = "";
						} else {
							self.loadPage = "&path=/" + jQuery(el).attr("title");
						}
						self.loadData();
					})
				});
				
				if (self.firstShow) {
					
					self.widgetContainer.css(self.options.cssAddWidget);
					
					if (!self.options.cssAddWidget.top && !self.options.cssAddWidget.left) {
						
						self.widgetContainer.css({
							top : (jQuery(window).height() / 2) - (self.widgetContainer.height() / 2),
							left : (jQuery(window).width() / 2) - (self.widgetContainer.width() / 2)
						});
					}
					
					self.widgetContainer.show();
				
					self.firstShow = false;
				}
				
				if (jQuery("#ownWidget-slider").length > 0) {
					
					jQuery("#ownWidget-slider").css({width : self.widgetContainer.width() - 12});
					jQuery("#ownWidget-slider > div").css({width : self.widgetContainer.width() - 15	});
					
					if (jQuery("#ownWidget-slider > div").length > 1) {
					   self.initMovingBox();
					} else {
						if (jQuery("#ownWidget-slider").html() == "") {
							jQuery("#ownWidget-slider").html("<div>No images found!</div>");
							jQuery("#SlideShowButton").hide();
						}
					}
				}
				
				if (jQuery("a[rel=fancyArea]").length > 0) {
					jQuery("a[rel=fancyArea]").fancybox({
						transitionIn : "elastic",
						transitionOut : "elastic",
						speedIn : 600,
						speedOut : 200,
						titlePosition : "over",
						titleFormat : function(x, w, u, v) {
							return '<span id="fancybox-title-over">Image ' + (u + 1) + " / " + w.length + " " + x + "</span>"
						}
					});
					jQuery("#SlideShowButton").show();
				}
				  
				}
			});
	   },
	   loadSuperSizedTheme:function() {
		jQuery.supersized.themeVars = {
			progress_delay : false,
			thumb_page : false,
			thumb_interval : false,
			image_path : this.options.path + this.options.appspath+ "/files_sharing_widget/img/",
			play_button : "#pauseplay",
			next_slide : "#nextslide",
			prev_slide : "#prevslide",
			next_thumb : "#nextthumb",
			prev_thumb : "#prevthumb",
			slide_caption : "#slidecaption",
			slide_current : ".slidenumber",
			slide_total : ".totalslides",
			slide_list : "#slide-list",
			thumb_tray : "#thumb-tray",
			thumb_list : "#thumb-list",
			thumb_forward : "#thumb-forward",
			thumb_back : "#thumb-back",
			tray_arrow : "#tray-arrow",
			tray_button : "#tray-button",
			progress_bar : "#progress-bar"
		}
	},
	 initSupersized:function() {
		var self=this;
		var htmlString = '<div id="slideshow-content" style="display:none;z-index:201;"><div id="closeSlideShow">X</div><div id="prevthumb"></div><div id="nextthumb"></div>	<a id="prevslide" class="load-item"></a>	<a id="nextslide" class="load-item"></a>	<div id="thumb-tray" class="load-item"><div id="thumb-back"></div><div id="thumb-forward"></div></div>	<div id="progress-back" class="load-item"><div id="progress-bar"></div></div><div id="controls-wrapper" class="load-item"><div id="controls"><a id="play-button"><img id="pauseplay" src="'+ this.options.path + this.options.appspath + '/files_sharing_widget/img/pause.png" /></a><div id="slidecounter"><span class="slidenumber"></span><span class="totalslides"></span></div><div id="slidecaption"></div><a id="tray-button"><img id="tray-arrow" src="' + this.options.path + this.options.appspath + '/files_sharing_widget/img/button-tray-up.png" /></a><ul id="slide-list"></ul></div></div></div>';
		jQuery(htmlString).appendTo("body");
		jQuery("#closeSlideShow").click(function() {
			if (jQuery.supersized.vars.slideshow_interval) {
				clearInterval(jQuery.supersized.vars.slideshow_interval);
			}
			self.imageSlide = [];
			jQuery("#supersized-holder").remove();
			jQuery("#slideshow-content").animate({opacity : "toggle"});
			jQuery("#thumb-list").remove();
			self.widgetContainer.animate({opacity : "toggle"});
		});
	},

	loadSupersized:function() {
		this.loadSuperSizedTheme();
		jQuery.supersized({
			slideshow : 1,
			autoplay : 0,
			start_slide : 1,
			stop_loop : 0,
			random : 0,
			slide_interval : 3000,
			transition : 6,
			transition_speed : 1000,
			new_window : 1,
			pause_hover : 0,
			keyboard_nav : 1,
			performance : 1,
			image_protect : 1,
			min_width : 0,
			min_height : 0,
			vertical_center : 1,
			horizontal_center : 1,
			fit_always : 0,
			fit_portrait : 1,
			fit_landscape : 0,
			slide_links : false,
			new_window : false,
			thumb_links : 1,
			thumbnail_navigation : 0,
			slides : this.imageSlide,
			progress_bar : 1,
			mouse_scrub : 0
		});
	},
     loginToFacebookApp:function(){
   	      jQuery('body').append('<div id="fb-root"></div>');
		  jQuery.getScript(document.location.protocol + '//connect.facebook.net/de_DE/all.js');
          window.fbAsyncInit = function() {
		        FB.init({appId:this.options.fbAppid, status: true, cookie: true, xfbml: true});		    
		};
       
   }	



	
	
}

})(); 


