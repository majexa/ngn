<?= Sflm::get('css')->getTags('textboxList') ?>
<?= Sflm::get('js')->getTags('textboxList') ?>

<br /><br /><br />
<input id="asd" />

<script>
window.addEvent('domready', function(){
  var t = new TextboxList('asd', {
    //bitsOptions: {editable: {addKeys: 188}},
    unique: true,
    plugins: {autocomplete: {
      //minLength: 3,
      queryRemote: true,
      remote: {
        url: '/c/ddTagsAc?strName=merryman&fieldName=cat'
      }
    }}
  });
});
</script>
