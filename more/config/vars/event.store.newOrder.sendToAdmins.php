<?php

$f = function($orderId, array $items) {
  EmailAdmin::send('Новый заказ в магазине', Tt()->getTpl('pageModules/storeOrder/newOrderEmail', [
      'orderId' => $orderId,
      'productsText' => implode('', array_map(function($v) {
        $s = "<b>{$v['title']}</b><br />
Цена: {$v['price']} руб.<br />
";
        if ($v['orderParams']) {
          foreach ($v['orderParams'] as $p) {
            $s .= "{$p['title']}: {$p['value']['title']}<br />";
          }
        }
        $s .= "Количество: {$v['cnt']} шт.<br />";
        $s .= "---------------------<br />";
        return $s;
      }, $items)),
      'orderText' => DdCore::htmlItem(Config::getVarVar('store', 'ordersPageId'), 'events', $orderId)]
  ));
};

return $f;