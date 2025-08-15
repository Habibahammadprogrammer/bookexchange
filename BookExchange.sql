-- Create Database
CREATE DATABASE IF NOT EXISTS BookExchangeDB;
USE BookExchangeDB;

-- Users Table
CREATE TABLE Users (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(190) NOT NULL UNIQUE,
    Phone VARCHAR(30),
    PasswordHash VARCHAR(255) NOT NULL,
    RatingAvg DECIMAL(3,2) DEFAULT 0.00,
    CreatedAt DATETIME DEFAULT NOW()
);

-- Genres Table
CREATE TABLE Genres (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(80) NOT NULL UNIQUE
);

-- Books Table
CREATE TABLE Books (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    OwnerId BIGINT NOT NULL,
    Title VARCHAR(200) NOT NULL,
    Author VARCHAR(160),
    ISBN VARCHAR(32),
    GenreId INT,
    BookCondition VARCHAR(20) DEFAULT 'good',
    Description TEXT,
    CoverUrl VARCHAR(500),
    Availability VARCHAR(20) DEFAULT 'available',
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (OwnerId) REFERENCES Users(Id) ON DELETE CASCADE,
    FOREIGN KEY (GenreId) REFERENCES Genres(Id)
);

-- BookImages Table
CREATE TABLE BookImages (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    BookId BIGINT NOT NULL,
    Url VARCHAR(500) NOT NULL,
    FOREIGN KEY (BookId) REFERENCES Books(Id) ON DELETE CASCADE
);

-- ExchangeRequests Table
CREATE TABLE ExchangeRequests (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    BookId BIGINT NOT NULL,
    OwnerId BIGINT NOT NULL,
    RequesterId BIGINT NOT NULL,
    OfferedBookId BIGINT,
    Status VARCHAR(20) DEFAULT 'pending',
    Message TEXT,
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (BookId) REFERENCES Books(Id),
    FOREIGN KEY (OfferedBookId) REFERENCES Books(Id),
    FOREIGN KEY (OwnerId) REFERENCES Users(Id),
    FOREIGN KEY (RequesterId) REFERENCES Users(Id)
);

-- Threads Table
CREATE TABLE Threads (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    RequestId BIGINT NOT NULL UNIQUE,
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (RequestId) REFERENCES ExchangeRequests(Id) ON DELETE CASCADE
);

-- Messages Table
CREATE TABLE Messages (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ThreadId BIGINT NOT NULL,
    SenderId BIGINT NOT NULL,
    Body TEXT NOT NULL,
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (ThreadId) REFERENCES Threads(Id) ON DELETE CASCADE,
    FOREIGN KEY (SenderId) REFERENCES Users(Id)
);

-- Notifications Table
CREATE TABLE Notifications (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    UserId BIGINT NOT NULL,
    Type VARCHAR(60) NOT NULL,
    Payload TEXT,
    ReadAt DATETIME,
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (UserId) REFERENCES Users(Id) ON DELETE CASCADE
);

-- Reports Table
CREATE TABLE Reports (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ReporterId BIGINT NOT NULL,
    TargetType VARCHAR(20) NOT NULL,
    TargetId BIGINT NOT NULL,
    Reason TEXT,
    Status VARCHAR(20) DEFAULT 'open',
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (ReporterId) REFERENCES Users(Id)
);

-- Reviews Table
CREATE TABLE Reviews (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ExchangeId BIGINT NOT NULL,
    ReviewerId BIGINT NOT NULL,
    RevieweeId BIGINT NOT NULL,
    Rating TINYINT NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
    Comment TEXT,
    CreatedAt DATETIME DEFAULT NOW(),
    FOREIGN KEY (ExchangeId) REFERENCES ExchangeRequests(Id),
    FOREIGN KEY (ReviewerId) REFERENCES Users(Id),
    FOREIGN KEY (RevieweeId) REFERENCES Users(Id)
);

-- UserPrivacy Table
CREATE TABLE UserPrivacy (
    UserId BIGINT PRIMARY KEY,
    ShowEmail TINYINT(1) DEFAULT 0,
    ShowPhone TINYINT(1) DEFAULT 0,
    FOREIGN KEY (UserId) REFERENCES Users(Id) ON DELETE CASCADE
);
