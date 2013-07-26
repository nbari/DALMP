<?php

/**
 * abstract class to test sessions instances
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
abstract class test_sessions_base extends PHPUnit_Framework_TestCase {

  abstract public function testAttributes();

  public function testOpen() {
    $this->assertTrue($this->sess->open(True, True));
  }

  /**
   * @depends testOpen
   */
  public function testWrite() {
    $this->assertTrue($this->sess->write('sid', 'session_data'));
  }

  /**
   * @depends testWrite
   */
  public function testRead() {
    $this->assertEquals('session_data', $this->sess->read('sid'));
  }

  /**
   * @depends testWrite
   */
  public function testDestroy() {
    $this->assertTrue($this->sess->destroy('sid'));
  }

  /**
   * @depends testOpen
   */
  public function testClose() {
    $this->assertTrue($this->sess->close());
  }

  /**
   * @depends testClose
   */
  public function testGC() {
    $this->assertTrue($this->sess->gc(True));
  }

  public function testWriteRef() {
    $GLOBALS['UID'] = 'ca893913e71db9dee9b63c204a5e5242';
    $this->assertTrue($this->sess->write('sid', 'session_data'));
  }

  public function testgetSessionsRefs() {
    $rs = $this->sess->getSessionsRefs();
    $this->assertEquals('ca893913e71db9dee9b63c204a5e5242', key(current($rs)));
  }

  public function testgetSessionRef() {
    $this->assertEquals(1, count($this->sess->getSessionRef('ca893913e71db9dee9b63c204a5e5242')));
  }

  public function testdelSessionRef() {
    $this->assertTrue($this->sess->delSessionRef('ca893913e71db9dee9b63c204a5e5242'));
    $this->assertEquals(array(), $this->sess->getSessionRef('ca893913e71db9dee9b63c204a5e5242'));
  }

}
