-- @BLOCK
SHOW TABLES;

-- @BLOCK
DESCRIBE Admins;

-- @BLOCK
INSERT INTO Admins(adminName, 'password')
VALUES
('admin', 'g03abc')
;

-- @BLOCK
SELECT * FROM Admins;

-- @BLOCK
UPDATE Admins SET id = 1 WHERE adminName = 'admin';
