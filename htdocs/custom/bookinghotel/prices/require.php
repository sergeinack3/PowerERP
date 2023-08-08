<?php
if(false === (@include '../main.inc.php')) // From htdocs directory
  require '../../main.inc.php'; // From "custom" directory

if(!$conf->bookinghotel->enabled) accessforbidden();
?>
