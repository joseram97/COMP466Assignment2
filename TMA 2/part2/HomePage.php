<?php 
    require "phpLibrary/queryFunctions.php";
    require "phpLibrary/EMLFunctions.php";

    // setcookie("loggedIn", true, time() + 3600, "/");
    // setcookie("username", "tstUser1", time() + 3600, "/");
    // setcookie("fullname", "Jeff Bezos", time() + 3600, "/");
    // setcookie("usertype", "SME", time() + 3600, "/");
    setcookie("loggedIn", false, time() + 3600, "/");
    setcookie("username", null, time() + 3600, "/");
    setcookie("fullname", null, time() + 3600, "/");
    setcookie("usertype", null, time() + 3600, "/");
?>
<!DOCTYPE html>
<!--This is the main page for the first assignment. Create the welcome page and enable all of the navigation-->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
        <title>B.O.A.T. Bookmark</title>
        <link rel="stylesheet" type="text/css" href="../shared/Template_CSS.css"/>
        <link rel="stylesheet" type="text/css" href="../shared/BoatOnlineCourses.css"/>
        <link href="https://fonts.googleapis.com/css?family=Barlow:400,500,600,700,900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet"/>
        <script src="HomePage.js"></script>
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
                <a class="navbar-itemlogo" href="">
                    <img class="brand-logo" src="../shared/images/COMP466Logo.png"/>
                </a>
                <div class="container container-fluid">
                    <div class="navbar-itemscontainer">
                        <?php 
                            if (isset($_COOKIE["loggedIn"])) {
                                print('<div class="navbar-item">');
                                    print('<a class="navbar-link" href="">Course Management</a>');
                                print('</div>');
                                if (isset($_COOKIE["courseSelected"])) {
                                    print('<div class="navbar-item">');
                                        print('<a class="navbar-link" href="CourseViewer.php">Course Viewer</a>');
                                    print('</div>');
                                    print('<div class="navbar-item">');
                                        print('<a class="navbar-link" href="Quizzes.php">Course Quizzes</a>');
                                    print('</div>');
                                }
                            }
                            else {
                                print('<div class="navbar-item">');
                                    print('<a class="navbar-link" href="">Home</a>');
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
                        else {
                            // Display the login/signin form and then the top 10 bookmark sites
                            print('
                                <div class="headerButtonContainer">
                                    <div class="BoatButton right" id="login_button" style="margin-right: 5px;">Log in</div>
                                    <div class="BoatButton right" id="signup_button">Sign up</div>
                                </div>
                            ');
                        }
                    ?>
                </div>
            </nav>
        </header>
        <div id="main_content" class="mainBodyContainer">
            <div class="main-title">
                <?php 
                        // Set up the rest of the html page. Utilize the cookies if the user is logged in
                        if (isset($_COOKIE["loggedIn"])) {
                            print('<h1></h1>');
                        }
                        else {
                            print('<h1>Welcome!</h1>');
                        }
                    ?>
            </div>
            <div>To go back to the TMA2 homepage: <a href="../tma2.html">Click Here!</a></div>
            <h1>B.O.A.T. Online Courses</h1>
            <?php
                if (isset($_COOKIE["loggedIn"])) {
                    if ($_COOKIE["usertype"] == "SME") {
                        print('<p>Welcome to your Course Management page! Here you can create, edit, or remove courses that you have added to this website! Your name and the time you created the course will be mentioned on the course list below (and to all users on the platform). Remember, you can sign out with the "Sign Out" button on the top right</p>');
                    }
                    else {
                        print('<p>Welcome to youe Course Management page! Here you can request and remove courses that you would like to partake in! You will have 2 lists, one for the courses you are currently enrolled in, and a 2nd list of available courses you can take! Remember, you can sign out with the "Sign Out" button on the top right</p>');
                    }
                }
                else {
                    print('<p>This site is made to help teach you various courses to expand your mind! Here you can sign up and take any course created by our wonderful <strong>subject matter experts</strong>. See below some examples of the courses that we offer!</p>');
                    print('<p>To get started, either log in, or sign up using the button prompts at the top of the screen.</p>');
                }
            ?>
            <?php
                // Set up any constants that may be used throughout the program
                $servername = "au-comp466-assignment2-server.mysql.database.azure.com";
                $username = "jmramirez@au-comp466-assignment2-server";
                $password = "Passw0rd";
                $DATABASE_NAME = "BoatOnlineCourses";
                $cookie_domain = "au-comp466-assignment2-web";
                // $cookie_domain = "https://localhost";
                $cookie_path = "/wwwroot/TMA$202/part2";
                $CONST_DISPLAY_BLOCK = "style=\"display:block;\"";
                $CONST_DISPLAY_NONE = "style=\"display:none;\"";
                $CONST_DISPLAY_OPACITY_FLEX = "style=\"display:flex; opacity: 1;\"";
                $promptOverlayDisplay = $CONST_DISPLAY_NONE; // Initially hidden NOTE: May not need this
                $promptFeedbackMessage = "";
                $promptFeedbackStyle = "";
                $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                $promptMessageCreateCourseStyle = $CONST_DISPLAY_NONE;
                $promptCreateCourseButtonStyle = $CONST_DISPLAY_NONE;
                $promptUpdateCourseButtonStyle = $CONST_DISPLAY_NONE;

                function checkUserExists($database, $username) {
                    $result = selectQueryBuilder($database, "Users", "*", "WHERE username = '$username'");

                    if (mysqli_num_rows($result) == 1) {
                        return true; // user does exist
                    }
                    else {
                        return false; // user does not exist
                    }
                }

                // Set any errors that may occur
                $internal_errors = libxml_use_internal_errors(true);
                error_reporting(E_ERROR | E_PARSE);
                // error_reporting(1);

                $webDatabase = mysqli_init();

                mysqli_real_connect($webDatabase, $servername, $username, $password, $DATABASE_NAME, 3306);
                if (mysqli_connect_errno($webDatabase)) {
                    die('Failed to connect to MySQL: '.mysqli_connect_error());
                }

                // Get the database with all of the valid tables
                // if (!(mysqli_select_db($webDatabase, $DATABASE_NAME))) {
                //     die("Unable to access the BoatOnlineCourses database. Perhaps it needs to be created?");
                // }

                // Set up all of the call responses based on the events triggered within the html page. The majority of this site will remain on a single page
                if (isset($_POST["login_entered"])) {
                    // Login the user
                    $user_login_username = trim($_POST["username"]);
                    $user_login_password = $_POST["password"];
                    $displayError = false;

                    // Check that the user is valid
                    $result = selectQueryBuilder($webDatabase, "Users", "*", "WHERE username = '$user_login_username'");

                    if (mysqli_num_rows($result) == 1) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Check that the password matches
                            if ($row["password"] == $user_login_password) {
                                // login the user
                                // setcookie("loggedIn", true, time()+3600*24*30, $cookie_path, $cookie_domain);
                                // setcookie("username", $user_login_username, time()+3600*24*30, $cookie_path, $cookie_domain);
                                // setcookie("fullname", $row["fullname"], time()+3600*24*30, $cookie_path, $cookie_domain);
                                // setcookie("usertype", $row["userType"], time()+3600*24*30, $cookie_path, $cookie_domain);
                                setcookie("loggedIn", true);
                                setcookie("username", $user_login_username);
                                setcookie("fullname", $row["fullname"]);
                                setcookie("usertype", $row["userType"]);

                                header("Location: HomePage.php");
                            }
                            else {
                                $promptFeedbackMessage = "Password is <strong>not</strong> correct for Username: <strong>$user_login_username</strong>";
                                $displayError = true;
                            }
                        }
                    }
                    else if (mysqli_num_rows($result) == 0) {
                        $promptFeedbackMessage = "Username <strong>$user_login_username</strong> doesn't exist with the site. Sign up if you want to use this username";
                        $displayError = true;
                    }
                    else {
                        $promptFeedbackMessage = "There was an issue acessing the database";
                        $displayError = true;
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Show login with error
                        $promptMessageLoginStyle = $CONST_DISPLAY_BLOCK;
                        $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                        $promptMessageCreateCourseStyle = $CONST_DISPLAY_NONE;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }
                }
                else if (isset($_POST["signup_entered"])) {
                    // Add the user to the database. Check if the username is valid, and if the password is correct
                    $user_signup_username = trim($_POST["signup_username"]);
                    $user_signup_fullname = trim($_POST["signup_fullname"]);
                    $user_signup_usertype = trim($_POST["signup_userType"]);
                    $user_signup_password = $_POST["signup_password"];
                    $user_signup_password2 = $_POST["signup_password2"];
                    $displayError = false;

                    // check to see if the username is already taken
                    $usernameExists = checkUserExists($webDatabase, $user_signup_username);

                    if ($usernameExists) {
                        $promptFeedbackMessage = "Username <strong>$user_signup_username</strong> already exists. Please try a different name.";
                        $displayError = true;
                    }
                    else {
                        // ensure that the password is typed in correctly
                        if ($user_signup_password != $user_signup_password2) {
                            $promptFeedbackMessage = "Password was not typed in correctly";
                            $displayError = true;
                        }
                        else {
                            // free to make username
                            if (!insertQueryBuilder($webDatabase, "Users", "username, fullname, password, userType", "'$user_signup_username', '$user_signup_fullname', '$user_signup_password', '$user_signup_usertype'")) {
                                $promptFeedbackMessage = "Unable to save account";
                                $displayError = true;
                            }
                            else {
                                // success
                                setcookie("loggedIn", true);
                                setcookie("username", $user_signup_username);
                                setcookie("fullname", $user_signup_fullname);
                                setcookie("userType", $user_signup_usertype);
                                header("Location: HomePage.php");
                            }
                        }
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Display sign up
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_BLOCK;
                        $promptMessageCreateCourseStyle = $CONST_DISPLAY_NONE;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }

                }
                else if (isset($_POST["edit_course_create"])) {
                    $cookie_username = $_COOKIE["username"];
                    $displayError = false;
                    $success = true;
                    $returnMessage = "";

                    if (isset($_FILES["createCourse_EMLFile"]) && ($_FILES["createCourse_EMLFile"]['error'] == UPLOAD_ERR_OK)) {
                        // Check if there are file images
                        if (isset($_FILES["createCourse_images"])) {
                            list($success, $returnMessage) = uploadCourseToDatabase($webDatabase, $cookie_username, $_FILES["createCourse_EMLFile"], $_FILES["createCourse_images"]);
                        }
                        else {
                            list($success, $returnMessage) = uploadCourseToDatabase($webDatabase, $cookie_username, $_FILES["createCourse_EMLFile"]);
                        }

                        if (!$success) {
                            $displayError = true;
                            $promptFeedbackMessage = $returnMessage;
                        }
                    }


                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Display sign up
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                        $promptMessageCreateCourseStyle = $CONST_DISPLAY_BLOCK;
                        $promptCreateCourseButtonStyle = $CONST_DISPLAY_BLOCK;
                        $promptUpdateCourseButtonStyle = $CONST_DISPLAY_NONE;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }
                }
                else if (isset($_POST["edit_course_update"])) {
                    $edit_courseid = $_POST["courseId"];
                    $cookie_username = $_COOKIE["username"];
                    $displayError = false;
                    $success = true;
                    $returnMessage = "";

                    // Remove any images within the server file system
                    deleteServerImages($webDatabase, $edit_courseid);

                    // remove the course from the database, then re-add with the current EML file and images
                    // NOTE: Make sure to check if the images already exist within the server
                    if (!deleteQueryBuilder($webDatabase, "Courses", "courseid = $edit_courseid AND userCreator = '$cookie_username'")) {
                        $displayError = true;
                        $promptFeedbackMessage = "There was an error with removing the course for editing";
                    }
                    else {

                        // Re-add new course and (optional) new images
                        if (isset($_FILES["createCourse_EMLFile"]) && ($_FILES["createCourse_EMLFile"]['error'] == UPLOAD_ERR_OK)) {
                            // Check if there are file images
                            if (isset($_FILES["createCourse_images"])) {
                                list($success, $returnMessage) = uploadCourseToDatabase($webDatabase, $cookie_username, $_FILES["createCourse_EMLFile"], $_FILES["createCourse_images"]);
                            }
                            else {
                                list($success, $returnMessage) = uploadCourseToDatabase($webDatabase, $cookie_username, $_FILES["createCourse_EMLFile"]);
                            }
    
                            if (!$success) {
                                $displayError = true;
                                $promptFeedbackMessage = $returnMessage;
                            }
                        }
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Display sign up
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                        $promptMessageCreateCourseStyle = $CONST_DISPLAY_BLOCK;
                        $promptCreateCourseButtonStyle = $CONST_DISPLAY_NONE;
                        $promptUpdateCourseButtonStyle = $CONST_DISPLAY_BLOCK;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }
                }
                else if (isset($_POST["addLearnerCourse"])) {
                    $add_courseid = $_POST["courseId"];
                    $cookie_username = $_COOKIE["username"];

                    if(!insertQueryBuilder($webDatabase, "UserCourseAssignments", "courseid, username", "$add_courseid, '$cookie_username'")) {
                        die("Unable to request and add the course");
                    }
                }
                else if (isset($_POST["deleteLearnerCourse"])) {
                    $delete_courseid = $_POST["courseId"];
                    $cookie_username = $_COOKIE["username"];

                    $deleteResult = deleteQueryBuilder($webDatabase, "UserCourseAssignments", "courseid = $delete_courseid AND username = '$cookie_username'");
                }
                else if (isset($_POST["deleteSMEcourse"])) {
                    $delete_courseid = $_POST["courseId"];
                    $cookie_username = $_COOKIE["username"];

                    deleteServerImages($webDatabase, $delete_courseid);
                    $deleteResult = deleteQueryBuilder($webDatabase, "Courses", "courseid = $delete_courseid AND userCreator = '$cookie_username'");
                }
                else if (isset($_POST["openCourse"])) {
                    // Open the course in the course viewer page
                    $selected_courseid = $_POST["courseId"];
                    setcookie("courseSelected", true);
                    setcookie("courseid", $selected_courseid);
                    setcookie("unitSelected", false);
                    
                    header("Location: CourseViewer.php");
                }

                print('<div id="main_course_home">');
                    
                    if (isset($_COOKIE["loggedIn"])) {
                        $cookie_username = $_COOKIE["username"];
                        $cookie_fullname = $_COOKIE["fullname"];
                        $cookie_usertype = $_COOKIE["usertype"];
                        print("<h2><strong>$cookie_fullname's Course Management</strong></h2>");
                        if ($cookie_usertype == "SME") {

                            // Show only the courses that the SME has created and the option to add more
                            print('<div id="createCourseButton" class="BoatButton left" style="width: fit-content;">Create Course</div>'); // Prompt will ask to supply EML file
                            print('<div class="courseListContainer">');
                            $coursesResult = selectQueryBuilder($webDatabase, "Courses", "*", "WHERE userCreator = '$cookie_username'");

                            if (mysqli_num_rows($coursesResult) == 0) {
                                print('<div class="courseItemContainer">');
                                    print('<div>');
                                        print('There are no courses assigned to your account. Please click the <strong>Create Course</strong> button to the top left of this box to start adding courses to this site!');
                                    print('</div>');
                                print('</div>');
                            }
                            else {
                                $row_count = 0;
                                while ($row = mysqli_fetch_assoc($coursesResult)) {
                                    $row_count = $row_count + 1;
                                    $rowcourseId = $row["courseid"];
                                    $rowcourseName = $row["courseName"];
                                    $rowcourseShortDescription = $row["courseShortDescription"];
                                    $rowcourseCreatedDate = date("M j, Y", strtotime($row["createdDate"]));
                                        print("<div>");
                                            print('<form method="post" action="HomePage.php" class="courseItemContainer">');
                                                print('<div class="courseItemInfo">');
                                                    print("<div class=\"courseNameStyle\">$rowcourseName<div class=\"courseDateText\">created: $rowcourseCreatedDate</div></div>");
                                                    print("<button type=\"submit\" name=\"openCourse\" class=\"courseLinkStyle\">Click Here to select course</button>");
                                                    print("<div class=\"courseDescriptionStyle\">$rowcourseShortDescription</div>");
                                                print('</div>');
                                                print('<div class="courseButtonsContainer">');
                                                    print("<div class=\"hidden\" id=\"courseid_$row_count\">$rowcourseId</div>"); // This is for the edit
                                                    print("<input type=\"text\" class=\"hidden\" name=\"courseId\" value=\"$rowcourseId\"/>");
                                                    print("<button type=\"button\" id=\"editcourse_$row_count\" class=\"courseButtonItem\">");
                                                        print('<img class="courseButtonImage" src="../shared/images/edit_icon.png"/>');
                                                    print('</button>');
                                                    print("<button type=\"submit\" name=\"deleteSMEcourse\" class=\"courseButtonItem\">");
                                                        print('<img class="courseButtonImage" src="../shared/images/delete_icon.png"/>');
                                                    print('</button>');
                                                print('</div>');
                                            print('</form>');
                                        print('</div>');
                                }
                            }
                            print('</div>');
                        }
                        else {
                            print('<div style="display: flex; padding: 0px 10px; justify-content: space-around;">
                                    <div><strong><u>Enrolled Courses</u></strong></div>
                                    <div><strong><u>Courses Available</u></strong></div>
                                  </div>');
                            // Show content for a normal user. Show's 2 lists, current courses registered in, and available courses to register
                            print('<div style="display: flex; margin-top: 10px;">');
                                $cookie_username = $_COOKIE["username"];
                                $coursesResult = selectQueryBuilder($webDatabase, "Courses as C", "*", "INNER JOIN UserCourseAssignments as UA ON C.courseid = UA.courseid WHERE UA.username = '$cookie_username'");

                                print('<div class="courseListContainer" style="max-width: 49%;">');
                                if (mysqli_num_rows($coursesResult) == 0) {
                                    print('<div class="courseItemContainer">');
                                        print('<div>');
                                            print('There are no courses assigned to your account. Please click the <strong>+</strong> button on any of the courses on the course list to the right');
                                        print('</div>');
                                    print('</div>');
                                }
                                else {
                                    $row_count = 0;
                                    while ($row = mysqli_fetch_assoc($coursesResult)) {
                                        $row_count = $row_count + 1;
                                        $rowcourseId = $row["courseid"];
                                        $rowcourseName = $row["courseName"];
                                        $rowcourseShortDescription = $row["courseShortDescription"];
                                        $rowcourseCreatedDate = date("M j, Y", strtotime($row["createdDate"]));
                                            print("<div>");
                                                print('<form method="post" action="HomePage.php" class="courseItemContainer">');
                                                    print('<div class="courseItemInfo">');
                                                        print("<div class=\"courseNameStyle\">$rowcourseName<div class=\"courseDateText\">created: $rowcourseCreatedDate</div></div>");
                                                        print("<button type=\"submit\" name=\"openCourse\" class=\"courseLinkStyle\">Click Here to select course</button>");
                                                        print("<div class=\"courseDescriptionStyle\">$rowcourseShortDescription</div>");
                                                    print('</div>');
                                                    print('<div class="courseButtonsContainer">');
                                                        print("<input type=\"text\" class=\"hidden\" name=\"courseId\" value=\"$rowcourseId\"/>");
                                                        print("<button type=\"submit\" name=\"deleteLearnerCourse\" class=\"courseButtonItem\">");
                                                            print('<img class="courseButtonImage" src="../shared/images/delete_icon.png"/>');
                                                        print('</button>');
                                                    print('</div>');
                                                print('</form>');
                                            print('</div>');
                                    }
                                }
                                print('</div>');
                                
                                $coursesAvailable = selectQueryBuilder($webDatabase, "Courses as C", "*", "WHERE C.courseid NOT IN (SELECT courseid FROM UserCourseAssignments as UA WHERE UA.username = '$cookie_username')");

                                print('<div class="courseListContainer" style="max-width: 49%;">');
                                if (mysqli_num_rows($coursesAvailable) == 0) {
                                    print('<div class="courseItemContainer">');
                                        print('<div>');
                                            print('<strong>There are no more courses available to request.</strong> Please wait for SME\'s to add more courses for you to continue learning!');
                                        print('</div>');
                                    print('</div>');
                                }
                                else {
                                    $row_count = 0;
                                    while ($row = mysqli_fetch_assoc($coursesAvailable)) {
                                        $row_count = $row_count + 1;
                                        $rowcourseId = $row["courseid"];
                                        $rowcourseName = $row["courseName"];
                                        $rowcourseShortDescription = $row["courseShortDescription"];
                                        $rowcourseCreatedDate = date("M j, Y", strtotime($row["createdDate"]));
                                            print("<div>");
                                                print('<form method="post" action="HomePage.php" class="courseItemContainer">');
                                                    print('<div class="courseItemInfo">');
                                                        print("<div class=\"courseNameStyle\">$rowcourseName<div class=\"courseDateText\">created: $rowcourseCreatedDate</div></div>");
                                                        print("<div class=\"courseDescriptionStyle\">$rowcourseShortDescription</div>");
                                                    print('</div>');
                                                    print('<div class="courseButtonsContainer" style="flex-direction: column;">');
                                                        print("<input type=\"text\" class=\"hidden\" name=\"courseId\" value=\"$rowcourseId\"/>");
                                                        print("<button type=\"submit\" name=\"addLearnerCourse\" class=\"courseButtonItem\">");
                                                            print('<img class="courseButtonImage" src="../shared/images/add_icon.png"/>');
                                                        print('</button>');
                                                        print("<button type=\"submit\" name=\"deleteLearnerCourse\" class=\"courseButtonItem\">");
                                                            print('<img class="courseButtonImage" src="../shared/images/delete_icon.png"/>');
                                                        print('</button>');
                                                    print('</div>');
                                                print('</form>');
                                            print('</div>');
                                    }
                                }
                                print('</div>');
                            print('</div>');
                        }
                    }
                    else {
                        print('<h2><strong>Some courses available on our site!</strong></h2>');
                        print('<div class="courseListContainer">');
                        $coursesResult = selectQueryBuilder($webDatabase, "Courses", "courseName, courseShortDescription, userCreator, createdDate", "ORDER BY createdDate DESC LIMIT 15");

                        $rowCount = 0;
                        while ($row = mysqli_fetch_assoc($coursesResult)) {
                            $rowCount = $rowCount + 1;
                            $rowcourseId = $row["courseid"];
                            $rowcourseName = $row["courseName"];
                            $rowcourseShortDescription = $row["courseShortDescription"];
                            $rowcourseCreatedDate = date("M j, Y", strtotime($row["createdDate"]));

                            print("<div class=\"courseItemContainer\">");
                                print('<div class="courseItemInfo">');
                                print("<div class=\"courseNameStyle\">$rowcourseName<div class=\"courseDateText\">created: $rowcourseCreatedDate</div></div>");
                                    print("<div class=\"courseDescriptionStyle\">$rowcourseShortDescription</div>");
                                print('</div>');
                            print('</div>');
                        }
                        print("</div>");
                    }
                print('</div>'); // This is the end of main_course_home

                print("<div id=\"overlay\" class=\"overlay centerChildElements\" $promptOverlayDisplay>");
                    print('<div id="PromptModal" class="overlayContainer">');
                        print('<div id="closeIconContainer" class="closeIconContainer">');
                            print('<span id="closeIconButton" class="closeIconButton" title="Close">&times;</span>');
                        print('</div>');
                        print('<div id="mainPrompt" class="promptBox centerChildElements flex">');
                            print("<div class=\"feedbackMessageBox\" id=\"feedbackDiv\" $promptFeedbackStyle>$promptFeedbackMessage</div>");
                                print('<form method="post" action="HomePage.php">');
                                    print("<div id=\"loginContent\" class=\"contentContainer\" $promptMessageLoginStyle>");
                                        print('<div class="introTitle">');
                                            print('Log In');
                                        print('</div>');
                                        print('<div id="LoginIntro" class="introText">');
                                            print('Please log in with your credentials');
                                        print('</div>');
                                        print('<div id="userInputsLogin" class="userInputContainer">');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Username: </strong></label>');
                                                print('<input type="text" class="userInputBox" placeholder="Please enter username..." name="username" required />');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Password: </strong></label>');
                                                print('<input type="password" class="userInputBox" placeholder="Enter password..." name="password" required />');
                                            print('</div>');
                                            print('<button class="userInputButton" type="submit" name="login_entered">Log In</button>');
                                        print('</div>');
                                    print('</div>');
                                print('</form>');
                                print('<form method="post" action="HomePage.php">');
                                    print("<div id=\"signupContent\" class=\"contentContainer\" $promptMessageSignupStyle>");
                                        print('<div class="introTitle">');
                                            print('Sign Up');
                                        print('</div>');
                                        print('<div id="SignupIntro" class="introText">');
                                            print('In order to sign up for the website, please enter a valid username and password below. Then click <strong>Sign up!</strong>');
                                        print('</div>');
                                        print('<div id="userInputsSignUp" class="userInputContainer">');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Username: </strong></label>');
                                                print('<input type="text" class="userInputBox" placeholder="Please enter username..." name="signup_username" required>');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Full Name: </strong></label>');
                                                print('<input type="text" class="userInputBox" placeholder="John Appleseed" name="signup_fullname" required>');
                                            print('</div>');
                                            print('<div class="radioInputContainer">');
                                                print('<label><strong>User Type: </strong></label>');
                                                print('<input type="radio" id="Learner" name="signup_userType" value="Learner">');
                                                print('<label for="Learner">Learner</label>');
                                                print('<input type="radio" id="SME" name="signup_userType" value="SME">');
                                                print('<label for="SME">Subject Matter Expert</label>');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Password: </strong></label>');
                                                print('<input type="password" class="userInputBox" placeholder="Enter password..." name="signup_password" required>');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Re-type Password: </strong></label>');
                                                print('<input type="password" class="userInputBox" placeholder="Enter password..." name="signup_password2" required>');
                                            print('</div>');
                                            print('<button class="userInputButton" type="submit" name="signup_entered">Sign up!</button>');
                                        print('</div>');
                                    print('</div>');
                                print('</form>');
                                print('<form method="post" action="HomePage.php" enctype="multipart/form-data">');
                                    print("<div id=\"createCourseContent\" class=\"contentContainer\" $promptMessageCreateCourseStyle>");
                                        print("<div id=\"createCourseIntro\" class=\"introTitle\" $promptCreateCourseButtonStyle>");
                                            print('Create Course');
                                        print('</div>');
                                        print("<div id=\"updateCourseIntro\" class=\"introTitle\" $promptUpdateCourseButtonStyle>");
                                            print('Edit Course');
                                        print('</div>');
                                        print('<div id="CreateCourseIntro" class="introText">');
                                            print('In order to create and upload a course to this website, please add the file of the EML (Education Markup Language) file below, and any images used for the course content!');
                                        print('</div>');
                                        print('<div id="userInputsCreateCourse" class="userInputContainer">');
                                            print("<input type=\"text\" class=\"hidden\" name=\"courseId\" id=\"edit_course_id\"/>");
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>EML File: </strong></label>');
                                                print('<input type="file" name="createCourse_EMLFile" required>');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Images: </strong></label>');
                                                print('<input type="file" name="createCourse_images[]" multiple>');
                                            print('</div>');
                                            print('<div class="userInputButtonContainer">');
                                                print('<button id="editCourseCancelButton" class="userInputButton" type="button" name="edit_course_canceled">Cancel</button>');
                                                print("<button id=\"editCourseCreateButton\" class=\"BoatButton\" $promptCreateCourseButtonStyle type=\"submit\" name=\"edit_course_create\">Upload</button>");
                                                print("<button id=\"editCourseUpdateButton\" class=\"BoatButton\" $promptUpdateCourseButtonStyle type=\"submit\" name=\"edit_course_update\">Edit</button>");
                                            print('</div>');
                                        print('</div>');
                                    print('</div>');
                                print('</form>');
                            print('</div>');
                        print('</div>');
                    print('</div>');
                print('</div>');
                

                libxml_use_internal_errors($internal_errors);
            ?>
            <p style="color: white; font-weight: 800;">
                B.O.A.T. Copyright (not real)
            </p>
        </div>
    </body>
</html>
<script>
</script>
