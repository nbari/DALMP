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

  public function getSessionData() {
    return "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
  }

  public function testOpen() {
    $this->assertTrue($this->sess->open(True, True));
  }

  /**
   * @depends testOpen
   */
  public function testWrite() {
    for ($i = 0; $i < 1000; $i++) {
      $this->assertTrue($this->sess->write(sha1("sid_{$i}"), $this->getSessionData()));
    }
  }

  /**
   * @depends testWrite
   */
  public function testRead() {
    for ($i = 0; $i < 1000; $i++) {
      $this->assertEquals($this->getSessionData(), $this->sess->read(sha1("sid_{$i}")));
    }
  }

  /**
   * @depends testWrite
   */
  public function testDestroy() {
    for ($i = 0; $i < 1000; $i++) {
      $this->assertTrue($this->sess->destroy(sha1("sid_{$i}")));
    }
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
    for ($i = 0; $i < 1000; $i++) {
      $GLOBALS['UID'] = sha1($i);
      $this->assertTrue($this->sess->write(sha1("sid_{$i}"), $this->getSessionData()));
    }
  }

  public function testgetSessionsRefs() {
    $this->assertEquals(1000, count($this->sess->getSessionsRefs()));
  }

  public function testgetSessionRef() {
    for ($i = 0; $i < 1000; $i++) {
      $this->assertEquals(1, count($this->sess->getSessionRef(sha1($i))));
    }
  }

  public function testdelSessionRef() {
    for ($i = 0; $i < 1000; $i++) {
      $this->assertTrue($this->sess->delSessionRef(sha1($i)));
      $this->assertEquals(array(), $this->sess->getSessionRef(sha1($i)));
    }
  }

}
