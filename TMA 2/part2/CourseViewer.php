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
        <script src="CourseViewer.js"></script>
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
                                        print('<a class="navbar-link" href="">Course Viewer</a>');
                                    print('</div>');
                                    print('<div class="navbar-item">');
                                        print('<a class="navbar-link" href="Quizzes.php">Course Quizzes</a>');
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

            if (isset($_POST["selectUnit"])) {
                $selectedUserNum = $_POST["unitNumber"];

                ob_start();
                setcookie("unitSelected", true);
                setcookie("unitNum", $selectedUserNum);
                ob_end_flush();
                
                header("Location: CourseViewer.php");
            }

            if (isset($_COOKIE["loggedIn"])) {
                print('<div style="display:flex; flex-direction: row; width: 100%">');
                    print('<div id="unit_selector" class="selectorContainer">');
                    // Get the units (and their numbers) and display them
                    $cookie_courseId = $_COOKIE["courseid"];
                    $unitResults = selectQueryBuilder($webDatabase, "Units", "unitNumber, unitTitle", "WHERE courseid=$cookie_courseId ORDER BY unitNumber");

                    while ($unitRow = mysqli_fetch_assoc($unitResults)) {
                        $selectorUnitNum = $unitRow["unitNumber"];
                        $selectorUnitTitle = $unitRow["unitTitle"];
        
                        print('<div class="selectorItemContainer">');
                            print('<form method="post" action="CourseViewer.php" class="selectorItem">');
                                print("<input type=\"text\" class=\"hidden\" name=\"unitNumber\" value=\"$selectorUnitNum\"/>");
                                print("<button type=\"submit\" name=\"selectUnit\" class=\"selectorUnitNumber\">Unit $selectorUnitNum</button>");
                                print("<div class=\"selectorUnitTitle\">$selectorUnitTitle</div>");
                            print('</form>');
                        print('</div>');
                    }
                    print('</div>');

                    print('<div id="main_content" class="mainBodyContainer" style="width: 90%">');
                    $cookie_username = $_COOKIE["username"];
                    $cookie_usertype = $_COOKIE["userType"];
                    $cookie_courseId = $_COOKIE["courseid"];
                    $cookie_unitId = $_COOKIE["unitNum"];

                    if (isset($_COOKIE["unitSelected"])) {
                        print('<div class="main-title">');
                            print("<h1>Unit $cookie_unitId</h1>");
                        print('</div>');
                        print('<div>To go back to the TMA2 homepage: <a href="../tma2.html">Click Here!</a></div>');

                        // Get all of the unit content information
                        $unitInfo = selectQueryBuilder($webDatabase, "Units", "unitTitle, unitDescription", "WHERE unitNumber=$cookie_unitId AND courseid=$cookie_courseId");

                        while ($row = mysqli_fetch_assoc($unitInfo)) {
                            $unitTitle = $row["unitTitle"];
                            $unitDescription = $row["unitDescription"];

                            print("<h1>$unitTitle</h1>");
                            print("<p>$unitDescription</p>");
                        }

                        // Get all of the lessons and their contents
                        $lessonInfo = selectQueryBuilder($webDatabase, "Lessons", "lessonNumber, lessonTitle", "WHERE unitNumber=$cookie_unitId AND courseid=$cookie_courseId");

                        while ($lessonRow = mysqli_fetch_assoc($lessonInfo)) {
                            $lessonNumber = $lessonRow["lessonNumber"];
                            $lessonTitle = $lessonRow["lessonTitle"];

                            print("<h2>$lessonTitle</h2>");
                            // Get the contents and their images (if available)
                            $lessonContentInfo = selectQueryBuilder($webDatabase, "LessonContents", "contentNumber, title, content", "WHERE courseid=$cookie_courseId AND unitNumber=$cookie_unitId AND lessonNumber=$lessonNumber");

                            while ($lessonContentRow = mysqli_fetch_assoc($lessonContentInfo)) {
                                $lessonContentNumber = $lessonContentRow["contentNumber"];
                                $lessonContentTitle = $lessonContentRow["title"]; // may be empty string or null
                                $lessonContentContent = $lessonContentRow["content"];

                                if ($lessonContentTitle != null && $lessonContentTitle != "") {
                                    print("<h3>$lessonContentTitle</h3>");
                                }

                                print("<p>$lessonContentContent</p>");
                                $lessonContentImages = selectQueryBuilder($webDatabase, "Images", "imageNumber, imageUrl", "WHERE courseid=$cookie_courseId AND unitNumber=$cookie_unitId AND lessonNumber=$lessonNumber AND contentNumber=$lessonContentNumber ORDER BY imageNumber");
                                
                                if (mysqli_num_rows($lessonContentImages) > 0) {
                                    if (mysqli_num_rows($lessonContentImages) > 1) {
                                        print('<div class="multipleImageContainer">');
                                        while ($lessonContentImageRow = mysqli_fetch_assoc($lessonContentImages)) {
                                            $imageUrl = $lessonContentImageRow["imageUrl"];
                                            print("<img src=\"./SME_Images/$imageUrl\" class=\"tutorialImage\" />");
                                        }
                                        print('</div>');
                                    }
                                    else {
                                        $imageRow = mysqli_fetch_row($lessonContentImages);
                                        $imageUrl = $imageRow[1];

                                        print("<img src=\"./SME_Images/$imageUrl\" class=\"tutorialImage\" style=\"max-width:100%\" />");
                                    }
                                }
                            }
                        }
                    }
                    else {
                        print('<div class="main-title">');
                            print("<h1>No Unit</h1>");
                        print('</div>');
                        print('<p>No Unit has been selected! Please select the unit from the left Unit selection pane.</p>');
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
