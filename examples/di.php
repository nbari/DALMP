<?php

require_once '../src/dalmp.php';

$di = new DALMP\DI();

#$ok = $di->database('utf8://root@localhost:3306/dalmp');
#print_r($ok);


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
