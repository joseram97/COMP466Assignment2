//-------------SETUP CONSTANTS AND STATES----------------
const LOGIN_STATE = "LOGIN";
const SIGNUP_STATE = "SIGNUP";
const BOOKMARK_ADD_STATE = "BOOKMARK_ADD";
const BOOKMARK_EDIT_STATE = "BOOKMARK_UPDATE";
var DIALOG_STATE;

const OVERLAY_FADE_DELAY = 500;
//-------------------------------------------------------

//---------------SETUP MUTATION OBSERVER---------------
const config = {
    characterData: true
};

const callback = function(mutationsList, observer) {
    // Use traditional 'for loops' for IE 11
    for(let mutation of mutationsList) {
        if (mutation.type === 'characterData') {
            console.log('The characters have changed.');
            console.log(mutation);
            console.log(JSON.stringify(mutation));
        }
    }
};

const observer = new MutationObserver(callback);
//----------------------------------------------------

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
    // idName format syntax: editBookmark_#
    var idNumStr = idName.substring(13, idName.length);
    var idNum = parseInt(idNumStr);
    // Replace the input text with the values pre-set, then for the user to edit them
    document.getElementById('edit_bookmark_name').value = document.getElementById("bookmarkItemName_" + idNum).innerHTML;
    document.getElementById('edit_bookmark_url').value = document.getElementById("bookmarkItemUrl_" + idNum).innerHTML;
    document.getElementById('prevBookmarkName').value = document.getElementById("bookmarkItemName_" + idNum).innerHTML;
    document.getElementById('prevBookmarkUrl').value = document.getElementById("bookmarkItemUrl_" + idNum).innerHTML;
}

function signout() {
    // clear the cookies and send the user back to the default site
    let pastDate = new Date();
    pastDate.setTime(pastDate.getTime() + -1*24*60*60*1000);
    document.cookie = `loggedIn=; expires=${pastDate.toUTCString()}; username=; expires=${pastDate.toUTCString()}; fullname=; expires=${pastDate.toUTCString()};`;
    window.location = "BookmarkSite.php";
}

function updateUIState() {
    switch (DIALOG_STATE) {
        case LOGIN_STATE:
            hideContent('signupContent');
            hideContent('editBookmarkContent');
            showContent('loginContent')
            break;
        case SIGNUP_STATE:
            hideContent('loginContent');
            hideContent('editBookmarkContent');
            showContent('signupContent');
            break;
        case BOOKMARK_EDIT_STATE:
            hideContent('signupContent');
            hideContent('loginContent');
            showContent('editBookmarkContent');
            showContent('editDialogUpdateButton');
            hideContent('editDialogSaveButton');
            showContent('updateBookmarkIntro');
            hideContent('addBookmarkIntro');
            break;
        case BOOKMARK_ADD_STATE:
            hideContent('signupContent');
            hideContent('loginContent');
            showContent('editBookmarkContent');
            showContent('editDialogSaveButton');
            hideContent('editDialogUpdateButton');
            showContent('addBookmarkIntro');
            hideContent('updateBookmarkIntro');
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

    if (document.getElementById('signout_button') != null) {
        document.getElementById('signout_button').addEventListener("click", function() {signout()}, false);
    }

    if (document.getElementById('closeIconButton') != null) {
        document.getElementById('closeIconButton').addEventListener("click", function() {OverlayFadeOut('overlay')}, false);
    }

    if (document.getElementById('addBookmarkButton') != null) {
        document.getElementById('addBookmarkButton').addEventListener("click", function() {
            DIALOG_STATE = BOOKMARK_ADD_STATE;
            updateUIState();
            showContent('overlay');
        }, false);
    }

    if (document.getElementById('editBookmarkCancelButton') != null) {
        document.getElementById('editBookmarkCancelButton').addEventListener("click", function() {
            OverlayFadeOut('overlay');
        }, false);
    }

    // set up the listeners for all of the potential edit buttons if the user has signed in
    var editCount = 1;

    while (document.getElementById('editBookmark_' + editCount) != null) {
        document.getElementById('editBookmark_' + editCount).addEventListener("click", function(el) {
            console.log(el);
            DIALOG_STATE = BOOKMARK_EDIT_STATE;
            updateUIState();
            fillInEditContent(this.id);
            showContent('overlay');
        }, false);
        editCount++;
    }

    // Set up the observer to the feedback form
    // const targetPromptFeedbackNode = document.getElementById('feedbackDiv');

    // observer.observe(targetPromptFeedbackNode, config);

    // Set initial prompt states
    DIALOG_STATE = LOGIN_STATE;
}

// add all of the listeners
window.addEventListener('load', startup, false);
// window.addEventListener('ended', function() {observer.disconnect();}, false);