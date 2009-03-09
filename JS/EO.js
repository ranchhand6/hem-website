/*
 * This script is written by Geert Van Aken
 * Please read the official documentation for more information
 * about the functions of this file.
 *
 * http://altum.be/products/emailobfuscator
 *
 * Please do not remove this information from the file and
 * report improvements that you make to this sourcecode
 *
 * Version 1.1.0
 * Date    2006/04/11
 */

var monkeyCode = 4 << 4;
var oldStatusText = "";

function EOa() {
  return String.fromCharCode(monkeyCode);
}

function EOd(pText) {
  var splitted = pText.split(",");
  var result = "";

  for (i = 0 ; i < splitted.length ; i++) {
    result += String.fromCharCode(splitted[i]);
  }
  return result;
}

function EOp() {
  var prefix = EOd('109,97,105');
  prefix += EOd('108,116');
  return prefix + EOd('111,58');
}

function EOad(pName, pdomain) {
  EOad(pName, pDomain, null);
}

function EOinitStatus(pName, pDomain) {
  oldStatusText = window.status;
  window.status = Loc(pName, pDomain);
}

function EOrestoreStatus() {
  window.status = oldStatusText;
}

function EOae(pName, pDomain, pSubj, pHover, pText, pClass) {

//  alert("pName = " + pName + "\npDomain = " + pDomain + "\npSubj = " + pSubj + "\npHover = " + pHover + "\npText = " + pText + "\npClass = " + pClass);

  var result = "<a href=\"JavaScript:EOad('" + pName + "','" + pDomain + "'";
  if (pSubj != null && pSubj.length > 2) {
    result += ",'" + pSubj + "'";
  }
  result += ");\"";

  if (pHover != null && pHover.length > 0) {
    result += " title=\"" + EOd(pHover) + "\"";
  }

  if (pClass != null && pClass.length > 0) {
    result += " class=\"" + pClass + "\"";
  }

  result += " onMouseOver=\"EOinitStatus('" + pName + "','" + pDomain + "');return true;\" onMouseOut=\"EOrestoreStatus();\"";

  result += ">" + EOd(pText) + "</a>";

//  alert(result);

  document.write(result);

}

function EOad(pName, pDomain, pSubj) {
  var loc = Loc(pName, pDomain);
  if (pSubj != null && pSubj.length > 0) {
    loc += "?" + EOd('115,117,98,106,101,99,116') + "=" + encodeURIComponent(EOd(pSubj));
  }

  document.location = loc;
}

function Loc(pName, pDomain) {
  var first = EOd(pName);
  var second = EOd(pDomain);
  var loc = EOp() + first + EOa() + second; 
  
  return loc;
}