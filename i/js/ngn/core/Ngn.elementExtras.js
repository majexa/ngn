Element.implement({
  values: function() {
    var r = {};
    this.getElements('input').each(function(el) {
      if (el.get('type') == 'radio') {
        if (el.get('checked')) {
          r = el.get('value');
        }
      } else if (el.get('type') == 'checkbox') {
        if (el.get('checked')) {
          r[el.get('name')] = el.get('value');
        }
      } else {
        r[el.get('name')] = el.get('value');
      }
    });
    return r;
  },
  getSizeWithMarginBorder: function() {
    var s = this.getSize();
    return {
      x: parseInt(this.getStyle('margin-left')) + parseInt(this.getStyle('margin-right')) + parseInt(this.getStyle('border-left-width')) + parseInt(this.getStyle('border-right-width')) + s.x,
      y: parseInt(this.getStyle('margin-top')) + parseInt(this.getStyle('margin-bottom')) + parseInt(this.getStyle('border-top-width')) + parseInt(this.getStyle('border-bottom-width')) + s.y
    };
  },
  getSizeWithMargin: function() {
    var s = this.getSize();
    return {
      x: parseInt(this.getStyle('margin-left')) + parseInt(this.getStyle('margin-right')) + s.x,
      y: parseInt(this.getStyle('margin-top')) + parseInt(this.getStyle('margin-bottom')) + s.y
    };
  },
  getSizeWithoutBorders: function() {
    var s = this.getSize();
    return {
      x: s.x - parseInt(this.getStyle('border-left-width')) - parseInt(this.getStyle('border-right-width')),
      y: s.y - parseInt(this.getStyle('border-top-width')) - parseInt(this.getStyle('border-bottom-width'))
    };
  },
  getSizeWithoutPadding: function() {
    var s = this.getSize();
    return {
      x: s.x - parseInt(this.getStyle('padding-left')) - parseInt(this.getStyle('padding-right')),
      y: s.y - parseInt(this.getStyle('padding-top')) - parseInt(this.getStyle('padding-bottom'))
    };
  },
  setSize: function(s) {
    if (!s.x && !s.y) throw new Error('No sizes defined');
    if (s.x) this.setStyle('width', s.x + 'px');
    if (s.y) this.setStyle('height', s.y + 'px');
    this.fireEvent('resize');
  },
  setValue: function(v) {
    this.set('value', v);
    this.fireEvent('change');
  },
  getPadding: function() {
    return {
      x: parseInt(this.getStyle('padding-left')) + parseInt(this.getStyle('padding-right')),
      y: parseInt(this.getStyle('padding-top')) + parseInt(this.getStyle('padding-bottom'))
    };
  },
  storeAppend: function(k, v) {
    var r = this.retrieve(k);
    this.store(k, r ? r.append(v) : r = [v]);
  },
  setTip: function(title) {
    if (!Ngn.tips) Ngn.initTips(this);
    if (this.retrieve('tip:native')) {
      Ngn.tips.hide(this);
      this.store('tip:title', title);
    } else {
      Ngn.tips.attach(this);
    }
  }
});
