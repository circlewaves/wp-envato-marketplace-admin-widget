(function ( $ ) {
	"use strict";

	$(function () {

		var cw_wpemaw_tabs = $( "#cw-wpemaw-tabs" ).tabs({
        activate: function(event, ui){
            //get the active tab index
            var active = $("#cw-wpemaw-tabs").tabs("option", "active");

            //save it to cookies
            $.cookie("cw_wpemaw_ActiveTab", active);
        }
		});
		
		//read the cookie
    var cw_wpemaw_ActiveTab = $.cookie("cw_wpemaw_ActiveTab");

    //make active needed tab
    if(cw_wpemaw_ActiveTab !== undefined) {
        cw_wpemaw_tabs.tabs("option", "active", cw_wpemaw_ActiveTab);
    }		

	});

}(jQuery));