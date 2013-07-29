<?php
class phpMorphy_GrammemsProvider_ru_RU extends phpMorphy_GrammemsProvider_ForFactory {
    static protected $self_encoding = 'windows-1251';
    static protected $instances = [];

    static protected $grammems_map = [ 
        '���' => ['��', '��', '��'], 
        '��������������' => ['��', '��'], 
        '�����' => ['��', '��'], 
        '�����' => ['��', '��', '��', '��', '��', '��', '��', '2'], 
        '�����' => ['���', '���'], 
        '�����' => ['���', '���', '���'], 
        '������������� �����' => ['���'], 
        '����' => ['1�', '2�', '3�'], 
        '���������' => ['��'], 
        '������������� �����' => ['�����'], 
        '������������ �������' => ['����'],
        '���' => ['��', '��'],
        '������������' => ['��', '��'],
        '��������� ������' => ['����'],
    ]; 

    function getSelfEncoding() {
        return 'windows-1251';
    }

    function getGrammemsMap() {
        return self::$grammems_map;
    }

    static function instance(phpMorphy $morphy) {
        $key = $morphy->getEncoding();

        if(!isset(self::$instances[$key])) {
            $class = __CLASS__;
            self::$instances[$key] = new $class($key);
        }

        return self::$instances[$key];
    }
}
