<?php

namespace Dallgoot\Yaml;

use DateTime;

$yaml = new YamlObject(0);

$yaml->key = new DateTime('2000-01-01');

return $yaml;