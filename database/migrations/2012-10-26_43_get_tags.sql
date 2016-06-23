DROP PROCEDURE IF EXISTS `GET_TAGS`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `GET_TAGS`(IN `_limit` int)
BEGIN
    SELECT *
    FROM `tags`
    LIMIT _limit;
END
;;
DELIMITER ;