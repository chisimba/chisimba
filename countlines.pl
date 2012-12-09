#!/usr/bin/perl -w

my @types = qw(php css xml xhtml html js sql inc);

foreach my $type (@types) {
    print "Lines in files of type: \t" . $type . ": ";
    my $command = "find . -name *.$type -type f -exec cat {} + | wc -l";
    print `$command`;
}
