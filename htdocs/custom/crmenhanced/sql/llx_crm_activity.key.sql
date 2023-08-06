ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_campaign` FOREIGN KEY (`fk_campaign`) REFERENCES `llx_crm_campaigns` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_contact` FOREIGN KEY (`fk_contact`) REFERENCES `llx_socpeople` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_mode` FOREIGN KEY (`fk_mode`) REFERENCES `llx_crm_activity_mode` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_new_owner` FOREIGN KEY (`fk_next_owner`) REFERENCES `llx_user` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_object` FOREIGN KEY (`fk_object`) REFERENCES `llx_crm_activity_object` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_owner` FOREIGN KEY (`fk_owner`) REFERENCES `llx_user` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_status` FOREIGN KEY (`fk_status`) REFERENCES `llx_crm_activity_status` (`rowid`);
ALTER TABLE llx_crm_activity  ADD CONSTRAINT `fk_activity_fk_thirdparty` FOREIGN KEY (`fk_thirdparty`) REFERENCES `llx_societe` (`rowid`);