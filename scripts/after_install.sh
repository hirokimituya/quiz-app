#!/bin/bash

set -eux

cd ~/quiz-maker
php artisan migrate --force
php artisan config:cache