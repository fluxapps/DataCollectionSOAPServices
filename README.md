# DataCollectionSOAPServices

## Installation
Start at your ILIAS root directory 

```
mkdir -p Customizing/global/plugins/Services/WebServices/SoapHook
cd Customizing/global/plugins/Services/WebServices/SoapHook
git clone https://github.com/studer-raimann/DataCollectionSOAPServices.git
```

As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.  

## Developer
Set the SOAP Cache to 0 in php.ini! e.g. 
/etc/php/7.0/apache2/conf.d/99-ilias.ini
soap.wsdl_cache_ttl = 0



## Contact
support@fluxlabs.ch
