DROP DATABASE IF EXISTS BoatOnlineCourses;

CREATE DATABASE BoatOnlineCourses;

USE BoatOnlineCourses;

-- In the Users table, the userType is either Learner or SME. When a user sign's up they will get to decide. There will be no extra security (for now) so technically anyone can be SME
CREATE TABLE Users (
    username VARCHAR(250),
    PRIMARY KEY (username),
    fullname VARCHAR(500),
    password VARCHAR(250),
    userType VARCHAR(250)
);

CREATE TABLE Courses (
    courseid INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (courseid),
    courseName VARCHAR(500),
    courseShortDescription VARCHAR(1000),
    userCreator VARCHAR(250),
    FOREIGN KEY (userCreator)
        REFERENCES Users(username)
        ON DELETE CASCADE,
    createdDate DATE
);

-- This table is used for learners when they want to request to join a course
CREATE TABLE UserCourseAssignments (
    username VARCHAR(250),
    courseid INT,
    FOREIGN KEY (username)
        REFERENCES Users(username)
        ON DELETE CASCADE,
    FOREIGN KEY (courseid)
        REFERENCES Courses(courseid)
        ON DELETE CASCADE,
    PRIMARY KEY (username, courseid)
);

CREATE TABLE Units (
    unitNumber INT,
    courseid INT,
    FOREIGN KEY (courseid)
        REFERENCES Courses(courseid)
        ON DELETE CASCADE,
    unitTitle VARCHAR(1000),
    unitDescription VARCHAR(5000),
    PRIMARY KEY (unitNumber, courseid)
);

CREATE TABLE Lessons (
    lessonNumber INT,
    unitNumber INT,
    courseid INT,
    FOREIGN KEY (unitNumber, courseid)
        REFERENCES Units(unitNumber, courseid)
        ON DELETE CASCADE,
    lessonTitle VARCHAR(1000),
    PRIMARY KEY (lessonNumber, unitNumber, courseid)
);

CREATE TABLE LessonContents (
    lessonNumber INT,
    unitNumber INT,
    courseid INT,
    contentNumber INT,
    title VARCHAR(500),
    content TEXT,
    FOREIGN KEY (lessonNumber, unitNumber, courseid)
        REFERENCES Lessons(lessonNumber, unitNumber, courseid)
        ON DELETE CASCADE,
    PRIMARY KEY (lessonNumber, unitNumber, courseid, contentNumber)
);

-- For the quizzes table, the correct answer must be a number between 1 and 4 (e.g. 1, 2, 3, 4)
CREATE TABLE Quizzes (
    questionNumber INT,
    unitNumber INT,
    courseid INT,
    FOREIGN KEY (unitNumber, courseid)
        REFERENCES Units(unitNumber, courseid)
        ON DELETE CASCADE,
    questionDescription VARCHAR(1000),
    answer1 VARCHAR(1000),
    answer2 VARCHAR(1000),
    answer3 VARCHAR(1000),
    answer4 VARCHAR(1000),
    correctAnswer INT,
    PRIMARY KEY (questionNumber, unitNumber, courseid)
);

CREATE TABLE Images (
    imageNumber INT,
    lessonNumber INT,
    unitNumber INT,
    courseid INT,
    contentNumber INT,
    FOREIGN KEY (lessonNumber, unitNumber, courseid, contentNumber)
        REFERENCES LessonsContents(lessonNumber, unitNumber, courseid, contentNumber)
        ON DELETE CASCADE,
    imageUrl VARCHAR(1000),
    PRIMARY KEY (imageNumber, lessonNumber, unitNumber, courseid, contentNumber)
);