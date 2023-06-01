FROM haproxy:2.4

#USER root
#
#RUN mkdir -p /var/lib/apt/lists && chown -R haproxy:haproxy /var/lib/apt/lists
#
#RUN apt-get update && \
#    apt-get install -y mariadb-client && \
#    rm -rf /var/lib/apt/lists/*
#
#USER haproxy

COPY ./development/haproxy/haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg
