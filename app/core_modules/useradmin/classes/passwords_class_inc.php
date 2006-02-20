<?
/**
* Class to generate alphanumeric passwords
*
* @author James Scoble
*/

class passwords
{
    // $letters is an array that will hold all the alphabetic components for the passwords
    var $letters;

    function passwords()
    {
        $this->init();
    }
    
    function init()
    {
        // Vowels
        $this->letters['vowels']=array('a','e','i','o','u','oo','ai','ae','y','ee','au');
        // Sharp Consonants
        $this->letters['sharpletters']=array('b','c','d','g','j','k','p','qu','t','x');
        // Long Consonants
        $this->letters['longletters']=array('f','h','l','m','n','r','s','v','w','y','z','th','sh');
        // End-of-word consonants
        $this->letters['endletters']=array('ck','nd','st','rd','ng','dle','nt','ld','rk');
        // Non-alphanumeric ASCII characters
        $this->letters['topkeys']=array('!','@','#','$','%','&','*',);
    }

    /**
    * This is a method to select an element at random from a specified array
    * @param string $arr the index of the $letters array to use
    * @returns string $get
    */
    function getRandom($arr)
    {
        $count=count($this->letters[$arr]);
        $rnum=rand(0,($count-1));
        $get=$this->letters[$arr][$rnum];
        //if (rand(0,2)){
        //    $get=ucfirst($get);
        //}
        return $get;
    }

    /**
    * This is a method to produce the alphabetic section of the password
    * @returns string $word - a pseudo-word generated from basic elements
    */
    function makeWord()
    {
        $word='';
        $word.=$this->getRandom('sharpletters');
        $word.=$this->getRandom('vowels');
        $word.=$this->getRandom('longletters');
        $word.=$this->getRandom('sharpletters');
        $word.=$this->getRandom('vowels');
        $word.=$this->getRandom('endletters');
        return $word;
    }

    /**
    * This is the method that makes the password
    * @returns string $word
    */
    function createPassword()
    {
        $word=$this->makeWord().$this->getRandom('topkeys').rand(100,999);
        return $word;
    }

}
?>
