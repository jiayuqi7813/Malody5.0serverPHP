#!/bin/sh

cd /root/history
rm -rf /var/www/html/*
cp -r wwwroot/* /var/www/html
