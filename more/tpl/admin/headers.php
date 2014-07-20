<meta http-equiv="Content-Type" content="text/html;charset=<?= CHARSET ?>">
<base href="/" />
<link rel="icon" href="./i/img/ngn/favicon.ico" type="image/x-icon" />
<?= Sflm::frontend('css')->getTags('admin') ?>
<script language="JavaScript" src="./i/js/tiny_mce/tiny_mce.js"></script>
<?= Sflm::frontend('js')->getTags('admin') ?>
<?= AdminModule::sf($d['adminModule']) ?>
<!--
<script type="text/javascript">
  window.addEvent('domready', function() {
    Ngn.cp.init();
  });
</script>
-->