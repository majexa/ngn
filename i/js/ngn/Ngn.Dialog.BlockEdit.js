Ngn.Dialog.BlockEdit = {};

Ngn.Dialog.BlockEdit.Dynamic = new Class({
  requiredOptions: ['blockId'],
  updateBlock: function() {
    var eCont = $('block_' + this.options.blockId).getElement('.bcont');
    eCont.set('load', {evalScripts: true}).load('/c/pageBlock/ajax_get/' + this.options.blockId);
  }
});

Ngn.Dialog.BlockEdit.Static = new Class({
  requiredOptions: ['className', 'type'],
  updateBlock: function() {
    var eCont = document.body.getElement('.pbt_' + this.options.type).getElement('.bcont');
    eCont.set('load', {evalScripts: true}).load('/c/pageBlock/ajax_getStatic/' + this.options.className + '/' + this.options.type);
  }
});