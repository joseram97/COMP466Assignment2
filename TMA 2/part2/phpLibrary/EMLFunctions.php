<?php
    // require "queryFunctions.php";

    // These functions will help manage the addition of an EML file within the database

    //----------------IMAGE INFORMATION-------------------------
    // NOTE: The image format for storage is as follows
    //       e.g. 1_U1_L1_RenderMouseCursor.png
    //          <courseid>_U<unitnumber>_L<lessonnumber>_<imagename>.png <- or jpeg, whatever the user has set it to

    // NOTE: The images will be stored in the SME_Images folder.
    //----------------IMGAE INFO END---------------------------

    //------------------CONSTANTS------------------
    $siteAbsoluteUrl = "http://localhost:3000/TMA%202/part2";
    // NOTE: This is reserved for adding the absolute site for the azure host location
    // $siteAbsoluteUrl = "C:\Users\joser\OneDrive\Desktop\UofA PDF's\Semester 2019-2020\Winter Term 2020\Athabasca COMP 466\Assignments\Assignment 2\TMA 2\part2";

    function deleteServerImages($webDatabase, $courseId) {

        $imageResults = selectQueryBuilder($webDatabase, "Images", "imageUrl", "WHERE courseid = $courseId");
        while ($row = mysqli_fetch_assoc($imageResults)) {
            $imageFolderPath = './SME_Images/';
            $imageFilePath = $row["imageUrl"];
            
            unlink($imageFolderPath . $imageFilePath);
        }
    }

    function uploadCourseToDatabase($webDatabase, $username, $EML_File, $Images_Files = null) {

        $courseXML = simplexml_load_file($EML_File["tmp_name"]);

        // Verify that the course EML is correct
        if ($courseXML->units->unit->count() != $courseXML->quizzes->quiz->count()) {
            return array(false, "The number of units does not equal the number of quizzes set within the EML course.");
        }

        $courseName = mysqli_real_escape_string($webDatabase, $courseXML["name"]);
        $courseDescription = mysqli_real_escape_string($webDatabase, $courseXML->courseDescription);
        
        // Verify the course name doesn't already exist
        if (!($nameResult = selectQueryBuilder($webDatabase, "Courses", "*", "WHERE courseName = '$courseName'"))) 
        {
            return array(false, "Error executing selecting the course name from the database.");
        }

        if (mysqli_num_rows($nameResult) > 0) {
            return array(false, "A course with this name already exists within the database.");
        }

        // We are good to insert all of the contents of the EML within the database

        // Inserting basic course information
        if (!insertQueryBuilder($webDatabase, "Courses", "courseName, courseShortDescription, userCreator, createdDate", "'$courseName', '$courseDescription', '$username', curdate()")) {
            return array(false, "There was an error with inserting the course name, description, and other fields into the Courses table in the database.");
        }

        // Inserting course information (units and lessons)
        $courseIdResult = selectQueryBuilder($webDatabase, "Courses", "courseid", "WHERE courseName = '$courseName'");
        $row = mysqli_fetch_row($courseIdResult);
        $courseId = $row[0];

        foreach($courseXML->units->unit as $unit) {
            $unitTitle = mysqli_real_escape_string($webDatabase, $unit["title"]);
            $unitNum = $unit["number"];
            $unitDescription = mysqli_real_escape_string($webDatabase, $unit->unitDescription);
            
            if (!insertQueryBuilder($webDatabase, "Units", "unitNumber, courseid, unitTitle, unitDescription", "$unitNum, $courseId, '$unitTitle', '$unitDescription'")) {
                return array(false, "There was an error with inserting the unit information to the database.");
            }

            foreach($unit->lesson as $lesson) {
                $lessonTitle = mysqli_real_escape_string($webDatabase, $lesson["title"]);
                $lessonNum = $lesson["number"];
                
                if (!insertQueryBuilder($webDatabase, "Lessons", "lessonNumber, unitNumber, courseid, lessonTitle", "$lessonNum, $unitNum, $courseId, '$lessonTitle'")) {
                    return array(false, "Error inserting lesson information in the database");
                }

                foreach ($lesson->lessonContent as $lessonContent) {
                    $lessonContentContent = mysqli_real_escape_string($webDatabase, $lessonContent->content);
                    $lessonContentNum = $lessonContent["number"];

                    try {
                        $lessonContentTitle = mysqli_real_escape_string($webDatabase, $lessonContent["title"]);
                    }
                    catch (Exception $e) {
                        $lessonContentTitle = "";
                    }

                    if (!insertQueryBuilder($webDatabase, "LessonContents", "contentNumber, lessonNumber, unitNumber, courseid, title, content", "$lessonContentNum, $lessonNum, $unitNum, $courseId, '$lessonContentTitle', '$lessonContentContent'")) {
                        return array(false, "Error inserting the lesson contents into the database");
                    }

                    try {
                        foreach ($lessonContent->images->image as $image) {
                            $imageNum = $image["number"];
                            $imageFolderPath = './SME_Images/'; // NOTE: Ensure that this location is for the base home path
                            $imageFileName = $image["fileName"];
                            $imageFilePath = mysqli_real_escape_string($webDatabase, "C" . $courseId . "_U" . $unitNum . "_L" . $lessonNum . "_" . $imageFileName);

                            foreach ($Images_Files["error"] as $key => $error) {
                                if ($error == UPLOAD_ERROR_OK) {                                    
                                    // basename() may prevent filesystem traversal attacks;
                                    // further validation/sanitation of the filename may be appropriate
                                    $name = basename($Images_Files["name"][$key]);

                                    if ($name == $imageFileName) {
                                        $tmp_name = $Images_Files["tmp_name"][$key];
                                        move_uploaded_file($tmp_name, $imageFolderPath . $imageFilePath);
                                        break;
                                    }
                                }
                            }
                            
                            if (!insertQueryBuilder($webDatabase, "Images", "imageNumber, lessonNumber, unitNumber, courseid, contentNumber, imageUrl", "$imageNum, $lessonNum, $unitNum, $courseId, $lessonContentNum, '$imageFilePath'")) {
                                return array(false, "Error inserting the image information into the database");
                            }

                        }
                    }
                    catch (Exception $e) {
                        // Do nothing, since there are no images to add
                    }

                }
            }
        }
        foreach ($courseXML->quizzes->quiz as $quiz) {
            $quizUnitNum = $quiz["unit"];

            foreach ($quiz->question as $question) {
                $questionNum = $question["number"];
                $questionText = mysqli_real_escape_string($webDatabase, $question->questionText);
                $correctAnswer = $question->correctAnswer;
                $answer1 = mysqli_real_escape_string($webDatabase, $question->answer1);
                $answer2 = mysqli_real_escape_string($webDatabase, $question->answer2);
                try {
                    $answer3 = mysqli_real_escape_string($webDatabase, $question->answer3);
                }
                catch (Exception $e) {
                    $answer3 = "";
                }

                try {
                    $answer4 = mysqli_real_escape_string($webDatabase, $question->answer4);
                }
                catch (Exception $e) {
                    $answer4 = "";
                }

                if (!insertQueryBuilder($webDatabase, "Quizzes", "questionNumber, unitNumber, courseid, questionDescription, answer1, answer2, answer3, answer4, correctAnswer", "$questionNum, $quizUnitNum, $courseId, '$questionText', '$answer1', '$answer2', '$answer3', '$answer4', $correctAnswer")) {
                    return array(false, "Error inserting the quiz information into the database");
                }
            }
        }

        // Inserting quiz information


        // NOTE: The return if passed is just true with no message.
        // return array(false, "Error message");
        return array(true, "Completed sucessfully inserting the course info into the database");
    }
?>