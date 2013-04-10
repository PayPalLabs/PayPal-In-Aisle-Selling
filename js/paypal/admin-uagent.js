var uagent = navigator.userAgent;

if ( uagent.indexOf("iPhone") >= 0 || uagent.indexOf("iPod") >= 0 || uagent.indexOf("iPad") >= 0 || uagent.indexOf("Android") >= 0 ) {

	if ( uagent.indexOf("iPhone") >= 0 || uagent.indexOf("iPod") >= 0 ) {
		// iPhones & iPods
		viewportString = "width=640, maximum-scale=0.5, user-scalable=no";
	} else if ( uagent.indexOf("Android") >= 0 ) {
		// Android Devices
		viewportString = "width=640, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, target-densityDpi=device-dpi";
	} else {
		// iPad & Other Tablets
		viewportString = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
	}

	var viewportObj = document.getElementById("metaViewport");
	viewportObj.setAttribute("content",viewportString);

}