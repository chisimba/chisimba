#!/usr/bin/perl
while (<>) {
    if (!/FILESGOHERE/) {
        print $_;
    } else {
        open FILELIST,'find Structures -type f | grep -v .arch-ids |';
        while (<FILELIST>) {
	    $md5sum = `md5sum $_`;
	    chomp($md5sum);
	    $md5sum = substr $md5sum, 0, 32;
#    $_ =~ s/\//\\\//g;
            chomp($_);
            print "    <file role=\"php\" md5sum=\"$md5sum\" name=\"$_\" />\n";
        }
    }
}
