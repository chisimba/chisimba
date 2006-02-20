<?php
/**
* Script to alter all tables in a database to support mdb2 
* 
* Author Paul Scott
* USAGE INSTRUCTIONS:
* Download the txt file that you see here, or copy and paste to a suitable web
* directory (i.e. /var/www/html/yourdir/) Rename the file to mirrorchanger.php and
* execute it through your browser. If you have a very large number of
* databases/Tables, you may want to execute this script on the command line by
* issuing the command:$ php -q utf8.php where the $ sign is the prompt, NOT TO
* BE TYPED!!
* When the script has completed, the Text : ""All done! Welcome to
* Mirroring"" will be displayed...
*/

       $host = 'localhost';
       $user = 'root';
       $password = '';
       $dbname = 'nextgen';

       // Put a 1 here to see any successful alterations.
       // A 0 here will default to showing errors only.
       $verbose = 1;
       mysql_connect("$host", "$user", "$password") or die ('Connection to database server not made!!\n');
       //$test = mysql_query("SHOW DATABASES") or die('No databases could be listed.\n');

       //For each database....
       //while($row=mysql_fetch_row($test))
       //{
               //Use the current database
               mysql_select_db($dbname);	//$row[0]);

               //Get all tables for current database
               $tables = mysql_query("SHOW TABLES LIKE '%%_seq'") or die('No tables could be found in database: $row[0]');

               //For each table.....
               while ($table = mysql_fetch_row($tables))
               {
                  //Alter the type to UTF-8 regardless of what type it was before.
                       $result = mysql_query("ALTER TABLE $table[0] ADD `sequence` VARCHAR( 255 ) NULL ;; "); //or die(mysql_error());

                       if($result)
                       {
                               if($verbose == 1)
                               {
                                       echo "$row[0]:$table[0] successfully altered\n";
                               }
                       }
                       else
                       {
                               echo "[ERROR] - $row[0]:$table[0] failed to alter\n";
                       }

               } //END for each table
       //} // END for each database
echo "All done! Welcome to Mirroring!";
?>
