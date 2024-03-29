<?php
/* Copyright (C) 2003-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2011 Regis Houssin        <regis@powererp.fr>
 * Copyright (C) 2017      Ferran Marcet        <fmarcet@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/pos/backend/numerotation/modules_tickets.php
 *	\ingroup    facture
 *	\brief      Fichier contenant la classe mere de generation des rickets en PDF
 * 				et la classe mere de numerotation des factures
 */

require_once(DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php');
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");   // Requis car utilise dans les classes qui heritent



/**
 *	\class      ModeleNumRefticketss
 *	\brief      Classe mere des modeles de numerotation des references de ticketss
 */
class ModeleNumRefticketss
{
    var $error = '';

    /**  Return if a module can be used or not
     * @return    boolean     true if module can be used
     */
    function isEnabled()
    {
        return true;
    }

    /**     Renvoi la description par defaut du modele de numerotation
     * @return    string      Texte descripif
     */
    function info()
    {
        global $langs;
        $langs->load("pos@pos");
        return $langs->trans("NoDescription");
    }

    /**  Renvoi un exemple de numerotation
     * @return    string      Example
     */
    function getExample()
    {
        global $langs;
        $langs->load("pos@pos");
        return $langs->trans("NoExample");
    }

    /**  Test si les numeros deja en vigueur dans la base ne provoquent pas
     *   de conflits qui empecheraient cette numerotation de fonctionner.
     * @return    boolean     false si conflit, true si ok
     */
    function canBeActivated()
    {
        return true;
    }

    /**  Renvoi prochaine valeur attribuee
     * @param     object societe        Objet societe
     * @param     object facture        Objet facture
     * @return    string      Valeur
     */
    function getNextValue($objsoc, $facture)
    {
        global $langs;
        return $langs->trans("NotAvailable");
    }

    /**  Renvoi version du modele de numerotation
     * @return    string      Valeur
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("VersionDevelopment");
        if ($this->version == 'experimental') return $langs->trans("VersionExperimental");
        if ($this->version == 'powererp') return DOL_VERSION;
        return $langs->trans("NotAvailable");
    }
}