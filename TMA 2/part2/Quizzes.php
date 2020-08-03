<?php
    require "phpLibrary/queryFunctions.php";
    ob_start();
?>
<!DOCTYPE html>
<!--This is the main page for the first assignment. Create the welcome page and enable all of the navigation-->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
        <title>B.O.A.T. Bookmark</title>
        <link rel="icon" type="image/png" href ="../shared/images/COMP466Logo_favicon.png"/>
        <link rel="stylesheet" type="text/css" href="../shared/Template_CSS.css"/>
        <link rel="stylesheet" type="text/css" href="../shared/BoatOnlineCourses.css"/>
        <link rel="stylesheet" type="text/css" href="../shared/BoatViewerStyles.css"/>
        <link href="https://fonts.googleapis.com/css?family=Barlow:400,500,600,700,900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet"/>
        <script src="Quizzes.js"></script>
    </head>
    <body>
        <header class="fixed">
            <div class="topRowHeader">
                <div class="container container-fluid topRowContainer">
                    <div class="topRowItem">
                        <strong>B.O.A.T.com</strong> - Welcome to B.O.A.T.com!
                    </div>
                </div>
            </div>
            <nav class="navbar-container">
                <a class="navbar-itemlogo" href="HomePage.php">
                    <img class="brand-logo" src="../shared/images/COMP466Logo.png"/>
                </a>
                <div class="container container-fluid">
                    <div class="navbar-itemscontainer">
                        <?php 
                            if (isset($_COOKIE["loggedIn"])) {
                                print('<div class="navbar-item">');
                                    print('<a class="navbar-link" href="HomePage.php">Course Management</a>');
                                print('</div>');
                                if (isset($_COOKIE["courseSelected"])) {
                                    print('<div class="navbar-item">');
                                        print('<a class="navbar-link" href="CourseViewer.php">Course Viewer</a>');
                                    print('</div>');
                                    print('<div class="navbar-item">');
                                        print('<a class="navbar-link" href="">Course Quizzes</a>');
                                    print('</div>');
                                }
                            }
                            else {
                                print('<div class="navbar-item">');
                                    print('<a class="navbar-link" href="HomePage.php">Home</a>');
                                print('</div>');
                            }
                        ?>
                    </div>
                    <?php 
                        // Set up the rest of the html page. Utilize the cookies if the user is logged in
                        if (isset($_COOKIE["loggedIn"])) {
                            // Display user specific bookmarks and additional bookmark functionality
                            print('<div class="BoatButton right" id="signout_button">Sign Out</div>');
                        }
                    ?>
                </div>
            </nav>
        </header>
        <?php
            // Set up any constants that may be used throughout the program
            $servername = getenv("PHP_MYSQL_DATABASESERVER");
            $username = getenv("PHP_MYSQL_USERNAME");
            $password = getenv("PHP_MYSQL_PASSWORD");
            $DATABASE_NAME = "BoatOnlineCourses";
            $CONST_DISPLAY_BLOCK = "style=\"display:block;\"";
            $CONST_DISPLAY_NONE = "style=\"display:none;\"";
            $CONST_DISPLAY_OPACITY_FLEX = "style=\"display:flex; opacity: 1;\"";
            $promptOverlayDisplay = $CONST_DISPLAY_NONE; // Initially hidden NOTE: May not need this
            $promptFeedbackMessage = "";
            $promptFeedbackStyle = "";
            $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
            $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
            $promptMessageEditStyle = $CONST_DISPLAY_NONE;

            // Set any errors that may occur
            $internal_errors = libxml_use_internal_errors(true);
            error_reporting(E_ERROR | E_PARSE);

            $webDatabase = mysqli_init();

            mysqli_real_connect($webDatabase, $servername, $username, $password, $DATABASE_NAME, 3306);
            if (mysqli_connect_errno($webDatabase)) {
                die('Failed to connect to MySQL: '.mysqli_connect_error());
            }

            if (isset($_POST["selectQuiz"])) {
                $selectedQuizNum = $_POST["quizNum"]; // This is basically the unit number as well

                ob_start();
                setcookie("quizSelected", true);
                setcookie("quizNum", $selectedQuizNum);
                ob_end_flush();
                
                header("Location: Quizzes.php");
            }

            if (isset($_COOKIE["loggedIn"])) {
                print('<div style="display:flex; flex-direction: row; width: 100%">');
                    print('<div id="quiz_selector" class="selectorContainer">');
                    // Get the units (and their numbers) and display them
                    $cookie_courseId = $_COOKIE["courseid"];
                    $unitResults = selectQueryBuilder($webDatabase, "Quizzes", "DISTINCT unitNumber", "WHERE courseid=$cookie_courseId ORDER BY unitNumber");

                    while ($unitRow = mysqli_fetch_assoc($unitResults)) {
                        $selectorUnitNum = $unitRow["unitNumber"];
        
                        print('<div class="selectorItemContainer">');
                            print('<form method="post" action="Quizzes.php" class="selectorItem">');
                                print("<input type=\"text\" class=\"hidden\" name=\"quizNum\" value=\"$selectorUnitNum\"/>");
                                print("<button type=\"submit\" name=\"selectQuiz\" class=\"selectorUnitNumber\">Quiz $selectorUnitNum</button>");
                                print("<div class=\"selectorUnitTitle\">For Unit $selectorUnitNum</div>");
                            print('</form>');
                        print('</div>');
                    }
                    print('</div>');

                    print('<div id="main_content" class="mainBodyContainer" style="width: 90%">');
                    $cookie_username = $_COOKIE["username"];
                    $cookie_usertype = $_COOKIE["userType"];
                    $cookie_courseId = $_COOKIE["courseid"];

                    if (isset($_COOKIE["quizSelected"])) {
                        $cookie_quizNum = $_COOKIE["quizNum"];

                        print('<div class="main-title">');
                            print("<h1>Quiz $cookie_quizNum</h1>");
                        print('</div>');
                        print('<div>To go back to the TMA2 homepage: <a href="../tma2.html">Click Here!</a></div>');

                        print('<form name="QuizForm" id="QuizForm" onsubmit="SubmitQuizAction(this)">');
                            print('<ol>');
                            // Get all of the questions
                            $unitQuestions = selectQueryBuilder($webDatabase, "Quizzes", "*", "WHERE courseid=$cookie_courseId AND unitNumber=$cookie_quizNum");
                            $numUnitQuestions = mysqli_num_rows($unitQuestions);
                            while ($questionRow = mysqli_fetch_assoc($unitQuestions)) {
                                $questionNum = $questionRow["questionNumber"];
                                $questionTitle = $questionRow["questionDescription"];
                                $questionCorrectAnswer = $questionRow["correctAnswer"];
                                $answer1 = htmlentities($questionRow["answer1"]);
                                $answer2 = htmlentities($questionRow["answer2"]);
                                $answer3 = htmlentities($questionRow["answer3"]); // NOTE: may be empty or null
                                $answer4 = htmlentities($questionRow["answer4"]); // NOTE: may be empty or null

                                print('<li class="quiz-question-container">');
                                    print("<div class=\"quiz-question-header\">$questionTitle</div>");
                                    print("<div id=\"div_Q$questionNum\" class=\"quiz-question-radiogroup\" correctAnswer=\"$questionCorrectAnswer\">");
                                        print('<label class="quiz-question-radioitem">');
                                            print("<input type=\"radio\" name=\"Q$questionNum\" value=\"1\" onclick=\"setQuestionAnswerValue(this.name, this.value)\"/>");
                                            print("<div id=\"AnswerText_1_Q$questionNum\" style=\"display: initial; margin-left: 5px;\">$answer1</div>");
                                        print('</label>');
                                        print("<div class=\"quizResult\" id=\"Answer_1_Q$questionNum\"></div>");
                                        print('<label class="quiz-question-radioitem">');
                                            print("<input type=\"radio\" name=\"Q$questionNum\" value=\"2\" onclick=\"setQuestionAnswerValue(this.name, this.value)\"/>");
                                            print("<div id=\"AnswerText_2_Q$questionNum\" style=\"display: initial; margin-left: 5px;\">$answer2</div>");
                                        print('</label>');
                                        print("<div class=\"quizResult\" id=\"Answer_2_Q$questionNum\"></div>");
                                        if ($answer3 != null && $answer3 != "") {
                                            print('<label class="quiz-question-radioitem">');
                                                print("<input type=\"radio\" name=\"Q$questionNum\" value=\"3\" onclick=\"setQuestionAnswerValue(this.name, this.value)\"/>");
                                                print("<div id=\"AnswerText_3_Q$questionNum\" style=\"display: initial; margin-left: 5px;\">$answer3</div>");
                                            print('</label>');
                                            print("<div class=\"quizResult\" id=\"Answer_3_Q$questionNum\"></div>");
                                        }
                                        if ($answer4 != null && $answer4 != "") {
                                            print('<label class="quiz-question-radioitem">');
                                                print("<input type=\"radio\" name=\"Q$questionNum\" value=\"4\" onclick=\"setQuestionAnswerValue(this.name, this.value)\"/>");
                                                print("<div id=\"AnswerText_4_Q$questionNum\" style=\"display: initial; margin-left: 5px;\">$answer4</div>");
                                            print('</label>');
                                            print("<div class=\"quizResult\" id=\"Answer_4_Q$questionNum\"></div>");
                                        }
                                    print('</div>');
                                print('</li>');
                            }
                            print('</ol><br>');
                            print('<input type="reset" value="Reset" class="BoatButton" onclick="ResetSubmitButton(document.getElementById(\'SubmitButton\'))"/>');
                            print('<br>');
                            print("<input id=\"SubmitButton\" type=\"button\" value=\"Submit\" class=\"BoatButton\" onclick=\"SubmitQuizAction(this, $numUnitQuestions)\"/>");
                            print('<div id="QuizScore" class="quizScore"></div>');
                        print('</form>');
                    }
                    else {
                        print('<div class="main-title">');
                            print("<h1>Quizzes</h1>");
                        print('</div>');
                        print('<p>Please select a quiz from the left Quiz selection pane.</p>');
                    }
                    print('</div>');
                print('</div>');
            }
            else {
                print('<p>You are not logged in. Please log in through the home page by clicking the Home button on the top</p>');
            }

            libxml_use_internal_errors($internal_errors);
        ?>
        <p style="color: white; font-weight: 800;">
            B.O.A.T. Copyright (not real)
        </p>
    </body>
</html>
<script>
</script>
