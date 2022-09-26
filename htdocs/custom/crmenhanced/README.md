# CRMENHANCED FOR <a href="https://www.powererp.org">POWERERP ERP CRM</a>

## Features
It provides simple but complete functionality of CRM (Customer Relationship Management).
<Ul>
<li> Clients / suppliers can be grouped </ li>
<li> Campaign creation and management </ li>
<li> Massive sending of Mail and SMS </ li>
<li> Statistics, reports and analysis of delivery rates on campaigns </ li>
</ Ul>

<!--
![Screenshot crmenhanced](img/screenshot_crmenhanced.png?raw=true "crmenhanced"){imgmd}
-->

Altri moduli sono disponibili su <a href="https://www.dolistore.com" target="_new">Dolistore.com</a>.



### Translations

The translation exists in the following languages: English, French, Spanish, Italian.

Translation into other languages ​​can be done manually by editing the files in the [langs] (langs) folders.



<!--

Install
-------

### From the ZIP file and GUI interface

- If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into
menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.


Note: If this screen tell you there is no custom directory, check your setup is correct: 

- In your Powererp installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$powererp_main_url_root_alt ...
    //$powererp_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your Powererp installation

    For example :

    - UNIX:
        ```php
        $powererp_main_url_root_alt = '/custom';
        $powererp_main_document_root_alt = '/var/www/Powererp/htdocs/custom';
        ```

    - Windows:
        ```php
        $powererp_main_url_root_alt = '/custom';
        $powererp_main_document_root_alt = 'C:/My Web Sites/Powererp/htdocs/custom';
        ```
        
### From a GIT repository

- Clone the repository in ```$powererp_main_document_root_alt/crmenhanced```

```sh
cd ....../custom
git clone git@github.com:gitlogin/crmenhanced.git crmenhanced
```

### <a name="final_steps"></a>Final steps

From your browser:

  - Log into Powererp as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module



-->


Licenze
--------

### Main code

![GPLv3 logo](img/gplv3.png)

GPLv3 or (at your option) any later version.

See [COPYING](COPYING) for more information.

#### Documentation

All texts and readmes.

![GFDL logo](img/gfdl.png)
