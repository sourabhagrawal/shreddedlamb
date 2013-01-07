var AJAX = function(url, params, callback, method)
{
	if(method == undefined)
		method = "POST";
	
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			callback(xmlhttp.status, xmlhttp.responseText);
		}
	}
	
	str = "";
	for(var key in params){
		str += key + "=" + encodeURIComponent(JSON.stringify(params[key]));
	}
	
	if(method == "GET")
		url = url + "?" + str;
	xmlhttp.open(method, url, true);
	
	if(method == "POST"){
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", str.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(str);
	}else{
		xmlhttp.send();
	}
}
