(function($){

	"use strict";
	  
	$(document).ready(function () {
		styler.init();
	});
	
	var styler = {
	
		init: function () {
			
			$("#template-styles h2 a").click(function(e){
				e.preventDefault();
				var div = $("#template-styles");
				console.log(div.css("left"));
				if (div.css("left") === "-135px") {
					$("#template-styles").animate({
						left: "0px"
					}); 
				} else {
					$("#template-styles").animate({
						left: "-135px"
					});
				}
			})

			$(".colors li a").click(function(e){
				e.preventDefault();
				var color = $(this).attr('class');
				color = color.replace('active', '').trim();
				$("#socialchef-style-color-css" ).attr("href", "http://www.themeenergy.com/themes/wordpress/social-chef/wp-content/themes/socialchef/css/theme-" + color + ".css" );
				
				if (typeof($(".intro .bg img" )) !== undefined && $(".intro .bg img" ).length > 0)
					$(".intro .bg img" ).attr("src", "http://www.themeenergy.com/themes/wordpress/social-chef/wp-content/themes/socialchef/images/intro-" + color + ".jpg" );
				
				$(this).parent().parent().find("a").removeClass("active");
				$(this).addClass("active");
			})
			
			$("#demo_enable_rtl").on('change', function (e) {
				if ($(this).prop('checked') )
					$('head').append('<link id="rtl-style-sheet" rel="stylesheet" href="http://www.themeenergy.com/themes/wordpress/social-chef/wp-content/themes/socialchef/css/style-rtl.css" type="text/css" />');
				else
					$('#rtl-style-sheet').remove();
			});
		
		}
		
	}
	
})(jQuery);