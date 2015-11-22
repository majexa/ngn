<? $this->tpl('admin/modules/tags/header', $d) ?>

<div class="tags">
  <div id="treeMenu" class="iconsSet">
    <small>
      <a href="#" class="add gray"><i></i><?= Lang::get('create') ?></a>
      <a href="#" class="rename gray"><i></i><?= Lang::get('rename') ?></a>
      <a href="#" class="delete gray"><i></i><?= Lang::get('delete') ?></a>
      <a href="#" class="toggle collapse gray"><i></i>Развернуть все</a>
    </small>
    <div class="clear"><!-- --></div>
  </div>
  <div id="treeContainer"></div></div>
</div>

<?= Sflm::frontend('js')->getTagsDebug('mif.tree') ?>

<script type="text/javascript">
var te;
var setHeight = function() {
  var h = Ngn.cp.getMainAreaHeight()
    - $('mainContent').getElement('.navSub').getSize().y
    - $('treeMenu').getSize().y;
  te.container.setStyle('height', h+'px'); 
};

$('body').addClass('twopanels');
window.addEvent('domready', function() {
  te = new Ngn.TreeEdit.Tags(
    'treeContainer',
    '<?= $d['groupName'] ?>',
    { buttons: 'treeMenu' }
  ).init();
  setHeight();
  window.addEvent('resize', setHeight);
});



</script>