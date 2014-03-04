#!/bin/bash
# This script uses the following syntax:
#    sh backup.sh USRFILES CONFIG USER_IMAGES BACKUPDIR

# The format for files is:
#   
#   tar -zcf usrfiles.tar.gz usrfiles
#   tar -zcf user_images.tar.gz user_images

echo "<h2>Backing up your Chisimba installation.</h2>"

#Get the path to usrfiles from $1 
USRFILES=$1
#Check if there is a supplied $1 input
if [ "$1" = "" ]; then
   echo "<span class='error'>You did not supply a path for usrfiles.</span>"
   exit 0
fi

#Get the path to config
#Check if there is a supplied $2 input
CONFIGPATH=$2
if [ "$2" = "" ]; then
   echo "<span class='error'>You did not supply a path to your config directory.</span>"
   exit 0
fi

#Get the path to user_images
#Check if there is a supplied $3 input
USERIMAGES=$3
if [ "$3" = "" ]; then
   echo "<span class='error'>You did not supply a path to your directory for user_images.</span>"
   exit 0
fi


#Get the path to write backup into from $4
#Check if there is a supplied $4 input
BACKUPPATH=$4
if [ "$4" = "" ]; then
   echo "<span class='error'>You did not supply a path to backup into.</span>"
   exit 0
fi

#Get the database user from $5
#Check if there is a supplied $5 input
DBUSER=$5
if [ "$5" = "" ]; then
   echo "<span class='error'>You did not supply a database username.</span>"
   exit 0
fi

#Get the database password from $6
#Check if there is a supplied $6 input
DBPASSWORD="-p$6"
if [ "$6" = "" ]; then
   echo "<span class='error'>You did not supply a database password.</span>"
   exit 0
fi

#Get the database name from $7
#Check if there is a supplied $7 input
DBNAME=$7
if [ "$7" = "" ]; then
   echo "<span class='error'>You did not supply a database name to access.</span>"
   exit 0
fi


echo "BACKING UP<br /><span class='redtxt'>$USRFILES</span>,<br ><span class='redtxt'>$CONFIGPATH</span><br />and <span class='redtxt'>$USERIMAGES</span><br />to <span class='bluetxt'>$BACKUPPATH</span>"

echo "<br />br />BACKING UP DATABASE<br /><span class='redtxt'>$DBNAME</span>"

tar -zcf config.tar.gz $CONFIGPATH
mv config.tar.gz $BACKUPPATH

tar -zcf usrfiles.tar.gz $USRFILES
mv usrfiles.tar.gz $BACKUPPATH

tar -zcf user_images.tar.gz $USERIMAGES
mv user_images.tar.gz $BACKUPPATH

echo "<br /><h2>Backup files in $BACKUPPATH:</h2>"
ls -l $BACKUPPATH

mkdir $BACKUPPATH/data
DATABACKUPPATH="$BACKUPPATH/data"

# Flush logs prior to the backup.
mysql -u $DBUSER $DBPASSWORD -e "use ${DBNAME}; FLUSH LOGS;"

# Zero the index
index=0

# Get the tables and their types and store this info in an array.
table_types=($(mysql -u $DBUSER $DBPASSWORD -e "show table status from $DBNAME" | awk '{ if ($2 == "MyISAM" || $2 == "InnoDB") print $1,$2}'))
table_type_count=${#table_types[@]}
echo "Found $table_type_count tables to backup<br />"

# Loop through the tables and apply the mysqldump option according to the 
# table type. The table specific SQL files will not contain any create 
# info for the table schema. It will be available in SCHEMA file
while [ "$index" -lt "$table_type_count" ]; do
    START=$(date +%s)
    TYPE=${table_types[$index + 1]}
    table=${table_types[$index]}
    if [ "$TYPE" = "MyISAM" ]; then
        DUMP_OPT="-u $DBUSER $DBPASSWORD $DBANME  --tables "
    else
        DUMP_OPT="-u $DBUSER $DBPASSWORD $DBNAME  --single-transaction --tables"
    fi
    mysqldump  $DUMP_OPT $table |gzip -c > $DATABACKUPPATH/$table.sql.gz
    index=$(($index + 2))
done
echo "Database export completed.\n"