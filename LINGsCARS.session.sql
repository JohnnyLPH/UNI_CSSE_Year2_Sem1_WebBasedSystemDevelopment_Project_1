-- @BLOCK
SHOW TABLES;

-- ____________________Admin Section: Start____________________
-- @BLOCK
DESCRIBE Admins;

-- @BLOCK
INSERT INTO Admins(adminName, adminPassword)
VALUES
('admin', 'g03abc')
;

-- @BLOCK
SELECT * FROM Admins;

-- @BLOCK
UPDATE Admins SET id = 1 WHERE adminName = 'admin';
-- ____________________Admin Section: End____________________
