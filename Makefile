WWW_FILES = common.php curconfig.php details.php edit.php index.php \
	style.css

AUX_FILES = password.php

all:

install:
	for f in $(WWW_FILES); do ln -sf `pwd`/$$f /var/www/html/net/.; done
	for f in $(AUX_FILES); do ln -sf `pwd`/$$f /var/switch-interface/.; done
