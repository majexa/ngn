<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title><?=
    ($d['adminModuleTitle'] ? $d['adminModuleTitle'] : ''). 
    ($d['pageTitle'] ? ' / '.strip_tags($d['pageTitle']) : '').' — '.SITE_TITLE ?></title>
  <? $this->tpl('admin/headers', $d) ?>
  <script type="text/javascript">
  window.addEvent('domready', function() {
    document.getElements('.apeform form').each(function(eForm) {
      Ngn.Form.factory(eForm);
    });
  });
  </script>
  <link rel="stylesheet" type="text/css" href="http://localhost:888/module/default.css" media="screen, projection" />
  <?/*
  <script src="http://localhost:888/sux.js"></script>
  */?>
</head>
