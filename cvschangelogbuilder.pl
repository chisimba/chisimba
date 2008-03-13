#!/usr/bin/perl
#-------------------------------------------------------
# Build a changelog file for a CVS project
# Serveral output format are available
#-------------------------------------------------------
# $Revision$ - $Author$ - $Date$
use strict; no strict "refs";
use Time::Local;
#use diagnostics;


#-------------------------------------------------------
# Defines
#-------------------------------------------------------
my $REVISION='$Revision$'; $REVISION =~ /\s(.*)\s/; $REVISION=$1;
my $VERSION="2.1 (build $REVISION)";

# ---------- Init variables --------
use vars qw/ $TagStart $Branch $TagEnd /;
my $Debug=0;
my $DIR;
my $PROG;
my $Extension;
my $Help='';
my $TagStart='';
my $TagEnd='';
my $Module='';
my $ModuleForCache='';
my $Output='';		# Default will be "listdeltabydate"
my $OutputDir='';
my $CvsRoot='';		# Example ":ntserver:127.0.0.1:d:/temp/cvs"
my $UseSsh=0;
my $RLogFile;
my $KeepRlogFile=0;
my $RepositoryPath;
my $nowtime = my $nowweekofmonth = my $nowdaymod = my $nowsmallyear = 0; 
my $nowsec = my $nowmin = my $nowhour = my $nowday = my $nowmonth = my $nowyear = my $nowwday = 0;
my $filename='';
my $fullfilename='';
my %filesym=();
my $fileformat='';
my $filerevision='';
my $filedate='';
my $fileuser='';
my $filestate='';
my $filechange='';
my $filelineadd=0;
my $filelinedel=0;
my $filelinechange=0;
my $filelog='';
my $oldfiledayuser='';
my $oldfilelog='';
my $EXTRACTFILENAME="^RCS file: (.+)";
my $EXTRACTSYMBOLICNAMEAREA="symbolic names:";
my $EXTRACTSYMBOLICNAMEENTRY="^\\s(.+): ([\\d\\.]+)";
my $EXTRACTFILEVERSION="^revision (.+)";
my $EXTRACTFILEDATEUSERSTATE="date: (.+)\\sauthor: (.*)\\sstate: ([^\\s]+)(.*)";
my $CVSCLIENT="cvs -f";
my $COMP="";                # Do no use compression because it seems to return bugged rlog files for some servers/clients.
my $ViewCvsUrl="";
my $ENABLEREQUESTFORADD=1;  # Allow cvs request to get number of lines for added/removed files.
my %IgnoreFileDir=();
my %OnlyFileDir=();
my %colorstate=('added'=>'#008822','changed'=>'#888888','removed'=>'#880000');
# ---------- Init Regex --------
use vars qw/ $regclean1 $regclean2 /;
$regclean1=qr/<(recnb|\/td)>/i;
$regclean2=qr/<\/?[^<>]+>/i;
# ---------- Init hash arrays --------
# For all
my %maxincludedver=();
my %minexcludedver=();
my %tagsfulldate=();
my %tagsshortdate=();
my %tagstags=();
my %Cache=();
# For output by date
my %DateUser=();
my %DateUserLog=();
my %DateUserLogFileRevState=();
my %DateUserLogFileRevLine=();
my %HourUser=();
# For output by file
my %FilesLastVersion=();
my %FilesChangeDate=();
my %FilesChangeUser=();
my %FilesChangeState=();
my %FilesChangeLog=();
# For output by log
my $LGMAXLOG=400;
my %LogChange=();
my %LogChangeDate=();
my %LogChangeUser=();
my %LogChangeState=();
# For output by user
my %UserChangeCommit=();
my %UserChangeLast=();
my %UserChangeLineAdd=();
my %UserChangeLineDel=();
my %UserChangeLineChange=();
# For html report output
my $MAXLASTLOG=200;


#------------------------------------------------------------------------------
# Functions
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# Function:		mkdir_recursive
# Return:		None
#------------------------------------------------------------------------------
sub mkdir_recursive() {
    my $mdir = shift;
    my $array = shift;

    if ($mdir && -d $mdir) { return 1; }

    if ($mdir =~ m/^(.+)[\/\\]+([^\/\\]*)$/) {
        my ($parent, $dir)=($1, $2);

        unless ($parent && -d $parent) {
            &mkdir_recursive($parent,$array);
        }
    
        if ($parent && $dir && -d $parent) {
            debug(" Create dir '$parent/$dir'",2);
            if (mkdir "$parent/$dir") {
                push @{$array}, "$parent/$dir";
                #print STDERR "$parent/$dir\n";
                return 1;
            } else {
                error("Cannot mkdir '$parent/$dir', $!\n");
                return 0;
            }
        } else {
            return 0;
        }
    }
    else {
        debug(" Create dir '$mdir'",2);
        if (mkdir "$mdir") {
            push @{$array}, "$mdir";
            #print STDERR "$parent/$dir\n";
            return 1;
        } else {
            error("Cannot mkdir '$mdir', $!\n");
            return 0;
        }
    }
}


#------------------------------------------------------------------------------
# Function:		Write a warning message
# Parameters:	$message
# Input:		$HeaderHTTPSent $HeaderHTMLSent $WarningMessage %HTMLOutput
# Output:		None
# Return:		None
#------------------------------------------------------------------------------
sub warning {
	print STDERR "Warning: $_[0]\n";
}

#-------------------------------------------------------
# Error
#-------------------------------------------------------
sub error {
	print STDERR "Error: $_[0]\n";
    exit 1;
}

#-------------------------------------------------------
# Debug
#-------------------------------------------------------
sub debug {
	my $level = $_[1] || 1;
	if ($Debug >= $level) { 
		my $debugstring = $_[0];
		if ($ENV{"GATEWAY_INTERFACE"}) { $debugstring =~ s/^ /&nbsp&nbsp /; $debugstring .= "<br>"; }
		print STDERR "DEBUG $level - ".time." : $debugstring\n";
		}
	0;
}

#-------------------------------------------------------
# Write
#-------------------------------------------------------
sub writeoutput {
    my $string=shift;
    my $screenonly=shift;
    print STDERR $string;
	0;
}
sub writeoutputfile {
    my $string=shift;
    if ($OutputDir) {
        print FILE $string;   
    } else {
        print $string;
    }
	0;
}


#-------------------------------------------------------
# LoadDataInMemory
# Load cache entries in Hash
#-------------------------------------------------------
sub LoadDataInMemory {

	# Define filename
	my $newfilename=$fullfilename;

	# Define filelog
	$filelog =~ s/\n\s*\n/\n/g;					# Remove blank lines
	$filelog =~ s/^\s*[\r\n]*//g;				# Remove starting blank
	my $newfilelog=ucfirst("$filelog");

	# DEFINE CHANGE STATUS (removed, changed or added) OF FILE
	my $newfilestate='';
	if ($Output =~ /^listdelta/ || $Output =~ /^buildhtmlreport/) {
		if ($Branch) {
			# We work in a secondary BRANCH: Change status can't be defined
			if (!$filesym{$fullfilename}{$Branch}) { return; }		# This entry is not in branch 
			$newfilestate="unknown";
		}
		else {
			# We work in main BRANCH
			if ($TagStart && $filesym{$fullfilename}{$TagStart}) {
				# File did exist at the beginning
				if ($TagEnd && ! $filesym{$fullfilename}{$TagEnd}) {	# File was removed between TagStart and TagEnd
					$newfilestate="removed";
				}
				else {
					if ($filestate !~ /dead/) {
						$newfilestate="changed";
					}
					else {
						$newfilestate="removed";
					}
				}
			}
			else {
				# File did not exist for required start
				if (! $TagEnd || $filesym{$fullfilename}{$TagEnd}) {		# File was added after TagStart (and before TagEnd)
					# If file contains Attic, this means it was removed so, as it didn't exists in start tag version,
					# this means we can ignore this file if we need a delta.
					if ($filename =~ /[\\\/]Attic([\\\/][^\\\/]+)/ && $Output =~ /^listdelta/) { return; }
					if ($filestate !~ /dead/) {
						if ($filechange) {
							$newfilestate="changed";	# TODO Sometimes it should be "added" (if added after a remove). This will be corrected later.
						}
						else {	# A file added after TagStart
							$newfilestate="added";
						}
					}
					else {
						$newfilestate="removed";
					}
				}
				else {
					$newfilestate="removed";
					return;
				}
			}
		}
	}
	# We know state
	# If added or removed, value for lines added and deleted is not correct, so we download file to count them
    if ($Output =~ /^buildhtmlreport/ && ($newfilestate eq 'added' || $newfilestate eq 'removed') && $fileformat ne 'b' && $ENABLEREQUESTFORADD) {
        my $filerevisiontoscan=$filerevision;
        if ($newfilestate eq 'removed') { $filerevisiontoscan=DecreaseVersion($filerevisiontoscan); }
        my $nbline=0;
	    my $relativefilename=ExcludeRepositoryFromPath("$fullfilename",0,1);
	    my $relativefilenamekeepattic=ExcludeRepositoryFromPath("$fullfilename",1,1);
        if (! $Cache{$relativefilename}{$filerevisiontoscan} || $Cache{$relativefilename}{$filerevisiontoscan} =~ /^ERROR/) {

            # If number of lines for file not available in cache file, we download file
            #--------------------------------------------------------------------------
            my $filenametoget=$relativefilenamekeepattic;
            # Create dir if not exists
            my @added_dir_to_remove=();
            my @added_files_to_remove=();
   	        debug(" Number of lines not available, need to get file '$filenametoget' $filerevisiontoscan\n",2);
            if ($filenametoget =~ /Attic\//) {
                my $dir=$filenametoget;
                #$dir =~ s/^[^\@]+\@//;
                $dir =~ s/[\/\\]*[^\/\\]+$//;
                if ($dir) {
                    # Create dir to allow cvs update
                    &mkdir_recursive("$dir/CVS",\@added_dir_to_remove);
                    if (! -f "$dir/CVS/Entries") {
                        debug(" Create file '$dir/CVS/Entries'",2);
                        push @added_files_to_remove, "$dir/CVS/Entries";
                        open(ENTRIESFILE,">$dir/CVS/Entries");
                        close(ENTRIESFILE);
                    }
                    if (! -f "$dir/CVS/Repository") {
                        debug(" Create file '$dir/CVS/Repository'",2);
                        push @added_files_to_remove, "$dir/CVS/Repository";
                	    my $relativepath=$relativefilename; $relativepath =~ s/[\\\/][^\/\\]+$//;
                        open(REPOSITORY,">$dir/CVS/Repository");
                        print REPOSITORY "$Module/$relativepath";
                        close(REPOSITORY);
                    }
                }
                $filenametoget =~ s/Attic\///;
            }
            # TODO update with both -p and -d does not work (don't know why).
            # Must change to first run update -d, then update -p -r xxx
	        #my $command="$CVSCLIENT $COMP -d ".$ENV{"CVSROOT"}." update -d";
	        #my $command="$CVSCLIENT $COMP -d ".$ENV{"CVSROOT"}." update -p -r $filerevisiontoscan $filenametoget";
	        my $command="$CVSCLIENT $COMP -d ".$ENV{"CVSROOT"}." update -p -d -r $filerevisiontoscan $filenametoget";
	        debug(" Getting file '$relativefilename' revision '$filerevisiontoscan'\n",3);
	        debug(" with command '$command'\n",3);
            my $errorstring='';
            my $pid = open(PH, "$command 2>&1 |");
            while (<PH>) {
                #chomp $_; s/\r$//;
                #debug("$_");
                if ($_ =~ /cvs \[update aborted\]: no repository/) { $errorstring=$_; $nbline=0; last; }
                if ($_ =~ /cvs \[update aborted\]:/) { $errorstring=$_; $nbline=0; last; }
                $nbline++;
            }   

            # Show or exploit result
            if ($errorstring) { 
                warning(" Failed to execute command: $command: $errorstring");
                if ($Cache{$relativefilename}{$filerevisiontoscan} =~ /^ERROR(\d*)$/) {
                    # If it was not in error before, we track the error in cache file
                    #print CACHE "$relativefilename $filerevisiontoscan ERROR".(int($1)+1)." $command: $errorstring\n";
                }
                else {
                    # If it was not in error before, we track the error in cache file
                    print CACHE "$relativefilename $filerevisiontoscan ERROR1 $command: $errorstring\n";
                }
            }
            else {
                debug(" Nb of line : $nbline",2);
                $Cache{$relativefilename}{$filerevisiontoscan}=$nbline; 
                # Save result in a cache for other run
                print CACHE "$relativefilename $filerevisiontoscan $nbline $fileformat\n";
            }
            close(PH);

            # Remove downloaded files and dir
            foreach my $filetodelete (@added_files_to_remove) {
                debug(" Remove file '$filetodelete'",2);
                unlink $filetodelete;
            }
            foreach my $dirtodelete (reverse @added_dir_to_remove) {
                debug(" Remove dir '$dirtodelete'",2);
                rmdir $dirtodelete;
            }

        }
        else {
            $nbline=$Cache{$relativefilename}{$filerevisiontoscan};
        }
        print STDERR ".";
        if ($newfilestate eq 'added') {
            $filechange="+$nbline -0";
            $filelineadd=$nbline;
        }
        if ($newfilestate eq 'removed') {
            debug(" Nb of line : $nbline $relativefilename $filerevisiontoscan",2);
            $filechange="+0 -$nbline";
            $filelinedel=$nbline;
        }
	}
	
	# Update last date of tags
	foreach my $tag (%{$filesym{$newfilename}}) {
		if ("$filesym{$newfilename}{$tag}" eq "$filerevision") {  # Prendre comparaison texte pour avoir 1.1 != 1.10
			if (! $tagsfulldate{$tag} || $filedate > $tagsfulldate{$tag}) {
				$tagsfulldate{$tag}=$filedate;
				$filedate =~ /^(\d\d\d\d\d\d\d\d)/;
				$tagsshortdate{$tag}="$1";
				debug(" Update date of tag '$tag' with full date '$filedate' and short date '$1' (from $newfilename $filerevision)",5);
			}
		}
	}		

	# All infos were found. We can process record
	debug(" >>>> File revision: $fileformat - $newfilename - $filerevision - $filedate - $fileuser - $filestate - $filelineadd - $filelinechange - $filelinedel - $filechange => $newfilestate",2);
	
	# For output by date
	if ($Output =~ /bydate/ || $Output =~ /forrpm/ || $Output =~ /buildhtmlreport/) {
		$filedate=~/(\d\d\d\d\d\d\d\d)\s(\d\d)/;
		my $fileday=$1;
		my $filehour=$2;
		$HourUser{"$filehour $fileuser"}++;
		$DateUser{"$fileday $fileuser"}++;
		$DateUserLog{"$fileday $fileuser"}{$newfilelog}++;
		$DateUserLogFileRevState{"$fileday $fileuser"}{$newfilelog}{"$newfilename $filerevision"}=$newfilestate;
		if ($newfilestate eq 'removed') {
			# Change a state of a revision from "changed" into "added" when previous revision was "removed"
			my $filerevisionnext=$filerevision;
			if ($filerevisionnext =~ /\.(\d+)$/) {
				my $newver=int($1)+1;
				$filerevisionnext =~ s/\.(\d+)$/\.$newver/;
			}
			if ($DateUserLogFileRevState{$oldfiledayuser}{$oldfilelog}{"$newfilename $filerevisionnext"} =~ /^changed$/) {
				debug(" Correct next version of $newfilename $filerevisionnext ($filerevisionnext should be 'added_forced' instead of 'changed')",3);
				$DateUserLogFileRevState{$oldfiledayuser}{$oldfilelog}{"$newfilename $filerevisionnext"}="added_forced";
			}
		}
		# When a version file does not exists in end, all versions are at state 'removed'.
		# We must change this into "changed" for those whose next revision exists and is 'removed'. Only last one stay 'removed'.
		if ($newfilestate eq 'removed') {
			my $filerevisionnext=$filerevision;
			if ($filerevisionnext =~ /\.(\d+)$/) {
				my $newver=int($1)+1;
				$filerevisionnext =~ s/\.(\d+)$/\.$newver/;
			}
			if ($DateUserLogFileRevState{$oldfiledayuser}{$oldfilelog}{"$newfilename $filerevisionnext"} =~ /^(removed|changed_forced)$/) {
				debug(" Correct version of $newfilename $filerevision ($filerevision should be 'changed_forced' instead of 'removed')",3);
				$DateUserLogFileRevState{"$fileday $fileuser"}{$newfilelog}{"$newfilename $filerevision"}='changed_forced';	# with _forced to not be change again by previous test
			}
		}
		# Var used to retrieve easily the revision already read just before the one processed in this function
		$oldfiledayuser="$fileday $fileuser";
		$oldfilelog="$newfilelog";

		my $filechangebis=$filechange; $filechangebis=~s/\-/ \-/;
		if ($fileformat ne 'b') {
		    $DateUserLogFileRevLine{"$fileday $fileuser"}{$newfilelog}{"$newfilename $filerevision"}=$filechangebis;
		}
		else {
		    $DateUserLogFileRevLine{"$fileday $fileuser"}{$newfilelog}{"$newfilename $filerevision"}='binary';
		}
	}
	
	# For output by file
	if ($Output =~ /byfile/ || $Output =~ /buildhtmlreport/) {
		if (! $FilesLastVersion{$newfilename}) { $FilesLastVersion{$newfilename}=$filerevision; }	# Save 'last' file version
		$FilesChangeDate{$newfilename}{$filerevision}=$filedate;
		$FilesChangeUser{$newfilename}{$filerevision}=$fileuser;
		$FilesChangeState{$newfilename}{$filerevision}=$newfilestate;
		$FilesChangeLog{$newfilename}{$filerevision}=$newfilelog;
	}
	
	# For output by log
	if ($Output =~ /bylog/ || $Output =~ /buildhtmlreport/) {
		$LogChange{$newfilelog}=1;
		$LogChangeDate{$newfilelog}{"$newfilename $filerevision"}=$filedate;
		$LogChangeUser{$newfilelog}{"$newfilename $filerevision"}=$fileuser;
		$LogChangeState{$newfilelog}{"$newfilename $filerevision"}=$newfilestate;
	}
	
	if ($Output =~ /^buildhtmlreport/) {
	    if (! $UserChangeLast{$fileuser} || int($UserChangeLast{$fileuser}) < int($filedate)) { $UserChangeLast{$fileuser}=$filedate; }
	    $UserChangeCommit{$fileuser}{$fullfilename}++;
	    if ($fileformat ne 'b') {
	        $UserChangeLineAdd{$fileuser}+=$filelineadd;
	        $UserChangeLineDel{$fileuser}+=$filelinedel;
	        $UserChangeLineChange{$fileuser}+=$filelinechange;
	    }
	}
}

#------------------------------------------------------------------------------
# Function:     Clean tags in a string
# Parameters:   stringtodecode
# Input:        None
# Output:       None
# Return:		decodedstring
#------------------------------------------------------------------------------
sub CleanFromTags {
	my $stringtoclean=shift;
	$stringtoclean =~ s/$regclean1/ /g;	# Replace <recnb> or </td> with space
	$stringtoclean =~ s/$regclean2//g;	# Remove <xxx>
	return $stringtoclean;
}

#------------------------------------------------------------------------------
# Function:      Format a date
# Input:         String "YYYYMMDD HH:MM:SS xxx"
#                Option "" or "rpm"
# Return:        string "YYYY-MM-DD HH:MM:SS xxx" if option is ""
#                String "Thu Mar 7 2002 xxx" if option is "rpm"
#                String "YYYY-MM-DD HH:MM" if option is "simple"
#------------------------------------------------------------------------------
sub FormatDate {
	my $string=shift;
	my $option=shift;
	if ($option eq 'rpm' && $string =~ /(\d\d\d\d)(\d\d)(\d\d)/) {
		my $ns=Time::Local::timelocal(0,0,0,$3,$2-1,$1);
		my $ctime=localtime($ns); $ctime =~ s/ 00:00:00//;
		$string =~ s/(\d\d\d\d)(\d\d)(\d\d)/$ctime/;
	}
	elsif ($option eq 'simple' && $string =~ /(\d\d\d\d)(\d\d)(\d\d)\s(\d\d):(\d\d)/) {
        $string="$1-$2-$3 $4:$5";
	}
    elsif ($string =~ /(\d\d\d\d)(\d\d)(\d\d)/) {
        $string="$1-$2-$3";
    }
	return "$string";
}

#------------------------------------------------------------------------------
# Function:      Format a state string with color
#------------------------------------------------------------------------------
sub FormatState {
	my $string=shift;
	my %colorstate=('added'=>'#008822','changed'=>'#888888','removed'=>'#880000');
    return "<font color=\"".$colorstate{$string}."\">$string</font>";
}

#------------------------------------------------------------------------------
# Function:      Format a viewcvs file link
#------------------------------------------------------------------------------
sub FormatCvsFileLink {
	my $url=shift;
	my $version=shift;
    if ($ViewCvsUrl) { 
        my $string='';
        $string="$ViewCvsUrl$Module/";
        $string.="$url";
        $string.="?rev=".$version;
    	return "<a href=\"$string\">$url</a>";
	}
	else {
	    return "$url";   
	}
}

#------------------------------------------------------------------------------
# Function:      Format a viewcvs diff link
#------------------------------------------------------------------------------
sub FormatCvsDiffLink {
	my $url=shift;
	my $version=shift;
    if ($ViewCvsUrl) { 
        my $string='';
        my $label='diff';
        $string="$ViewCvsUrl$Module/";
        $string.="$url";
        if (CompareVersionBis($version,"1.1")>0) {
            my $versionprec=DecreaseVersion($version);
            $string.=".diff?r1=".$versionprec;
            $string.="&r2=".$version;
        }
        else {
            $string.="?rev=".$version;
        }    
    	return "<a href=\"$string\">$label</a>";
	}
	else {
	    return "$url";   
	}
}

#------------------------------------------------------------------------------
# Function:      Format a number
# Input:         number precision
# Return:        dd.d
#                String "Thu Mar 7 2002 xxx" if option is "rpm"
#                String "YYYY-MM-DD HH:MM" if option is "simple"
#------------------------------------------------------------------------------
sub RoundNumber {
	my $number=shift;
	my $precision=shift;
    foreach (1..$precision) { $number*=10; }
    $number=int($number);
    foreach (1..$precision) { $number/=10; }
	return "$number";
}

#------------------------------------------------------------------------------
# Function:      Compare 2 CVS version number
# Input:         $a $b
# Output:        -1 if $a < $b, 1 if $a >= $b
#------------------------------------------------------------------------------
sub CompareVersion {
	my $a=shift;
	my $b=shift;
	my $aint; my $adec; my $bint; my $bdec;
	if ($a =~ /^(\d+)\.(\d+)$/) { $aint=int($1); $adec=int($2); } else { $aint=int($a); $adec=0; }
	if ($b =~ /^(\d+)\.(\d+)$/) { $bint=int($1); $bdec=int($2); } else { $bint=int($b); $bdec=0; }
	if ($aint < $bint) { return -1; }
	if ($aint > $bint) { return 1; }
	if ($adec < $bdec) { return -1; }
	return 1;
}

#------------------------------------------------------------------------------
# Function:      Compare 2 CVS version number
# Input:         $a $b
# Output:        -1 if $a < $b, 0 if $a = $b, 1 if $a > $b
#------------------------------------------------------------------------------
sub CompareVersionBis {
	my $a=shift;
	my $b=shift;
	my $aint; my $adec; my $bint; my $bdec;
	if ($a =~ /^(\d+)\.(\d+)$/) { $aint=int($1); $adec=int($2); } else { $aint=int($a); $adec=0; }
	if ($b =~ /^(\d+)\.(\d+)$/) { $bint=int($1); $bdec=int($2); } else { $bint=int($b); $bdec=0; }
	if ($aint < $bint) { return -1; }
	if ($aint > $bint) { return 1; }
	if ($adec < $bdec) { return -1; }
	if ($adec > $bdec) { return 1; }
	return 0;
}

#------------------------------------------------------------------------------
# Function:      Decrease a version number by one
# Input:         1.159
# Output:        1.158
#------------------------------------------------------------------------------
sub DecreaseVersion {
	my $a=shift;
	my $aint; my $adec;
	if ($a =~ /^(\d+)\.(\d+)$/) { $aint=int($1); $adec=int($2); } else { $aint=int($a); $adec=0; }
    $adec--;
	return "$aint.$adec";
}

#------------------------------------------------------------------------------
# Function:      Remove repository path from a full path
# Input:         string keepattic removemodule
# Output:        a string path
#------------------------------------------------------------------------------
sub ExcludeRepositoryFromPath {
	my $file=shift;
	my $keepattic=shift;
	my $removemodule=shift;
	if (! $keepattic) { $file =~ s/[\\\/]Attic([\\\/][^\\\/]+)/$1/; }
	if ($removemodule) {
		$file =~ s/^$ModuleForCache\@//;											# Remove Module name
	}
	$file =~ s/^($ModuleForCache\@)(\w:)?$RepositoryPath[\\\/]$Module[\\\/]/$1/;	# Remove path repository
	$file =~ s/^(\w:)?$RepositoryPath[\\\/]$Module[\\\/]//;
	return $file;
}

#------------------------------------------------------------------------------
# Function:     Return day of week of a day
# Parameters:	"$year$month$day"
# Return:		1-7 (1 = monday, 7=sunday)
#------------------------------------------------------------------------------
sub DayOfWeek {
	shift =~ /(\d\d\d\d)(\d\d)(\d\d)/;
	my ($day, $month, $year) = ($3, $2, $1);
	if ($Debug) { debug("DayOfWeek for $day $month $year",4); }
	if ($month < 3) {  $month += 10;  $year--; }
	else { $month -= 2; }
	my $cent = sprintf("%1i",($year/100));
	my $y = ($year % 100);
	my $dw = (sprintf("%1i",(2.6*$month)-0.2) + $day + $y + sprintf("%1i",($y/4)) + sprintf("%1i",($cent/4)) - (2*$cent)) % 7;
	$dw += 7 if ($dw<0);
    if (! $dw) { $dw = 7; } # It's sunday
	if ($Debug) { debug(" is $dw",4); }
	return $dw;
}


#-------------------------------------------------------
# MAIN
#-------------------------------------------------------
my $QueryString=join(' ', @ARGV);
if ($QueryString =~ /debug=(\d+)/i)    			{ $Debug=$1; }
if ($QueryString =~ /m(?:odule|)=([^\s]+)/i)	{ $Module=$1; }
if ($QueryString =~ /output=([^\s]+)/i)   		{ $Output=$1; }
if ($QueryString =~ /branch=([^\s]+)/i)			{ $Branch=$1; }
if ($QueryString =~ /tagstart=([^\s]+)/i) 		{ $TagStart=$1; }
if ($QueryString =~ /tagend=([^\s]+)/i)   		{ $TagEnd=$1; }
if ($QueryString =~ /-ssh/)    					{ $UseSsh=1 }
if ($QueryString =~ /rlogfile=([:\-\.\\\/\wè~]+)/i) { $RLogFile=$1; }
if ($QueryString =~ /keeprlogfile/i)            { $KeepRlogFile=1; }
if ($QueryString =~ /dir=([^\s]+)/i)    		{ $OutputDir=$1; }
if ($QueryString =~ /viewcvsurl=([^\s]+)/i)  	{ $ViewCvsUrl=$1; }
if ($QueryString =~ /-d=([^\s]+)/)      		{ $CvsRoot=$1; }
if ($QueryString =~ /-h/)      					{ $Help=1; }
if ($QueryString =~ /-ignore=([^\s]+)/i)        { $IgnoreFileDir{$1}=1; }
if ($QueryString =~ /-only=([^\s]+)/i)        	{ $OnlyFileDir{$1}=1; }
($DIR=$0) =~ s/([^\/\\]+)$//; ($PROG=$1) =~ s/\.([^\.]*)$//; $Extension=$1;
$DIR||='.'; $DIR =~ s/([^\/\\])[\\\/]+$/$1/;
debug("Parameter Module       : $Module");
debug("Parameter Output       : $Output");
debug("Parameter OutputDir    : $OutputDir");
debug("Parameter Branch       : $Branch");
debug("Parameter ViewCvsUrl   : $ViewCvsUrl");
debug("Parameter IgnoreFileDir: ".join(',',keys %IgnoreFileDir));
if ($ViewCvsUrl && $ViewCvsUrl !~ /\/$/) { $ViewCvsUrl.="/"; }

# On determine chemin complet du repertoire racine et on en deduit les repertoires de travail
my $REPRACINE;
if (! $ENV{"SERVER_NAME"}) {
	$REPRACINE=($DIR?$DIR:".")."/..";
} else {
	$REPRACINE=$ENV{"DOCUMENT_ROOT"};
}

my %param=();
if ($Output) {
    if ($Output =~ s/:(.*)//g) {
        # There is some parameter on output option
        foreach my $key (split(/,/,$1)) { $param{$key}=1; }
        if ($param{'nolimit'}) { $MAXLASTLOG=0; }
    }
	my %allowedvalueforoutput=(
	"listdeltabydate"=>1,
	"listdeltabylog"=>1,
	"listdeltabyfile"=>1,
	"listdeltaforrpm"=>1,
	"buildhtmlreport"=>1
	);
	if (! $allowedvalueforoutput{$Output}) {
		writeoutput("----- $PROG $VERSION (c) Laurent Destailleur -----\n");
		writeoutput("Unknown value for output parameter.\n");
		exit 1;
	}
}

if ($Help || ! $Output) {
	writeoutput("----- $PROG $VERSION (c) Laurent Destailleur -----\n");
	writeoutput("$PROG generates advanced ChangeLog/Report files for CVS projects/modules.\n");
	writeoutput("Note 1: Your cvs client (cvs or cvs.exe) must be in your PATH.\n");
	writeoutput("Note 2: To use $PROG with a csv client through ssh, add option -ssh.\n");
	writeoutput("\nUsage:\n");
	writeoutput("  $PROG.$Extension -output=outputmode [-m=module -d=repository] [options]\n");
	writeoutput("\n");
	writeoutput("Where 'outputmode' is:\n");
	writeoutput("  listdeltabydate  To get a changelog between 2 versions, sorted by date\n");
	writeoutput("  listdeltabylog   To get a changelog between 2 versions, sorted by log\n");
	writeoutput("  listdeltabyfile  To get a changelog between 2 versions, sorted by file\n");
	writeoutput("  listdeltaforrpm  To get a changelog between 2 versions for rpm spec files\n");
	writeoutput("  buildhtmlreport  To build an html report\n");
	writeoutput("\n");
	writeoutput("  Note that \"between 2 versions\" means (depends on tagstart/tagend options):\n");
	writeoutput("  * from start to a tagged version (version changes included)\n");
	writeoutput("  * from a start version (excluded) to another tagged version (included)\n");
	writeoutput("  * or from a tagged version until now (version changes excluded)\n");
	writeoutput("\n");
	writeoutput("The 'module' and 'repository' are the CVS module name and the CVS repository.\n");
	writeoutput("  If current directory is the root of a CVS project built from a cvs checkout,\n");
	writeoutput("  cvschangelogbuilder will retreive module and repository value automatically.\n");
	writeoutput("  If no local copy of repository are available or to force other value, use:\n");
	writeoutput("  -m=module           To force value of module name\n");
	writeoutput("  -d=repository       To force value of CVSROOT\n");
	writeoutput("\n");
	writeoutput("Options are:\n");
	writeoutput("  -branch=branchname  To work on another branch than default branch (!)\n");
	writeoutput("  -tagstart=tagname   To specify start tag version\n");
	writeoutput("  -tagend=tagend      To specify end tag version\n");
	writeoutput("\n");
	writeoutput("  !!! WARNING: If you use tagstart and/or tagend, check that tags are in SAME\n");
	writeoutput("  BRANCH. Also, it must be the default branch, if not, you MUST use -branch to\n");
	writeoutput("  give the name of the branch, otherwise you will get unpredicable result.\n");
	writeoutput("\n");
	writeoutput("  -ssh                To run CVS through ssh (this only set CVS_RSH=\"ssh\")\n");
	writeoutput("  -rlogfile=rlogfile  If an up-to-date log file already exist localy, you can use\n");
	writeoutput("                       this option to avoid log download, for a faster result.\n");
	writeoutput("  -keeprlogfile       Once process is finished, you can ask to not remove the\n");
	writeoutput("                       downloaded log file.\n");
	writeoutput("  -dir=dirname        Output is built in directory dirname.\n");
	writeoutput("  -viewcvsurl=viewcvsurl   File's revisions in reports built by buildhtmlreport\n");
	writeoutput("                           output are links to \"viewcvs\".\n");
	writeoutput("  -ignore=file/dir    To exclude a file/dir off report.\n");
	writeoutput("  -debug=x            To output on stderr debug info with level x\n");
	writeoutput("\n");
	writeoutput("Example:\n");
	writeoutput("  $PROG.$Extension -module=myproject -output=listdeltabyfile -tagstart=myproj_2_0 -d=john\@cvsserver:/cvsdir\n");
	writeoutput("  $PROG.$Extension -module=mymodule  -output=listdeltabydate -d=:ntserver:127.0.0.1:d:/mycvsdir\n");
	writeoutput("  $PROG.$Extension -module=mymodule  -output=listdeltabylog  -d=:pserver:user\@127.0.0.1:/usr/local/cvsroot\n");
	writeoutput("  $PROG.$Extension -module=mymodule  -output=buildhtmlreport -d=:ext:user\@cvs.sourceforge.net:/cvsroot\n");
	writeoutput("\n");
	exit 0;
}

# Get current time
my $nowtime=time;
my ($nowsec,$nowmin,$nowhour,$nowday,$nowmonth,$nowyear) = localtime($nowtime);
if ($nowyear < 100) { $nowyear+=2000; } else { $nowyear+=1900; }
my $nowsmallyear=$nowyear;$nowsmallyear =~ s/^..//;
if (++$nowmonth < 10) { $nowmonth = "0$nowmonth"; }
if ($nowday < 10) { $nowday = "0$nowday"; }
if ($nowhour < 10) { $nowhour = "0$nowhour"; }
if ($nowmin < 10) { $nowmin = "0$nowmin"; }
if ($nowsec < 10) { $nowsec = "0$nowsec"; }
# Get tomorrow time (will be used to discard some record with corrupted date (future date))
my ($tomorrowsec,$tomorrowmin,$tomorrowhour,$tomorrowday,$tomorrowmonth,$tomorrowyear) = localtime($nowtime+86400);
if ($tomorrowyear < 100) { $tomorrowyear+=2000; } else { $tomorrowyear+=1900; }
my $tomorrowsmallyear=$tomorrowyear;$tomorrowsmallyear =~ s/^..//;
if (++$tomorrowmonth < 10) { $tomorrowmonth = "0$tomorrowmonth"; }
if ($tomorrowday < 10) { $tomorrowday = "0$tomorrowday"; }
if ($tomorrowhour < 10) { $tomorrowhour = "0$tomorrowhour"; }
if ($tomorrowmin < 10) { $tomorrowmin = "0$tomorrowmin"; }
if ($tomorrowsec < 10) { $tomorrowsec = "0$tomorrowsec"; }
my $timetomorrow=$tomorrowyear.$tomorrowmonth.$tomorrowday.$tomorrowhour.$tomorrowmin.$tomorrowsec;	


# -- Start for module


# Check/Retreive module
my $ModuleChoosed=$Module;
if (! $Module || $Output =~ /^buildhtmlreport/) {
    $Module='';
	if (-s "CVS/Repository") {
		open(REPOSITORY,"<CVS/Repository") || error("Failed to open 'CVS/Repository' file to get module name.");
		while (<REPOSITORY>) {
			chomp $_; s/\r$//;
			$Module=$_;
			last;
		}
		close(REPOSITORY);
	}
}
if ($Output =~ /^buildhtmlreport/ && ! $Module) {
	writeoutput("\n");
	error("To generate the buildhtmlreport output, $PROG must be launched from the checkout root directory of project.");
}
if ($Output =~ /^buildhtmlreport/ && $ModuleChoosed && $Module && $Module ne $ModuleChoosed) {
	writeoutput("\n");
	error("To generate the buildhtmlreport output, $PROG must be launched from the right checkout root directory.\n$PROG is launched from a checkout root directory of module '$Module' but you ask a report for module '$ModuleChoosed'.");
}
if (! $Module) {
	writeoutput("\n");
	error("The module name was not provided and could not be detected.\nUse -m=cvsmodulename option to specifiy module name.\n\nExample: $PROG.$Extension -output=$Output -module=mymodule -d=:pserver:user\@127.0.0.1:/usr/local/cvsroot");
}

writeoutput(ucfirst($PROG)." launched for module: $Module\n",1);

# Define ModuleForCache (use for cache file and fullfilename)
$ModuleForCache=$Module;
$ModuleForCache =~ s/\//_/g; # In case $Module contains '/' because caught from a subdirectory of CVS tree


# Check/Retreive CVSROOT environment variable (needed to get $RepositoryPath)
my $CvsRootChoosed=$CvsRoot;
if (! $CvsRoot || $Output =~ /^buildhtmlreport/) {
    $CvsRoot='';
	# Try to get CvsRoot from CVS repository
	if (-s "CVS/Root") {
		open(REPOSITORY,"<CVS/Root") || error("Failed to open 'CVS/Root' file to get repository value.");
		while (<REPOSITORY>) {
			chomp $_; s/\r$//;
			$CvsRoot=$_;
			last;
		}
		close(REPOSITORY);
	}
}
if ($Output =~ /^buildhtmlreport/ && ! $CvsRoot) {
	writeoutput("\n");
	error("To generate the buildhtmlreport output, $PROG must be launched from a checkout root directory of project.");
}
if ($Output =~ /^buildhtmlreport/ && $CvsRootChoosed && $CvsRoot && $CvsRoot ne $CvsRootChoosed) {
    writeoutput("\n");
	error("To generate the buildhtmlreport output, $PROG must be launched from the right checkout root directory.\n$PROG is launched from a checkout root directory of module '$Module' with cvsroot '$CvsRoot' but you ask a report ".($ModuleChoosed?"for module '$ModuleChoosed' ":"")."with a different cvsroot '$CvsRootChoosed'.");
}
if (! $CvsRoot) {
	# Try to set CvsRoot from CVSROOT environment variable
	if ($ENV{"CVSROOT"}) { $CvsRoot=$ENV{"CVSROOT"}; }
}
if (! $CvsRoot) {
	writeoutput("\n");
	error("The repository value to use was not provided and could not be detected.\nUse -d=repository option to specifiy repository value.\n\nExample: $PROG.$Extension -output=$Output -module=mymodule -d=:pserver:user\@127.0.0.1:/usr/local/cvsroot");
}
if ($OutputDir) {
    $OutputDir.="/";
}

$ENV{"CVSROOT"}=$CvsRoot;
writeoutput(ucfirst($PROG)." launched for CVSRoot: $CvsRoot\n",1);

$RepositoryPath=$CvsRoot; $RepositoryPath=~s/.*:([^:]+)/$1/;
writeoutput(ucfirst($PROG)." launched for directory repository: $RepositoryPath\n",1);
$RepositoryPath=quotemeta($RepositoryPath);

writeoutput(ucfirst($PROG)." launched for Branch: ".($Branch?"$Branch":"HEAD")."\n",1);

# Set use of ssh or not
if ($UseSsh) {
	writeoutput("Set CVS_RSH=ssh\n",1);
	$ENV{'CVS_RSH'}='ssh';
}

# SUMMARY OF PARAMETERS
#------------------------------------------

# LAUNCH CVS COMMAND RLOG TO WRITE RLOGFILE
#------------------------------------------
if (! $RLogFile) {
    print STDERR "Need to download cvs log file, please wait...\n";
	# Define temporary file
	my $TmpDir="";
	$TmpDir||=$ENV{"TMP"};
	$TmpDir||=$ENV{"TEMP"};
	$TmpDir||='/tmp';
	my $TmpFile="$TmpDir/$PROG.$ModuleForCache.$$.tmp";
	open(TEMPFILE,">$TmpFile") || error("Failed to open temp file '$TmpFile' for writing. Check directory and permissions.");
	my $command;
	#$command="$CVSCLIENT rlog ".($TagStart||$TagEnd?"-r$TagStart:$TagEnd ":"")."$Module";
	if ($Branch) {
		$command="$CVSCLIENT $COMP -d ".$ENV{"CVSROOT"}." rlog -r${Branch} $Module";
	}
	else {
		$command="$CVSCLIENT $COMP -d ".$ENV{"CVSROOT"}." rlog -b ".($TagStart||$TagEnd?" -r${TagStart}::${TagEnd}":"")." $Module";
	}
	writeoutput("Downloading temporary cvs rlog file '$TmpFile'\n",1);
	writeoutput("with command '$command'\n",1);
	debug("CVSROOT value is '".$ENV{"CVSROOT"}."'");
	my $result=`$command 2>&1`;
	print TEMPFILE "$result";
	close TEMPFILE;
	if (! $result || $result !~ /cvs \w+: Logging/i) {		# With log we get 'cvs server: Logging awstats' and with rlog we get 'cvs rlog: Logging awstats'
		error("Failure in cvs command: '$command'\n$result");
	}
	$RLogFile=$TmpFile;
}

# ANALYZE RLOGFILE
#------------------------
writeoutput("Analyzing rlog file '$RLogFile'\n",1);
if ($Output =~ /^buildhtmlreport/) {
    # Try to read cache file
    my $cachefile="${OutputDir}${PROG}_${ModuleForCache}.cache";
    debug(" Search for cache file '$cachefile' into current directory",1);
    if (-f $cachefile) {
        writeoutput("Load cache file '$cachefile' with number of lines for added files...\n",1);
        open(CACHE,"<$cachefile") || error("Failed to open cache file '$cachefile' for reading");
        while (<CACHE>) {
            chomp $_; s/\r$//;
            if (! $_) { next; }
            my ($file,$revision,$nbline,undef)=split(/\s+/,$_);
            debug(" Load cache entry for ($file,$revision)=$nbline",2);
            $Cache{$file}{$revision}=$nbline;   # If duplicate records, the last one will be used
        }
        close CACHE;
    } else {
        print STDERR "No cache file can be found. This probably means you run $PROG for\n";
        print STDERR "the first time. Building cache for the first update can take a very long\n";
        print STDERR "time (between several seconds to hours depending on your CVS server response\n";
        print STDERR "time), so please wait...\n";
    }
    # Open cache file to write new files entries
    open(CACHE,">>$cachefile") || error("Failed to open cache file '$cachefile' for writing");
}

open(RLOGFILE,"<$RLogFile") || error("Can't open rlog file");
my $waitfor="filename";
while (<RLOGFILE>) {
	chomp $_; s/\r$//;
	my $line="$_";

	debug("Analyze line: $line (waitfor=$waitfor)",3);

	# Check if there is a warning in rlog file
	#if ($line =~ /^cvs rlog: warning: no revision/) { print("$line\n"); next; }

	# Wait for a new file
	if ($waitfor eq "filename") {
        #if ($line =~ /^cvs rlog: Logging (.*)/) { $Module=$1; } # Set module name from log file
		if ($line =~ /$EXTRACTFILENAME/i) {
			$filename="$1";
			$filename =~ s/,v$//;					# Clean filename if ended with ",v"
			$fullfilename="$ModuleForCache\@$filename";
			my $truefilename=ExcludeRepositoryFromPath("$filename",0,1);
			# We found a new filename
			debug("Found a new file '$fullfilename'",2);
            my $qualified=1;
            # Check if file qualified
            foreach my $key (keys %IgnoreFileDir) {
                if ($truefilename =~ /$key/) { $qualified=-1; last; }
            }
            if (scalar keys %OnlyFileDir) {
                $qualified=-2; 
                foreach my $key (keys %OnlyFileDir) {
                    if ($truefilename =~ /$key/) { $qualified=1; last; }
                }
            }
    		if ($qualified > 0) {
    			debug("File is qualified to be included in report",2);
    			$waitfor="symbolic_name";
	    		$maxincludedver{"$fullfilename"}=0;
			    $minexcludedver{"$fullfilename"}=0;
            }
    		if ($qualified == -1) {
    			debug("File discarded by ignore option",2);
            }
    		if ($qualified == -2) {
    			debug("File discarded by only option",2);
            }
		}
		next;
	}

	# Wait for symbolic names area
	if ($waitfor eq "symbolic_name") {
		if ($line =~ /$EXTRACTSYMBOLICNAMEAREA/i) {
			# We found symbolic names area
			$waitfor="symbolic_name_entry";
			debug("Found symbolic name area",2);
		}
		next;
	}

	# Wait for symbolic names entry
	if ($waitfor eq "symbolic_name_entry") {
		if ($line =~ /$EXTRACTSYMBOLICNAMEENTRY/i) {
			# We found symbolic name entry
			# We set symbolic name. Example: $filesym{$fullfilename}{MYAPPLI_1_0}=2.31
			$filesym{$fullfilename}{$1}=$2;
			debug("Found symbolic name entry $1 is for version $filesym{$fullfilename}{$1}",2);
			if ($TagEnd && $TagEnd eq $1) {
				$maxincludedver{"$fullfilename"}=$2;
				debug(" Max included version for file '$fullfilename' set to $2",3);
			}
			if ($TagStart && $TagStart eq $1) {
				$minexcludedver{"$fullfilename"}=$2;
				debug(" Min excluded version for file '$fullfilename' set to $2",3);
			}
		}
		else {
            if ($line =~ /^keyword substitution: (\S+)/) { $fileformat=$1; }
			$waitfor="revision";
		}
		next;
	}

	# Wait for a revision
	if ($waitfor eq "revision") {
		if ($line =~ /^=====/) {
			# No revision found
			$waitfor="filename";
			debug(" No revision found. Now waiting for '$waitfor'.",2);
			$fileformat=''; $filedate=''; $fileuser=''; $filestate=''; $filechange=''; $filelog=''; $filelineadd=0; $filelinedel=0; $filelinechange=0;
			next;	
		}
		if ($line =~ /$EXTRACTFILEVERSION/i) {
			# We found a new revision number
			$filerevision=$1;
			$waitfor="dateuserstate";
			debug("Found a new revision number: $filerevision. Now waiting for '$waitfor'.",2);
		}
		next;
	}

	# Wait for date and user of revision
	if ($waitfor eq "dateuserstate") {
		if ($line =~ /$EXTRACTFILEDATEUSERSTATE/i) {
			# We found date/user line
			$filedate=$1; $fileuser=$2; $filestate=$3; $filechange=$4;
			$filedate =~ s/\///g;
			$filelineadd=0; $filelinedel=0; $filelinechange=0;
			if ($filechange =~ s/.*([\+\-]\d+)\s+([\+\-]\d+).*/$1$2/g) {
            	$filelineadd=int($1); $filelinedel=(-int($2));
		    	if ($filelineadd>=$filelinedel) { $filelineadd-=$filelinedel; $filelinechange=$filelinedel; $filelinedel=0; }
		    	else { $filelinedel-=$filelineadd; $filelinechange=$filelineadd; $filelineadd=0; }
			}
			else {
				$filechange="";	# It's not a change but an add with cvsnt (+x -x are not reported with cvsnt)
			}
			$filedate =~ s/[\s;]+$//; $fileuser =~ s/[\s;]+$//; $filestate =~ s/[\s;]+$//; $filechange =~ s/\s+//g;
			$waitfor="log";
			debug("Found a new date/user/state/nbadd/nbchange/nbdel $filedate $fileuser $filestate $filelineadd $filelinechange $filelinedel. Now waiting for '$waitfor'.",2);
		}
		next;
	}

	# Wait for log comment
	if ($waitfor eq "log") {
		if ($line =~ /^branches:/) { next; }
		if ($line =~ /^-----/) {
			$waitfor="revision";
			# Load all data for this revision file in memory
			debug("Info are complete, we store them",2);
			LoadDataInMemory();
			debug(" Revision info are stored. Now waiting for '$waitfor'.",2);
			$filedate=''; $fileuser=''; $filestate=''; $filechange=''; $filelog=''; $filelineadd=0; $filelinedel=0; $filelinechange=0;
			next;	
		}
		if ($line =~ /^=====/) {
			$waitfor="filename";
			# Load all data for this revision file in memory
			debug("Info are complete, we store them",2);
			LoadDataInMemory();
			debug(" Revision info are stored. Now waiting for '$waitfor'.",2);
			$filedate=''; $fileuser=''; $filestate=''; $filechange=''; $filelog=''; $filelineadd=0; $filelinedel=0; $filelinechange=0;
			next;	
		}
		# Line is log
		debug("Found a new line for log: $line",2);
		$filelog.="$line\n";
		next;
	}
}
close RLOGFILE;
if ($Output =~ /^buildhtmlreport/) {
    close CACHE;
}

# Build %tagsshortdate
#---------------------
foreach my $tag (keys %tagsshortdate) {
    # $tagsshortdate{v1_0}=20041201
    # $tagstags{20041201}{v1_0}=1
	$tagstags{$tagsshortdate{$tag}}{$tag}=1;
	debug("Add entry in tagstags for key $tagsshortdate{$tag} with value $tag",2);
}

# BUILD OUTPUT
#------------------------
my $OutputRootFile="${PROG}_".($Branch?"(${Branch})_$Module":"$Module");

# Start of true output
if ($OutputDir) {
    open(FILE,">${OutputDir}${OutputRootFile}.html") || error("Error: Failed to open file '${OutputRootFile}.html' for output in directory '${OutputDir}'.");
}

writeoutput("\nBuild output for option '$Output'\n",1);

# Build header
my $headstring='';
my $rangestring='';
if ($Output !~ /buildhtmlreport$/) {
    $headstring.="\nChanges for $Module";
}
else {
    $headstring.="\nCVS report for module <b>$Module</b>";
}
if ($Branch) {
    $headstring.=" in branch $Branch";
    $rangestring.="Branch $Branch";
}
else {
    $rangestring.="Main Branch (HEAD)";
}
if ($TagStart) {
	if ($TagEnd) {
	    $headstring.=" beetween $TagStart (excluded) and $TagEnd (included)";
	    $rangestring.= " - Beetween $TagStart (excluded) and $TagEnd (included)"; 
	}
	else {
	    $headstring.=" since $TagStart (excluded)";
	    $rangestring.= " - Since $TagStart (excluded)";
	}
}
elsif ($TagEnd) {
	$headstring.=" in $TagEnd";
    $rangestring.= " in $TagEnd";
}
$headstring.="\n built by $PROG $VERSION with option $Output.";
if ($Output !~ /buildhtmlreport$/) {
    writeoutputfile "$headstring\n\n";
}
else {
    writeoutputfile "<html>\n<head>\n";
    writeoutputfile "<meta name=\"generator\" content=\"$PROG $VERSION\" />\n";
    writeoutputfile "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";
    writeoutputfile "<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />\n";
    writeoutputfile "<meta http-equiv=\"description\" content=\"$headstring\" />\n";
    writeoutputfile "<title>CVS report for $Module</title>\n";
    writeoutputfile <<EOF;
<style type="text/css">
<!--
body { font: 11px verdana, arial, helvetica, sans-serif; background-color: #FFFFFF; margin-top: 0; margin-bottom: 0; }
.aws_bodyl  { }
.aws_border { background-color: #FFE0B0; padding: 1px 1px 1px 1px; margin-top: 0; margin-bottom: 0; }
.aws_title  { font: 13px verdana, arial, helvetica, sans-serif; font-weight: bold; background-color: #FFE0B0; text-align: center; margin-top: 0; margin-bottom: 0; padding: 1px 1px 1px 1px; color: #000000; }
.aws_blank  { font: 13px verdana, arial, helvetica, sans-serif; background-color: #FFE0B0; text-align: right; margin-bottom: 0; padding: 1px 1px 1px 1px; }
.aws_data {
	background-color: #FFFFFF;
	border-top-width: 1px;   
	border-left-width: 0px;  
	border-right-width: 0px; 
	border-bottom-width: 0px;
}
.aws_formfield { font: 13px verdana, arial, helvetica; }
.aws_button {
	font-family: arial,verdana,helvetica, sans-serif;
	font-size: 12px;
	border: 1px solid #ccd7e0;
	background-image : url(/icon/other/button.gif);
}
th		{ border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 2px 1px 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:center; color: #000000; }
th.aws	{ border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 2px 1px 1px; font-size: 13px; font-weight: bold; }
td		{ border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 1px 1px 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:center; color: #000000; }
td.aws	{ border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 1px 1px 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:left; color: #000000; }
td.awr	{ border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 1px 1px 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:right; color: #000000; }
td.awsm	{ border-left-width: 0px; border-right-width: 0px; border-top-width: 0px; border-bottom-width: 0px; padding: 0px 0px 0px 0px; font: 11px verdana, arial, helvetica, sans-serif; text-align:left; color: #000000; }
b { font-weight: bold; }
a { font: 11px verdana, arial, helvetica, sans-serif; }
a:link    { color: #0011BB; text-decoration: none; }
a:visited { color: #0011BB; text-decoration: none; }
a:hover   { color: #605040; text-decoration: underline; }
div { font: 12px 'Arial','Verdana','Helvetica', sans-serif; text-align: justify; }
.CTooltip { position:absolute; top: 0px; left: 0px; z-index: 2; width: 380px; visibility:hidden; font: 8pt 'MS Comic Sans','Arial',sans-serif; background-color: #FFFFE6; padding: 8px; border: 1px solid black; }
//-->
</style>
EOF
    writeoutputfile "</head>\n";
    writeoutputfile "<body>\n";
}

# For output by date
if ($Output =~ /bydate$/ || $Output =~ /forrpm$/) {
	if (scalar keys %DateUser) {
		foreach my $dateuser (reverse sort keys %DateUser) {
			my $firstlineprinted=0;
			foreach my $logcomment (sort keys %{$DateUserLog{$dateuser}}) {
				foreach my $revision (sort keys %{$DateUserLogFileRevState{$dateuser}{$logcomment}}) {
					$revision=~/(.*)\s([\d\.]+)/;
					my ($file,$version)=($1,$2);
					if ($maxincludedver{"$file"} && (CompareVersionBis($2,$maxincludedver{"$file"}) > 0)) { debug("For file '$file' $2 > maxincludedversion= ".$maxincludedver{"$file"},3); next; }
					if ($minexcludedver{"$file"} && (CompareVersionBis($2,$minexcludedver{"$file"}) <= 0)) { debug("For file '$file' $2 <= minexcludedversion= ".$minexcludedver{"$file"},3); next; }
					if (! $firstlineprinted) {
						if ($Output =~ /forrpm$/) { writeoutputfile "* ".FormatDate($dateuser,'rpm')."\n"; }
						else { writeoutputfile FormatDate($dateuser)."\n"; }
						$firstlineprinted=1;
					}
					my $state=$DateUserLogFileRevState{$dateuser}{$logcomment}{$revision};
					$state =~ s/_forced//;
					if ($Output !~ /forrpm$/) {
						writeoutputfile "\t* ".ExcludeRepositoryFromPath($file)." $version ($state):\n";
					}
				}
				chomp $logcomment;
				$logcomment =~ s/\r$//;
				if ($firstlineprinted) {
					foreach my $logline (split(/\n/,$logcomment)) {
						if ($Output =~ /forrpm$/) { writeoutputfile "\t- $logline\n"; }
						else { writeoutputfile "\t\t$logline\n"; }
					}
				}
			}
			if ($firstlineprinted) { writeoutputfile "\n"; }
		}	
	}
	else {
		writeoutputfile "No change detected.\n";	
	}
}

# For output by file
if ($Output =~ /byfile$/) {
	if (scalar keys %FilesLastVersion) {
		foreach my $file (sort keys %FilesLastVersion) {
			my $firstlineprinted=0;
			my $val='';
			foreach my $version (reverse sort { &CompareVersion($a,$b) } keys %{$FilesChangeDate{$file}}) {
				if ($maxincludedver{"$file"} && (CompareVersionBis($version,$maxincludedver{"$file"}) > 0)) { debug("For file '$file' $version > maxincludedversion= ".$maxincludedver{"$file"},3); next; }
				if ($minexcludedver{"$file"} && (CompareVersionBis($version,$minexcludedver{"$file"}) <= 0)) { debug("For file '$file' $version <= minexcludedversion= ".$minexcludedver{"$file"},3); next; }
				if (! $firstlineprinted) {
					writeoutputfile ExcludeRepositoryFromPath($file)."\n";
					$firstlineprinted=1;
				}
				writeoutput sprintf ("\t* %-16s ",$version." (".$FilesChangeState{$file}{$version}.")");
				writeoutputfile FormatDate($FilesChangeDate{$file}{$version})."\t$FilesChangeUser{$file}{$version}\n";
				my $logcomment=$FilesChangeLog{$file}{$version};
				chomp $logcomment;
				$logcomment =~ s/\r$//;
				if ($firstlineprinted) {
					foreach my $logline (split(/\n/,$logcomment)) {
						writeoutputfile "\t\t$logline\n";
					}
				}
			}
		}	
	}
	else {
		writeoutputfile "No change detected.\n";	
	}
}

# For output by log
if ($Output =~ /bylog$/) {
	if (scalar keys %LogChange) {
		foreach my $logcomment (sort keys %LogChange) {
			my $firstlineprinted=0;
			my $newlogcomment=substr($logcomment,0,$LGMAXLOG);
			if (length($logcomment)>$LGMAXLOG) { $newlogcomment.="..."; }
			foreach my $revision (sort { &CompareVersion($a,$b) } keys %{$LogChangeDate{$logcomment}}) {
				$revision=~/^(.*)\s([\d\.]+)$/;
				my ($file,$version)=($1,$2);
				if ($maxincludedver{"$file"} && (CompareVersionBis($2,$maxincludedver{"$file"}) > 0)) { debug("For file '$file' $2 > maxincludedversion= ".$maxincludedver{"$file"},3); next; }
				if ($minexcludedver{"$file"} && (CompareVersionBis($2,$minexcludedver{"$file"}) <= 0)) { debug("For file '$file' $2 <= minexcludedversion= ".$minexcludedver{"$file"},3); next; }
				if (! $firstlineprinted) {
					writeoutputfile "$newlogcomment\n";
					$firstlineprinted=1;
				}
				$file=ExcludeRepositoryFromPath($file);
				writeoutputfile "\t* ".FormatDate($LogChangeDate{$logcomment}{$revision})." $LogChangeUser{$logcomment}{$revision}\t $file $version ($LogChangeState{$logcomment}{$revision})\n";
			}
			if ($firstlineprinted) { writeoutputfile "\n"; }
		}	
	}
	else {
		writeoutputfile "No change detected.\n";	
	}
}



# Building html report
#---------------------
if ($Output =~ /buildhtmlreport$/) {
    writeoutput("Generating HTML report...\n",1);

    my ($errorstringlines,$errorstringpie,$errorstringbars)=();
    if (!eval ('require "GD/Graph/lines.pm";')) { 
        $errorstringlines=($@?"Error: $@":"Error: Need Perl module GD::Graph::lines");
    }
    if (!eval ('require "GD/Graph/pie.pm";')) { 
        $errorstringpie=($@?"Error: $@":"Error: Need Perl module GD::Graph::pie");
    }
    if (!eval ('require "GD/Graph/bars.pm";')) { 
        $errorstringbars=($@?"Error: $@":"Error: Need Perl module GD::Graph::bars");
    }

    my $color_user="#FFF0E0";
    my $color_commit="#B0A0DD"; # my $color_commit="#9988EE";
    my $color_commit2="#C0B0ED";
    my $color_file="#AA88BB";   # my $color_file="#AA88BB";
    my $color_lines="#E0D8F0";
    my $color_lines2="#EFE2FF";
    my $color_last="#A8C0A8";   #   my $color_last="#88A495";

    my $color_lightgrey="#F8F6F8";
    my $color_grey="#CDCDCD";

    # Made some calculation on commits by user
    my %nbcommit=(); my %nbfile=();
    foreach my $user (sort keys %UserChangeCommit) {
        foreach my $file (keys %{$UserChangeCommit{$user}}) {
           $nbcommit{$user}+=$UserChangeCommit{$user}{$file};
           $nbfile{$user}++;
        }
    }

    # Made some calculation on state
    my $TotalFile=0;
    my %TotalFile=();
    my %TotalFileMonth=();
    my %TotalFileDay=();
    my $TotalCommit=0;
    my $TotalCommitMonth=0;
    my $TotalCommitDay=0;
    my %TotalUser=();
    my %TotalUserMonth=();
    my %TotalUserDay=();

    my $TotalLine=0;

    my $LastCommitDate=0;
    my %TotalCommitByState=('added'=>0,'changed'=>0,'removed'=>0);
    my %TotalCommitMonthByState=('added'=>0,'changed'=>0,'removed'=>0);
    my %TotalCommitDayByState=('added'=>0,'changed'=>0,'removed'=>0);
    my %TotalLineByState=('added'=>0,'changed'=>0,'removed'=>0);
    my %TotalLineMonthByState=('added'=>0,'changed'=>0,'removed'=>0);
    my %TotalLineDayByState=('added'=>0,'changed'=>0,'removed'=>0);

    foreach my $dateuser (reverse sort keys %DateUser) {
        $dateuser=~/(\d\d\d\d)(\d\d)(\d\d)\s+(\S+)/;
        my ($year,$month,$day,$user)=($1,$2,$3,$4);
        if ($dateuser > $LastCommitDate) { $LastCommitDate="$year$month$day"; }
    	foreach my $logcomment (sort keys %{$DateUserLog{$dateuser}}) {
    		foreach my $filerevision (sort keys %{$DateUserLogFileRevState{$dateuser}{$logcomment}}) {
                my ($file,$revision)=split(/\s+/,$filerevision);
    			my $state=$DateUserLogFileRevState{$dateuser}{$logcomment}{$filerevision};
    			$state =~ s/_forced//;
                $TotalCommitByState{$state}++;
                $TotalFile{$file}++;
                $TotalUser{$user}++;
                if ($year == $nowyear && $month == $nowmonth) {
                    $TotalCommitMonthByState{$state}++;
                    $TotalFileMonth{$file}++;
                    $TotalUserMonth{$user}++;
                }
                if ($year == $nowyear && $month == $nowmonth && $day == $nowday) {
                    $TotalCommitDayByState{$state}++;
                    $TotalFileDay{$file}++;
                    $TotalUserDay{$user}++;
                }
            }
        }
    }
    $TotalCommit=$TotalCommitByState{'added'}+$TotalCommitByState{'changed'}+$TotalCommitByState{'removed'};
    $TotalCommitMonth=$TotalCommitMonthByState{'added'}+$TotalCommitMonthByState{'changed'}+$TotalCommitMonthByState{'removed'};
    $TotalCommitDay=$TotalCommitDayByState{'added'}+$TotalCommitDayByState{'changed'}+$TotalCommitDayByState{'removed'};
    $TotalFile=$TotalCommitByState{'added'}-$TotalCommitByState{'removed'};
    
    my @absi=(); my @ordo=(); my %ordonbcommituser=();
    my $cumul=0; my %cumulnbcommituser=();

    # Made some calculation on commit by date, by user
    my %yearmonth=(); my %yearmonthusernbcommit=();
    my $minyearmonth='';
    my $maxyearmonth='';
    foreach my $dateuser (sort keys %DateUser) {  # By ascending date
        my ($date,$user)=split(/\s+/,$dateuser);
        if ($date =~ /^(\d\d\d\d)(\d\d)(\d\d)/) {
            my ($year,$month,$day)=($1,$2,$3);
        	foreach my $logcomment (sort keys %{$DateUserLog{$dateuser}}) {
        		foreach my $revision (sort keys %{$DateUserLogFileRevState{$dateuser}{$logcomment}}) {
                    my ($add,$del)=split(/\s+/,$DateUserLogFileRevLine{$dateuser}{$logcomment}{$revision});
                    my $delta=int($add)+int($del);
                    $yearmonthusernbcommit{$user}{"$year$month"}++;
                    $yearmonth{"$year$month"}+=$delta;
                    if ($delta >=0) {
                        $TotalLineByState{'added'}+=$delta;
                        $TotalLineByState{'changed'}+=(int($add)-$delta);
                    } else {
                        $TotalLineByState{'removed'}+=$delta;
                        $TotalLineByState{'changed'}+=(-int($del)+$delta);
                    }
                    if ($year == $nowyear && $month == $nowmonth) {
                        if ($delta >=0) {
                            $TotalLineMonthByState{'added'}+=$delta;
                            $TotalLineMonthByState{'changed'}+=(int($add)-$delta);
                        } else {
                            $TotalLineMonthByState{'removed'}+=$delta;
                            $TotalLineMonthByState{'changed'}+=(-int($del)+$delta);
                        }
                    }
                    if ($year == $nowyear && $month == $nowmonth && $day == $nowday) {
                        if ($delta >=0) {
                            $TotalLineDayByState{'added'}+=$delta;
                            $TotalLineDayByState{'changed'}+=(int($add)-$delta);
                        } else {
                            $TotalLineDayByState{'removed'}+=$delta;
                            $TotalLineDayByState{'changed'}+=(-int($del)+$delta);
                        }
                    }
                }
            }
            if ($TotalLineByState{'removed'}==0) { $TotalLineByState{'removed'}="-0"; }
            if ($TotalLineMonthByState{'removed'}==0) { $TotalLineMonthByState{'removed'}="-0"; }
            if ($TotalLineDayByState{'removed'}==0) { $TotalLineDayByState{'removed'}="-0"; }
            if (! $minyearmonth) { $minyearmonth="$year$month"; }
            $maxyearmonth="$year$month";
        }
    }
    $TotalLine=$TotalLineByState{'added'}+$TotalLineByState{'removed'};

    # Define absi and ordo and complete holes
    # We start with cursor = lower YYYYMM
    my $cursor=$minyearmonth;
    do {
        push @absi, substr($cursor,0,4)."-".substr($cursor,4,2);
        $cumul+=$yearmonth{$cursor};
        push @ordo, ($cumul>=0?$cumul:0);	# $cumul should not be negative but occurs sometimes when cvs errors
        foreach my $user (keys %yearmonthusernbcommit) {
            $cumulnbcommituser{$user}+=$yearmonthusernbcommit{$user}{$cursor};
            if ($yearmonthusernbcommit{$user}{$cursor}) { push @{$ordonbcommituser{$user}}, $yearmonthusernbcommit{$user}{$cursor}; }
            else { push @{$ordonbcommituser{$user}}, 0; }
        }
        $cursor=~/(\d\d\d\d)(\d\d)/;
        # Increase cursor for next month
        $cursor=sprintf("%04d%02d",(int($1)+(int($2)>=12?1:0)),(int($2)>=12?1:(int($2)+1)));
    }
    until ($cursor > $maxyearmonth);

    
writeoutputfile <<EOF;
<a name="top">&nbsp;</a>
EOF


# PARAMETERS
#-----------
writeoutputfile <<EOF;
<table width="100%"><tr><td>
<a name="parameters">&nbsp;</a><br>
<table class="aws_border" border="0" cellpadding="2" cellspacing="0">
<tr><td class="aws_title" width="70%">CVS analysis' parameters</td><td class="aws_blank">&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data parameters" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF

writeoutputfile "<tr><td class=\"aws\" width=\"200\" colspan=2>Project&nbsp;module&nbsp;name</td><td class=\"aws\" width=\"400\"><b>$Module</b></td></tr>\n";
writeoutputfile "<tr><td class=\"aws\" width=\"200\" colspan=2>CVS&nbsp;root&nbsp;used</td><td class=\"aws\" width=\"400\"><b>$CvsRoot</b></td></tr>\n";
writeoutputfile "<tr><td class=\"aws\" colspan=2>Range&nbsp;analysis</td><td class=\"aws\"><b>$rangestring</b></td></tr>\n";
writeoutputfile "<tr><td class=\"aws\" colspan=2>Date&nbsp;analysis</td><td class=\"aws\"><b>".FormatDate("$nowyear-$nowmonth-$nowday $nowhour:$nowmin")."</b></td></tr>\n";

writeoutputfile <<EOF;
</table></td></tr></table>
EOF


# LINKS
#------
writeoutputfile <<EOF;
</td><td>
$headstring<br>
<a href="#summary">Summary</a> &nbsp; 
<a href="#linesofcode">Lines&nbsp;of&nbsp;code</a> &nbsp; 
<a href="#developers">Developers&nbsp;activity</a> &nbsp; 
<a href="#daysofweek">Days&nbsp;of&nbsp;week</a> &nbsp; 
<a href="#hours">Hours</a> &nbsp;
<a href="#tags">Tags</a> &nbsp;
<a href="#lastlogs">Last&nbsp;commits</a> &nbsp;
</td></tr></table>
<br />
EOF


# SUMMARY
#--------
writeoutputfile <<EOF;
<a name="summary">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Summary</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data summary" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF

writeoutputfile "<tr bgcolor=\"#FFF0E0\"><th colspan=\"2\">Current status indicators</th><th width=\"180\">Value</th><th width=\"180\">&nbsp;</th><th width=\"180\">&nbsp;</th></tr>\n";

writeoutputfile "<tr><td class=\"aws\">Files currently in repository</td><td bgcolor=\"$color_file\" width=\"10\">&nbsp;</td>";
writeoutputfile "<td width=\"180\" align=\"center\">".($TotalFile>0?"<b>$TotalFile</b>":"0")."</td>";
writeoutputfile "<td width=\"360\" colspan=\"2\">&nbsp;</td>";
writeoutputfile "</tr>\n";

writeoutputfile "<tr><td class=\"aws\">Lines of code currently in repository (on non binary files only)</td><td bgcolor=\"$color_lines\" width=\"10\">&nbsp;</td>";
writeoutputfile "<td width=\"180\">".($TotalLine>0?"<b>$TotalLine</b>":"0")."</td>";
writeoutputfile "<td width=\"360\" colspan=\"2\">&nbsp;</td>";
writeoutputfile "</tr>\n";


writeoutputfile "<tr bgcolor=\"#FFF0E0\"><th width=\"200\" colspan=\"2\">Activity indicators</th><th width=\"180\">From start</th><th width=\"180\">This month</th><th width=\"180\">Today</th></tr>\n";

writeoutputfile "<tr><td class=\"aws\">Number of developers</td><td bgcolor=\"$color_user\" width=\"10\">&nbsp;</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUser?"<b>".(scalar keys %TotalUser)."</b>":"0")."</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUserMonth?"<b>".(scalar keys %TotalUserMonth)."</b>":"0")."</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUserDay?"<b>".(scalar keys %TotalUserDay)."</b>":"0")."</td>";
writeoutputfile "</tr>\n";

writeoutputfile "<tr><td class=\"aws\">Number of commits</td><td bgcolor=\"$color_commit\"></td>";
writeoutputfile "<td>".($TotalCommit?"<b>$TotalCommit</b>":"0")."</td>";
writeoutputfile "<td>".($TotalCommitMonth?"<b>$TotalCommitMonth</b>":"0")."</td>";
writeoutputfile "<td>".($TotalCommitDay?"<b>$TotalCommitDay</b>":"0")."</td>";
writeoutputfile "</tr>\n";

writeoutputfile "<tr><td class=\"aws\" valign=\"top\">Number of commits by status</td><td bgcolor=\"$color_commit2\" class=\"aws\">&nbsp;</td>";
writeoutputfile "<td valign=\"top\">".($TotalCommitByState{'added'}?"<b>$TotalCommitByState{'added'}</b> to add new file<br>":"").($TotalCommitByState{'changed'}?"<b>$TotalCommitByState{'changed'}</b> to change existing file<br>":"").($TotalCommitByState{'removed'}?"<b>$TotalCommitByState{'removed'}</b> to remove file":"")."&nbsp;</td>";
writeoutputfile "<td valign=\"top\">".($TotalCommitMonthByState{'added'}?"<b>$TotalCommitMonthByState{'added'}</b> to add new file<br>":"").($TotalCommitMonthByState{'changed'}?"<b>$TotalCommitMonthByState{'changed'}</b> to change existing file<br>":"").($TotalCommitMonthByState{'removed'}?"<b>$TotalCommitMonthByState{'removed'}</b> to remove file":"")."&nbsp;</td>";
writeoutputfile "<td valign=\"top\">".($TotalCommitDayByState{'added'}?"<b>$TotalCommitDayByState{'added'}</b> to add new file<br>":"").($TotalCommitDayByState{'changed'}?"<b>$TotalCommitDayByState{'changed'}</b> to change existing file<br>":"").($TotalCommitDayByState{'removed'}?"<b>$TotalCommitDayByState{'removed'}</b> to remove file":"")."&nbsp;</td>";
writeoutputfile "</tr>\n";

writeoutputfile "<tr><td class=\"aws\">Different files commited</td><td bgcolor=\"$color_file\">&nbsp;</td>";
writeoutputfile "<td>".(scalar keys %TotalFile?"<b>".(scalar keys %TotalFile)."</b>":"0")."</td>";
writeoutputfile "<td>".(scalar keys %TotalFileMonth?"<b>".(scalar keys %TotalFileMonth)."</b>":"0")."</td>";
writeoutputfile "<td>".(scalar keys %TotalFileDay?"<b>".(scalar keys %TotalFileDay)."</b>":"0")."</td>";
writeoutputfile "</tr>\n";

writeoutputfile "<tr><td class=\"aws\">Lines added / modified / removed (on non binary files only)</td><td bgcolor=\"$color_lines\" width=\"10\">&nbsp;</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUser?"<b>":"")."+$TotalLineByState{'added'} / $TotalLineByState{'changed'} / $TotalLineByState{'removed'}".(scalar keys %TotalUser?"</b>":"")."</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUserMonth?"<b>":"")."+$TotalLineMonthByState{'added'} / $TotalLineMonthByState{'changed'} / $TotalLineMonthByState{'removed'}".(scalar keys %TotalUserMonth?"</b>":"")."</td>";
writeoutputfile "<td width=\"180\">".(scalar keys %TotalUserDay?"<b>":"")."+$TotalLineDayByState{'added'} / $TotalLineDayByState{'changed'} / $TotalLineDayByState{'removed'}".(scalar keys %TotalUserDay?"</b>":"")."</td>";
writeoutputfile "</tr>\n";

# Last commit
my $pos=1;
if ($LastCommitDate >= int("$nowyear${nowmonth}01")) { $pos=2; }
if ($LastCommitDate >= int("$nowyear$nowmonth$nowday")) { $pos=3; }
writeoutputfile "<tr><td class=\"aws\">Last commit</td><td bgcolor=\"$color_last\">&nbsp;</td><td><b>".($pos>=1?FormatDate($LastCommitDate):"&nbsp;")."</b></td><td><b>".($pos>=2?FormatDate($LastCommitDate):"&nbsp;")."</b></td><td><b>".($pos>=3?FormatDate($LastCommitDate):"&nbsp;")."</b></td></tr>\n";

writeoutputfile <<EOF;
</table></td></tr></table><br />
EOF


# LINES OF CODE
#--------------
writeoutputfile <<EOF;
<a name="linesofcode">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Lines of code*</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td class="aws" colspan="2">
<table class="aws_data month" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">

<tr><td class="aws">
This chart represents the balance between number of lines added and removed in non binary files (source files).<br>
<center>
EOF

writeoutputfile "<table width=\"100%\">";
#writeoutputfile "<tr><td align=\"left\" colspan=\"3\">This chart represents the balance between number of lines added and removed in non binary files (source files).</td></tr>\n";
writeoutputfile "<tr><td>&nbsp;</td>";
# Build chart
if ($errorstringlines) {
    writeoutputfile "<td>Perl module GD::Graph::lines must be installed to get charts</td>";   
}
else {
    my $MAXABS=15;  # TODO limit to 10
    my $col="#706880"; $col=~s/#//;
    # Build graph
    my $pngfile="${OutputRootFile}_codelines.png";
    my @data = ([@absi],[@ordo]);
    my $graph = GD::Graph::lines->new(640, 240);
    $graph->set( 
          #title             => 'Some simple graph',
          transparent       => 1,
          x_label           => 'Month', x_label_position =>0.5, x_label_skip =>6, x_all_ticks =>1, x_long_ticks =>0, x_ticks =>1,
          y_label           => 'Code lines', y_min_value =>0, y_label_skip =>1, y_long_ticks =>1, y_tick_number =>10, #y_number_format   => "%06d",
          boxclr            => $color_lightgrey,
          fgclr             => $color_grey,
          line_types        => [1, 2, 3],
          dclrs             => [ map{ sprintf("#%06x",(hex($col)+(hex("050503")*$_))) } (0..($MAXABS-1)) ]
          #borderclrs        => [ qw(blue green pink blue) ],
    ) or die $graph->error;
#    # Defini la légende
#    $graph->set_legend(("All developers"));
#    $graph->set_legend_font("");
#    $graph->set(legend_placement=>'Right');
    my $gd = $graph->plot(\@data) or die $graph->error;
    open(IMG, ">${OutputDir}$pngfile") or die "Error $!";
    binmode IMG;
    print IMG $gd->png;
    close IMG;
    # End build graph
    writeoutputfile "<td><br><img src=\"$pngfile\" border=\"0\"></td>";
}
writeoutputfile "<td>&nbsp;</td></tr>\n";
writeoutputfile "</table>\n";

writeoutputfile <<EOF;
</center>
</td></tr></table></td></tr></table><br />
EOF


# BY DEVELOPERS
#--------------
my $MAXABS=5;
writeoutputfile <<EOF;
<a name="developers">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Developers activity*</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data users" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
<tr bgcolor="#FFF0E0"><th width="140">Developer</th><th bgcolor="$color_commit" width="140">Number of commits</th><th bgcolor="$color_file" width="140">Different files commited</th><th bgcolor="$color_lines" width="140">Lines*<br>(added, modified, removed)</th><th bgcolor="$color_lines2" width="140">Lines by commit*<br>(added, modified, removed)</th><th bgcolor="$color_last" width="140">Last commit</th><th>&nbsp; </th></tr>
EOF

foreach my $developer (reverse sort { $nbcommit{$a} <=> $nbcommit{$b} } keys %nbcommit) {
    writeoutputfile "<tr><td class=\"aws\">";
    writeoutputfile $developer;
    writeoutputfile "</td><td>";
    writeoutputfile $nbcommit{$developer};
    writeoutputfile "</td><td>";
    writeoutputfile $nbfile{$developer};
    writeoutputfile "</td><td>";
    writeoutputfile $UserChangeLineAdd{$developer}." / ".$UserChangeLineChange{$developer}." / ".$UserChangeLineDel{$developer};
    writeoutputfile "</td><td>";
    writeoutputfile RoundNumber($UserChangeLineAdd{$developer}/$nbcommit{$developer},1)." / ".RoundNumber($UserChangeLineChange{$developer}/$nbcommit{$developer},1)." / ".RoundNumber($UserChangeLineDel{$developer}/$nbcommit{$developer},1);
    writeoutputfile "</td><td>";
    writeoutputfile FormatDate($UserChangeLast{$developer},'simple');
    writeoutputfile "</td>";
    writeoutputfile "<td>&nbsp;</td>";
    writeoutputfile "</tr>";
}

# Define another hash limited to $MAXABS
my $i=0;
my %newnbcommit=();
my $libother="Others (".((scalar keys %nbcommit) - $MAXABS).")";
foreach my $developer (reverse sort { $nbcommit{$a} <=> $nbcommit{$b} } keys %nbcommit) {
    $i++;
    if ($i <= $MAXABS) {
        $newnbcommit{$developer}=$nbcommit{$developer};
    } else {
        $newnbcommit{$libother}+=$nbcommit{$developer};
    }
}
$i=0;
my %newnbfile=();
$libother="Others (".((scalar keys %nbfile) - $MAXABS).")";
foreach my $developer (reverse sort { $nbfile{$a} <=> $nbfile{$b} } keys %nbfile) {
    $i++;
    if ($i <= $MAXABS) {
        $newnbfile{$developer}=$nbfile{$developer};
    } else {
        $newnbfile{$libother}+=$nbfile{$developer};
    }
}

if (scalar keys %newnbcommit > 1) {
    if ($errorstringpie) {
        writeoutputfile "<tr><td colspan\"7\">Perl module GD::Graph::pie must be installed to get charts</td></tr>";
    }
    else {
        # Build graph for developer commit ratio, hash used: newnbcommit{developer}=nb
        my $col=$color_commit; $col=~s/#//;
        my $pngfilenbcommit="${OutputRootFile}_developerscommit.png";
        my @data = ([keys %newnbcommit],[values %newnbcommit]);
        my $graph = GD::Graph::pie->new(170, 138);
        $graph->set( 
              title             => "Number of commits",
              axislabelclr      => qw(black),
              textclr           => $color_commit,
              transparent       => 1,
              accentclr         => $color_grey,
              dclrs             => [ map{ sprintf("#%06x",(hex($col)+(hex("050501")*$_))) } (0..((scalar keys %newnbcommit)-1)) ]
        ) or die $graph->error;
        my $gd = $graph->plot(\@data) or die $graph->error;
        open(IMG, ">${OutputDir}$pngfilenbcommit") or die $!;
        binmode IMG;
        print IMG $gd->png;
        close IMG;
        # End build graph
        # Build graph for developer file ratio, hash used: newnbfile{developer}=nb
        my $pngfilefile="${OutputRootFile}_developersfile.png";
        my @data = ([keys %newnbfile],[values %newnbfile]);
        my $graph = GD::Graph::pie->new(170, 138);
        $col=$color_file; $col=~s/#//;
        $graph->set( 
              title             => 'Different files',
              axislabelclr      => qw(black),
              textclr           => $color_file,
              transparent       => 1,
              accentclr         => $color_grey,
              dclrs             => [ map{ sprintf("#%06x",(hex($col)+(hex("050503")*$_))) } (0..((scalar keys %newnbfile)-1)) ]
        ) or die $graph->error;
        my $gd = $graph->plot(\@data) or die $graph->error;
        open(IMG, ">${OutputDir}$pngfilefile") or die $!;
        binmode IMG;
        print IMG $gd->png;
        close IMG;
        # End build graph
        writeoutputfile "<tr><td colspan=\"7\"><br><img src=\"$pngfilenbcommit\" border=\"0\"> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; <img src=\"$pngfilefile\" border=\"0\"></td></tr>\n";
    }
}
# Number of commits by developer in time
$MAXABS=15; # TODO Mettre limit en utilisant newnbcommit au lieu de nbcommit mais necessite pour ca un newordonbcommituser
if (scalar keys %nbcommit > 0) {
    if ($errorstringpie) {
        writeoutputfile "<tr><td colspan\"7\">Perl module GD::Graph::pie must be installed to get charts</td></tr>";
    }
    else {
        my $TICKSNB=10;
        my $col=$color_commit; $col=~s/#//;
        # Build graph for activity by developer
        my $pngfile="${OutputRootFile}_commitshistorybyuser.png";

        my $maxordo=0;
        my @data = ();
        my @legend = ();
        #my @absi = ();
        push @data, [@absi];
        my $numdev=0;
        foreach my $developer (reverse sort { $nbcommit{$a} <=> $nbcommit{$b} } keys %nbcommit) {
            my @ordo=();
            $numdev++;
            if ($numdev > $MAXABS) { last; }
            debug("Add array for developer=$developer",3);
            foreach my $val (@{$ordonbcommituser{$developer}}) {
                if ($val > $maxordo) { $maxordo=$val; }
            }
            push @data, [@{$ordonbcommituser{$developer}}];
            push @legend, ucfirst($developer);
        }
        # We level value for maxordo;
        $maxordo=int($maxordo*1.05+1);
    
        my $graph = GD::Graph::lines->new(640+40, 240);
        $graph->set( 
              #title             => 'Some simple graph',
              transparent       => 1,
              x_label           => 'Month', x_label_position =>0.5, x_label_skip =>6, x_all_ticks =>1, x_long_ticks =>0, x_ticks =>1,
              y_label           => 'Number of commits', y_min_value =>0, y_max_value =>$maxordo, y_label_skip =>1, y_long_ticks =>1, y_tick_number=>$TICKSNB,  #y_number_format   => "%06d",
              textclr           => $color_commit,
              boxclr            => $color_lightgrey,
              fgclr             => $color_grey,
#              line_types        => [1, 2, 3],
#              dclrs             => [ map{ sprintf("#%06x",(hex($col)+(hex("050503")*$_))) } (0..($MAXABS-1)) ]
              #borderclrs        => [ qw(blue green pink blue) ],
        ) or die $graph->error;
        # Defini la légende
        $graph->set_legend(@legend);
        $graph->set_legend_font("");
        $graph->set(legend_placement=>'Right');
        my $gd = $graph->plot(\@data) or die $graph->error;
        open(IMG, ">${OutputDir}$pngfile") or die $!;
        binmode IMG;
        print IMG $gd->png;
        close IMG;
        # End build graph
        writeoutputfile "<tr><td colspan=\"7\"><br><img src=\"$pngfile\" border=\"0\"></td></tr>";
    }
}
writeoutputfile <<EOF;
</table>
</td></tr></table><br />
EOF


# BY DAYS OF WEEK
#----------------
writeoutputfile <<EOF;
<a name="daysofweek">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Activity by days of week</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data daysofweek" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF

if ($errorstringbars) {
    writeoutputfile "<tr><td>Perl module GD::Graph::bars must be installed to get charts</td></tr>";
}
else {
    my @absi=('Mon','Tue','Wed','Thi','Fri','Sat','Sun'); my @ordo=(); my $cumul=0;
    # We need to build array values for chart
    foreach my $dateuser (sort keys %DateUser) {
        my ($date,$user)=split(/\s+/,$dateuser);
        my $dayofweek=&DayOfWeek($date);
        $ordo[$dayofweek-1]+=$DateUser{"$dateuser"};
    }
    # Build graph
    my $pngfile="${OutputRootFile}_daysofweek.png";
    my @data = ([@absi],[@ordo]);
    my $graph = GD::Graph::bars->new(260, 200);
    $graph->set( 
          #title             => 'Some simple graph',
          transparent       => 1,
          x_label           => 'Days of week', x_label_position =>0.5, x_all_ticks =>1, x_long_ticks =>0, x_ticks =>1, x_number_format => "%02d",
          y_label           => 'Number of commits', y_min_value =>0, y_label_skip =>1, y_long_ticks =>1, y_tick_number =>10, #y_number_format   => "%06d",
          textclr           => $color_commit,
          boxclr            => $color_lightgrey,
          fgclr             => $color_grey,
          dclrs             => [ $color_commit ],
          accentclr         => "#444444",
          #borderclrs        => [ qw(blue green pink blue) ],
    ) or die $graph->error;
    my $gd = $graph->plot(\@data) or die $graph->error;
    open(IMG, ">${OutputDir}$pngfile") or die $!;
    binmode IMG;
    print IMG $gd->png;
    close IMG;
    # End build graph
    writeoutputfile "<tr><td align=\"center\"><br><img src=\"$pngfile\" border=\"0\"></td></tr>";
}

writeoutputfile <<EOF;
</table></td></tr></table><br />
EOF


# BY HOURS
#---------
writeoutputfile <<EOF;
<a name="hours">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Activity by hours</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data hours" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF
if ($errorstringbars) {
    writeoutputfile "<tr><td>Perl module GD::Graph::bars must be installed to get charts</td></tr>";
}
else {
    my @absi=(0..23); my @ordo=(); my $cumul=0;
    # We need to build array values for chart
    foreach my $houruser (sort keys %HourUser) {
        my ($hour,$user)=split(/\s+/,$houruser);
        $ordo[int($hour)]+=$HourUser{"$houruser"};
    }
    # Build graph
    my $pngfile="${OutputRootFile}_hours.png";
    my @data = ([@absi],[@ordo]);
    my $graph = GD::Graph::bars->new(640, 240);
    $graph->set( 
          #title             => 'Some simple graph',
          transparent       => 1,
          x_label           => 'Hours', x_label_position =>0.5, x_all_ticks =>1, x_long_ticks =>0, x_ticks =>1, x_number_format => "%02d",
          y_label           => 'Number of commits', y_min_value =>0, y_label_skip =>1, y_long_ticks =>1, y_tick_number =>10, #y_number_format   => "%06d",
          textclr           => $color_commit,
          boxclr            => $color_lightgrey,
          fgclr             => $color_grey,
          dclrs             => [ $color_commit ],
          accentclr         => "#444444",
          #borderclrs        => [ qw(blue green pink blue) ],
    ) or die $graph->error;
    my $gd = $graph->plot(\@data) or die $graph->error;
    open(IMG, ">${OutputDir}$pngfile") or die $!;
    binmode IMG;
    print IMG $gd->png;
    close IMG;
    # End build graph
    writeoutputfile "<tr><td align=\"center\"><br><img src=\"$pngfile\" border=\"0\"></td></tr>";
}

writeoutputfile <<EOF;
</table></td></tr></table><br />
EOF


my $widthdate=90;
my $widthfulldate=160;
my $widthdev=90;
my $widthtag=100;

# TAGS
#-----
writeoutputfile <<EOF;
<a name="tags">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Last tags by date</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data tags" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF
writeoutputfile "<tr bgcolor=\"#FFF0E0\">";
writeoutputfile "<th width=\"$widthdate\">Date</th>";
writeoutputfile "<th width=\"$widthfulldate\">Full date</th>";
writeoutputfile "<th width=\"80\">Tags</th>";
writeoutputfile "<th>&nbsp;</th>";
writeoutputfile "</tr>\n";
foreach my $tag (reverse sort { $tagsfulldate{$a} <=> $tagsfulldate{$b} } keys %tagsfulldate) {
    writeoutputfile "<tr>";
    writeoutputfile "<td valign=\"top\">".FormatDate($tagsshortdate{$tag})."</td>";
    writeoutputfile "<td valign=\"top\">";
  	writeoutputfile FormatDate($tagsfulldate{$tag},'simple');
    writeoutputfile "</td>";
    writeoutputfile "<td valign=\"top\">";
  	writeoutputfile "$tag";
    writeoutputfile "</td>";
    writeoutputfile "<td>&nbsp;</td>";
    writeoutputfile "</tr>\n";
}
writeoutputfile <<EOF;
</table></td></tr></table><br />
EOF


# LASTLOGS
#---------
my $cursor=0;
writeoutputfile <<EOF;
<a name="lastlogs">&nbsp;</a><br />
<table class="aws_border" border="0" cellpadding="2" cellspacing="0" width="100%">
<tr><td class="aws_title" width="70%">Last commit logs</td><td class="aws_blank"><a href="#top">Top</a>&nbsp;</td></tr>
<tr><td colspan="2">
<table class="aws_data lastlogs" border="2" bordercolor="#ECECEC" cellpadding="2" cellspacing="0" width="100%">
EOF
writeoutputfile "<tr bgcolor=\"#FFF0E0\">";
writeoutputfile "<th width=\"80\">Tags</th>";
writeoutputfile "<th width=\"$widthdate\">Date</th><th width=\"$widthdev\">Developer</th><th>Last ".($MAXLASTLOG?"$MAXLASTLOG ":"")."Commit Logs</th></tr>";
foreach my $dateuser (reverse sort keys %DateUser) {
    my ($date,$user)=split(/\s+/,$dateuser);

    writeoutputfile "<tr>";
	my $shortdate=$date;
    writeoutputfile "<td valign=\"top\">";
	if (keys %{$tagstags{$shortdate}}) {
		foreach my $tag (reverse sort keys %{$tagstags{$shortdate}}) {
	    	writeoutputfile "$tag<br>";
		}
	} else {
	    	writeoutputfile "&nbsp;";
	}
    writeoutputfile "</td>";
    writeoutputfile "<td valign=\"top\">".FormatDate($date)."</td>";
    writeoutputfile "<td valign=\"top\">".$user."</td>";
    writeoutputfile "<td class=\"aws\">";
	foreach my $logcomment (sort keys %{$DateUserLog{$dateuser}}) {
        $cursor++;
        my $comment=$logcomment;
		chomp $comment;
		$comment =~ s/\r$//;
		foreach my $logline (split(/\n/,$comment)) {
			writeoutputfile "<b>".CleanFromTags($logline)."</b><br>\n";
		}
		foreach my $filerevision (reverse sort keys %{$DateUserLogFileRevState{$dateuser}{$logcomment}}) {
			$filerevision=~/(.*)\s([\d\.]+)/;
			my ($file,$version)=($1,$2);
			if ($maxincludedver{"$file"} && (CompareVersionBis($2,$maxincludedver{"$file"}) > 0)) { debug("For file '$file' $2 > maxincludedversion= ".$maxincludedver{"$file"},3); next; }
			if ($minexcludedver{"$file"} && (CompareVersionBis($2,$minexcludedver{"$file"}) <= 0)) { debug("For file '$file' $2 <= minexcludedversion= ".$minexcludedver{"$file"},3); next; }
			my $state=$DateUserLogFileRevState{$dateuser}{$logcomment}{$filerevision};
			$state =~ s/_forced//;
#			writeoutputfile "* ".FormatCvsFileLink(ExcludeRepositoryFromPath($file,0,0),$version)." $version (".FormatState($state);
			writeoutputfile "* ".FormatCvsFileLink(ExcludeRepositoryFromPath($file,0,1),$version)." $version (".FormatState($state);
			my $lines=$DateUserLogFileRevLine{$dateuser}{$logcomment}{$filerevision};
			writeoutputfile ($state eq 'added' && $lines?" <font color=\"#008822\">$lines</font>":"");
			writeoutputfile ($state eq 'changed' && $lines?" <font color=\"#888888\">$lines</font>":"");
			writeoutputfile ($state eq 'removed' && $lines?" <font color=\"#880000\">$lines</font>":"");
            if ($ViewCvsUrl && $DateUserLogFileRevLine{$dateuser}{$logcomment}{$filerevision} !~ /binary/) {
                if ($state eq 'changed') {
			        writeoutputfile ", ".FormatCvsDiffLink(ExcludeRepositoryFromPath($file),$version);
			    }
            }
			writeoutputfile ")<br>\n";
		}
        if ($MAXLASTLOG && $cursor >= $MAXLASTLOG) { last; }
	}
    writeoutputfile "</td></tr>";
    if ($MAXLASTLOG && $cursor >= $MAXLASTLOG) {
        my $rest="some"; # TODO put here value of not shown commits
        writeoutputfile "<tr><td valign=\"top\" colspan=\"3\" align=\"left\">Other commits are hidden...</td></tr>";
        last;
    }
}	

writeoutputfile <<EOF;
</table></td></tr></table><br />
EOF

}   # End buildhtmlreport

# Footer
if ($Output =~ /buildhtmlreport$/) {
    writeoutputfile "<table width=100%><tr><td class=\"aws\"><div style=\"align: left; font-size:11px; \">* on non binary files only</div></td>";
	writeoutputfile "<td class=\"awr\"><b><a href=\"http://cvschangelogb.sourceforge.net\" target=\"awstatshome\">Created by $PROG $VERSION</a></b></td></tr></table>";
    writeoutputfile "<br />\n";
	writeoutputfile "</body>\n</html>\n";
}


# Start of true output
if ($OutputDir) {
    close FILE;
}

if (! $KeepRlogFile) {
    writeoutput("Remove temporary rlog file\n",1);
    unlink "$RLogFile";
}

print STDERR ucfirst($PROG)." finished successfully.\n";


0;
