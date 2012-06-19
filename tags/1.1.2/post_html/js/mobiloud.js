
function mobiloud_orient()
{
  switch(window.orientation){  
            case 0: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/iphone_portrait.css";  
            break;  

            case -90: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/iphone_landscape.css";  
            break;  

            case 90: document.getElementById("orient_css").href = "/wp-content/plugins/mobiloud-mobile-app-plugin/post_html/css/iphone_landscape.css";  
            break;  
  }
}

window.onload = mobiloud_orient(); 