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