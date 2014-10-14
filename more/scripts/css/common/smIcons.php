<?

$cords = array(
  'answer' => array(-183, -290),
  'link' => array(-136, -238),
  'start' => array(-44, -185),
  'settings' => array(2, -108),
  'comments' => array(-322, -55),
  'comments2' => array(-414, -55),
  'user' => array(-229, -4),
  'god' => array(-412, -239),
  'editPage' => array(-366, -446),
  'lock' => array(-412, -368),
  'index' => array(-228, -368),
  'static' => array(-411, -471),
  'dynamic' => array(-251, -469),
  'variant' => array(-457, -287 ),
  'edit' => array(-320, -30),
  'add' => array(-365, -316),
  'delete' => array(2, -264),
  'move' => array(-90, -290),
  'activate' => array(-136, -108),
  'deactivate' => array(-90, -108),
  'author' => array(-228, -3),
  'prev' => array(-320, -343),
  'next' => array(-274, -343),
  'time' => array(-297, -209),
  'play' => array(-43, -235),//array(-113, -365, true),
  'stop' => array(-67, -365, true),
  'pause' => array(-21, -365, true),
  'rss' => array(2, -4, true),
  'stat' => array(2, -212),
  'subscribed' => array(-455, -393),
  'unsubscribed' => array(-455, -367),
  'publish' => array(-44, -264),
  'slices' => array(-44, -108),
  'image' => array(-412, -30),
  'view' => array(-459, -235),
  'cleanup' => array(-135, -316),
  'tag' => array(2, -30),
  'tag2' => array(-90, -30),
  'tag3' => array(-136, -30),
  'shift' => array(-482, -494),
  'global' => array(-182, -394),
  'local' => array(-90, -394),
  'record' => array(-366, -368),
  'phone' => array(2, -418, true),
  'upload' => array(-275, -264),
  'list' => [-319, -471],
  'xls' => [-367, -494],
  'copy' => [1, -54],
  'font' => [-178, -78],
  'bgSettings' => [-204, -77],
  'ok' => [-44, -264]
);

foreach ($cords as $class => $c) {
  print '.smIcons .' . $class . ' i, .smIcons.' . $class . ' i { background-position: ' . $c[0] . 'px ' . $c[1] . "px }\n";
  if (!empty($c[2])) print '.smIcons .' . $class . ' i { width: 14px; height: 14px; }' . "\n";
}

?>

.smIcons.inline {
margin-right: 0px;
}
.smIcons.inline i {
vertical-align: middle;
margin-right: 0px;
}
.smIcons.inline i, a.smIcons.inline {
display: inline-block;
float: none;
}
.smIcons i {
background-image: url(/i/img/icons/pack1.png);
background-repeat: no-repeat;
}
.smIcons a, a.smIcons {
display: block;
float: left;
margin-right: 15px;
text-decoration: none;
cursor: pointer;
}
.bordered.smIcons a, a.smIcons.bordered {
margin-right: 3px;
border: 1px solid transparent;
}
.bordered.smIcons i {
margin-right: 0px;
}
.bordered.smIcons a:hover, a:hover.smIcons.bordered {
border: 1px solid #CCCCCC;
}
.smIcons a.noborder {
margin-right: 15px;
border: none;
}
.smIcons a:hover.noborder {
border: none !important;
}
.smIcons a:hover, a:hover.smIcons, .smIcons a.over, a.over.smIcons {
text-decoration: underline;
}
.smIcons i {
margin-right: 3px;
display: block;
width: 15px;
height: 15px;
float: left;
}
.descr .tooltip {
margin-left: 5px;
}

.smIcons .disabled i {
background: none;
}
.smIcons a:hover.disabled {
background: none !important;
}