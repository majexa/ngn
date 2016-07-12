<?php

class TestSflmDebugPaths extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
  }

  function testDebugPathOnAddingClass() {
    // ��������� ����� ���� ������ � ������ ���������� �����
    Sflm::$debugPaths = [
      'js' => [
        'test/Ngn.Sub.B.js'
      ]
    ];
    // ��������� �����
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // ��������� ��������� ���� ���������
    Sflm::frontend('js')->store();
    // �������������� ��������
    Sflm::setFrontend('js');
    // ��������� �����
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // ��������� ��������� ���� ���������
    Sflm::frontend('js')->store();
    // ��������� ��� ��� ����� ����������� ������ ����
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->getTagsFinal(), 'Ngn.Sub.B'));
    // ����������� ������ �� ������ ���� � �������� �����
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B'));
    // ���� ��� ������ ���� Ngn.Sub.A �� �������� ����������� Ngn.Sub.B � �������� ��� � ���������� �����
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.A'));
  }

  /**
   * ���� �����, ��� � ���������� ������, ������ ��������� ��� �� ������� ����� �������� ����� ���������� �����
   */
  function testDebugPathOnAddingNamespace() {
    // ��������� ����� ���� ������ � ������ ���������� �����
    Sflm::$debugPaths = [
      'js' => [
        'Ngn.Sub'
      ]
    ];
    // ��������� �����
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // ��������� ��������� ���� ���������
    Sflm::frontend('js')->store();
    // �������������� ��������
    Sflm::setFrontend('js');
    // ��������� �����
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // ��������� ��������� ���� ���������
    Sflm::frontend('js')->store();
    // ������ �������������� ��������� ��� ��� �� ��� ����� ����������
    $parentPos = strpos(Sflm::frontend('js')->getTagsFinal(), 'Ngn.Sub.js');
    $this->assertTrue($parentPos !== false);
    // �� � ��� ������ ������, ��� � � ���������� �������
    $classPos = strpos(Sflm::frontend('js')->getTagsFinal(), 'Ngn.Sub.B.js');
    $this->assertTrue($classPos !== false);
    // ���������� ���� ������ ���������� ������� � ������ ������� �������� � �����������
    $this->assertTrue($parentPos < $classPos);
    // ����� �� ������ ���� � ����
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub = '));
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B = '));
  }

  function test() {
    Sflm::$debugPaths = [
      'js' => [
        'test/Ngn.MtClassUsage.js'
      ]
    ];
    Sflm::frontend('js')->addClass('Ngn.MtClassUsage');
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->code(), 'Fx.CSS = new Class'));
  }

}