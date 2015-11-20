
$options = R::get('options');
$im = DdCore::imSystem($options['str']);
foreach ($im->form->fields as $f) {
  if (!DdFieldCore::isImageType($f['type'])) continue;
  $names[] = $f['name'];
}
if (!$names) {
  print "no image fields\n";
  return;
}
$n = 0;
foreach ($im->items->getItems() as $item) {
  foreach ($names as $name) {
    if (!$item[$name]) continue;
    $im->makeThumbs(WEBROOT_PATH.$item[$name]);
    print ".";
    $n++;
  }
}
print "\ndone $n\n";
