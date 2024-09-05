#!/bin/bash
/usr/sbin/sshd
# /usr/sbin/httpd -DFOREGROUND

# Start and enable SSH, run drush, and start Apache
CMD ["sh", "-c", "service ssh start && sleep 10 && vendor/bin/drush updb -y && vendor/bin/drush cim -y && vendor/bin/drush cr && apache2ctl -D FOREGROUND"]
