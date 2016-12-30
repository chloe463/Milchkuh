use phpunit;
DROP PROCEDURE IF EXISTS Milchkuh_Test_Procedure;
DELIMITER //
CREATE PROCEDURE Milchkuh_Test_Procedure()
BEGIN
    SELECT * FROM phpunit.milchkuh_test;
END;
//
DELIMITER ;
