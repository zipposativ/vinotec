#!/bin/sh

mkdir -p /data/vinotec

rm -rf /usr/share/nginx/html
ln -s /data/vinotec /usr/share/nginx/html

nginx -g "daemon off;"
