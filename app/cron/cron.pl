###############################################################################
# cron -- perl implementation of cron
###############################################################################
#
# by  Scott McMahan
#
###############################################################################

###############################################################################
# Usage: cron <crontab file>
#
# Where <crontab file> is a valid crontab file in the usual UNIX format.
# Defaults to file named "crontab" in current directory
###############################################################################
# There are some configuration options after the comments.
# They're probably okay as-is, but may be worth looking at.
###############################################################################

###############################################################################
# Author's Notes:
# Perl is obviously a prerequisite for using this program!
###############################################################################

###############################################################################
# If you're not familiar with crontab files, they have lines
# with the following:
#
# min hour monthday month weekday command
#
#  - Lines beginning with '#' are comments and are ignored.
#  - The numeric entries can be separated by commas -- eg 1,2,3
#  - Ranges for each are as follows:
#           minute (0-59),
#           hour (0-23),
#           day of the month (1-31),
#           month of the year (1-12),
#           day of the week (0-6 with 0=Sunday).
###############################################################################

###############################################################################
# configuration section
###############################################################################

# Note there are two levels of logging: normal logging, controlled by
# the logfile variable, logs all commands executed and when they're
# executed. The message facility prints messages about everything that
# the program does, and is sent to the screen unless the msgfile
# variable is set. Use the msgfile only in emergencies, as voluminous
# output is generated (so much so that leaving msgfile active all night
# could exhaust available disk space on all but the most capacious
# systems) -- its primary purpose is for emergency debugging.
# Due to the peculiar construction of the log functions, the log and
# msg files can be the same file. This may or may not be good.
#
# There is now a third level: setting $silent to 1, means that the screen
# just displays the same activity messages as the log file.

$silent = 1 ;
$logfile = "cronlog.txt";
$msgfile = ""; # assign this only in emergency

# end of configuration

###############################################################################
# in_csl searches for an element in a comma separated list
###############################################################################

sub in_csl {
    ($what, $csl) = @_;
    MSG("Processing CSL");
    @a = split(/,/, $csl);
    @b = ();
    map {
        if (/(\d+)-(\d+)/) {
            push @b,$1..$2;
        } else {
            push @b,$_;
        }
    } @a;
    for $x (@b) {
        MSG("is $what equal to item $x?");
        if ($what eq $x) {
            return 1;
        }
    }
    return 0;
}

###############################################################################
# main program
###############################################################################

if (defined $ARGV[0]) {
    ACT("using $ARGV[0] as crontab file\n");
    $crontab = $ARGV[0];
}
else {
    ACT("using default file crontab\n");
    $crontab = "crontab";
}

while (1) {

    open(F, "$crontab") or die "Can't open crontab; file $crontab: $!\n";
    $line = 0;

    # mon = 0..11 and wday = 0..6
    ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) =
        localtime(time);
    $mon++; # to get it to agree with the cron syntax
    $year %= 100;   # Y2K fix

    while (<F>) {
       $line++;

       if (/^$/) {
        MSG("blank line $line");
        next;
       }

       if (/^#/) {
        MSG("comment on line $line");
        next;
       }

       ($tmin, $thour, $tmday, $tmon, $twday, $tcommand) = split(/ +/, $_, 6);

       MSG("it is now $hour:$min:$sec on $mon/$mday/$year wday=$wday");
       MSG("should we do $thour:$tmin on $tmon/$tmday/--, wday=$twday?");

       $do_it = 0; # assume don't do it until proven otherwise

       # do it -- this month?
       if ( ($tmon eq "*") || ($mon == $tmon) || &in_csl($mon, $tmon)) {
           $do_it = 1;
           MSG("the month is valid");
       }
       else {
           $do_it = 0;
           MSG("cron: the month is invalid");
       }

       # do it -- this day of the month?
       if ( $do_it && ( ($tmday eq "*")
            || ($mday == $tmday) || &in_csl($mday, $tmday)) ) {
           $do_it = 1;
           MSG("the day of month is valid");
       }
       else {
           $do_it = 0;
           MSG("the day of month is invalid");
       }

       # do it -- this day of the week?
       if ( $do_it && ( ($twday eq "*")
            || ($wday == $twday) || &in_csl($wday, $twday)) ) {
           $do_it = 1;
           MSG("the day of week is valid");
       }
       else {
           $do_it = 0;
           MSG("the day of week is invalid");
       }

       # do it -- this hour?
       if ( $do_it && ( ($thour eq "*") ||
            ($hour == $thour)|| &in_csl($hour, $thour) ) ) {
           $do_it = 1;
           MSG("the hour is valid");
       }
       else {
           $do_it = 0;
           MSG("the hour is invalid");
       }

       # do it -- this minute?
       if ( $do_it && ( ($tmin eq "*") ||
            ($min == $tmin) || &in_csl($min, $tmin) ) ) {
           $do_it = 1;
           MSG("the min is valid");
       }
       else {
           $do_it = 0;
           MSG("the minute is invalid");
       }

       if ($do_it) {
           chop $tcommand;
           ACT("executing command <$tcommand>");
           system ("$tcommand");
#        system ("start $tcommand");
       }
    }

    close(F);
    MSG("***-----***");
    sleep(60);
}

exit;


###############################################################################
# Log activity
###############################################################################

sub ACT {
    # mon = 0..11 and wday = 0..6
    ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) =
    localtime(time);
    $mon++; # to get it to agree with the cron syntax
    $year %= 100;   # Y2K fix

    printf "cron*[%02d/%02d/%02d %02d:%02d]: @_\n", $mon,$mday,$year,$hour,$min;

    # since we're appending the log, always open it only as little
    # as necessary, so if we crash the info will be there
    open(LOGFILE, ">>$logfile") or return;
    printf LOGFILE "cron*[%02d/%02d/%02d %02d:%02d]: @_\n", $mon,$mday,$year,$hour,$min;
    close(LOGFILE);
}

###############################################################################
# routine to log messages
# logs to screen unless $silent is 1. If $msgfile is set to a filename, screen
# output also goes to that file
###############################################################################

sub MSG {
    return if $silent ;

    # mon = 0..11 and wday = 0..6
    ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) =
    localtime(time);
    $mon++; # to get it to agree with the cron syntax
    $year %= 100;   # Y2K fix

    printf "cron*[%02d/%02d/%02d %02d:%02d]: @_\n", $mon,$mday,$year,$hour,$min;

    return unless $msgfile;

    # since we're appending the log, always open it only as little
    # as necessary, so if we crash the info will be there
    open(LOGFILE, ">>$logfile") or return;
    printf LOGFILE "cron*[%02d/%02d/%02d %02d:%02d]: @_\n", $mon,$mday,$year,$hour,$min;
    close(LOGFILE);

}