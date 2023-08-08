ALTER TABLE llx_crm_event ADD CONSTRAINT `fk_crm_eventfk_crm_messages` FOREIGN KEY (`fk_message`) REFERENCES `llx_crm_messages` (`rowid`);
