<?php

$cache = NgnCache::c(['lifetime' => 120]);
$page = DbModelPages::getHomepage();
if (!($sitemapHtml = $cache->load('sitemap'))) {
  $o = Menu::getUlObjById($page['id'], 10);
  //$o->setExpandItems(Config::getVar('showItemsOnMap', true));
  $sitemapHtml = $o->html();
  $cache->save($sitemapHtml, 'sitemap', ['pages']);
}
print $sitemapHtml;
