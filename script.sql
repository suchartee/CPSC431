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
