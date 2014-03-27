#!/bin/sh

php artisan l4gettext:compile
php artisan l4gettext:extract

for lang in nb_NO en_US; do
    echo $lang
    mv app/locale/$lang/LC_MESSAGES/messages.po app/locale/$lang/LC_MESSAGES/messages.old.po
    if msgmerge app/locale/$lang/LC_MESSAGES/messages.old.po app/storage/l4gettext/messages.pot -o app/locale/$lang/LC_MESSAGES/messages.po; then
        rm app/locale/$lang/LC_MESSAGES/messages.old.po
    else
        echo
        echo "ERROR: msgmerge for $lang failed!";
        echo "Restoring old .po file"
        rm app/locale/$lang/LC_MESSAGES/messages.po
        mv app/locale/$lang/LC_MESSAGES/messages.old.po app/locale/$lang/LC_MESSAGES/messages.po
    fi
done

