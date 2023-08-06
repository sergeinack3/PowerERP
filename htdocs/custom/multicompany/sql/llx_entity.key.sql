-- ============================================================================
-- Copyright (C) 2020-2024 iPowerWorld
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
--
-- ============================================================================


ALTER TABLE llx_entity ADD INDEX idx_entity_fk_user_creat (fk_user_creat);

ALTER TABLE llx_entity ADD CONSTRAINT fk_entity_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user (rowid);