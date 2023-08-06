
-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_presence` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`employe` int(11) NULL,
--   	`in_time` date NULL,
--   	`out_time` date NULL
-- );	


-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_award` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`employe` int(11) NULL,
--   	`label` varchar(255) NULL,
--   	`type` varchar(255) NULL,
--   	`amount` DECIMAL(10,2) NULL,
--   	`date` date NULL,
--   	`month` date NULL,
--   	`description` text NULL
-- );	


-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_promotion` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`employe` int(11) NULL,
--   	`in_time` date NULL,
--   	`out_time` date NULL
-- );	


-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_complain` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`label` varchar(255) NULL,
--   	`complainby` int(11) NULL,
--   	`against` date NULL,
--   	`date` date NULL,
--   	`description` text NULL
-- );	

-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_warning` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`label` varchar(255) NULL,
--   	`warningby` int(11) NULL,
--   	`against` date NULL,
--   	`date` date NULL,
--   	`description` text NULL
-- );	

-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_resignation` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`label` varchar(255) NULL,
--   	`employe` int(11) NULL,
--   	`warningby` int(11) NULL,
--   	`against` date NULL,
--   	`date` date NULL,
--   	`date_notice` date NULL,
--   	`reason` text NULL
-- );	

-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_termination` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`reason` varchar(355) NULL,
--   	`employe` int(11) NULL,
--   	`date` date NULL,
--   	`date_notice` date NULL,
--   	`description` text NULL
-- );	

-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_holiday` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`reason` varchar(355) NULL,
--   	`employe` int(11) NULL,
--   	`date` date NULL,
--   	`date_notice` date NULL,
--   	`description` text NULL
-- );	

-- CREATE TABLE IF NOT EXISTS `".MAIN_DB_PREFIX."hrm_request_holiday` (
--   	`rowid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   	`reason` varchar(355) NULL,
--   	`employe` int(11) NULL,
--   	`date_start` date NULL,
--   	`date_end` date NULL,
--   	`description` text NULL
-- );	

-- ENGINE=InnoDB DEFAULT CHARSET=latin1;