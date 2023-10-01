# TRANSIT FOR [POWERERP](https://www.powererp.org)

## Features

Description of the module...

<!--
![Screenshot parcautomobile](img/screenshot_parcautomobile.png?raw=true "ParcAutomobile"){imgmd}
-->

Other external modules are available on [Dolistore.com](https://www.dolistore.com).

## Translations

Translations can be completed manually by editing files into directories _langs_.

<!--
This module contains also a sample configuration for Transifex, under the hidden directory [.tx](.tx), so it is possible to manage translation using this service.

For more informations, see the [translator's documentation](https://wiki.powererp.org/index.php/Translator_documentation).

There is a [Transifex project](https://transifex.com/projects/p/powererp-module-template) for this module.
-->

<!--

## Installation

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

- Clone the repository in ```$powererp_main_document_root_alt/parcautomobile```

```sh
cd ....../custom
git clone git@github.com:gitlogin/parcautomobile.git parcautomobile
```

### <a name="final_steps"></a>Final steps

From your browser:

  - Log into Powererp as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module

-->

## Licenses

### Main code

GPLv3 or (at your option) any later version. See file COPYING for more information.

### Documentation

All texts and readmes are licensed under GFDL.
