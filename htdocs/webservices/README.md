API SOAP howto
==============

This directory contains files to make Powererp a server of SOAP Web Services.

WARNING: It is highly recommended to use the REST APIs instead of SOAP APIs: You will find more API, faster and easier to use in the the module REST API than into this module. 


Explore the api
---------------

* To see all Webservices provided by Powererp, just call the following Url:
http://mydomain.com/mypowererp/webservices/admin/index.php


Access to the API
-----------------

* WSDL file of a Web service provided by Powererp can be obtained at:
http://mydomain.com/mypowererp/webservices/server_xxx.php?wsdl

Note, you can test this Webservices by calling the page http://mydomain.com/mypowererp/webservices/demo_wsclient_xxx.php (You must first remove the -NORUN into file).
