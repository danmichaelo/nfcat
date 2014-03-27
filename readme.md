## Near Field Cat (NFCAT) 

Librarians love [near field communication](http://education.guardian.co.uk/librariesunleashed/story/0,,2293195,00.html) 
and [cats](https://en.wikipedia.org/wiki/Library_cat), 
so we made the near field cat!

This is supposed to be the server-side part of the project.

### Install ###

This project uses [Composer](http://getcomposer.org) and [Bower](https://github.com/bower/bower) to track dependencies.

1. `composer install` to fetch back-end deps
2. `bower install` to fetch front-end deps
3. Check that the folder `app/storage` is writable by the www user
4. `php artisan config:publish danmichaelo/ncip` to create the NCIP config file
5. `php artisan config:publish netson/l4gettext` to create the gettext config file
6. Update config files in `app/config`

