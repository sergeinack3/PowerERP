#----------------------------------------------------------------------------
# \file         powererp.pl
# \brief        PowerERP script install for Virtualmin Pro
# \author       (c)2009-2020 Regis Houssin  <regis.houssin@inodbox.com>
#----------------------------------------------------------------------------


# script_powererp_desc()
sub script_powererp_desc
{
return "PowerERP";
}

sub script_powererp_uses
{
return ( "php" );
}

# script_powererp_longdesc()
sub script_powererp_longdesc
{
return "PowerERP ERP/CRM is a powerful Open Source software to manage a professional or foundation activity (small and medium enterprises, freelancers).";
}

sub script_powererp_author
{
return "Regis Houssin";
}

# script_powererp_versions()
sub script_powererp_versions
{
return ( "14.0.5", "13.0.5", "12.0.5", "11.0.5", "10.0.7", "9.0.4", "8.0.6", "7.0.5" );
}

sub script_powererp_release
{
return 2;	# for mysqli fix
}

sub script_powererp_category
{
return "Commerce";
}

sub script_powererp_php_vers
{
return ( 5 );
}

sub script_powererp_php_modules
{
local ($d, $ver, $phpver, $opts) = @_;
local ($dbtype, $dbname) = split(/_/, $opts->{'db'}, 2);
return $dbtype eq "mysql" ? ("mysql") : ("pgsql");
}

sub script_powererp_dbs
{
local ($d, $ver) = @_;
return ("mysql", "postgres");
}

# script_powererp_depends(&domain, version)
sub script_powererp_depends
{
local ($d, $ver, $sinfo, $phpver) = @_;
local @rv;

if ($ver >= 3.6) {
	# Check for PHP 5.3+
	local $phpv = &get_php_version($phpver || 5, $d);
	if (!$phpv) {
		push(@rv, "Could not work out exact PHP version");
		}
	elsif ($phpv < 5.3) {
		push(@rv, "PowerERP requires PHP version 5.3 or later");
		}
	}
if ($ver >= 12.0) {
	# Check for PHP 5.6+
	local $phpv = &get_php_version($phpver || 5, $d);
	if (!$phpv) {
		push(@rv, "Could not work out exact PHP version");
		}
	elsif ($phpv < 5.6) {
		push(@rv, "PowerERP requires PHP version 5.6 or later");
		}
	}

return @rv;
}

# script_powererp_params(&domain, version, &upgrade-info)
# Returns HTML for table rows for options for installing powererp
sub script_powererp_params
{
local ($d, $ver, $upgrade) = @_;
local $rv;
local $hdir = &public_html_dir($d, 1);
if ($upgrade) {
	# Options are fixed when upgrading
	local ($dbtype, $dbname) = split(/_/, $upgrade->{'opts'}->{'db'}, 2);
	$rv .= &ui_table_row("Database for PowerERP tables", $dbname);
	local $dir = $upgrade->{'opts'}->{'dir'};
	$dir =~ s/^$d->{'home'}\///;
	$rv .= &ui_table_row("Install directory", $dir);
	}
else {
	# Show editable install options
	local @dbs = &domain_databases($d, [ "mysql"]);
	$rv .= &ui_table_row("Database for PowerERP tables",
		     &ui_database_select("db", undef, \@dbs, $d, "powererp"));
	$rv .= &ui_table_row("Install sub-directory under <tt>$hdir</tt>",
			     &ui_opt_textbox("dir", &substitute_scriptname_template("powererp", $d), 30, "At top level"));
	if ($d->{'ssl'} && $ver >= 3.0) {
		$rv .= &ui_table_row("Force https connection?",
				     &ui_yesno_radio("forcehttps", 0));
		}
	}
return $rv;
}

# script_powererp_parse(&domain, version, &in, &upgrade-info)
# Returns either a hash ref of parsed options, or an error string
sub script_powererp_parse
{
local ($d, $ver, $in, $upgrade) = @_;
if ($upgrade) {
	# Options are always the same
	return $upgrade->{'opts'};
	}
else {
	local $hdir = &public_html_dir($d, 0);
	$in{'dir_def'} || $in{'dir'} =~ /\S/ && $in{'dir'} !~ /\.\./ ||
		return "Missing or invalid installation directory";
	local $dir = $in{'dir_def'} ? $hdir : "$hdir/$in{'dir'}";
	local ($newdb) = ($in->{'db'} =~ s/^\*//);
	return { 'db' => $in->{'db'},
		 'newdb' => $newdb,
		 'dir' => $dir,
		 'path' => $in->{'dir_def'} ? "/" : "/$in->{'dir'}",
		 'forcehttps' => $in->{'forcehttps'}, };
	}
}

# script_powererp_check(&domain, version, &opts, &upgrade-info)
# Returns an error message if a required option is missing or invalid
sub script_powererp_check
{
local ($d, $ver, $opts, $upgrade) = @_;
$opts->{'dir'} =~ /^\// || return "Missing or invalid install directory";
$opts->{'db'} || return "Missing database";
if (-r "$opts->{'dir'}/conf/conf.php") {
	return "PowerERP appears to be already installed in the selected directory";
	}
local ($dbtype, $dbname) = split(/_/, $opts->{'db'}, 2);
local $clash = &find_database_table($dbtype, $dbname, "llx_.*");
$clash && return "PowerERP appears to be already using the selected database (table $clash)";
return undef;
}

# script_powererp_files(&domain, version, &opts, &upgrade-info)
# Returns a list of files needed by powererp, each of which is a hash ref
# containing a name, filename and URL
sub script_powererp_files
{
local ($d, $ver, $opts, $upgrade) = @_;
local @files = ( { 'name' => "source",
	   'file' => "PowerERP_$ver.tar.gz",
	   'url' => "http://prdownloads.sourceforge.net/powererp/powererp-$ver.tgz" } );
return @files;
}

sub script_powererp_commands
{
return ("tar", "gunzip");
}

# script_powererp_install(&domain, version, &opts, &files, &upgrade-info)
# Actually installs powererp, and returns either 1 and an informational
# message, or 0 and an error
sub script_powererp_install
{
local ($d, $version, $opts, $files, $upgrade, $domuser, $dompass) = @_;

local ($out, $ex);
if ($opts->{'newdb'} && !$upgrade) {
        local $err = &create_script_database($d, $opts->{'db'});
        return (0, "Database creation failed : $err") if ($err);
        }
local ($dbtype, $dbname) = split(/_/, $opts->{'db'}, 2);
local $dbuser = $dbtype eq "mysql" ? &mysql_user($d) : &postgres_user($d);
local $dbpass = $dbtype eq "mysql" ? &mysql_pass($d) : &postgres_pass($d, 1);
local $dbphptype = $dbtype eq "mysql" && $version < 3.6 ? "mysql" :
		   $dbtype eq "mysql" ? "mysqli" : "pgsql";
local $dbhost = &get_database_host($dbtype, $d);
local $dberr = &check_script_db_connection($dbtype, $dbname, $dbuser, $dbpass);
return (0, "Database connection failed : $dberr") if ($dberr);

# Extract tar file to temp dir and copy to target
local $temp = &transname();
local $err = &extract_script_archive($files->{'source'}, $temp, $d,
			     $opts->{'dir'}, "powererp-$ver/htdocs");
$err && return (0, "Failed to extract source : $err");

# Add config file
local $cfiledir = "$opts->{'dir'}/conf/";
local $docdir = "$opts->{'dir'}/documents";
local $altdir = "$opts->{'dir'}/custom";
local $cfile = $cfiledir."conf.php";
local $oldcfile = &transname();
local $olddocdir = &transname();
local $oldaltdir = &transname();
local $url;

$tmpl = &get_template($d->{'template'});
$mycharset = $tmpl->{'mysql_charset'};
$mycollate = $tmpl->{'mysql_collate'};
$pgcharset = $tmpl->{'postgres_encoding'};
$charset = $dbtype eq "mysql" ? $mycharset : $pgcharset;
$collate = $dbtype eq "mysql" ? $mycollate : "C";

$path = &script_path_url($d, $opts);
if ($path =~ /^https:/ || $d->{'ssl'}) {
        $url = "https://$d->{'dom'}";
}
else {
        $url = "http://$d->{'dom'}";
}
if ($opts->{'path'} =~ /\w/) {
	$url .= $opts->{'path'};
}

if (!$upgrade) {
	local $cdef = "$opts->{'dir'}/conf/conf.php.example";
	&copy_source_dest_as_domain_user($d, $cdef, $cfile);
	&set_permissions_as_domain_user($d, 0777, $cfiledir);
	&copy_source_dest_as_domain_user($d, $cfile);
	&run_as_domain_user($d, "mkdir ".quotemeta($docdir));
	&set_permissions_as_domain_user($d, 0777, $docdir);
}
else {
	# Preserve old config file, documents and custom directory
	&copy_source_dest($cfile, $oldcfile);
	&copy_source_dest($docdir, $olddocdir);
	&copy_source_dest($altdir, $oldaltdir);
}

if ($upgrade) {
	# Put back original config file, documents and custom directory
	&copy_source_dest_as_domain_user($d, $oldcfile, $cfile);
	&copy_source_dest_as_domain_user($d, $olddocdir, $docdir);
	&copy_source_dest_as_domain_user($d, $oldaltdir, $altdir);
	
	# First page (Update database schema)
	local @params = ( [ "action", "upgrade" ],
			  [ "versionfrom", $upgrade->{'version'} ],
			  [ "versionto", $ver ],
	 		 );
	local $err = &call_powererp_wizard_page(\@params, "upgrade", $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Second page (Migrate some data)
	local @params = ( [ "action", "upgrade" ],
			  [ "versionfrom", $upgrade->{'version'} ],
			  [ "versionto", $ver ],
			 );
	local $err = &call_powererp_wizard_page(\@params, "upgrade2", $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Third page (Update version number)
	local @params = ( [ "action", "upgrade" ],
			  [ "versionfrom", $upgrade->{'version'} ],
			  [ "versionto", $ver ],
			  [ "installlock", "444" ],
			 );
	local $p = $ver >= 3.8 ? "step5" : "etape5";
	local $err = &call_powererp_wizard_page(\@params, $p, $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Remove the installation directory. (deprecated)
	# local $dinstall = "$opts->{'dir'}/install";
	# $dinstall  =~ s/\/$//;
	# $out = &run_as_domain_user($d, "rm -rf ".quotemeta($dinstall));
	
	}
else {
	# First page (Db connection and config file creation)
	local @params = ( [ "main_dir", $opts->{'dir'} ],
			  [ "main_data_dir", $opts->{'dir'}."/documents" ],
			  [ "main_url", $url ],
			  [ "db_type", $dbphptype ],
			  [ "db_host", $dbhost ],
			  [ "db_name", $dbname ],
			  [ "db_user", $dbuser ],
			  [ "db_pass", $dbpass ],
			  [ "action", "set" ],
			  [ "main_force_https", $opts->{'forcehttps'} ],
			  [ "powererp_main_db_character_set", $charset ],
			  [ "powererp_main_db_collation", $collate ],
			  [ "usealternaterootdir", "1" ],
			  [ "main_alt_dir_name", "custom" ],
			 );
	local $p = $ver >= 3.8 ? "step1" : "etape1";
	local $err = &call_powererp_wizard_page(\@params, $p, $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Second page (Populate database)
	local @params = ( [ "action", "set" ] );
	local $p = $ver >= 3.8 ? "step2" : "etape2";
	local $err = &call_powererp_wizard_page(\@params, $p, $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Third page (Add administrator account)
	local @params = ( [ "action", "set" ],
			  [ "login", "admin" ],
			  [ "pass", $dompass ],
			  [ "pass_verif", $dompass ],
			  [ "installlock", "444" ],
	 		 );
	local $p = $ver >= 3.8 ? "step5" : "etape5";
	local $err = &call_powererp_wizard_page(\@params, $p, $d, $opts);
	return (-1, "PowerERP wizard failed : $err") if ($err);
	
	# Remove the installation directory (deprecated)
	# local $dinstall = "$opts->{'dir'}/install";
	# $dinstall  =~ s/\/$//;
	# $out = &run_as_domain_user($d, "rm -rf ".quotemeta($dinstall));
	
	# Protect config file
	&set_permissions_as_domain_user($d, 0644, $cfile);
	&set_permissions_as_domain_user($d, 0755, $cfiledir);
	}
 
# Return a URL for the user
local $rp = $opts->{'dir'};
$rp =~ s/^$d->{'home'}\///;
local $adminurl = $url;
return (1, "PowerERP installation complete. Go to <a target=_new href='$url'>$url</a> to use it.", "Under $rp using $dbtype database $dbname", $url, 'admin', $dompass);
}

# call_powererp_wizard_page(&parameters, step-no, &domain, &opts)
sub call_powererp_wizard_page
{
local ($params, $page, $d, $opts) = @_;
local $params = join("&", map { $_->[0]."=".&urlize($_->[1]) } @$params );
local $ipage = $opts->{'path'}."/install/".$page.".php";
local ($iout, $ierror);
&post_http_connection($d, $ipage, $params, \$iout, \$ierror);
if ($ierror) {
	return $ierror;
	}
return undef;
}

# script_powererp_uninstall(&domain, version, &opts)
# Un-installs a powererp installation, by deleting the directory.
# Returns 1 on success and a message, or 0 on failure and an error
sub script_powererp_uninstall
{
local ($d, $version, $opts) = @_;

# Remove the contents of the target directory
local $derr = &delete_script_install_directory($d, $opts);
return (0, $derr) if ($derr);

# Remove all llx_ tables from the database
# 10 times because of constraints
for(my $i=0; $i<10; $i++) {
	&cleanup_script_database($d, $opts->{'db'}, "llx_");
	}

# Take out the DB
if ($opts->{'newdb'}) {
        &delete_script_database($d, $opts->{'db'});
        }

return (1, "PowerERP directory and tables deleted.");
}

# script_powererp_realversion(&domain, &opts)
# Returns the real version number of some script install, or undef if unknown
sub script_powererp_realversion
{
local ($d, $opts, $sinfo) = @_;
local $lref = &read_file_lines("$opts->{'dir'}/filefunc.inc.php", 1);
foreach my $l (@$lref) {
		if ($l =~ /'DOL_VERSION',\s?'([0-9a-z\.\-]+)'/) {
                return $1;
                }
        }
return undef;
}

# script_powererp_check_latest(version)
# Checks if some version is the latest for this project, and if not returns
# a newer one. Otherwise returns undef.
sub script_powererp_check_latest
{
local ($ver) = @_;
local @vers = &osdn_package_versions("powererp",
				$ver >= 14.0 ? "powererp\\-(12\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 13.0 ? "powererp\\-(12\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 12.0 ? "powererp\\-(12\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 11.0 ? "powererp\\-(11\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 10.0 ? "powererp\\-(10\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 9.0 ? "powererp\\-(9\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 8.0 ? "powererp\\-(8\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 7.0 ? "powererp\\-(7\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 6.0 ? "powererp\\-(6\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 5.0 ? "powererp\\-(5\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 4.0 ? "powererp\\-(4\\.0\\.[0-9\\.]+)\\.tgz" :
				$ver >= 3.9 ? "powererp\\-(3\\.9\\.[0-9\\.]+)\\.tgz" :
				$ver >= 3.8 ? "powererp\\-(3\\.8\\.[0-9\\.]+)\\.tgz" :
				$ver >= 3.7 ? "powererp\\-(3\\.7\\.[0-9\\.]+)\\.tgz" :
				$ver >= 3.6 ? "powererp\\-(3\\.6\\.[0-9\\.]+)\\.tgz" :
				$ver >= 3.5 ? "powererp\\-(3\\.5\\.[0-9\\.]+)\\.tgz" :
				$ver >= 2.9 ? "powererp\\-(2\\.9\\.[0-9\\.]+)\\.tgz" :
                              "powererp\\-(2\\.8\\.[0-9\\.]+)\\.tgz");
return "Failed to find versions" if (!@vers);
return $ver eq $vers[0] ? undef : $vers[0];
}

sub script_powererp_site
{
return 'https://www.powererp.org/';
}

sub script_powererp_passmode
{
return 2;
}

1;
