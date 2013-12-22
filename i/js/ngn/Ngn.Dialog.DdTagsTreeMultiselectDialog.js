Ngn.Dialog.DdTagsTreeMultiselectDialog = new Class({
  Extends: Ngn.Dialog,

  options: {
    id: 'metro',
    dialogClass: 'treeMultiselectDialog',
    title: '',
    textInfo: '',
    height: 400,
    noPadding: false,
    cancel: false,
    bindBuildMessageFunction: true
  },

  /**
   * @param Ngn.Dialog.DdTagsTreeMultiselectDialogable
   * @param opts
   */
  initialize: function(formEl, opts) {
    this.formEl = formEl;
    this.parent(opts);
  },

  buildMessage: function() {
    this.container = new Element('div.apeform');
    if (this.options.textInfo) new Element('div', {
      'class': 'textInfo',
      html: this.options.textInfo
    }).inject(this.container);
    var eSelectAll = Elements.from('<div class="selectAll"><input type="checkbox" id="selectAll' + this.options.id + '" /> <label for="selectAll' + this.options.id + '">выбрать все</label></div>')[0]
    eSelectAll.inject(this.container);
    eSelectAll = eSelectAll.getElement('input');
    eSelectAll.addEvent('change', function(a) {
      if (eSelectAll.get('checked')) {
        this.formEl.selectOnlyFirstLevel();
      } else {
        this.formEl.unselectAll();
      }
    }.bind(this));
    this.formEl.eTree.inject(this.container);
    this.formEl.eParent = this.container;
    this.formEl.initUpdate(this.container);
    return this.container;
  }

});