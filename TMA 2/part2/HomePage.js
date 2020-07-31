//-------------SETUP CONSTANTS AND STATES----------------
const LOGIN_STATE = "LOGIN";
const SIGNUP_STATE = "SIGNUP";
const CREATE_COURSE_STATE = "CREATE_COURSE";
const UPDATE_COURSE_STATE = "UPDATE_COURSE";
var DIALOG_STATE;

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

function fillInEditContent(idName) {
    // idName format syntax: editcourse_#
    var idNumStr = idName.substring(11, idName.length);
    var idNum = parseInt(idNumStr);
    // Replace the input text with the values pre-set, then for the user to edit them
    document.getElementById('edit_course_id').value = document.getElementById("courseid_" + idNum).innerHTML;
}

function signout() {
    // clear the cookies and send the user back to the default site
    let pastDate = new Date();
    pastDate.setTime(pastDate.getTime() + -1*24*60*60*1000);
    document.cookie = `loggedIn=; expires=${pastDate.toUTCString()}; path="/";
                       username=; expires=${pastDate.toUTCString()}; path="/";
                       fullname=; expires=${pastDate.toUTCString()}; path="/";
                       courseSelected=false; expires=${pastDate.toUTCString()}; path="/";
                       courseid=; expires=${pastDate.toUTCString()}; path="/";
                       unitSelected=false; expires=${pastDate.toUTCString()}; path="/";
                       unitNum=; expires=${pastDate.toUTCString()}; path="/";
                       quizSelected=false; expires=${pastDate.toUTCString()}; path="/";
                       quizid=; expires=${pastDate.toUTCString()};`;
    window.location = "HomePage.php";
}

function updateUIState() {
    switch (DIALOG_STATE) {
        case LOGIN_STATE:
            hideContent('signupContent');
            hideContent('createCourseContent');
            showContent('loginContent')
            break;
        case SIGNUP_STATE:
            hideContent('loginContent');
            hideContent('createCourseContent');
            showContent('signupContent');
            break;
        case CREATE_COURSE_STATE:
            hideContent('loginContent');
            hideContent('signupContent');
            hideContent('editCourseUpdateButton');
            showContent('createCourseContent');
            showContent('editCourseCreateButton');
            showContent('createCourseIntro');
            hideContent('updateCourseIntro');
            break;
        case UPDATE_COURSE_STATE:
            hideContent('loginContent');
            hideContent('signupContent');
            showContent('editCourseUpdateButton');
            showContent('createCourseContent');
            hideContent('editCourseCreateButton');
            showContent('updateCourseIntro');
            hideContent('createCourseIntro');
            break;
        default:
            break;
    }
}

function OverlayFadeOut(OverlayElement) {
	/*This function is to be used for the tutorial overlay ONLY*/
	document.getElementById(OverlayElement).style.opacity = 0;
	setTimeout(function() {
		hideContent(OverlayElement);
	}, OVERLAY_FADE_DELAY);
}

function startup() {

    if (document.getElementById('login_button') != null) {
        // Show the login prompt
        document.getElementById('login_button').addEventListener("click", function() {
            DIALOG_STATE = LOGIN_STATE;
            updateUIState();
            showContent('overlay');
        }, false);
    }

    if (document.getElementById('signup_button') != null) {
        document.getElementById('signup_button').addEventListener("click", function() {
            DIALOG_STATE = SIGNUP_STATE;
            updateUIState();
            showContent('overlay');
        }, false);
    }

    if (document.getElementById('createCourseButton') != null) {
        document.getElementById('createCourseButton').addEventListener("click", function() {
            DIALOG_STATE = CREATE_COURSE_STATE;
            updateUIState();
            showContent('overlay');
        }, false);
    }

    if (document.getElementById('editCourseCancelButton') != null) {
        document.getElementById('editCourseCancelButton').addEventListener("click", function() {
            OverlayFadeOut('overlay');
        }, false);
    }

    if (document.getElementById('signout_button') != null) {
        document.getElementById('signout_button').addEventListener("click", function() {signout()}, false);
    }

    if (document.getElementById('closeIconButton') != null) {
        document.getElementById('closeIconButton').addEventListener("click", function() {OverlayFadeOut('overlay')}, false);
    }

    // set up the listeners for all of the potential edit buttons if the SME has signed in
    var editCount = 1;

    while (document.getElementById('editcourse_' + editCount) != null) {
        document.getElementById('editcourse_' + editCount).addEventListener("click", function(el) {
            DIALOG_STATE = UPDATE_COURSE_STATE;
            updateUIState();
            fillInEditContent(this.id);
            showContent('overlay');
        }, false);
        editCount++;
    }

    // Set initial prompt states
    DIALOG_STATE = LOGIN_STATE;
}

// add all of the listeners
window.addEventListener('load', startup, false);