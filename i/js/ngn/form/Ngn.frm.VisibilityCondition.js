Ngn.Frm.VisibilityCondition = new Class({

  initialize: function(eForm, sectionName, condFieldName, cond) {
    this.sectionName = sectionName;
    this.initSectionSelector();
    this.eSection = eForm.getElement(this.sectionSelector);
    if (!this.eSection) {
      c('Element "' + this.sectionSelector + '" does not exists');
      return;
    }
    /*
     this.fx = new Fx.Slide(this.eSection, {
     duration: 200,
     transition: Fx.Transitions.Pow.easeOut
     });
     this.fx.hide();
     */
    var toggleSection = function(v, isFx) {
      // v необходима для использования в условии $d['cond']
      var flag = (eval(cond));
      if (!flag) {
        // Если скрываем секцию, необходимо снять все required css-классы в её полях
        this.eSection.getElements('.required').each(function(el) {
          el.removeClass('required');
          el.addClass('required-disabled');
        });
      } else {
        this.eSection.getElements('.required-disabled').each(function(el) {
          el.removeClass('required-disabled');
          el.addClass('required');
        });
      }
      if (isFx && 0) {
        // если нужно завернуть не развёрнутую до конца секцию,
        // нужно просто скрыть её
        if (flag == this.fx.open)
          flag ? (function() {
            this.fx.show();
          }).delay(200, this) : (function() {
            this.fx.hide();
          }).delay(200, this); else
          flag ? this.fx.slideIn() : this.fx.slideOut();
      } else {
        this.eSection.setStyle('display', flag ? 'block' : 'none');
        this.eSection.getElements(Ngn.Frm.selector).each(function(el) {
          el.set('disabled', !flag);
        });
      }
    }.bind(this);
    toggleSection(Ngn.Frm.getValueByName(condFieldName), false);
    Ngn.Frm.addEvent('change', condFieldName, toggleSection, true);
    Ngn.Frm.addEvent('focus', condFieldName, toggleSection, true);
  }

});
