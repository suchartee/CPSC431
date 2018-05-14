DROP DATABASE IF EXISTS db_project;
CREATE DATABASE IF NOT EXISTS db_project;

DROP USER IF EXISTS 'observer';
DROP USER IF EXISTS 'operator';
DROP USER IF EXISTS 'manager';
DROP USER IF EXISTS 'admin';
DROP USER IF EXISTS 'accountauth';

USE db_project;

CREATE TABLE Question
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Question VARCHAR(255) NOT NULL
);

CREATE TABLE Role
(ID INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY,
RoleName VARCHAR(50) NOT NULL
);

INSERT INTO Role(RoleName) VALUES
('observer'),
('operator'),
('manager'),
('admin'),
('accountauth');

CREATE TABLE Account
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Username VARCHAR(100) NOT NULL,
Password VARCHAR(255) NOT NULL,
Email VARCHAR(255) NOT NULL,
RoleID INTEGER UNSIGNED NOT NULL,
QuestionNum INTEGER UNSIGNED NOT NULL,
Answer VARCHAR(255) NOT NULL,
LastLogin DATETIME,

CHECK (Email REGEXP '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'),

FOREIGN KEY (QuestionNum) REFERENCES Question(ID) ON DELETE CASCADE ON UPDATE CASCADE,

FOREIGN KEY (Roleid) REFERENCES Role(ID) ON DELETE CASCADE ON UPDATE CASCADE
);


INSERT INTO Question (Question) VALUES
('Where were you when you had your first kiss?'),
('Where were you New Year\'s 2000?'),
('What is the last name of the teacher who gave you your first failing grade?'),
('What was the last name of your third grade teacher?'),
('Where were you when you first heard about 9/11?');

INSERT INTO Account VALUES
('1', 'admin', '$2y$10$vy3JnZWwdBag0.SipB9GCu/gFTplBiNU0JTLyUIYtZ1X1THK55NVK', 'admin@admin.com', 4, '1', 'Answer', NULL);
-- Password for admin is admin

CREATE TABLE Team
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
TeamName VARCHAR(100) NOT NULL,
WinCount INTEGER UNSIGNED DEFAULT 0,
LostCount INTEGER UNSIGNED DEFAULT 0,
CoachID INTEGER UNSIGNED
);


CREATE TABLE Player
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Firstname VARCHAR(100),
Lastname VARCHAR(150) NOT NULL,
TeamID INTEGER UNSIGNED NOT NULL,

INDEX  (Lastname),
UNIQUE (Lastname, Firstname),

FOREIGN KEY (TeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Coach
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Firstname VARCHAR(100),
Lastname VARCHAR(150) NOT NULL,
TeamID INTEGER UNSIGNED NOT NULL,

INDEX  (Lastname),
UNIQUE (Lastname, Firstname),

FOREIGN KEY (TeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE CASCADE
);


ALTER TABLE Team
ADD FOREIGN KEY (CoachID) REFERENCES Coach(ID) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE Statistics
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
PlayerID INTEGER UNSIGNED NOT NULL,
PlayTimeMin TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
PlayTimeSec TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
Point TINYINT UNSIGNED NOT NULL DEFAULT 0,
Assist TINYINT UNSIGNED NOT NULL DEFAULT 0,
Rebound TINYINT UNSIGNED NOT NULL DEFAULT 0,

FOREIGN KEY (PlayerID) REFERENCES Player(ID) ON DELETE CASCADE ON UPDATE CASCADE,

CHECK((PlayTimeMin < 40 AND PlayTimeSec < 60) OR (PlayTimeMin = 40 AND PlayTimeSec = 0))
);


CREATE TABLE Matches
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
HomeTeamID INTEGER UNSIGNED NOT NULL,
AwayTeamID INTEGER UNSIGNED NOT NULL,
HomeScore INTEGER UNSIGNED NOT NULL,
AwayScore INTEGER UNSIGNED NOT NULL,
DatePlayed DATE NOT NULL,
WinTeamID INTEGER UNSIGNED NOT NULL,
LostTeamID INTEGER UNSIGNED NOT NULL,

FOREIGN KEY (HomeTeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (AwayTeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE 	CASCADE,
FOREIGN KEY (WinTeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (LostTeamID) REFERENCES Team(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE LoginAttempts
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Username VARCHAR(100) NOT NULL,
Attempt INTEGER NOT NULL,
LastAttempt DATETIME NOT NULL,
NextAttempt DATETIME
);


CREATE TABLE Buttons_observer
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Name VARCHAR(255) NOT NULL,
Link VARCHAR(255) NOT NULL
);

INSERT INTO Buttons_observer(Name, Link) VALUES
('Home', 'index.php'),
('About', 'about.php'),
('View Player', 'player.php'),
('View Team', 'team.php'),
('View Coach', 'coach.php'),
('View Match', 'match.php'),
('View Statistics', 'statistics.php'),
('View Profile', 'profile.php'),
('Change Password', 'changepassword.php'),
('Logout', 'log_out.php');


CREATE TABLE Buttons_operator
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Name VARCHAR(255) NOT NULL,
Link VARCHAR(255) NOT NULL
);

INSERT INTO Buttons_operator(Name, Link) VALUES
('Add New Player', 'addnewplayer.php'),
('Modify Player', 'modifyplayer.php'),
('Delete Player', 'deleteplayer.php'),
('Add New Team', 'addnewteam.php'),
('Modify Team', 'modifyteam.php'),
('Delete Team', 'deleteteam.php'),
('Add New Coach', 'addnewcoach.php'),
('Modify Coach', 'modifycoach.php'),
('Delete Coach', 'deletecoach.php'),
('Add New Match', 'addnewmatch.php'),
('Modify Match', 'modifymatch.php'),
('Delete Match', 'deletematch.php'),
('Add New Statistic', 'addnewstatistic.php'),
('Modify Statistic', 'modifystatistic.php'),
('Delete Statistic', 'deletestatistic.php');

CREATE TABLE Buttons_manager
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Name VARCHAR(255) NOT NULL,
Link VARCHAR(255) NOT NULL
);

INSERT INTO Buttons_manager(Name, Link) VALUES
('View User Account', 'privileges.php'),
('Add New User Account', 'addnewuser.php'),
('Modify User Account', 'modifyuseraccount.php');


CREATE TABLE Buttons_admin
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Name VARCHAR(255) NOT NULL,
Link VARCHAR(255) NOT NULL
);

INSERT INTO Buttons_admin(Name, Link) VALUES
('Delete User Account', 'deleteuseraccount.php');

CREATE TABLE Buttons_change
(
ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT Primary Key,
Name VARCHAR(255) NOT NULL,
Link VARCHAR(255) NOT NULL
);

INSERT INTO Buttons_change(Name, Link) VALUES
('Change Player Information', 'changeplayerinfo.php'),
('Change Coach Information', 'changecoachinfo.php'),
('Change Team Information', 'changeteaminfo.php'),
('Change Match Information', 'changematchinfo.php'),
('Change Statistic Information', 'changestatinfo.php'),
('Change User Account Information', 'changeuseraccountinfo.php'),
('Change User Password', 'changeuserpassword.php');

GRANT SELECT ON db_project.Question TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Player TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Team TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Statistics TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Coach TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Matches TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Role TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';
GRANT SELECT ON db_project.Buttons_observer TO 'observer'@'localhost' IDENTIFIED BY 'observerPassword';

GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Question TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Player TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Team TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Statistics TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Coach TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Matches TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT ON db_project.Buttons_observer TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT ON db_project.Buttons_operator TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';
GRANT SELECT ON db_project.Buttons_change TO 'operator'@'localhost' IDENTIFIED BY 'operatorPassword';

GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Question TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Player TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Team TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Statistics TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Coach TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_project.Matches TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT, INSERT, UPDATE ON db_project.Account TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT,INSERT,UPDATE ON db_project.LoginAttempts TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT,INSERT,UPDATE ON db_project.Role TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT ON db_project.Buttons_observer TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT ON db_project.Buttons_operator TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT ON db_project.Buttons_manager TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';
GRANT SELECT ON db_project.Buttons_change TO 'manager'@'localhost' IDENTIFIED BY 'managerPassword';

GRANT ALL PRIVILEGES ON db_project.* TO 'admin'@'localhost' IDENTIFIED BY 'adminPassword';

GRANT SELECT, INSERT, UPDATE ON db_project.Account TO 'accountauth'@'localhost' IDENTIFIED BY 'accountauthPassword';
GRANT SELECT, INSERT, UPDATE ON db_project.LoginAttempts TO 'accountauth'@'localhost' IDENTIFIED BY 'accountauthPassword';
GRANT SELECT ON db_project.Question TO 'accountauth'@'localhost' IDENTIFIED BY 'accountauthPassword';

INSERT INTO Team (TeamName, WinCount, lostCount) VALUES
('Lakers', 5, 2),
('Golden State Warrior', 4, 3),
('Titan CSUF Basketball', 10, 8),
('Bruins UCLA Basketball', 3, 2)
;

INSERT INTO Player (FirstName, LastName, TeamID) VALUES
('Travis', 'Wear', 1),
('Lonzo', 'Ball', 1),
('Brook', 'Lopez', 1),
('Alex', 'Caruso', 1),
('Nick', 'Young', 2),
('David', 'West', 2),
('Quinn', 'Cook', 2),
('Alice', 'Kiti', 2),
('Su', 'Htet', 3),
('Don', 'Vu', 3),
('Random', 'Person', 3),
('May', 'White', 3),
('Julie', 'Kim', 4),
('Nancy', 'Smith', 4),
('John', 'Doe', 4),
('Alex', 'Nguyen', 4)
;

INSERT INTO Coach (FirstName, LastName, TeamID) VALUES
('Luke', 'Walton', 1),
('Steve', 'Kerr', 2),
('Tom', 'Bettens', 3),
('Somename', 'Somefamily', 4)
;

UPDATE Team SET CoachID = 1 WHERE ID = 1;
UPDATE Team SET CoachID = 2 WHERE ID = 2;
UPDATE Team SET CoachID = 3 WHERE ID = 3;
UPDATE Team SET CoachID = 4 WHERE ID = 4;

INSERT INTO Matches (HomeTeamID, AwayTeamID, HomeScore, AwayScore, DatePlayed, WinTeamID, LostTeamID) VALUES
(1, 2, 35, 40, '2018-4-25', 2, 1),
(4, 3, 50, 20, '2018-4-20', 4, 3),
(2, 4, 20, 42, '2018-4-25', 4, 2),
(3, 1, 45, 35, '2018-4-20', 3, 1)
;

INSERT INTO Statistics (PlayerID, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound) VALUES
(1, 30, 0, 25, 10, 2),
(2, 22, 1, 10, 27, 10),
(3, 30, 23, 22, 33, 4),
(4, 20, 45, 33, 30, 2),
(4, 10, 56, 7, 2, 3)
;
