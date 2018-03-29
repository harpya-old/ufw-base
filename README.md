# ufw-base




## Installation



  composer require harpya/ufw-base
 

## Usage


First of all, initialize the framework

 vendor/bin/ufw --init
 

Make sure that your webserver have the ~/public directory, and the mod_rewrite is working.


After this, create the application

 vendor/bin/ufw --create=myApp
 


Point your browser to address `http://YOUR_IP/info` to see phpinfo() page, and
`http://YOUR_IP/app/myApp/` to see the welcome message.


 


