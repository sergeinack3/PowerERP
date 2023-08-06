ALTER TABLE llx_pos_tickets ADD fk_place integer DEFAULT 0 AFTER fk_soc;
ALTER TABLE llx_pos_tickets CHANGE fk_place fk_place INT( 11 ) NULL;  
ALTER TABLE llx_pos_ticketsdet ADD note TEXT NULL; 
ALTER TABLE llx_pos_cash ADD barcode TINYINT NOT NULL DEFAULT '0' AFTER tactil; 
ALTER TABLE llx_pos_ticketsdet ADD localtax1_type INT NULL AFTER localtax1_tx;
ALTER TABLE llx_pos_ticketsdet ADD localtax2_type INT NULL AFTER localtax2_tx;
ALTER TABLE llx_pos_facture ADD fk_control_cash INT NULL;
ALTER TABLE llx_pos_facture ADD fk_place INT NULL;
ALTER TABLE llx_pos_facture ADD UNIQUE INDEX idx_facture_uk_facnumber (fk_facture);
ALTER TABLE llx_pos_cash ADD fk_modepaybank_extra INT NULL;
ALTER TABLE llx_pos_cash ADD fk_paybank_extra INT NULL;
ALTER TABLE llx_pos_control_cash ADD  ref varchar(30) NOT NULL;
ALTER TABLE llx_pos_facture ADD customer_pay double(24,8) DEFAULT 0;
ALTER TABLE llx_pos_tickets CHANGE fk_cash fk_cash integer NOT NULL;
ALTER TABLE llx_pos_cash ADD printer_name varchar(30) NULL;

-- UPDATE POWERERP 3.9-4.0 --
ALTER TABLE llx_pos_tickets ADD multicurrency_total_ht double(24,8) DEFAULT 0;
ALTER TABLE llx_pos_tickets ADD multicurrency_total_tva double(24,8) DEFAULT 0;
ALTER TABLE llx_pos_tickets ADD multicurrency_total_ttc double(24,8) DEFAULT 0;

ALTER TABLE llx_pos_ticketsdet ADD multicurrency_total_ht double(24,8) DEFAULT 0;
ALTER TABLE llx_pos_ticketsdet ADD multicurrency_total_tva double(24,8) DEFAULT 0;
ALTER TABLE llx_pos_ticketsdet ADD multicurrency_total_ttc double(24,8) DEFAULT 0;

-- REMOVE BAD CONSTRAINT
ALTER TABLE llx_pos_tickets DROP FOREIGN KEY fk_tickets_fk_tickets_source;

-- UPDATE BAD CONSTRAINT
ALTER TABLE llx_pos_ticketsdet DROP FOREIGN KEY fk_ticketsdet_fk_tickets;
ALTER TABLE llx_pos_ticketsdet ADD CONSTRAINT fk_ticketsdet_fk_tickets FOREIGN KEY (fk_tickets) REFERENCES llx_pos_tickets (rowid);


