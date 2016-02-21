<?php

class TestClassCore extends NgnTestCase {

  function test1() {
    $method = new ReflectionMethod('TestClassCore', 'a');
    $docComment = $method->getDocComment();
    $this->assertTrue(ClassCore::getDocComment($docComment, 'title') === "line1\nline2\n\nline4");
    $this->assertTrue(ClassCore::getDocComment($docComment, 'options') === 'opt1 {@opt2}');
    $this->assertTrue(ClassCore::getDocComment($docComment, 'param')[1]['descr'] === 'line2');
    $r = ClassCore::getDocComment($docComment, 'doc');
    $this->assertTrue($r['text'] === "#Header#\n\nLine1\nLine2\n\nLine3\n");
    $this->assertTrue($r['path'] === 'index');
  }

  /**
   * line1
   * line2
   *
   * line4
   *
   * @param integer $name1 line1
   * @param integer $name2 line2
   *
   * @options opt1 {@opt2}
   *
   * @doc index
   * #Header#
   *
   * Line1
   * Line2
   *
   * Line3
   *
   */
  function a() {
  }

}
