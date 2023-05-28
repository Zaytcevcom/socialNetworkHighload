# Настройка асинхронной репликации

## 0. Подготовительные работы
В файл my.cnf в секцию [mysqld] необходимо добавить следующие строки:
```
server_id=1
```

Где значение **server_id** уникально для каждого сервера.

## 1. Настройка master сервера
```
mysql -u root -p
```

Создание пользователя для репликации:
```
CREATE USER 'replication'@'%' IDENTIFIED BY '1234567890';
GRANT REPLICATION SLAVE ON *.* TO 'replication'@'%';
FLUSH PRIVILEGES;
ALTER USER 'replication'@'%' IDENTIFIED WITH mysql_native_password BY '1234567890';
SHOW MASTER STATUS;
```
Запомнить значения File и Position.

## 2. Настройка slave сервера (с импортом БД)
* Импортировать БД

```
mysql -u root -p
```

Изменения источника репликации:
```
CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-source', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_LOG_FILE='binlog.000003', SOURCE_LOG_POS=57897983;
START REPLICA;
SHOW REPLICA STATUS;
```
Где SOURCE_LOG_FILE и SOURCE_LOG_POS значения из последнего пункта настройки мастера

## 2*. Настройка slave сервера (без ручного создания и импорта БД)
```
mysql -u root -p
```

Изменения источника репликации:
```
CHANGE REPLICATION SOURCE TO SOURCE_HOST='hl-mysql-source', SOURCE_USER='replication', SOURCE_PASSWORD='1234567890', SOURCE_LOG_FILE='binlog.000001', SOURCE_LOG_POS=0;
START REPLICA;
SHOW REPLICA STATUS;
```

## Измерение нагрузки

CPU:
```
docker stats hl-mysql-source
```

Disc usage:
```
docker exec hl-mysql-source df -h
```

Memory usage:
```
docker stats --no-stream hl-mysql-source | grep hl-mysql-source
```
 
