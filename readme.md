#reset all migrations and drop tables
php artisan migrate:reset

#migrate everything
php artisan migrate
php artisan db:seed

#iOS/Android icon generator
https://makeappicon.com/

#Favicon generator
http://www.favicon-generator.org/

#laravel jwt boilerplate
https://github.com/francescomalatesta/laravel-api-boilerplate-jwt

#GitHub Token
6aa47845287ddfa019e6aabed531e397f50e5689

#AWS
- Add this for AWS with your own information: 
AWS_KEY=AKIAJKLILUIBUPZUUVDQ 
AWS_SECRET=zk5ftgenehYiSLWsdYPiz+N66QYr1NPEJgs/7TAQ 
AWS_BUCKET=beyondi.test.perform365.bucket.com

- How to get KEY and SECRET: 
1) Go to https://console.aws.amazon.com/iam/home?region=us-west-2#/users and edit your user. Under Permissions tab click on Add Permission and add "AmazonS3FullAccess" permission. 
2) Under Security Credentials tab click on "Create access key" button and reate new KEY and SECRET

#Get file dimensions
https://stackoverflow.com/questions/4847752/how-to-get-video-duration-dimension-and-size-in-php/25741135

#htaccess allow access to one file
https://stackoverflow.com/questions/20978086/htaccess-allow-access-to-one-php-file

#HOW to set CronJob
https://laracasts.com/discuss/channels/laravel/invalid-argument-supplied-for-foreach-when-scheduling-task
* * * * * /usr/bin/php-cli /home/thettaco/public_html/MIMIC.TEST.COM/artisan schedule:run >> /home/thettaco/public_html/my.log 2>&1
the same is for A2Hosting
* * * * * /usr/bin/php-cli /home/undergr1/WWW.GOMIMIC.COM/artisan schedule:run >> /home/undergr1/public_html/my.log 2>&1

#LUCY Landing Page
https://themewagon.com/themes/lucy-best-free-responsive-bootstrap-app-landing-page-template/

#Install Imagick on windows
https://www.youtube.com/watch?v=fh0YJl1dUs8
Imagic for PHP7.2
https://github.com/mkoppanen/imagick/issues/224#issuecomment-367532736
Just use x86 
http://windows.php.net/downloads/pecl/snaps/imagick/3.4.3/ -> php_imagick-3.4.3-7.2-ts-vc15-x86.zip
http://windows.php.net/downloads/pecl/deps/ -> ImageMagick-7.0.7-11-vc15-x86.zip