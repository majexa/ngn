<? $d['pcd']['pageAdminSettings'] = $d['pageAdminSettings'] ?>
<? $d['pcd']['adminPageControllerSettings'] = $d['settings'] ?>
<div id="pageControllerSettings" style="display:none"><?= json_encode($d['pcd']['page']['settings']) ?></div>
<div class="str_<?= $d['pcd']['page']['strName'] ?>">
<?
$this->tpl('admin/modules/pages/twoPanels',
  $d + [
    'rightPanelTpl' => $d['pcd']['tpl'],
    'rightPanelData' => $d['pcd']
  ] 
);
?>
</div>

<script type="text/javascript">
  Ngn.toObj('Ngn.site.page', <?= Arr::jsObj($d['pcd']['page']->r) ?>);
</script>
