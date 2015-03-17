CREATE TABLE IF NOT EXISTS `case2013_todo_task` (
 `todo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `title` varchar(45) NOT NULL, `descript` tinytext NOT NULL,
 `date_due` int(11) NOT NULL,
 `is_done` enum('Y','N') NOT NULL DEFAULT 'N',
 PRIMARY KEY (`todo_id`),
 UNIQUE KEY `todo_id_UNIQUE` (`todo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='todolist'

