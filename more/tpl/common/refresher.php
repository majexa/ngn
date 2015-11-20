<style>
  .refresher {
    padding: 30px 50px 0px 50px;
  }
</style>

<div class="refresher">
  <? $this->tpl($d['subTpl'], $d) ?>
</div>
<script>
  (function() {
    var eWorkflow = document.getElement('.refresher');
    new Request({
      url: Ngn.Url.getPath(),
      onComplete: function(html) {
        Elements.from(html).each(function(el) {
          if (el.get('tag') == 'div') {
            eWorkflow.set('html', el.getElement('.refresher').get('html'));
          }
        });
      }
    }).send();
  }).periodical(10000);
</script>