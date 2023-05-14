FROM mysql:latest

RUN mkdir -p /var/log/mysql && chown -R mysql:mysql /var/log/mysql

COPY ./development/mysql/async/source/my.cnf /etc/mysql/conf.d/my.cnf

ENV MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
ENV MYSQL_DATABASE=${MYSQL_DATABASE}

EXPOSE 3306
