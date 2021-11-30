<?php

namespace Dallgoot\Yaml;


use StdClass;

$o = new StdClass;

$o->key1 = new Compact([1, 2, 3]);


return $o;