DROP PROCEDURE IF EXISTS `GET_TAGS`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `GET_TAGS`(IN `_limit` int)
BEGIN
    PREPARE stmt FROM "
			  SELECT t.*, COUNT(pt.`id`) `count`
			  FROM `tags` t
			  JOIN `post_tags` pt ON pt.`tag_id` = t.`id`
			  GROUP BY t.`id`
			  ORDER BY `count` DESC
			  LIMIT ?;";

    SET @rows = _limit;
    EXECUTE stmt USING @rows;
END
;;
DELIMITER ;