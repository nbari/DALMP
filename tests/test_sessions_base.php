<?php

/**
 * abstract class to test sessions instances
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
abstract class test_sessions_base extends PHPUnit_Framework_TestCase
{
  abstract public function testAttributes();

  public function getSessionData($i)
  {
    return "{$i}. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
  }

  public function testOpen()
  {
    $this->asserttrue($this->sess->open(true, true));
  }

  /**
   * @depends testOpen
   */
  public function testWrite()
  {
    for ($i = 0; $i < 100; $i++) {
      $this->asserttrue($this->sess->write(sha1("sid_{$i}"), $this->getSessionData($i)));
    }
  }

  /**
   * @depends testWrite
   */
  public function testRead()
  {
    for ($i = 0; $i < 100; $i++) {
      $this->assertEquals($this->getSessionData($i), $this->sess->read(sha1("sid_{$i}")));
    }
  }

  /**
   * @depends testWrite
   */
  public function testDestroy()
  {
    for ($i = 0; $i < 100; $i++) {
      $this->asserttrue($this->sess->destroy(sha1("sid_{$i}")));
    }
  }

  /**
   * @depends testOpen
   */
  public function testClose()
  {
    $this->asserttrue($this->sess->close());
  }

  /**
   * @depends testClose
   */
  public function testGC()
  {
    $this->asserttrue($this->sess->gc(true));
  }

  public function testWriteRef()
  {
    for ($i = 0; $i < 100; $i++) {
      $GLOBALS['UID'] = sha1($i);
      $this->asserttrue($this->sess->write(sha1("sid_{$i}"), $this->getSessionData($i)));
    }
  }

  public function testGetSessionsRefs()
  {
    $this->assertEquals(100, count($this->sess->getSessionsRefs()));
  }

  public function testGetSessionRef()
  {
    for ($i = 0; $i < 100; $i++) {
      $this->assertEquals(1, count($this->sess->getSessionRef(sha1($i))));
    }
  }

  public function testDelSessionRef()
  {
    for ($i = 0; $i < 100; $i++) {
      $this->asserttrue($this->sess->delSessionRef(sha1($i)));
      $this->assertEquals(array(), $this->sess->getSessionRef(sha1($i)));
    }
  }

}
