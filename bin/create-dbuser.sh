#!/bin/bash
# Thanks google, this script came from:
# http://jetpackweb.com/blog/2009/07/20/bash-script-to-create-mysql-database-and-user/
#
BTICK='`'
EXPECTED_ARGS=3
E_BADARGS=65
MYSQL=`which mysql`

Q1="CREATE DATABASE IF NOT EXISTS ${BTICK}$1${BTICK};"
Q2="GRANT ALL ON ${BTICK}$1${BTICK}.* TO '$2'@'localhost' IDENTIFIED BY '$3';"
Q3="FLUSH PRIVILEGES;"
SQL="${Q1}${Q2}${Q3}"

if [ $# -ne $EXPECTED_ARGS ]
then
echo "Usage: $0 dbname dbuser dbpass"
exit $E_BADARGS
fi

$MYSQL -uroot -p -e "$SQL"

