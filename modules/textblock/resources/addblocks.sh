#!/bin/bash

CLASSPATH="../classes"
IFROM=$1
ITO=$2

if [ $# -ne 2 ] 
then
    echo "USAGE: $0 start_number end_number"
    echo "Following will add extra blocks starting at 21 up to 45 all inclusive"
    echo "Example: $0 21 45"
    exit
fi

#Preparing register file for BLOCK additions
cat "../register.conf" > "register.conf.bak"
cat "../register.conf" > "register.conf"

for i in `seq $IFROM $ITO`
do
    sed "s/1/$i/g" "$CLASSPATH/block_text1_class_inc.php" > "$CLASSPATH/block_text"$i"_class_inc.php"
    echo "BLOCK: text"$i >> "register.conf"
done

cp -frv register.conf ../register.conf
