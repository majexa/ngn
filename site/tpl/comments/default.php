<div class="msgs cntr_comments">
<div class="items">
<?
foreach ($d['items'] as $v) {
  $v['text_f'] = '<a href="'.$v['link'].'">'.$v['text_f'].'</a>';
  $this->tpl('common/msg', $v);
}
?>
</div>
</div>
