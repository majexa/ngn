Ngn.Frm.VisibilityCondition = new Class({

  initialize: function(eForm, sectionName, condFieldName, cond) {
    this.sectionName = sectionName;
    this.initSectionSelector();
    this.eSection = eForm.getElement(this.sectionSelector);
    if (!this.eSection) {
      console.debug('Element "' + this.sectionSelector + '" does not exists');
      return;
    }
    var toggleSection = function(v) {
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
      this.eSection.setStyle('display', flag ? 'block' : 'none');
      this.eSection.getElements(Ngn.Frm.selector).each(function(el) {
        el.set('disabled', !flag);
      });
    }.bind(this);
    toggleSection(Ngn.Frm.getValueByName(condFieldName, eForm));
    Ngn.Frm.addEvent('change', condFieldName, toggleSection, eForm);
    Ngn.Frm.addEvent('focus', condFieldName, toggleSection, eForm);
  }

});
