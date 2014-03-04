
<?php

$content = <<<abcbcadddggg
<div class="jukskeileft">

<div class="jukskeitopicintroduction">
<div class="jukskeiinner">
<h2 class="jukskeiintroheading">Living off the river</h2>
This is a general introducFon to the topic for this page, and should
touch on the different subtopics. Aenean et luctus dui. Nam nec
erat in dolor sollicitudin mollis. Quisque sagiPs aliquam nisl
Fncidunt Fncidunt. Phasellus sed eros arcu. In hac habiÅ tasse
platea dictumst. . Sed accumsan aliquet nulla, eget sodales nisi
fermentum in. Aenean et luctus dui. Nam nec erat in dolor
sollicitudin mollis. Quisque sagiPs aliquam nisl Fncidunt Fncidunt.
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst.
Phasellus sed eros arcu. Aenean et luctus dui. Nam nec erat in
dolor sollicitudin mollis.
</div>
</div>

<div class="jukskeiarticle">
<div class="jukskeiinner">
<h2 class="jukskeiarticleheading">The lifeblood of a community</h2>
<h3 class="jukskeiarticleauthor">By student three</h3>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesFbulum malesuada
semper neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet nulla,
eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor
sollicitudin mollis. Quisque sagiPs aliquam nisl Fncidunt Fncidunt. Phasellus
sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus, scelerisque
dapibus trisFque fermentum, condimentum at libero. Nam odio lacus, mollis at
sodales sit amet, hendrerit quis lectus. Quisque a nunc ut mauris rutrum
sollicitudin id vitae tortor.
</div>
</div>

<div class="jukskeivideo">
<div class="jukskeiinner">
[YOUTUBE]http://www.youtube.com/watch?v=PgExc5zJJ9U[/YOUTUBE]
<h2 class="jukskeivideocaption">'We baptise our children in this river'</h2>
<h3 class="jukskeivideotitle">VIDEO | The Jukskei is a focal point for many of
Joburgís religious groups.</h3>
<ul class="jukskeivideocontributors">
<li>Smart Student 1 - Photography</li>
<li>Smart Student 2 - Audio</li>
<li>Smart Student 1 - Production</li>
</ul>
</div>
</div>




</div> 
<div class="jukskeiright">




<div class="jukskeiarticle">
<div class="jukskeiinner">
<h2 class="jukskeiarticleheading">Urban warriors vs Survivors</h2>
<h3 class="jukskeiarticleauthor">By smart student 6</h3>

This is the article smart student three wrote.

</div>
</div>

<div class="jukskeislideshow">
<div class="jukskeiinner">
[slideshare id=1980831&doc=iabchoustonvisual-kmlprint-090910180423-phpapp01]
<h2 class="jukskeislideshowcaption">'When the river floods we donít go to school'</h2>
<h3 class="jukskeislideshowtitle">SLIDESHOW | . Quisque sagiPs aliquam nisl Fncidunt Fncidunt.
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor
metus, scelerisque dapibus trisFque fermentum, condimentum at
libero.
</h3>
<ul class="jukskeislideshowcontributors">
<li>Smart Student 1 - Photography</li>
<li>Smart Student 2 - Audio</li>
<li>Smart Student 1 - Production</li>
</ul>
</div>
</div>

<div class="jukskeiarticle">
<div class="jukskeiinner">
<h2 class="jukskeiarticleheading">Another article in this topic</h2>
<h3 class="jukskeiarticleauthor">By smart student 3</h3>

Lorem ipsum dolor sit amet, consectetur adipiscing elit.
VesFbulum malesuada semper neque, a facilisis turpis
vehicula commodo. Sed accumsan aliquet nulla, eget
sodales nisi fermentum in. Aenean et luctus dui. Nam nec
erat in dolor sollicitudin mollis. Quisque sagiPs aliquam nisl
Fncidunt Fncidunt. Phasellus sed eros arcu. In hac habitasse
platea dictumst. Ut dolor metus, scelerisque dapibus
trisFque fermentum, condimentum at libero. Nam odio
lacus, mollis at sodales sit amet, hendrerit quis lectus.
Quisque a nunc ut mauris rutrum sollicitudin id vitae tortor.

</div>
</div>


</div>


abcbcadddggg;


$objWashout = $this->getObject('washout', 'utilities');
echo $objWashout->parseText($content);

?>