<?php
if (!defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
if (!defined('NOCSRFCHECK'))     define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL', 1);
if (!defined('NOLOGIN'))         define('NOLOGIN', 1); // File must be accessed by logon page so without login
if (!defined('NOREQUIREHTML'))   define('NOREQUIREHTML', 1);
if (!defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX', '1');
define('ISLOADEDBYSTEELSHEET', '1');
session_cache_limiter('public');

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";

global $langs;
// Define css type
top_httphead('text/css');

$constecv = powererp_get_const($db, 'TESTECV', $conf->entity);
$constrecrutement = powererp_get_const($db, 'TESTRECRUTEMENT', $conf->entity);
if(!empty($constrecrutement)){
?>
#id-top #mainmenutd_recrutement{
    display: none !important;
}
<?php
}
if(!empty($constecv)){
?>
#id-top #mainmenutd_ecv{
    display: none !important;
}
<?php
}
?>
/*#id-top #mainmenutd_recrutement{
    display: none !important;
}
#id-top #mainmenutd_ecv{
    display: none !important;
}*/