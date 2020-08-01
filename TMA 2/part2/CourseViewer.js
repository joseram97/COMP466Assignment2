const OVERLAY_FADE_DELAY = 500;
//-------------------------------------------------------

function hideContent(element) {
    document.getElementById(element).style.display = "none";
}

function hideErrors() {
    document.getElementById('feedbackDiv').style.display = "none";
}

function showContent(element) {
    hideErrors();

    switch (element) {
        case "LogInPrompt":
            document.getElementById(element).style.display = "block";
            break;
        case "overlay":
            document.getElementById(element).style.display = "flex";
            setTimeout(function() {
                document.getElementById(element).style.opacity = "1";
            }, 150);
            break;
    
        default:
            document.getElementById(element).style.display = "block";
            break;
    }
}

function signout() {
    // clear the cookies and send the user back to the default site
    let pastDate = new Date();
    pastDate.setTime(pastDate.getTime() + -1*24*60*60*1000);
    document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + pastDate.toUTCString() + ";"); });
    window.location = "HomePage.php";
}

function OverlayFadeOut(OverlayElement) {
	/*This function is to be used for the tutorial overlay ONLY*/
	document.getElementById(OverlayElement).style.opacity = 0;
	setTimeout(function() {
		hideContent(OverlayElement);
	}, OVERLAY_FADE_DELAY);
}

function startup() {

    if (document.getElementById('signout_button') != null) {
        document.getElementById('signout_button').addEventListener("click", function() {signout()}, false);
    }

    if (document.getElementById('closeIconButton') != null) {
        document.getElementById('closeIconButton').addEventListener("click", function() {OverlayFadeOut('overlay')}, false);
    }

}

// add all of the listeners
window.addEventListener('load', startup, false);