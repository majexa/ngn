Ngn.Dialog.RequestFormTabs = new Class({
  Extends: Ngn.Dialog,

  options: {
    tabsSelector: 'h2[class=tab]',
    selectedTab: 0,
    draggable: true,
    openerType: 'default',
    okDestroy: false
  },

  initialize: function(opts) {
    opts = opts || {};
    opts.ok = this.submit.bind(this);
    this.parent(opts);
    this.toggle('ok', false);
    this.dialog.addClass('dialog-tabs');
  },

  submit: function() {
    this.getForm(this.tabs.selected).submit();
  },

  /**
   * @return Ngn.Form
   */
  getForm: function(tabIndex) {
    return this.forms[this.tabs.tabs[tabIndex].name];
  },

  urlRequest: function(_response) {
    this.toggle('ok', true);
    this.parent(_response);
    this.tabs = new Ngn.Tabs(this.message, {
      selector: this.options.tabsSelector,
      show: this.options.selectedTab,
      stopClickEvent: true
    });
    this.tabs.addEvent('select', function(toggle, container, index) {
      this.setSubmitTitle(index);
    }.bind(this));
    this.tabs.eMenu.inject(this.titleText);
    for (var i=0; i<this.tabs.tabs.length; i++) {
      this.tabs.tabs[i].submitTitle = this.tabs.tabs[i].container.get('data-submitTitle');
    }
    this.message.getElements('form').each(function(eForm, n) {
      this.initForm(eForm);
    }.bind(this));
  },

  setSubmitTitle: function(tabIndex) {
    if (this.tabs.tabs[tabIndex].submitTitle) this.setOkText(this.tabs.tabs[tabIndex].submitTitle);
    else this.setOkText(this.options.okText);
  },

  forms: {},

  initForm: function(eForm) {
    var form = Ngn.Form.factory(eForm, {ajaxSubmit: true});
    form.options.dialog = this;
    this.forms[eForm.get('id')] = form;
    this.setSubmitTitle(this.tabs.selected);
    form.validator.options.scrollToErrorsOnSubmit = false;
    var obj = this;
    form.addEvent('failed', function(r) {
      var f = Elements.from(r.form)[0].getElement('form');
      f.replaces(this.eForm);
      obj.initForm(f);
      obj.loading(false);
    });
    form.addEvent('submit', function(r) {
      this.loading(true);
    }.bind(this));
    form.addEvent('complete', function(r) {
      if (!r.form) {
        if (r.nextFormUrl) {
          new Request.JSON({
            url: r.nextFormUrl,
            onComplete: function(r2) {
              if (!r2.form) throw new Error('Form does not exists in next form url "'+r.nextFormUrl+'"');
              var eNewForm = Elements.from(r2.form)[0].getElement('form');
              this.tabs.tabs[this.tabs.selected].name = eNewForm.get('id');
              if (r2.submitTitle) this.tabs.tabs[this.tabs.selected].submitTitle = r2.submitTitle;
              this.loading(false);
              this.initForm(eNewForm.replaces(eForm));
            }.bind(this)
          }).send();
        } else {
          var formName = eForm.get('name');
          if (formName) {
            var methodName = 'this.submitSuccess'+ucfirst(eForm.get('name'));
            var method = eval(methodName);
            try {
              method.bind(this)(r);
            } catch (e) {
              throw new Error('Method "'+methodName+'" does not exists');
            }
          }
        }
      } else {
        var par = eForm.getParent();
        eForm.dispose();
        par.set('html', r.form);
        this.loading(false);
        this.initForm(par.getElement('form'));
      }
    }.bind(this));
  }

});
