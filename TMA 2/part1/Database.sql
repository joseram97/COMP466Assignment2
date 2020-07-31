DROP DATABASE IF EXISTS BookmarkWeb;

CREATE DATABASE BookmarkWeb;

USE BookmarkWeb;

CREATE TABLE Users (
    username VARCHAR(250),
    PRIMARY KEY (username),
    fullname VARCHAR(500),
    password VARCHAR(250)
);

CREATE TABLE Bookmarks (
    username VARCHAR(250),
    FOREIGN KEY(username)
        REFERENCES Users(username)
        ON DELETE CASCADE,
    bookmarkName VARCHAR(500),
    bookmarkURL VARCHAR(500),
    PRIMARY KEY (username, bookmarkName)
);