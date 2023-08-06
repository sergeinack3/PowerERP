#---------------------------------------------------------
# Spec file to build a rpm file
#
# This is an example to build a rpm file. You can use this 
# file to build a package for your own distributions and 
# edit it if you need to match your rules.
# --------------------------------------------------------

Name: powererp
Version: __VERSION__
Release: __RELEASE__
Summary: ERP and CRM software for small and medium companies or foundations 
Summary(es): Software ERP y CRM para pequeñas y medianas empresas, asociaciones o autónomos
Summary(fr): Logiciel ERP & CRM de gestion de PME/PMI, auto-entrepreneurs ou associations
Summary(it): Programmo gestionale per piccole imprese, fondazioni e liberi professionisti

License: GPL-3.0+
#Packager: Laurent Destailleur (Eldy) <eldy@users.sourceforge.net>
Vendor: PowerERP dev team

URL: https://www.powererp.org
Source0: https://www.powererp.org/files/lastbuild/package_rpm_opensuse/%{name}-%{version}.tgz
Patch0: %{name}-forrpm.patch
BuildArch: noarch
BuildRoot: %{_tmppath}/%{name}-%{version}-build

Group: Productivity/Office/Management
Requires: apache2, apache2-mod_php, php >= 5.3.0, php-gd, php-ldap, php-imap, php-mysql, php-openssl, dejavu
Requires: mysql-community-server, mysql-community-server-client 
%if 0%{?suse_version}
BuildRequires: update-desktop-files fdupes
%endif

# Set yes to build test package, no for release (this disable need of /usr/bin/php not found by OpenSuse)
AutoReqProv: no


%description
An easy to use CRM & ERP open source/free software package for small  
and medium companies, foundations or freelances. It includes different 
features for Enterprise Resource Planning (ERP) and Customer Relationship 
Management (CRM) but also for different other activities.
PowerERP was designed to provide only features you need and be easy to 
use.

%description -l es
Un software ERP y CRM para pequeñas y medianas empresas, asociaciones
o autónomos. Incluye diferentes funcionalidades para la Planificación 
de Recursos Empresariales (ERP) y Gestión de la Relación con los
Clientes (CRM) así como para para otras diferentes actividades. 
PowerERP ha sido diseñado para suministrarle solamente las funcionalidades
que necesita y haciendo hincapié en su facilidad de uso.
    
%description -l fr
Logiciel ERP & CRM de gestion de PME/PMI, autoentrepreneurs, 
artisans ou associations. Il permet de gérer vos clients, prospect, 
fournisseurs, devis, factures, comptes bancaires, agenda, campagnes mailings
et bien d'autres choses dans une interface pensée pour la simplicité.

%description -l it
Un programmo gestionale per piccole e medie
imprese, fondazioni e liberi professionisti. Include varie funzionalità per
Enterprise Resource Planning e gestione dei clienti (CRM), ma anche ulteriori
attività. Progettato per poter fornire solo ciò di cui hai bisogno 
ed essere facile da usare.
Programmo web, progettato per poter fornire solo ciò di 
cui hai bisogno ed essere facile da usare.


#---- prepo
%prep
%setup -q
%patch0 -p0 -b .patch


#---- build
%build
# Nothing to build


#---- install
%install
%{__rm} -rf $RPM_BUILD_ROOT

%{__mkdir} -p $RPM_BUILD_ROOT%{_sysconfdir}/%{name}
%{__install} -m 644 build/rpm/conf.php $RPM_BUILD_ROOT%{_sysconfdir}/%{name}/conf.php
%{__install} -m 644 build/rpm/httpd-powererp.conf $RPM_BUILD_ROOT%{_sysconfdir}/%{name}/apache.conf
%{__install} -m 644 build/rpm/file_contexts.powererp $RPM_BUILD_ROOT%{_sysconfdir}/%{name}/file_contexts.powererp
%{__install} -m 644 build/rpm/install.forced.php.opensuse $RPM_BUILD_ROOT%{_sysconfdir}/%{name}/install.forced.php

%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/pixmaps
%{__install} -m 644 doc/images/appicon_64.png $RPM_BUILD_ROOT%{_datadir}/pixmaps/%{name}.png
%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/applications
#desktop-file-install --delete-original --dir=$RPM_BUILD_ROOT%{_datadir}/applications build/rpm/%{name}.desktop
%{__install} -m 644 build/rpm/powererp.desktop $RPM_BUILD_ROOT%{_datadir}/applications/%{name}.desktop

%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/%{name}/build/rpm
%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/%{name}/build/tgz
%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/%{name}/htdocs
%{__mkdir} -p $RPM_BUILD_ROOT%{_datadir}/%{name}/scripts
%{__cp} -pr build/rpm/*     $RPM_BUILD_ROOT%{_datadir}/%{name}/build/rpm
%{__cp} -pr build/tgz/*     $RPM_BUILD_ROOT%{_datadir}/%{name}/build/tgz
%{__cp} -pr htdocs  $RPM_BUILD_ROOT%{_datadir}/%{name}
%{__cp} -pr scripts $RPM_BUILD_ROOT%{_datadir}/%{name}
%{__rm} -rf $RPM_BUILD_ROOT%{_datadir}/%{name}/htdocs/includes/ckeditor/_source  
%{__rm} -rf $RPM_BUILD_ROOT%{_datadir}/%{name}/htdocs/includes/fonts

# Lang
echo "%defattr(0644, root, root, 0755)" > %{name}.lang
echo "%dir %{_datadir}/%{name}/htdocs/langs" >> %{name}.lang
for i in $RPM_BUILD_ROOT%{_datadir}/%{name}/htdocs/langs/*_*
do
  lang=$(basename $i)
  lang1=`expr substr $lang 1 2`; 
  lang2=`expr substr $lang 4 2 | tr "[:upper:]" "[:lower:]"`; 
  echo "%dir %{_datadir}/%{name}/htdocs/langs/${lang}" >> %{name}.lang
  if [ "$lang1" = "$lang2" ] ; then
    echo "%lang(${lang1}) %{_datadir}/%{name}/htdocs/langs/${lang}/*.lang"
  else
    echo "%lang(${lang}) %{_datadir}/%{name}/htdocs/langs/${lang}/*.lang"
  fi
done >>%{name}.lang

%if 0%{?suse_version}

# Enable this command to tag desktop file for suse
%suse_update_desktop_file powererp

# Enable this command to allow suse detection of duplicate files and create hardlinks instead
%fdupes $RPM_BUILD_ROOT%{_datadir}/%{name}/htdocs

%endif


#---- clean
%clean
%{__rm} -rf $RPM_BUILD_ROOT



#---- files
%files -f %{name}.lang

%defattr(0755, root, root, 0755)

%dir %_datadir/powererp

%dir %_datadir/powererp/scripts
%_datadir/powererp/scripts/*

%defattr(-, root, root, 0755)
%doc COPYING ChangeLog doc/index.html htdocs/langs/HOWTO-Translation.txt

%_datadir/pixmaps/powererp.png
%_datadir/applications/powererp.desktop

%dir %_datadir/powererp/build

%dir %_datadir/powererp/build/rpm
%_datadir/powererp/build/rpm/*

%dir %_datadir/powererp/build/tgz
%_datadir/powererp/build/tgz/*

%dir %_datadir/powererp/htdocs
%_datadir/powererp/htdocs/accountancy
%_datadir/powererp/htdocs/adherents
%_datadir/powererp/htdocs/admin
%_datadir/powererp/htdocs/api
%_datadir/powererp/htdocs/asset
%_datadir/powererp/htdocs/asterisk
%_datadir/powererp/htdocs/barcode
%_datadir/powererp/htdocs/blockedlog
%_datadir/powererp/htdocs/bookmarks
%_datadir/powererp/htdocs/bookcal
%_datadir/powererp/htdocs/bom
%_datadir/powererp/htdocs/categories
%_datadir/powererp/htdocs/collab
%_datadir/powererp/htdocs/comm
%_datadir/powererp/htdocs/commande
%_datadir/powererp/htdocs/compta
%_datadir/powererp/htdocs/conf
%_datadir/powererp/htdocs/contact
%_datadir/powererp/htdocs/contrat
%_datadir/powererp/htdocs/core
%_datadir/powererp/htdocs/cron
%_datadir/powererp/htdocs/custom
%_datadir/powererp/htdocs/datapolicy
%_datadir/powererp/htdocs/dav
%_datadir/powererp/htdocs/debugbar
%_datadir/powererp/htdocs/delivery
%_datadir/powererp/htdocs/don
%_datadir/powererp/htdocs/ecm
%_datadir/powererp/htdocs/emailcollector
%_datadir/powererp/htdocs/eventorganization
%_datadir/powererp/htdocs/expedition
%_datadir/powererp/htdocs/expensereport
%_datadir/powererp/htdocs/exports
%_datadir/powererp/htdocs/externalsite
%_datadir/powererp/htdocs/fichinter
%_datadir/powererp/htdocs/fourn
%_datadir/powererp/htdocs/ftp
%_datadir/powererp/htdocs/holiday
%_datadir/powererp/htdocs/hrm
%_datadir/powererp/htdocs/imports
%_datadir/powererp/htdocs/includes
%_datadir/powererp/htdocs/install
%_datadir/powererp/htdocs/intracommreport
%_datadir/powererp/htdocs/knowledgemanagement
%_datadir/powererp/htdocs/langs/HOWTO-Translation.txt
%_datadir/powererp/htdocs/loan
%_datadir/powererp/htdocs/mailmanspip
%_datadir/powererp/htdocs/margin
%_datadir/powererp/htdocs/modulebuilder
%_datadir/powererp/htdocs/mrp
%_datadir/powererp/htdocs/multicurrency
%_datadir/powererp/htdocs/opensurvey
%_datadir/powererp/htdocs/partnership
%_datadir/powererp/htdocs/paybox
%_datadir/powererp/htdocs/paypal
%_datadir/powererp/htdocs/printing
%_datadir/powererp/htdocs/product
%_datadir/powererp/htdocs/projet
%_datadir/powererp/htdocs/public
%_datadir/powererp/htdocs/recruitment
%_datadir/powererp/htdocs/reception
%_datadir/powererp/htdocs/resource
%_datadir/powererp/htdocs/salaries
%_datadir/powererp/htdocs/societe
%_datadir/powererp/htdocs/stripe
%_datadir/powererp/htdocs/supplier_proposal
%_datadir/powererp/htdocs/support
%_datadir/powererp/htdocs/theme
%_datadir/powererp/htdocs/takepos
%_datadir/powererp/htdocs/ticket
%_datadir/powererp/htdocs/user
%_datadir/powererp/htdocs/variants
%_datadir/powererp/htdocs/webhook
%_datadir/powererp/htdocs/webservices
%_datadir/powererp/htdocs/website
%_datadir/powererp/htdocs/workstation
%_datadir/powererp/htdocs/zapier
%_datadir/powererp/htdocs/*.ico
%_datadir/powererp/htdocs/*.patch
%_datadir/powererp/htdocs/*.php
%_datadir/powererp/htdocs/*.txt

%dir %{_sysconfdir}/powererp

%defattr(0664, root, www)
%config(noreplace) %{_sysconfdir}/powererp/conf.php
%config(noreplace) %{_sysconfdir}/powererp/apache.conf
%config(noreplace) %{_sysconfdir}/powererp/install.forced.php
%config(noreplace) %{_sysconfdir}/powererp/file_contexts.powererp



#---- post (after unzip during install)
%post

echo Run post script of packager powererp_opensuse.spec

# Define vars
export docdir="/var/lib/powererp/documents"
export apachelink="%{_sysconfdir}/apache2/conf.d/powererp.conf"
export apacheuser='wwwrun';
export apachegroup='www';

# Remove powererp install/upgrade lock file if it exists
%{__rm} -f $docdir/install.lock

# Create empty directory for uploaded files and generated documents 
echo Create document directory $docdir
%{__mkdir} -p $docdir

# Set correct owner on config files
%{__chown} -R root:$apachegroup /etc/powererp/*

# If a conf already exists and its content was already completed by installer
export config=%{_sysconfdir}/powererp/conf.php
if [ -s $config ] && grep -q "File generated by" $config
then 
  # File already exist. We add params not found.
  echo Add new params to overwrite path to use shared libraries/fonts
  grep -q -c "powererp_lib_FPDI_PATH" $config      || [ ! -d "/usr/share/php/fpdi" ]   || echo "<?php \$powererp_lib_FPDI_PATH='/usr/share/php/fpdi'; ?>" >> $config
  #grep -q -c "powererp_lib_GEOIP_PATH" $config    || echo "<?php \$powererp_lib_GEOIP_PATH=''; ?>" >> $config
  grep -q -c "powererp_lib_NUSOAP_PATH" $config    || [ ! -d "/usr/share/php/nusoap" ] || echo "<?php \$powererp_lib_NUSOAP_PATH='/usr/share/php/nusoap'; ?>" >> $config
  grep -q -c "powererp_lib_ODTPHP_PATHTOPCLZIP" $config || [ ! -d "/usr/share/php/libphp-pclzip" ]  || echo "<?php \$powererp_lib_ODTPHP_PATHTOPCLZIP='/usr/share/php/libphp-pclzip'; ?>" >> $config
  #grep -q -c "powererp_lib_TCPDF_PATH" $config    || echo "<?php \$powererp_lib_TCPDF_PATH=''; ?>" >> $config
  grep -q -c "powererp_js_CKEDITOR" $config        || [ ! -d "/usr/share/javascript/ckeditor" ]  || echo "<?php \$powererp_js_CKEDITOR='/javascript/ckeditor'; ?>" >> $config
  grep -q -c "powererp_js_JQUERY" $config          || [ ! -d "/usr/share/javascript/jquery" ]    || echo "<?php \$powererp_js_JQUERY='/javascript/jquery'; ?>" >> $config
  grep -q -c "powererp_js_JQUERY_UI" $config       || [ ! -d "/usr/share/javascript/jquery-ui" ] || echo "<?php \$powererp_js_JQUERY_UI='/javascript/jquery-ui'; ?>" >> $config
  grep -q -c "powererp_js_JQUERY_FLOT" $config     || [ ! -d "/usr/share/javascript/flot" ]      || echo "<?php \$powererp_js_JQUERY_FLOT='/javascript/flot'; ?>" >> $config
  grep -q -c "powererp_font_DOL_DEFAULT_TTF_BOLD" $config || echo "<?php \$powererp_font_DOL_DEFAULT_TTF_BOLD='/usr/share/fonts/truetype/DejaVuSans-Bold.ttf'; ?>" >> $config      
fi

# Create a config link powererp.conf
if [ ! -L $apachelink ]; then
  apachelinkdir=`dirname $apachelink`
  if [ -d $apachelinkdir ]; then
    echo Create powererp web server config link from %{_sysconfdir}/powererp/apache.conf to $apachelink
    ln -fs %{_sysconfdir}/powererp/apache.conf $apachelink
  else
    echo Do not create link $apachelink - web server conf dir $apachelinkdir not found. web server package may not be installed
  fi
fi

echo Set permission to $apacheuser:$apachegroup on /var/lib/powererp
%{__chown} -R $apacheuser:$apachegroup /var/lib/powererp
%{__chmod} -R o-w /var/lib/powererp

# Restart web server
echo Restart web server
if [ -f %{_sysconfdir}/init.d/httpd ]; then
  %{_sysconfdir}/init.d/httpd restart
fi
if [ -f %{_sysconfdir}/init.d/apache2 ]; then
  %{_sysconfdir}/init.d/apache2 restart
fi

# Restart mysql
echo Restart mysql
if [ -f /etc/init.d/mysqld ]; then
  /sbin/service mysqld restart
fi
if [ -f /etc/init.d/mysql ]; then
  /sbin/service mysql restart
fi

# Show result
echo
echo "----- PowerERP %version-%release - (c) PowerERP dev team -----"
echo "PowerERP files are now installed (into /usr/share/powererp)."
echo "To finish installation and use PowerERP, click on the menu" 
echo "entry PowerERP ERP-CRM or call the following page from your"
echo "web browser:"  
echo "http://localhost/powererp/"
echo "-------------------------------------------------------"
echo


#---- postun (after upgrade or uninstall)
%postun

if [ "x$1" = "x0" ] ;
then
  # Remove
  echo "Removed package"
  
  # Define vars
  export apachelink="%{_sysconfdir}/apache2/conf.d/powererp.conf"
  
  # Remove apache link
  if [ -L $apachelink ] ;
  then
    echo "Delete apache config link for PowerERP ($apachelink)"
    %{__rm} -f $apachelink
    status=purge
  fi
  
  # Restart web servers if required
  if [ "x$status" = "xpurge" ] ;
  then
    # Restart web server
    echo Restart web server
    if [ -f %{_sysconfdir}/init.d/httpd ]; then
      %{_sysconfdir}/init.d/httpd restart
    fi
    if [ -f %{_sysconfdir}/init.d/apache2 ]; then
      %{_sysconfdir}/init.d/apache2 restart
    fi
  fi
else
  # Upgrade
  echo "No remove action done (this is an upgrade)"
fi


# version x.y.z-0.1.a for alpha, x.y.z-0.2.b for beta, x.y.z-0.3 for release
%changelog
__CHANGELOGSTRING__
