Ngn.Form.El.DdTagsTreeSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var adviceId = 'ddTagsTreeSelectAdvice' + this.eRow.n;
    new Element('div', {id: adviceId}).inject(this.eRow, 'bottom');
    // this.eRow.getElement('input').addClass("validate-one-required2 msgPos:'" + adviceId + "'");
    this.initEls(this.eRow);
  },

  getNodes: function(eParent) {
    if (!eParent) eParent = this.eRow;
    var r = [];
    eParent.getElements('a').each(function(eA) {
      var eUl = this.eRow.getElement('.nodes_' + eA.get('data-id'));
      if (!eUl) return;
      r.push([eA, eUl]);
    }.bind(this));
    return r;
  },

  initEls: function(eParent) {
    this.getNodes().each(function(els) {
      var eA = els[0], eUl = els[1];
      this.toggle(eUl, false);
      eA.addEvent('click', function(e) {
        e.preventDefault();
        if (!!eA.get('data-loadChildren') && !eA.retrieve('loaded')) {
          new Ngn.Request.Loading({
            url: '/c/ddTagsTreeMultiselect/' + this.strName + '/' + Ngn.frm.getPureName(this.eRow.getElement('input').get('name')) + '/' + eA.get('data-id'),
            onComplete: function(html) {
              eA.store('loaded', true);
              eUl.set('html', Elements.from(html)[0].get('html'));
              this.toggle(eUl, true);
              this.initEls(eUl);
              this.form.fireEvent('newElement', eUl);
            }.bind(this)
          }).send();
          return;
        }
        this.toggle(eUl);
      }.bind(this));
    }.bind(this));
    eParent.getElements('input').each(function(el) {
      if (el.get('checked')) this.openUp(el);
    }.bind(this));
  },

  openUp: function(el) {
    var eUl = el.getParent('ul');
    if (!eUl) return;
    this.toggle(eUl, true);
    this.openUp(eUl);
  },

  toggle: function(eUl, flag) {
    if (flag === undefined) flag = eUl.getStyle('display') == 'block' ? false : true;
    eUl.setStyle('display', flag ? 'block' : 'none');
  },

  selectOnlyFirstLevel: function() {
    var uls = this.getNodes();
    for (var i=0; i<uls.length; i++) this.toggle(uls[i], false);
    this.eRow.getElements('input').each(function(el) {
      el.set('checked', false);
    });
    c(uls);
  }

});