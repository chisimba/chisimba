Package: PEAR::Translation2
http://pear.php.net/Translation2
--------------------------------

PEAR::Translation2 offers a gettext driver along with the db-based ones.

Gettext is designed to offer the best performance when
used on its own, without wrappers like this one.

This driver resorts to the .mo parser provided by the
PEAR::File_Gettext class to offer the getPage() functionality.
This can be useful to get all the strings from a domain
and to pass them to a template engine, for instance.

Obviously, it may lead to worse gettext performance.
If you want to use gettext at the max speed, don't use this class ;),
or at least turn 'prefetch' off AND don't use the getPage() method.
Anyway, I haven't done any benchmark, so I can't say how worse it is.
The speed difference may be negligible, I really don't know.

End of the necessary preface :)


=============================
Usage example
=============================
<?php

$params = array(
    'prefetch' => false
);
$gettext_options = array(
    'langs_avail_file' => '/path/to/langs.ini',
    'domains_path_file' => '/path/to/domains.ini',
    'default_domain' => 'messages'
);

$tr =& Translation2::factory('gettext', $gettext_options, $params);
$tr->setLang('en');

echo $tr->get('mystring');

$tr->setPage('otherDomain');
echo $tr->get('aStringFromOtherDomain');

print_r($tr->getPage('thirdDomain'));

?>


=============================
langs.ini file format example
=============================
; lang code can be in "lang_DIALECT" or in "lang" format

[en_US]
name = English
encoding = iso-8859-1

[en_GB]
name = English
encoding = iso-8859-1

[de_DE]
name = Deutsch
encoding = iso-8859-1

[de_AT]
name = Deutsch
encoding = iso-8859-1

[it]
name = italiano
encoding = iso-8859-1


===============================
domains.ini file format example
===============================
; path the "locale" dir of each domain
messages = /usr/data/locale
errors = /usr/data/locale
myApp =  /usr/data/locale
myOtherApp = /usr/newData/locale

