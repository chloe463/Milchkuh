DROP PROCEDURE IF EXISTS Milchkuh_Test_Procedure;
DELIMITER //
CREATE PROCEDURE Milchkuh_Test_Procedure()
BEGIN
    SELECT * FROM test.milchkuh_test;
END;
//
DELIMITER ;
