It is the code of the former hitleshirado.com site. It is a PHP application based in Silex.

Feel free to take it and get it up and running again.

## What do you need to get this up and running? ##

- php
- apache
- memcached on localhost, default port
- php-gd

## How to configure apache? ##

```
<VirtualHost *:80>
    DocumentRoot /var/www/hiteleshirado.com_down/
    ServerName hiteleshirado.com
    ServerAlias www.hiteleshirado.com

    RewriteEngine On

    RewriteCond %{HTTP_HOST} ^www\. [NC]
    RewriteRule ^/(.*) http://hiteleshirado.com/$1 [R=301,L]

    RewriteCond %{DOCUMENT_ROOT}%{SCRIPT_FILENAME} !-f
    RewriteCond %{DOCUMENT_ROOT}%{SCRIPT_FILENAME} !-d
    RewriteRule . /index.php [L]

</VirtualHost>
```

## How do I import the existing database? ##

Take the repository of the images created (see @hiteleshirado github user).

This contains all the images and their related description in a TXT file. They are in time order, the first parrt of the file name is the timestamp. The 2nd part is a random character sequence.
We use this structure basically to avoid using database and keep simpliticy.

They should be organized to more subfolders though, because directory listing can get slow with large amount of files).

**All personal information (Facebook ID) has been cleared from teh TXT files)!**

You should take these fiels and put it to the ./images directory.

## What about the facebook app id? ##

You need to create your own app on Fcebook and replace every referencer to the original.

## How can I get more info? ##

Write to info@hiteleshirado.com

## Can I take the domain as well? ##

Yes, write to info@hiteleshirado.com.

## This is a really crappy code, isn't it? ##

Yes, it is. It countains some quick solutions, doesn't really scale, not tested and in general not something you would be prod of.
The point was to get it working.

Note the the vendor directory comtains code of external libraries. I did not have the time to put these to git submodules, so I jsut commit them as part of this repo.
