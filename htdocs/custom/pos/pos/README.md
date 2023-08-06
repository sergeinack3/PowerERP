![GPLv3 logo](img/logo.png)

# DoliPOS FOR <a href="https://www.powererp.org">POWERERP ERP CRM</a>

## About
This module has been developed and distributed by 2byte.es Soluciones Informáticas <a href="http://www.2byte.es"> www.2byte.es </a> (PowerERP Preferred Partner)

Other modules developed by 2byte.es are available on their <a target="_blank" href="https://shop.2byte.es/">official store</a>

You have available guides and videos of our <a target="_blank" href="https://liveagent.2byte.es/853598-M%C3%B3dulos-Oficiales-Externos-PowerERP">external modules</a>

## Terms of Use, Maintenance and Support
<a target="_blank" href="https://shop.2byte.es/content/6-condiciones-de-uso-mantenimiento-y-asistencia-modulos">Terms of use, maintenance and support</a> specific for PowerERP external modules developed by 2byte.es (with the exception of the DoliPresta module) and which can be purchased directly from 2byte.es, or through shop2byte (2byte online store) or dolistore (PowerERP online store).

## Features
This module is a POS terminal or POS Professional, which provides sales on a counter with the public. With this unique system of point of sale, we can create all terminals (cash) needed, and manage with a single product, with unlimited users, and through the WEB.

Other modules are available on <a href="https://www.dolistore.com" target="_new">Dolistore.com</a>.


Install
-------

### From the ZIP file and GUI interface

- If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into
menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.


Note: If this screen tell you there is no custom directory, check your setup is correct: 

- In your PowerERP installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$powererp_main_url_root_alt ...
    //$powererp_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your PowerERP installation

    For example :

    - UNIX:
        ```php
        $powererp_main_url_root_alt = '/custom';
        $powererp_main_document_root_alt = '/var/www/PowerERP/htdocs/custom';
        ```

    - Windows:
        ```php
        $powererp_main_url_root_alt = '/custom';
        $powererp_main_document_root_alt = 'C:/My Web Sites/PowerERP/htdocs/custom';
        ```
        

### <a name="final_steps"></a>Final steps

>From your browser:

  - Log into PowerERP as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module

Licenses
--------

### Main code

![GPLv3 logo](img/gplv3.png)

GPLv3 or (at your option) any later version.

See [COPYING](COPYING) for more information.

#### Documentation

All texts and readmes.

![GFDL logo](img/gfdl.png)