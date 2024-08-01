# Laravel Chat System Project
<<<<<<< HEAD
1.composer install  
2..env:  
->copy .env example  
->make your own database and complete the .env  
->generate key: php artisan key:generate  
=======
## test
>>>>>>> 38eb40e63894b2322e414a0ad9ba8277a6d12092
### How to add to branch in there are conflicts
git merge
error: Merging is not possible because you have unmerged files.
  
git add .
git commit -m "Your modifications Message"  
git merge  
Daca zice : Already up to date. E Bn  
git push


git fetch origin
git branch -a
git branch -b feature1_auth origin/feature1_auth
git pull

php artisan install:api --passport
php artisan passport:client --personal

git branch -a
git fetch
git switch feature2_middleware


/***PENTRU SWAGGER***/

//composer.json
"require": {
"php": "^8.2",
"darkaonline/l5-swagger": "^8.6",
"laravel/framework": "^11.9",
"laravel/passport": "^12.0",
"laravel/tinker": "^2.9",
"tymon/jwt-auth": "^2.1"
}

composer update --with-all-dependencies
composer require tymon/jwt-auth --with-all-dependencies
composer require darkaonline/l5-swagger tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
///jwt-auth secret [wM4u7q4n3fEbyjLKT1loHtTzsXD8bu1Eo3V3O3mQDFuQWqR9lZWAU9DA9aACO1fU] set successfully.
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

https://www.bacancytechnology.com/blog/laravel-swagger-integration
http://your-app-url/api/documentation
