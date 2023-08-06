-- ============================================================================
-- Copyright (C) 2011 Juanjo Menent <jmenent@2byte.es>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- $Id: llx_pos_paiement_tickets.key.sql,v 1.1 2011-08-04 16:33:26 jmenent Exp $
-- ===========================================================================


-- Supprimme orhpelins pour permettre mont�e de la cl�
-- V4 DELETE llx_paiement_tickets FROM llx_paiement_tickets LEFT JOIN llx_tickets ON llx_paiement_tickets.fk_tickets = llx_tickets.rowid WHERE llx_tickets.rowid IS NULL;
-- V4 DELETE llx_paiement_tickets FROM llx_paiement_tickets LEFT JOIN llx_paiement ON llx_paiement_tickets.fk_tickets = llx_paiement.rowid WHERE llx_paiement.rowid IS NULL;

ALTER TABLE llx_paiement_tickets ADD INDEX idx_paiement_tickets_fk_tickets (fk_tickets);
ALTER TABLE llx_paiement_tickets ADD CONSTRAINT fk_paiement_tickets_fk_tickets FOREIGN KEY (fk_tickets) REFERENCES llx_pos_tickets (rowid);

ALTER TABLE llx_paiement_tickets ADD INDEX idx_paiement_tickets_fk_paiement (fk_paiement);
ALTER TABLE llx_paiement_tickets ADD CONSTRAINT fk_paiement_tickets_fk_paiement FOREIGN KEY (fk_paiement) REFERENCES llx_paiement (rowid);


ALTER TABLE llx_paiement_tickets ADD UNIQUE INDEX uk_paiement_tickets(fk_paiement, fk_tickets);

