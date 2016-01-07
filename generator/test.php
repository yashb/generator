<?php

$sql = <<<'SQL'
select *
  from $tablename
 where id in [$order_ids_list]
   and product_name = "widgets"
SQL;

$x = <<<EOFj
The point of the "argument" was to $var illustrate the use of here documents
EOFj;

#---------------------------------------------------

$str = <<<'EOD'
Example of string $var
spanning multiple lines
using nowdoc syntax.
EOD;

#---------------------------------------------------

$html = <<<'HTML'
  <div class='something'>
    <ul class='mylist'>
      <li>$something</li>
      <li>$whatever</li>
      <li>$testing123</li>
    </ul>
  </div>
HTML;

#---------------------------------------------------

$css = <<<'CSS'

.col
{ 
    color: #00A8FF; 
} 

CSS;

#---------------------------------------------------

$css = <<<JS

var i = 0;

JS;
