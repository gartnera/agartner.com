#!/bin/bash

for file in $(find $(find * -maxdepth 0 -type d) -type f); do
        sed -i 's|href="/blog"|href="https://blog.agartner.com/"|g' $file;
        sed -i 's|href="/profile"|href="https://profile.agartner.com/"|g' $file;
done;

sed -i 's|AuthUserFile.*|AuthUserFile /var/www/blog/admin/.htpasswd|' /var/www/blog/admin/.htaccess

mkdir -p blog/posts/json