Ngn.Dialog.DdTagsTreeMultiselectDialog = new Class({
  Extends: Ngn.Dialog,

  options: {
    id: 'metro',
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
    this.container = new Element('div');
    if (this.options.textInfo) new Element('div', {
      'class': 'textInfo',
      html: this.options.textInfo
    }).inject(this.container);
    var eSelectAll = Elements.from('<div class="selectAll"><input type="checkbox" id="selectAll' + this.options.id + '" /> <label for="selectAll' + this.options.id + '">выбрать все</label></div>')[0]
    eSelectAll.inject(this.container);
    eSelectAll.addEvent('change', function() {
      this.formEl.selectOnlyFirstLevel();
    }.bind(this));
    this.formEl.eTree.inject(this.container);
    this.container.getElements('input').each(function(el) {
      el.addEvent('change', function() {
        this.formEl.updateHiddens(Ngn.frm.getValues(this.container.getElements('input')));
      }.bind(this));
    }.bind(this));
    return this.container;
  }

});