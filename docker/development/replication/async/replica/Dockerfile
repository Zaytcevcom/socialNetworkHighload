FROM mysql:latest

COPY ./development/replication/async/replica/my.cnf /etc/mysql/conf.d/my.cnf

ENV MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
ENV MYSQL_DATABASE=${MYSQL_DATABASE}

EXPOSE 3306
