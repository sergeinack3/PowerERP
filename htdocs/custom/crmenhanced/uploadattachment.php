<?php
/* Copyright (C) 2018 Marcello Gribaudo  <marcello.gribaudo@opigi.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    crmenhanced/uploadattachment
 * \ingroup crmenhanced
 * \brief   Upload a mail attachments
 *
 * Upload the given attachment
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into powererp root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';


clearstatcache();

$original_file = GETPOST("filename", "alpha");
$filename = basename($original_file);
$type=dol_mimetype($original_file);

// Output file on browser
$original_file_osencoded=dol_osencode($original_file);	// New file name encoded in OS encoding charset

// This test if file exists should be useless. We keep it to find bug more easily
if (! file_exists($original_file_osencoded)) {
    setEventMessages($langs->trans("ErrorFileDoesNotExists",$original_file), null);
    exit;
}

header('Content-Description: File Transfer');
if ($encoding)   
    header('Content-Encoding: '.$encoding);
if ($type)       
    header('Content-Type: '.$type.(preg_match('/text/',$type)?'; charset="'.$conf->file->character_set_client:''));
    header("Content-type: application/zip"); 
// Add MIME Content-Disposition from RFC 2183 (inline=automatically displayed, atachment=need user action to open)
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Length: ' . dol_filesize($original_file));
// Ajout directives pour resoudre bug IE
header('Cache-Control: Public, must-revalidate');
header('Pragma: public');


readfile($original_file_osencoded);
