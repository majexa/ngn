Ngn.Form.El.DdTagsTreeSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var adviceId = 'ddTagsTreeSelectAdvice' + this.eRow.n;
    new Element('div', {id: adviceId}).inject(this.eRow, 'bottom');
    // this.eRow.getElement('input').addClass("validate-one-required2 msgPos:'" + adviceId + "'");
    this.eParent = this.eRow;
    this.initEls();
  },

  getNodes: function(eParent) {
    if (!eParent) eParent = this.eParent;
    var r = [];
    eParent.getElements('a').each(function(eA) {
      if (!eA.getParent('li')) {
        throw new Error('Link with ID=' + eA.get('data-id') + ' has no children');
      }
      var eUl = eA.getParent('li').getNext('ul');
      r.push([eA, eUl]);
    }.bind(this));
    return r;
  },

  initEls: function(eParent) {
    if (!eParent) eParent = this.eParent;
    this.getNodes(eParent).each(function(els) {
      var eA = els[0], eUl = els[1];
      this.toggle(eUl, false);
      eA.addEvent('click', function(e) {
        e.preventDefault();
        if (!!eA.get('data-loadChildren') && !eA.retrieve('loaded')) {
          this.requestOpen(eUl, eA);
          return;
        }
        this.toggle(eUl);
      }.bind(this));
    }.bind(this));
    this.eParent.getElements('input').each(function(el) {
      if (el.get('checked')) this.openUp(el);
    }.bind(this));
  },

  requestOpen: function(eUl, eA) {
    new Ngn.Request.Loading({
      url: '/c/ddTagsTreeMultiselect/' + this.strName + '/' + this.name + '/' + eA.get('data-id'),
      onComplete: function(html) {
        eA.store('loaded', true);
        eUl.set('html', Elements.from(html)[0].get('html'));
        this.toggle(eUl, true);
        this.initEls(eUl);
        this.form.fireEvent('newElement', eUl);
      }.bind(this)
    }).send();
  },

  openUp: function(el) {
    var eUl = el.getParent('ul');
    if (!eUl) return;
    this.toggle(eUl, true);
    this.openUp(eUl);
  },

  toggle: function(eUl, flag) {
    if (!flag) {
      //throw new Error('!');
    }
    if (flag === undefined) flag = eUl.getStyle('display') == 'block' ? false : true;
    eUl.setStyle('display', flag ? 'block' : 'none');
  },

  selectOnlyFirstLevel: function() {
    var uls = this.getNodes();
    for (var i=0; i<uls.length; i++) this.toggle(uls[i][1], false);
    this.unselectAll();
    this.eParent.getElement('ul').getChildren('li input').set('checked', true);
  },

  unselectAll: function() {
    this.eParent.getElements('ul input').set('checked', false);
  }

});