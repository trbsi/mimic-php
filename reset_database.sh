#!/bin/bash  
read -p "DO YOU REALLY WANT TO RESET A DATABASE? " choice
case "$choice" in 
  * ) echo "BE CAREFUL WHAT YOU WISH FORM";
esac
read -p "LIKE REALLY REALLY? " choice
case "$choice" in 
  zelim ) php artisan migrate:reset; php artisan migrate; php artisan db:seed;;
  n|N ) echo "no";;
  * ) echo "invalid";;
esac
