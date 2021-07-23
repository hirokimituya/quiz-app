#!/bin/bash

set -eux

cd ~/quiz-maker
php artisan migrate --force