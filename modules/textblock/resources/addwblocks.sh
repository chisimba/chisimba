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

touch register.conf.txt

for i in `seq $IFROM $ITO`
do
    sed "s/1/$i/g" "$CLASSPATH/block_widetext1_class_inc.php" > "$CLASSPATH/block_widetext"$i"_class_inc.php"
    echo "BLOCK: widetext"$i >> "register.conf.txt"
done