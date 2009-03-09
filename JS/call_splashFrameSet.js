// JavaScript Document
var isNav4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) == 4);
if (parent == window) {
    // Don't do anything if NN4 is printing frame
    if (!isNav4 || (isNav4 && window.innerWidth != 0)) {
        
           location.replace("/index.html?mainFrame=" + escape(location.href));
        }  
    
}
