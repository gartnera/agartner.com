#!/bin/bash

for file in $(find -type f); do
        sed -i 's|https://blog.agartner.com|https:/https://blog.agartner.com.agartner.com|g' $file;
        sed -i 's|https://profile.agartner.com|https:/https://profile.agartner.com.agartner.com|g' $file;
done;

sed -i 's|AuthUserFile.*|AuthUserFile /var/www/admin/.htpasswd|' /var/www/admin/.htaccess
