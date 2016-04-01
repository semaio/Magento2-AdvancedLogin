# Magento2-AdvancedLogin

This module enhances the customer login process in Magento 2.

## Facts

Version: 2.0.0

## Features

### Customer number

This module adds a new customer attribute "customer_number" to Magento. This attribute can be used with 
the AdvancedLogin features.

The attribute will be shown in the backend customer grid by default.

### Enhanced login process

By default, Magento only allows login via email and password.

For some merchantes (especially B2B) it is important, that customers are also allowed (or are restricted)
to login by a customer attribtue (e.g. customer number). 

This module provices the functionality that you can choose the login mode in the store configuration and 
define the customer attribute (Default value: *customer_number*) which is used for the login.

Support login modes are:

* Login only via email
* Login only via customer attribute
* Login via customer attribute or email


## Support

If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/semaio/Magento2-AdvancedLogin/issues).

## Contribution

Any contribution to the development of MageSetup is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

## Licence

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

## Copyright

(c) 2016 Rouven Alexander Rieker
