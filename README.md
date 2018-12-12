# win32service library

Library for ease to use the Win32Service PHP extension

## Install

This library requires the [Win32Service](https://pecl.php.net/package/win32service) extension for PHP.

```cmd
c:\> composer require win32service/service-library
```

## Use for register or unregister the service

See the example 'example/adminAction.php'. The code in this example needs the administrator right.

> Security : Do not execute this from the web request for security reasons.

## Use to manage any services

See the example 'example/managementActions.php'. The code in this example needs the administrator rights or a specific account with limited rights.

### With Administrator right

If you execute their actions with the administrator right, please limit the action to the local host. 

> Security : Do not execute this from the web request for security reasons.


### With a service account with limited rights

The recommended way for use remotely their actions is to make a new service account in your ActiveDirectory without any right.

Open the right of the account for each service needed to manage. To open the right, see the [Microsoft Documentation](https://support.microsoft.com/en-us/help/914392/best-practices-and-guidance-for-writers-of-service-discretionary-access-control-lists)

After the service is registered run this command to read the right set on the service 

```cmd
C:\> sc sdshow my_test_service
```

Replace the `my_test_service` by your service identifier to read the information.

> Note : Run this command in cmd administrator console.

The result example:

```
D:(A;;CCLCSWRPWPDTLOCRRC;;;SY)(A;;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;BA)(A;;CCLCSWLOCRRC;;;IU)(A;;CCLCSWLOCRRC;;;SU)S:(AU;FA;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;WD)
```

Get the SID for the service account, with this command:
```cmd
C:\> wmic useraccount where (name='<username>' and domain='<domain_short_name>') get name,sid
```

The response:

```
Name        SID
<username>  S-1-5-21-1553544295-1745644848-8500016-126000
```

Add the string into the right after changing the <SID>:

```
(A;;RPWPRCDT;;;<SID>)
```

The resulst will be gone:

```
(A;;RPWPRCDT;;;S-1-5-21-1553544295-1745644848-8500016-126000)
```

> Note : the `CCLCRPWPRCDT` represent the right to set for the SID. See the [Microsoft Documentation](https://support.microsoft.com/en-us/help/914392/best-practices-and-guidance-for-writers-of-service-discretionary-access-control-lists) to know the representation.

The final right is like this:

```
D:(A;;CCLCSWRPWPDTLOCRRC;;;SY)(A;;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;BA)(A;;CCLCSWLOCRRC;;;IU)(A;;CCLCSWLOCRRC;;;SU)(A;;RPWPRCDT;;;S-1-5-21-1553544295-1745644848-8500016-126000)S:(AU;FA;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;WD)
```

Now, set the new right for the service with the Administrator CMD window:

```cmd
C:\> sc scset my_test_service D:(A;;CCLCSWRPWPDTLOCRRC;;;SY)(A;;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;BA)(A;;CCLCSWLOCRRC;;;IU)(A;;CCLCSWLOCRRC;;;SU)(A;;RPWPRCDT;;;S-1-5-21-1553544295-1745644848-8500016-126000)S:(AU;FA;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;WD)
```

Replace the `my_test_service` by your service identifier to write the security descriptor.


## Run tests

> Prerequisites : [Composer](https://getcomposer.org) is installed on your computer.

After clone or download this repository open CMD window and go to the project directory.
Run this command for download needed dependencies:

```cmd
c:\> composer install -o
```

And run Atoum for tests:

```cmd
c:\> vendor/bin/atoum.bat
```

## Contributing

If you want to contribute, you can open an issue to propose your idea.
If you can write the code, fork this repository and write the code, finally submit your enhancement by a pull request.
