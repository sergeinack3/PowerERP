-- Copyright (C) 2015 	   Charlie Benke       <charlie@patas-monkey.com>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
--

--
-- Ne pas placer de commentaire en fin de ligne, ce fichier est parsé lors
-- de l'install et tous les sigles '--' sont supprimés.
--

--
-- process element
--

insert into llx_process_element ( element, tablename, mainmenu, leftmenu, class) values ('propal', 'propale','commercial', 'propals', 'comm/propal/class/propal.class.php');
insert into llx_process_element ( element, tablename, mainmenu, leftmenu, class) values ('commande', 'commande','commercial', 'orders', 'commande/class/commande.class.php');
insert into llx_process_element ( element, tablename, mainmenu, leftmenu, class) values ('facture', 'facture','accountancy', 'customers_bills', 'compta/facture/class/facture.class.php');
insert into llx_process_element ( element, tablename, mainmenu, leftmenu, class) values ('commande_fourn', 'commande_fournisseur','commercial', 'suppliers_orders', 'fourn/class/fournisseur.commande.class.php');
insert into llx_process_element ( element, tablename, mainmenu, leftmenu, class) values ('facture_fourn', 'facture_fourn','accountancy', 'suppliers_bills', 'fourn/class/fournisseur.facture.class.php');
