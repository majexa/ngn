Ngn.Form.El.DdTagsTreeSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var adviceId = 'ddTagsTreeSelectAdvice' + this.eRow.n;
    new Element('div', {id: adviceId}).inject(this.eRow, 'bottom');
    // this.eRow.getElement('input').addClass("validate-one-required2 msgPos:'" + adviceId + "'");
    this.initEls(this.eRow);
  },

  getNodesParent: function() {
    return this.eRow;
  },

  initEls: function(eParent) {
    eParent.getElements('a').each(function(el) {
      //var eUl = this.eRow;
      var eUl = this.eRow.getElement('.nodes_' + el.get('data-id'));
      if (!eUl) throw new Error('Nodes with ID=' + el.get('data-id') + ' is absent');
      eUl.setStyle('display', 'none');
      el.addEvent('click', function(e) {
        e.preventDefault();
        if (!!el.get('data-loadChildren') && !el.retrieve('loaded')) {
          new Ngn.Request.Loading({
            url: '/c/ddTagsTreeMultiselect/' + this.strName + '/' + Ngn.frm.getPureName(this.eRow.getElement('input').get('name')) + '/' + el.get('data-id'),
            onComplete: function(html) {
              el.store('loaded', true);
              eUl.set('html', Elements.from(html)[0].get('html'));
              eUl.setStyle('display', 'block');
              this.initEls(eUl);
              this.form.fireEvent('newElement', eUl);
            }.bind(this)
          }).send();
          return;
        }
        eUl.setStyle('display', eUl.getStyle('display') == 'block' ? 'none' : 'block');
      }.bind(this));
    }.bind(this));
    eParent.getElements('input').each(function(el) {
      if (el.get('checked')) this.openUp(el);
    }.bind(this));
  },

  openUp: function(el) {
    var eUl = el.getParent('ul');
    if (!eUl) return;
    eUl.setStyle('display', 'block');
    this.openUp(eUl);
  }

});