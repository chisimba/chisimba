<?php

$content = <<<abcbcadddggg


<div class="jukskeifullarticle">
<h2 class="jukskeifullarticleheading">
LIVING OFF THE RIVER | Religion and the Jukskei
</h2>

<h3 class="jukskeifullarticleauthor">
BY SMART STUDENT 3
</h3>
<img src="skins/wits_jukskei/images/articlesampleimage.gif" class="jukskeiarticleimage" />
<span class="jukskeiquotenormal">
In hac habiÅ] tasse platea dictumst. . Sed accumsan aliquet nulla, eget sodales nisi fermentum in.
Aenean et luctus dui. Nam nec erat in dolor sollicitudin mollis. Quisque sagiSs aliquam nisl
Tncidunt Tncidunt. Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Phasellus sed eros
arcu. Aenean et luctus dui. Nam nec erat in dolor sollicitudin mollis.
</span>

Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesTbulum malesuada
semper neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet nulla, eget
sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin
mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt.
Eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor
sollicitudin mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt. Phasellus sed eros
arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus, scelerisque dapibus trisTque
fermentum, condimentum at libero. 

<div class="jukskeivideoright">
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

Nam odio lacus, mollis at sodales sit amet.
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus,
scelerisque dapibus trisTque fermentum, condimentum at libero. Nam odio lacus,
mollis at sodales sit amet, hendrerit quis lectus. Quisque a nunc ut mauris rutrum.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesTbulum malesuada
semper neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet nulla, eget
sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin
mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt.

<span class="jukskeiquoteright">
"Aenean et luctus dui. Nam nec erat in dolor sollicitudin
mollis."
</span>

Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesTbulum malesuada
semper neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet nulla, eget
sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin
mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesTbulum malesuada semper neque, a facilisis turpis vehicula commodo. Sed accumsan
aliquet nulla, eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin mollis. Quisque sagiSs aliquam nisl Tncidunt
Tncidunt.
Eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudi
n mollis
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus, scelerisqu
e dapibus trisTque fermentum, condimentum at libero. Nam odio lacus, mollis at
sodales sit amet, hendrerit quis lectus. Quisque a nunc ut mauris rutrum.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. VesTbulum malesuada semper
neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet nulla, eget sodales
nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin mollis. Quis
que sagiSs aliquam nisl Tncidunt Tncidunt. consectetur adipiscing elit. VesTbulum
malesuada semper neque, a facilisis turpis vehicula commodo. Sed accumsan aliquet
nulla, eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor
sollicitudin mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt.
Eget sodales nisi fermentum in. Aenean et luctus dui. Nam nec erat in dolor sollicitudin mollis. Quisque sagiSs aliquam nisl Tncidunt Tncidunt.
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus, scelerisque dapibus trisTque fermentum, condimentum at libero. Nam
odio lacus, mollis at sodales sit amet. Scelerisque dapibus trisTque fermentum, condimentum at libero. Nam odio lacus, mollis at sodales sit amet
Phasellus sed eros arcu. In hac habiÅ] tasse platea dictumst. Ut dolor metus, scelerisque dapibus trisTque fermentum, condimentum at libero. Nam
odio lacus, mollis at sodales sit amet, hendrerit quis lectus. Quisque a nunc ut mauris rutrum.
CHILDREN pick their way across the Jukskei



<div><a href="#" class="jukskeiblocklink">READ MY BLOG</a></div>
<div><a href="#" class="jukskeiblocklink">SEE A PHOTO ESSAY ON THIS TOPIC</a></div>

</div>




abcbcadddggg;


$objWashout = $this->getObject('washout', 'utilities');
echo $objWashout->parseText($content);

?>