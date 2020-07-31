//----------SETTING UP CONSTANTS---------------
var CSS_QUIZ_RESULT_CORRECT = "quizResultCorrect";
var CSS_QUIZ_RESULT_INCORRECT = "quizResultIncorrect";
var QuizResultsDict = {};
var QuizResultDivElementIDs = [];
var CurrentQuiz;
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
    document.cookie = `loggedIn=; expires=${pastDate.toUTCString()};
                       username=; expires=${pastDate.toUTCString()};
                       fullname=; expires=${pastDate.toUTCString()};
                       courseSelected=false; expires=${pastDate.toUTCString()};
                       courseid=; expires=${pastDate.toUTCString()};
                       unitSelected=false; expires=${pastDate.toUTCString()};
                       unitNum=; expires=${pastDate.toUTCString()};
                       quizSelected=false; expires=${pastDate.toUTCString()};
                       quizid=; expires=${pastDate.toUTCString()};`;
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

function SubmitQuizAction(SubmitButton, numberOfQuestions) {

    // Calculate the score and show the results

    // Need a way to pass through the questions from the "current quiz" using php
    var UserCorrectQuestionsAnswered = 0;
    
    for (i=1; i <= numberOfQuestions; i++)
    {
        var QuestionID = "Q" + i;
        var QuestionCorrectAnswer = document.getElementById("div_Q" + i).getAttribute("correctAnswer"); // Need to verify that his may be an int
        var ChosenAnswer = QuizResultsDict[QuestionID];
        var ResultElementID = "Answer_" + ChosenAnswer + "_" + QuestionID;
        var ResultElement = document.getElementById(ResultElementID);
        QuizResultDivElementIDs.push(ResultElementID);
        if (ChosenAnswer == QuestionCorrectAnswer)
        {
            UserCorrectQuestionsAnswered++;
            // Modify the results div to be green
            ResultElement.classList.add(CSS_QUIZ_RESULT_CORRECT);
            ResultElement.innerHTML = "<b>Correct!<\/b>";
        }
        else
        {
            // Modify the results div to be red
            console.log("Gettting answer");
            console.log("AnswerText_" + QuestionCorrectAnswer + "_" + QuestionID);
            var CorrectAnswerText = document.getElementById("AnswerText_" + QuestionCorrectAnswer + "_" + QuestionID).innerHTML;
            ResultElement.classList.add(CSS_QUIZ_RESULT_INCORRECT);
            ResultElement.innerHTML = "<b>Incorrect. The correct answer was:<\/b><br><br>" + CorrectAnswerText;
        }
    }

    var QuizResultString = "You have correctly answered: " + UserCorrectQuestionsAnswered + "\/" + numberOfQuestions;
    
    var QuizResultPercentage = (UserCorrectQuestionsAnswered/numberOfQuestions)*100;

    var QuizResultPercentageString = "That is: " + QuizResultPercentage + "%";

    document.getElementById('QuizScore').innerHTML = QuizResultString + "<br>" + QuizResultPercentageString;

    SubmitButton.disabled = true;
    SubmitButton.style.cursor = "default";

    // Clear the dict
    QuizResultsDict = {};
}

function setQuestionAnswerValue(AnswerName, AnswerValue)
{
    QuizResultsDict[AnswerName] = AnswerValue;
}

function ResetSubmitButton(SubmitButton)
{
    for (var ElementID of QuizResultDivElementIDs)
    {
        var DivObject = document.getElementById(ElementID);
        DivObject.innerHTML = "";
        if (DivObject.classList.contains(CSS_QUIZ_RESULT_CORRECT))
        {
            DivObject.classList.remove(CSS_QUIZ_RESULT_CORRECT);
        }
        else
        {
            // It is assumed that it contains the 'incorrect' css class
            DivObject.classList.remove(CSS_QUIZ_RESULT_INCORRECT);
        }
    }

    document.getElementById('QuizScore').innerHTML = "";

    SubmitButton.disabled = false;
    SubmitButton.style.cursor = "pointer";
}

// add all of the listeners
window.addEventListener('load', startup, false);