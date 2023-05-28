# Настройка полусинхронной репликации

## 0. Подготовительные работы
В файл my.cnf в секцию [mysqld] необходимо добавить следующие строки:
```
server_id=1
log_bin=mysql-bin
relay_log=mysql-relay-bin
```

Где значения **server_id** и **relay_log** уникальны для каждого сервера.

## 1. Включение row-based репликации и GTID
Для включения row-based репликации в MySQL необходимо в файл my.cnf в секцию [mysqld] добавить следующую строку:
```
binlog_format=ROW
```

Для включения GTID в MySQL необходимо в файл my.cnf в секцию [mysqld] добавить следующие строки:<br>
```
gtid_mode=ON
enforce_gtid_consistency=ON
```

Указывается как для **master**, так и для **slave** серверов.

## 2. Настройка master сервера
```
mysql -u root -p
```

Установка плагинов для полусинхронной репликации:
```
INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so';
INSTALL PLUGIN rpl_semi_sync_replica SONAME 'semisync_replica.so';
SET GLOBAL rpl_semi_sync_source_enabled = 1;
```

Создание пользователя для репликации:
```
CREATE USER 'replication'@'%' IDENTIFIED BY '1234567890';
GRANT REPLICATION SLAVE ON *.* TO 'replication'@'%';
FLUSH PRIVILEGES;
ALTER USER 'replication'@'%' IDENTIFIED WITH mysql_native_password BY '1234567890';
SHOW MASTER STATUS;
```

## 3. Настройка slave сервера

```
mysql -u root -p
```

Установка плагинов для полусинхронной репликации:
```
INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so';
INSTALL PLUGIN rpl_semi_sync_replica SONAME 'semisync_replica.so';
SET GLOBAL rpl_semi_sync_replica_enabled = 1;
```

Изменения источника репликации:
```
CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-source', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_AUTO_POSITION=1;
START REPLICA;
SHOW REPLICA STATUS;
```

## 4. Промоутинг slave сервера до master (при аварийном выключении master)

```
mysql -u root -p
```

Выключение репликации и изменение настроек плагинов на самом свежем slave сервере:
```
STOP REPLICA;
SET GLOBAL rpl_semi_sync_replica_enabled = 0;
SET GLOBAL rpl_semi_sync_source_enabled = 1;
```

Изменения источника репликации на самый свежий slave на оставшихся slave серверах:
```
STOP REPLICA;
CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-replica-1', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_AUTO_POSITION=1;
START REPLICA;
SHOW REPLICA STATUS;
```

Где SOURCE_HOST - самый свежий slave сервер
