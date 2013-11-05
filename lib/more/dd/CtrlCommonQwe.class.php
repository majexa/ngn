<?php

class CtrlCommonQwe extends CtrlCommonScripts {

  function action_default() {
    sendHeader();
    $this->hasOutput = false;
    $a = (new DdItems('a'))->getItemF(1);
    $b = (new DdItems('a'))->getItem(1);
    print "<table><tr><td valign='top'><small>";
    prr($a['region']);
    print "</td><td valign='top'><small>";
    prr($b['region']);
    print "</td><td valign='top'><small>";
    $ddo = new Ddo('a', 'siteItem');
    $ddo->titled = true;
    print $ddo->setItem($a)->els();
    print "</td><td valign='top'><small>";
    print $ddo->setItems((new DdItems('a'))->getItems())->els();
    print "</td></tr></table>";
  }

}
