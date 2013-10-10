<?php
class nav extends object {

    public function  init() {
        $this->loadclass('link','htmlelements');
    }

    public  function show() {
        /*      $twobtalent=new link($this->uri(array('action'=>'viewstory','category'=> 'twobtalent')));
        $twobtalent->extra=' id= "twobtalent"';
        $twobtalent->link='<span>two be talent</span>';

        $aboutus=new link($this->uri(array('action'=>'viewstory','category'=> 'aboutus')));
        $aboutus->extra=' id= "aboutus"';
        $aboutus->link='<span>about us</span>';

        $poems=new link($this->uri(array('action'=>'viewstory','category'=> 'poems')));
        $poems->extra=' id= "poems"';
        $poems->link='<span>poems</span>';

        $onlinegal=new link($this->uri(array('action'=>'viewstory','category'=> 'onlinegal')));
        $onlinegal->extra=' id= "onlinegal"';
        $onlinegal->link='<span>onlinegal</span>';
        */

        $aboutus=new link($this->uri(array('action'=>'viewstory','category'=> 'aboutus')));
        $aboutus->link='<h3>About Us</h3>';

        $mission=new link($this->uri(array('action'=>'viewstory','category'=> 'mission')));
        $mission->link='<h3>Our Mission</h3>';

        $values=new link($this->uri(array('action'=>'viewstory','category'=> 'values')));
        $values->link='<h3>Values</h3>';

        $strategicboard=new link($this->uri(array('action'=>'viewstory','category'=> 'strategicboard')));
        $strategicboard->link='<h3>Strategic Board</h3>';

        $contactus=new link($this->uri(array('action'=>'viewstory','category'=> 'contactus')));
        $contactus->link='<h3>Contact Us</h3>';

        $str='<br/><br/>
            <ul id="xnav-secondary">';
        $str.='<li>'.$aboutus->show().'</li>';
        $str.='<li>'.$mission->show().'</li>';
        $str.='<li>'.$values->show().'</li>';
        $str.='<li>'.$strategicboard->show().'</li>';
        $str.='<li>'.$contactus->show().'</li>';
        $str.="</ul>";

        return $str;
    }
}

?>
