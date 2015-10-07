FROM debian:wheezy
ENV DEBIAN_FRONTEND noninteractive
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# Get packages
RUN apt-get update
RUN apt-get install -y \
    sudo \
	vim \
	git \
	apache2 \
	php-apc \
	php5-fpm \
	php5-cli \
	php5-pgsql \
	php5-gd \
	php5-curl \
	libapache2-mod-php5 \
	curl \
	postgresql \
	postgresql-contrib \
	openssh-server \
	wget \
	supervisor
RUN apt-get clean

# Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# PHP
RUN sed -i 's/display_errors = Off/display_errors = On/' /etc/php5/apache2/php.ini
RUN sed -i 's/display_errors = Off/display_errors = On/' /etc/php5/cli/php.ini
RUN sed -i 's/local   all             postgres                                peer/local   all             postgres                                trust/' /etc/postgresql/9.1/main/pg_hba.conf

# Apache
# Listen port should be changed to forwarded port
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/default
RUN sed -i 's/\/var\/www/\/srv\/www\/ods\/public_html/' /etc/apache2/sites-available/default
RUN echo "Listen 8080" >> /etc/apache2/ports.conf
RUN sed -i 's/VirtualHost *:80/VirtualHost */' /etc/apache2/sites-available/default
RUN a2enmod rewrite

# SSH
RUN echo 'root:root' | chpasswd
RUN sed -i 's/PermitRootLogin without-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN mkdir /var/run/sshd && chmod 0755 /var/run/sshd
RUN mkdir -p /root/.ssh/ && touch /root/.ssh/authorized_keys
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

# Supervisor
RUN echo -e '[program:apache2]\ncommand=/bin/bash -c "source /etc/apache2/envvars && exec /usr/sbin/apache2 -DFOREGROUND"\nautorestart=true\n\n' >> /etc/supervisor/supervisord.conf
RUN echo -e '[program:postgresql]\ncommand=/usr/lib/postgresql/9.1/bin/postgres -D /var/lib/postgresql/9.1/main -c config_file=/etc/postgresql/9.1/main/postgresql.conf\nuser=postgres\nautorestart=true\n\n' >> /etc/supervisor/supervisord.conf
RUN echo -e '[program:sshd]\ncommand=/usr/sbin/sshd -D\n\n' >> /etc/supervisor/supervisord.conf

# Drupal and PostgreSQL
RUN mkdir -p /srv/www
RUN cd /srv/www && \
	git clone https://github.com/Cartografica/ods.git
RUN cp /srv/www/ods/public_html/sites/default/default.settings.php /srv/www/ods/public_html/sites/default/settings.php
RUN chmod a+w /srv/www/ods/public_html/sites/default -R && \
	chown -R www-data:www-data /srv/www/ods/public_html
RUN /etc/init.d/postgresql start && \
	cd /srv/www/ods && \
	createdb -U postgres -w pnud && \
	psql -U postgres -w pnud < db/pnud.sql && \
	psql -U postgres -w -c "alter user postgres with password 'postgres';"

EXPOSE 80 3306 22
CMD exec supervisord -n