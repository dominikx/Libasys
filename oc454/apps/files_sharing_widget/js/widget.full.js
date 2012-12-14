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
    var widOpt=ownWidgetOptions;          
    var widgetContainer;        
    var overlay; 
    var loadPage="";
    var firstShow=true;
	var imageSlide = [];

	
	if (window.jQuery === undefined || window.jQuery.fn.jquery !== "1.8.2") {
		var script_tag = document.createElement("script");
		script_tag.setAttribute("type", "text/javascript");
		script_tag.setAttribute("src", widOpt.path + "apps/files_sharing_widget/js/jquery-pack.min.js");
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

  function initMovingBox(){
  		 var h=widgetContainer.height() - 80;
					    if(jQuery('#albumPics').length > 0) var h=widgetContainer.height() -210;
						
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
							  widgetContainer.prepend('<div id="ownWidget-Loader">&nbsp;</div>');
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
  }
  
	function loadData() {
		
		jQuery.ajax({
			dataType : "jsonp",
			jsonp : "jsonp_callback",
			url : widOpt.path + "widget.php?iToken=" + rawurlencode(widOpt.crypt) + loadPage,
			
			success : function(data) {
				
				jQuery("#ownWidget-output").html("");
				jQuery("#ownWidget-output").html(data.databack);
				jQuery("#ownWidget-header").html(data.nav + " " + data.folder);
				
				
				if (jQuery("#loginForm").length > 0) {
					jQuery("#loginForm #iSubmit").click(function(event) {
						event.preventDefault();
						loadPage = "&password=" + jQuery("#password").val();
						loadData();
					});
				}
				jQuery("#ownWidget-header .loadAlbum").each(function(i, el) {
					jQuery(el).click(function() {
						if (jQuery(el).attr("title") == "") {
							loadPage = "";
						} else {
							loadPage = "&path=/" + jQuery(el).attr("title");
						}
						loadData();
					})
				});
				
				if (firstShow) {
					
					widgetContainer.css(widOpt.cssAddWidget);
					
					if (!widOpt.cssAddWidget.top && !widOpt.cssAddWidget.left) {
						widgetContainer.css({
							top : (jQuery(window).height() / 2) - (widgetContainer.height() / 2),
							left : (jQuery(window).width() / 2) - (widgetContainer.width() / 2)
						});
					}
					
					widgetContainer.show();
				
					firstShow = false;
				}
				
				if (jQuery("#ownWidget-slider").length > 0) {
					
					jQuery("#ownWidget-slider").css({width : widgetContainer.width() - 12});
					jQuery("#ownWidget-slider > div").css({width : widgetContainer.width() - 15	});
					
					if (jQuery("#ownWidget-slider > div").length > 1) {
					   initMovingBox();
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
	}

	function loadSuperSizedTheme() {
		jQuery.supersized.themeVars = {
			progress_delay : false,
			thumb_page : false,
			thumb_interval : false,
			image_path : widOpt.path + "apps/files_sharing_widget/img/",
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
	}

	function initSupersized() {
		var htmlString = '<div id="slideshow-content" style="display:none;"><div id="closeSlideShow">X</div><div id="prevthumb"></div><div id="nextthumb"></div>	<a id="prevslide" class="load-item"></a>	<a id="nextslide" class="load-item"></a>	<div id="thumb-tray" class="load-item"><div id="thumb-back"></div><div id="thumb-forward"></div></div>	<div id="progress-back" class="load-item"><div id="progress-bar"></div></div><div id="controls-wrapper" class="load-item"><div id="controls"><a id="play-button"><img id="pauseplay" src="' + widOpt.path + 'apps/files_sharing_widget/img/pause.png" /></a><div id="slidecounter"><span class="slidenumber"></span><span class="totalslides"></span></div><div id="slidecaption"></div><a id="tray-button"><img id="tray-arrow" src="' + widOpt.path + 'apps/files_sharing_widget/img/button-tray-up.png" /></a><ul id="slide-list"></ul></div></div></div>';
		jQuery(htmlString).appendTo("body");
		jQuery("#closeSlideShow").click(function() {
			if (jQuery.supersized.vars.slideshow_interval) {
				clearInterval(jQuery.supersized.vars.slideshow_interval);
			}
			imageSlide = [];
			jQuery("#supersized-holder").remove();
			jQuery("#slideshow-content").animate({opacity : "toggle"});
			jQuery("#thumb-list").remove();
			widgetContainer.animate({opacity : "toggle"});
		});
	}

	function loadSupersized() {
		loadSuperSizedTheme();
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
			slides : imageSlide,
			progress_bar : 1,
			mouse_scrub : 0
		});
	}

	function addWidgetControls() {
		if (widOpt.display == "hidden") {
			var CloseButton = jQuery('<div id="ownWidget-closeButton">X</div>');
			widgetContainer.prepend(CloseButton);
		}
		widgetContainer.prepend('<div id="ownWidget-output" class="jcarousel"></div>');
		var wHeader = jQuery('<div id="ownWidget-header"></div><div id="SlideShowButton">Slideshow</div>');
		widgetContainer.prepend(wHeader);
		
		jQuery("#SlideShowButton").click(function() {
			widgetContainer.animate({
				opacity : "toggle"
			}, 500, function() {
				jQuery("body").append("<div id='supersized-holder'></div>");
				jQuery("#supersized-loader").remove();
				jQuery("#supersized").remove();
				jQuery("#supersized-holder").append("<div id='supersized-loader'></div><ul id='supersized'></ul>");
				jQuery("#supersized").show();
				jQuery("#slideshow-content").animate({opacity : "toggle"});
				jQuery("a[rel=fancyArea]").each(function(i, el) {
					imageSlide.push({
						image : el.href,
						title : el.title.replace(/</, "&lt;").replace(/>/, "&gt;"),
						thumb : el.children[0].src
					});
				});
				loadSupersized();
			});
		});
		jQuery("#SlideShowButton").hide();
		if (widOpt.display == "hidden") {
			CloseButton.click(function() {
				widgetContainer.animate({
					opacity : "toggle"
				}, 500, function() {
					if (widOpt.modal) {
						overlay.height(0);
						overlay.width(0);
					}
					firstShow = true;
					widgetContainer.hide();
				});
			});
		}
	}

	function showWidget(){
		widgetContainer.hide();
		addWidgetControls();
		loadData();
		initSupersized();
	}
  
	function createShowButton() {
		widgetContainer.hide();
		if (widOpt.modal) {
			overlay = jQuery('<div id="ownWidget-overlayWid"></div>').appendTo("body");
		}
		addWidgetControls();
		var DisplayButton = jQuery('<div id="ownWidget-displayButton">' + widOpt.buttonlabel + "</div>");
		DisplayButton.css(widOpt.cssAddButton);
		DisplayButton.appendTo("body");
		DisplayButton.click(function() {
			if (widOpt.modal) {
				overlay.height(jQuery(window).height());
				overlay.width(jQuery(window).width());
				overlay.animate({
					opacity : 0.5
				}, 500, function() {
					 loadData();
				});
			} else {
				 loadData();
			}
		});
	}

	function main() {
		jQuery(document).ready(function($) {
			widgetContainer = $("#ownWidget-container");
			$("<link>", {
				rel : "stylesheet",
				type : "text/css",
				href : widOpt.path + "apps/files_sharing_widget/css/widget.full.css"
			}).appendTo("head");
			if($.browser.msie && $.browser.version<9){
				//widgetContainer.css('opacity',0.3);
			}
			if (widOpt.display == "hidden") {
				createShowButton();
			} else {
				showWidget();
			}
			
		});
	}

})(); 