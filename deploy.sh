#!/bin/sh

# echo "Deploying"

# # Change Path to the project
# cd /var/www/html/

# # Pull From Bitbucket Repository
# git stash
# git pull origin master

# # Change permissions for all files
# sudo chmod -R 0777 /var/www/html

# # Install/update composer dependencies
# composer install

# # Force Migrate all new tables
# php artisan migrate --force

# # Clear log caches and config caches
# php artisan config:cache

# echo "Deployed !"