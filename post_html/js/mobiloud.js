var mobiloud_is_ipad = navigator.userAgent.match(/iPad/i) != null;

function mobiloud_orient()
{
	platform = "iphone";
	if(mobiloud_is_ipad) platform = "ipad";
  switch(window.orientation){  
            case 0: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/"+platform+"_portrait.css";  
            break;  

            case -90: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/"+platform+"_landscape.css";  
            break;  

            case 90: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/"+platform+"_landscape.css";  
            break;  
  }
}

window.onload = mobiloud_orient(); 