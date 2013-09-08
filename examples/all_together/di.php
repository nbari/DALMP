<?php

require_once '../../src/dalmp.php';

$di = new DALMP\DI();

$ok = $di->database('utf8://root:mysql@localhost:3306/dalmp');
$ok->debug(True);

$now = $ok->getOne('SELECT NOW()');
echo $now,PHP_EOL;
sleep(10);
$now = $ok->getOne('SELECT NOW()');
echo $now,PHP_EOL;
sleep(10);
$city = $ok->PgetAll('SELECT * FROM City WHERE name like ?', '%timor%');
print_r($city);
sleep(10);
$now = $ok->getOne('SELECT NOW()');
echo $now,PHP_EOL;

class teste {

  public function __construct() {
    print_r(func_get_args());
  }

  public function foo() {
    echo __CLASS__ . __METHOD__;
  }

}

$di->addObject('teste', new teste());

$a = $di->teste();

$a->foo();

$di->addObject('test2', $di->share(function () {
  return new teste();
}));

echo PHP_EOL;
$b = $di->test2();
$b->foo();

echo PHP_EOL;
$c = $di->test2();
$c->foo();

echo PHP_EOL;

$d = $di->test2();
$d->foo();
