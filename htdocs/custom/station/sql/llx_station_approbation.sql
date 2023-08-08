CREATE TABLE llx_station_approbation(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	lastname varchar(128) NOT NULL, 
	firstname varchar(128) NOT NULL, 
	user integer NOT NULL, 
	role_station integer NOT NULL, 
	date_creation datetime NOT NULL, 
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	status integer NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;