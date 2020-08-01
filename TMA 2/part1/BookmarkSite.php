<!DOCTYPE html>
<!--This is the main page for the first assignment. Create the welcome page and enable all of the navigation-->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
        <title>B.O.A.T. Bookmark</title>
        <link rel="stylesheet" type="text/css" href="../shared/Template_CSS.css"/>
        <link rel="stylesheet" type="text/css" href="../shared/BookmarkStyle.css"/>
        <link href="https://fonts.googleapis.com/css?family=Barlow:400,500,600,700,900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet"/>
        <script src="BookmarkSite.js"></script>
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
                        <div class="navbar-item">
                            <a class="navbar-link" href="">Bookmarks</a>
                        </div>
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
                            // Display user specific bookmarks and additional bookmark functionality
                            print('<h1></h1>');
                        }
                        else {
                            // Display the login/signin form and then the top 10 bookmark sites
                            print('<h1>Welcome!</h1>');
                        }
                    ?>
            </div>
            <div>To go back to the TMA2 homepage: <a href="../tma2.html">Click Here!</a></div>
            <h1>Website Bookmarks</h1>
            <p>This page will help you manage all of your favorite bookmarks on the web. You can also view some of the most popular bookmarks found on the web based on other users!</p>
            <?php
                // Set up any constants that may be used throughout the program
                $servername = "au-comp466-assignment2-server.mysql.database.azure.com";
                $username = "jmramirez@au-comp466-assignment2-server";
                $password = "Passw0rd";
                $DATABASE_NAME = "bookmarkweb";
                $CONST_DISPLAY_BLOCK = "style=\"display:block;\"";
                $CONST_DISPLAY_NONE = "style=\"display:none;\"";
                $CONST_DISPLAY_OPACITY_FLEX = "style=\"display:flex; opacity: 1;\"";
                $promptOverlayDisplay = $CONST_DISPLAY_NONE; // Initially hidden NOTE: May not need this
                $promptFeedbackMessage = "";
                $promptFeedbackStyle = "";
                $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                $promptMessageEditStyle = $CONST_DISPLAY_NONE;
                $promptAddBookmarkButtonStyle = $CONST_DISPLAY_NONE;
                $promptUpdateBookmarkButtonStyle = $CONST_DISPLAY_NONE;
                

                // Set up any functions that can be helpful
                function selectQueryBuilder($database, $tableName, $fields, $condition) {
                    $selectQuery = "SELECT $fields FROM $tableName ";

                    if ($condition != null) {
                        $selectQuery = $selectQuery . $condition;
                    }

                    if (!($result = mysqli_query($database, $selectQuery))) {
                        print("Unable to execute select query");
                        die(mysqli_error());
                    }

                    return $result; // Ensure areas where this function is being called, check the results
                    
                }

                function insertQueryBuilder($database, $tableName, $valueFields, $values) {
                    $insertQuery = "INSERT INTO $tableName ($valueFields) VALUES ($values);";
                    
                    if (!($result = mysqli_query($database, $insertQuery))) {
                        print("Unable to execute insert query");
                        die(mysqli_error($result));
                    }

                    if ($result) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }

                function deleteQueryBuilder($database, $tableName, $condition) {
                    $deleteQuery = "DELETE FROM $tableName WHERE $condition";

                    if (!($result = mysqli_query($database, $deleteQuery))) {
                        print("Unable to execute delete query");
                        die(mysqli_error());
                    }

                    if ($result) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }

                function updateQueryBuilder($database, $tableName, $updateValues, $condition) {
                    // NOTE: Ensure that the updateValues parameters includes the following syntax
                    //       column1 = value1, column2 = value2, ...
                    //       And also ensure that it uses the correct column names
                    $updateQuery = "UPDATE $tableName SET $updateValues WHERE $condition";

                    if (!($result = mysqli_query($database, $updateQuery))) {
                        print("Unable to execute update query");
                        die(mysqli_error());
                    }

                    if ($result) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }

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

                // Create the connection
                $webDatabase = mysqli_init();

                mysqli_real_connect($webDatabase, $servername, $username, $password, $DATABASE_NAME, 3306);
                if (mysqli_connect_errno($webDatabase)) {
                    die('Failed to connect to MySQL: '.mysqli_connect_error());
                }

                // Set up all of the call responses based on the events triggered within the html page. The majority of this site will remain on a single page
                if (isset($_POST["login_entered"])) {
                    // Login the user
                    $user_login_username = trim($_POST["username"]);
                    $user_login_password = $_POST["password"];
                    $displayError = false;

                    // Display Login
                    $result = selectQueryBuilder($webDatabase, "Users", "*", "WHERE username = '$user_login_username'");

                    if (mysqli_num_rows($result) == 1) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Check that the password matches
                            if ($row["password"] == $user_login_password) {
                                // login the user
                                setcookie("loggedIn", true);
                                setcookie("username", $user_login_username);
                                setcookie("fullname", $row["fullname"]);
                                header("Location: BookmarkSite.php");
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
                        $promptMessageEditStyle = $CONST_DISPLAY_NONE;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }
                }
                else if (isset($_POST["signup_entered"])) {
                    // Add the user to the database. Check if the username is valid, and if the password is correct
                    $user_signup_username = trim($_POST["signup_username"]);
                    $user_signup_fullname = trim($_POST["signup_fullname"]);
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
                            if (!insertQueryBuilder($webDatabase, "Users", "username, fullname, password", "'$user_signup_username', '$user_signup_fullname', '$user_signup_password'")) {
                                $promptFeedbackMessage = "Unable to save account";
                                $displayError = true;
                            }
                            else {
                                // success
                                setcookie("loggedIn", true);
                                setcookie("username", $user_signup_username);
                                setcookie("fullname", $user_signup_fullname);
                                header("Location: BookmarkSite.php");
                            }
                        }
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Display sign up
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_BLOCK;
                        $promptMessageEditStyle = $CONST_DISPLAY_NONE;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                    }

                }
                else if (isset($_POST["edit_bookmark_add"])) {
                    // Add a new bookmark
                    $cookie_username = $_COOKIE["username"];
                    $user_bookmark_name = trim($_POST["edit_bookmark_name"]);
                    $user_bookmark_url = trim($_POST["edit_bookmark_url"]);
                    $displayError = false;

                    if (filter_var($user_bookmark_url, FILTER_VALIDATE_URL) === false) {
                        $promptFeedbackMessage = "Bookmark URL <strong>$user_bookmark_url</strong> is not <strong>valid</strong>";
                        $displayError = true;
                    }
                    else {
                        $urlHeaders = @get_headers($user_bookmark_url);
                        if ($urlHeaders && strpos($urlHeaders[0], '200')) {
                            // The url exists
                            // Check to see if the user already created the same name
                            $checkNameExists = selectQueryBuilder($webDatabase, "Bookmarks", "bookmarkName", "WHERE bookmarkName = '$user_bookmark_name' AND username = '$cookie_username'");

                            if (mysqli_num_rows($checkNameExists) == 1) {
                                $promptFeedbackMessage = "Bookmark name <strong>$user_bookmark_name</strong> already exists";
                                $displayError = true;
                            }
                            else {
                                // Good to insert
                                if (!insertQueryBuilder($webDatabase, "Bookmarks", "username, bookmarkName, bookmarkUrl", "'$cookie_username', '$user_bookmark_name', '$user_bookmark_url'")) {
                                    $promptFeedbackMessage = "Unable to save bookmark. Bookmark name: $user_bookmark_name, Bookmark Url: $user_bookmark_url. Error with server.";
                                    $displayError = true;
                                }
                                else {
                                    header("Location: BookmarkSite.php");
                                }
                            }
                        }
                        else {
                            $promptFeedbackMessage = "Bookmark Url <strong>$user_bookmark_url</strong> <strong>does not exist</strong>";
                            $displayError = true;
                        }
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";

                        // Display sign up
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                        $promptMessageEditStyle = $CONST_DISPLAY_BLOCK;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                        $promptAddBookmarkButtonStyle = $CONST_DISPLAY_BLOCK;
                        $promptUpdateBookmarkButtonStyle = $CONST_DISPLAY_NONE;
                    }
                }
                else if (isset($_POST["edit_bookmark_update"])) {
                    // Edit an existing bookmark
                    $cookie_username = $_COOKIE["username"];
                    $user_bookmark_name = trim($_POST["edit_bookmark_name"]);
                    $user_bookmark_url = trim($_POST["edit_bookmark_url"]);
                    $prev_user_bookmark_name = $_POST["prevBookmarkName"];
                    $prev_user_bookmark_url = $_POST["prevBookmarkUrl"];
                    $displayError = false;

                    if (filter_var($user_bookmark_url, FILTER_VALIDATE_URL) === false) {
                        $promptFeedbackMessage = "Bookmark URL <strong>$user_bookmark_url</strong> is not <strong>valid</strong>";
                        $displayError = true;
                    }
                    else {
                        $urlHeaders = @get_headers($user_bookmark_url);
                        if ($urlHeaders && strpos($urlHeaders[0], '200')) {
                            if ($prev_user_bookmark_name == $user_bookmark_name && $prev_user_bookmark_url == $user_bookmark_url) {
                                // No changes have been made
                                $promptFeedbackMessage = "No changes have been made. Values are the same as before";
                                $displayError = true;
                            }
                            else if ($prev_user_bookmark_name == $user_bookmark_name) {
                                // Good to Update
                                if (!updateQueryBuilder($webDatabase, "Bookmarks", "bookmarkName = '$user_bookmark_name', bookmarkUrl = '$user_bookmark_url'", "username = '$cookie_username' AND bookmarkName = '$prev_user_bookmark_name'")) {
                                    $promptFeedbackMessage = "Unable to update bookmark. Bookmark Name: $user_bookmark_name, Bookmark Url: $user_bookmark_url. Error with server.";
                                    $displayError = true;
                                }
                                else {
                                    header("Location: BookmarkSite.php");
                                }
                            }
                            else {
                                // Check that the name is not already taken
                                $checkNameExists = selectQueryBuilder($webDatabase, "Bookmarks", "bookmarkName", "WHERE bookmarkName = '$user_bookmark_name' AND username = '$cookie_username'");
    
                                if (mysqli_num_rows($checkNameExists) == 1) {
                                    $promptFeedbackMessage = "Bookmark name <strong>$user_bookmark_name</strong> already exists";
                                    $displayError = true;
                                }
                                else {
                                    // Good to Update
                                    if (!updateQueryBuilder($webDatabase, "Bookmarks", "bookmarkName = '$user_bookmark_name', bookmarkUrl = '$user_bookmark_url'", "username = '$cookie_username' AND bookmarkName = '$prev_user_bookmark_name'")) {
                                        $promptFeedbackMessage = "Unable to update bookmark. Bookmark Name: $user_bookmark_name, Bookmark Url: $user_bookmark_url. Error with server.";
                                        $displayError = true;
                                    }
                                    else {
                                        header("Location: BookmarkSite.php");
                                    }
        
                                }
                            }   
                        }
                        else {
                            $promptFeedbackMessage = "Bookmark Url <strong>$user_bookmark_url</strong> <strong>does not exist</strong>";
                            $displayError = true;
                        }
                        
                    }

                    if ($displayError) {
                        $promptFeedbackStyle = "style=\"color: red; display: block;\"";
                        $promptMessageLoginStyle = $CONST_DISPLAY_NONE;
                        $promptMessageSignupStyle = $CONST_DISPLAY_NONE;
                        $promptMessageEditStyle = $CONST_DISPLAY_BLOCK;
                        $promptOverlayDisplay = $CONST_DISPLAY_OPACITY_FLEX;
                        $promptAddBookmarkButtonStyle = $CONST_DISPLAY_NONE;
                        $promptUpdateBookmarkButtonStyle = $CONST_DISPLAY_BLOCK;
                    }
                }
                else if (isset($_POST["deleteBookmark"])) {
                    // Delete a bookmark for the user
                    $delete_bookmarkName = $_POST["bookmarkName"];
                    $delete_bookmarkUrl = $_POST["bookmarkUrl"];
                    $cookie_username = $_COOKIE["username"];

                    $deleteResult = deleteQueryBuilder($webDatabase, "Bookmarks", "username = '$cookie_username' AND bookmarkName = '$delete_bookmarkName' AND bookmarkUrl = '$delete_bookmarkUrl'");

                }

                print('<div id="main_bookmark_page">');
                    
                    if (isset($_COOKIE["loggedIn"])) {
                        $cookie_username = $_COOKIE["username"];
                        $cookie_fullname = $_COOKIE["fullname"];
                        print("<h2><strong>$cookie_fullname's Bookmarks</strong></h2>");
                        print('<div id="addBookmarkButton" class="BoatButton left" style="width: fit-content;">Add Bookmark</div>');
                        print('<div class="bookmarkListContainer">');
                        $bookmarksResult = selectQueryBuilder($webDatabase, "Bookmarks", "bookmarkName, bookmarkUrl", "WHERE username = '$cookie_username'");

                        if (mysqli_num_rows($bookmarksResult) == 0) {
                            print('<div class="bookmarkItemContainer">');
                                print('<div>');
                                    print('There are no bookmarks assigned to your account. Please click the <strong>Add Bookmark</strong> button to the top left of this box');
                                print('</div>');
                            print('</div>');
                        }
                        else {
                            $row_count = 0;
                            while ($row = mysqli_fetch_assoc($bookmarksResult)) {
                                $row_count = $row_count + 1;
                                $rowBookmarkName = $row["bookmarkName"];
                                $rowBookmarkUrl = $row["bookmarkUrl"];
                                    print("<div class=\"bookmarkItemContainer\">");
                                        print('<div class="bookmarkItemInfo">');
                                            print("<div id=\"bookmarkItemName_$row_count\" class=\"bookmarkNameStyle\">$rowBookmarkName</div>");
                                            print("<div id=\"bookmarkItemUrl_$row_count\" class=\"bookmarkUrlStyle\" onClick=\"window.open('$rowBookmarkUrl', '_blank')\">$rowBookmarkUrl</div>");
                                        print('</div>');
                                        print('<form method="post" action="BookmarkSite.php" class="bookmarkButtonsContainer">');
                                            print("<input type=\"text\" class=\"hidden\" name=\"bookmarkName\" value=\"$rowBookmarkName\"/>");
                                            print("<input type=\"text\" class=\"hidden\" name=\"bookmarkUrl\" value=\"$rowBookmarkUrl\"/>");
                                            print("<button type=\"button\" id=\"editBookmark_$row_count\" class=\"bookmarkButtonItem\">");
                                                print('<img class="bookmarkButtonImage" src="../shared/images/edit_icon.png"/>');
                                            print('</button>');
                                            print("<button type=\"submit\" name=\"deleteBookmark\" class=\"bookmarkButtonItem\">");
                                                print('<img class="bookmarkButtonImage" src="../shared/images/delete_icon.png"/>');
                                            print('</button>');
                                        print('</form>');
                                    print('</div>');
                            }
                        }
                        
                        print("</div>");
                    }
                    else {
                        print('<h2><strong>Top 10 user saved bookmarks!</strong></h2>');
                        print('<div class="bookmarkListContainer">');
                        $bookmarksResult = selectQueryBuilder($webDatabase, "Bookmarks", "Count(bookmarkUrl) as bookmarkCount, bookmarkUrl", "GROUP BY bookmarkUrl ORDER BY Count(bookmarkUrl) DESC LIMIT 10");

                        $rowCount = 0;
                        while ($row = mysqli_fetch_assoc($bookmarksResult)) {
                            $rowCount = $rowCount + 1;
                            $rowBookmarkCount = $row["bookmarkCount"];
                            $rowBookmarkUrl = $row["bookmarkUrl"];

                            $usedText = "users";
                            if ($rowBookmarkCount == 1) {
                                $usedText = "user";
                            }

                            print("<div class=\"bookmarkItemContainer\">");
                                print('<div class="bookmarkItemInfo">');
                                    print("<div class=\"bookmarkNameStyle\">$rowCount<div class=\"bookmarkUserCountText\">Used by $rowBookmarkCount $usedText</div></div>");
                                    print("<div class=\"bookmarkUrlStyle\" onClick=\"window.open('$rowBookmarkUrl', '_blank')\">$rowBookmarkUrl</div>");
                                print('</div>');
                            print('</div>');
                        }
                        print("</div>");
                    }
                print('</div>'); // This is the end of main_bookmark_page

                print("<div id=\"overlay\" class=\"overlay centerChildElements\" $promptOverlayDisplay>");
                    print('<div id="PromptModal" class="overlayContainer">');
                        print('<div id="closeIconContainer" class="closeIconContainer">');
                            print('<span id="closeIconButton" class="closeIconButton" title="Close">&times;</span>');
                        print('</div>');
                        print('<div id="mainPrompt" class="promptBox centerChildElements flex">');
                            print("<div class=\"feedbackMessageBox\" id=\"feedbackDiv\" $promptFeedbackStyle>$promptFeedbackMessage</div>");
                                print('<form method="post" action="BookmarkSite.php">');
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
                                print('<form method="post" action="BookmarkSite.php">');
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
                                print('<form method="post" action="BookmarkSite.php">');
                                    print("<div id=\"editBookmarkContent\" class=\"contentContainer\" $promptMessageEditStyle>");
                                        print("<div id=\"addBookmarkIntro\" class=\"introTitle\" $promptAddBookmarkButtonStyle>");
                                            print('Add Bookmark');
                                        print('</div>');
                                        print("<div id=\"updateBookmarkIntro\" class=\"introTitle\" $promptUpdateBookmarkButtonStyle>");
                                            print('Edit Bookmark');
                                        print('</div>');
                                        print("<input type=\"text\" class=\"hidden\" id=\"prevBookmarkName\" name=\"prevBookmarkName\" />");
                                        print("<input type=\"text\" class=\"hidden\" id=\"prevBookmarkUrl\" name=\"prevBookmarkUrl\" />");
                                        print('<div id="userInputsSignUp" class="userInputContainer">');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Name: </strong></label>');
                                                print('<input type="text" class="userInputBox" placeholder="Enter bookmark name..." id="edit_bookmark_name" name="edit_bookmark_name" required>');
                                            print('</div>');
                                            print('<div class="singleInputContainer">');
                                                print('<label><strong>Url: </strong></label>');
                                                print('<input type="text" class="userInputBox" placeholder="Enter bookmark url..." id="edit_bookmark_url" name="edit_bookmark_url" required>');
                                            print('</div>');
                                            print('<div class="userInputButtonContainer">');
                                                print('<button id="editBookmarkCancelButton" class="userInputButton" type="button" name="edit_bookmark_canceled">Cancel</button>');
                                                print("<button id=\"editDialogSaveButton\" class=\"BoatButton\" $promptAddBookmarkButtonStyle type=\"submit\" name=\"edit_bookmark_add\">Save</button>");
                                                print("<button id=\"editDialogUpdateButton\" class=\"BoatButton\" $promptUpdateBookmarkButtonStyle type=\"submit\" name=\"edit_bookmark_update\">Edit</button>");
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
