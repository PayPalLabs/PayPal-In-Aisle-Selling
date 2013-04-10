if (window.screen.width <= 640) {
	viewportString = "width=device-width initial-scale=0.5 maximum-scale=0.5 user-scalable=no target-densitydpi=320";
} else {
	viewportString = "width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=no";
}

var viewportObj = document.getElementById("metaViewport");
viewportObj.setAttribute("content",viewportString);