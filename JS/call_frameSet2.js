// JavaScript Document
var isNav4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) == 4);
if (parent == window) {
    // Don't do anything if NN4 is printing frame
    if (!isNav4 || (isNav4 && window.innerWidth != 0)) {
//        if (location.replace) {
           // Use replace(), if available, to keep current page out of history
           location.replace("/Frames2/Frameset.html?mainFrame=" + escape(location.href));
//        } else {
 //          location.href = "/Frames/Frameset.html?mainFrame=" + escape(location.href);
 //       }
    }
}
