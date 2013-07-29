<?php
class phpMorphy_GrammemsProvider_ru_RU extends phpMorphy_GrammemsProvider_ForFactory {
    static protected $self_encoding = 'windows-1251';
    static protected $instances = [];

    static protected $grammems_map = [ 
        'род' => ['МР', 'ЖР', 'СР'], 
        'одушевленность' => ['ОД', 'НО'], 
        'число' => ['ЕД', 'МН'], 
        'падеж' => ['ИМ', 'РД', 'ДТ', 'ВН', 'ТВ', 'ПР', 'ЗВ', '2'], 
        'залог' => ['ДСТ', 'СТР'], 
        'время' => ['НСТ', 'ПРШ', 'БУД'], 
        'повелительная форма' => ['ПВЛ'], 
        'лицо' => ['1Л', '2Л', '3Л'], 
        'краткость' => ['КР'], 
        'сравнительная форма' => ['СРАВН'], 
        'превосходная степень' => ['ПРЕВ'],
        'вид' => ['СВ', 'НС'],
        'переходность' => ['ПЕ', 'НП'],
        'безличный глагол' => ['БЕЗЛ'],
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
