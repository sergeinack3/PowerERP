

CREATE TABLE llx_payrollmod_session (

  rowid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  libelle varchar(255) NOT NULL,
  description text,
  model_paie varchar(255) NOT NULL,
  date_start date NOT NULL,
  date_end date NOT NULL,
  fk_user int(11) DEFAULT NULL,
  date_creation datetime DEFAULT CURRENT_TIMESTAMP  NOT NULL,
  filigrane varchar(255) DEFAULT NULL,
  status integer DEFAULT '0' NOT NULL

)ENGINE=innodb;