<?php

namespace Dallgoot\Yaml;

use Stdclass;

$yaml = new YamlObject(0);

$o = new Stdclass;

$o->key = 'a';

$yaml->key1 = new Compact([1, 2, $o]);

return $yaml;