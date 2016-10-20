Ngn.Dialog.RequestFormBase = new Class({
  Extends: Ngn.Dialog,

  options: {
    okDestroy: false,
    jsonRequest: true,
    autoSave: false,
    getFormData: function() {
      return Ngn.Frm.toObj(this.form.eForm);
    },
    onFormResponse: Function.from(),
    onFormRequest: Function.from(),
    onSubmitSuccess: Function.from()
  },

  initialize: function(options) {
    options = options || {};
    options.ok = this.submit.bind(this);
    if (options.submitUrl == undefined) {
      if (options.jsonSubmit == undefined) options.jsonSubmit = false;
      options.submitUrl = options.url;
    }
    this.parent(options);
    this.toggle('ok', false);
    window.addEvent('keypress', function(e) {
      if (e.key != 'enter' || e.target.get('tag') == 'textarea') return;
      e.preventDefault();
      this.submit();
    }.bind(this));
  },

  form: null,
  response: null,

  urlResponse: function(r) {
    this.parent(r);
    this.response = r;
    if (r.submitTitle) this.setOkText(r.submitTitle);
    if (r.jsOptions) {
      if (r.jsOptions.onOkClose)
        this.addEvent('okClose', r.jsOptions.onOkClose);
    }
    this.setMessage(r.form, false);
    this.form = Ngn.Form.factory(this.message.getElement('form'), {
      ajaxSubmit: true,
      ajaxSubmitUrl: this.options.submitUrl,
      disableInit: true
    });
    this.form.options.dialog = this; // Важно передавать объект Диалога в объект
    // Формы после выполнения конструктура, иначе объект
    // Даилога не будет содержать созданого объекта Формы
    this.form.init();
    this.fireEvent('formResponse');
    this.form.addEvent('submit', function(r) {
      this.fireEvent('formRequest');
      this.loading(true);
    }.bind(this));
    this.form.addEvent('failed', function(r) {
      this.urlResponse(r);
      this.loading(false);
    }.bind(this));
    this.form.addEvent('complete', function(r) {
      this.response = r;
      this.okClose();
      this.fireEvent('submitSuccess', r);
    }.bind(this));
    this.resizeByCols();
    if (this.options.autoSave) {
      new Ngn.Frm.Saver(this.form.eForm, {
        url: this.options.submitUrl,
        jsonRequest: true
      });
    }
    this.initEvents();
    this.formInit();
    this.initPosition();
  },

  // abstract
  initEvents: function() {
  },

  resizeByCols: function() {
    var cols = this.form.eForm.getElements('.type_col');
    if (!cols.length) return;
    //var maxY = 0;
    var ys = [];
    var x = 0;
    for (var i = 0; i < cols.length; i++) {
      ys[i] = cols[i].getSize().y;
      x += cols[i].getSize().x;
    }
    //for (var i=0; i<cols.length; i++) cols[i].setStyle('height', ys.max() + 'px');
    this.dialog.setStyle('width', (x + 12) + 'px');
  },

  formInit: function() {
  },

  submit: function() {
    this._submit();
  },

  finishClose: function() {
    this.parent();
    // если в последнем респонзе есть ссылка не следующую форму
    if (this.isOkClose && this.response.nextFormUrl) {
      var opt = {};
      if (this.response.nextFormOptions) opt = Object.merge(opt, this.response.nextFormOptions);
      opt.url = this.response.nextFormUrl;
      new Ngn.Dialog.RequestForm(opt);
    }
  }

  // abstract
  //_submit: {}

});

Ngn.Dialog.Form = new Class({
  Extends: Ngn.Dialog.RequestFormBase,

  options: {
    onSubmit: Function.from()
  },

  _submit: function() {
    this.fireEvent('submit', this.options.getFormData.bind(this)());
    this.okClose();
  }

});

Ngn.Dialog.RequestForm = new Class({
  Extends: Ngn.Dialog.RequestFormBase,

  options: {
    autoSave: false,
    formEvents: false
    //cacheRequest: false
  },

  _submit: function() {
    this.form.submit();
  },

  initEvents: function() {
    if (!this.options.formEvents) return;
    var obj = this;
    for (var i = 0; i < this.options.formEvents.length; i++) {
      var evnt = this.options.formEvents[i];
      this.message.getElement('[name=' + evnt.fieldName + ']').addEvent(evnt.fieldEvent, function() {
        obj.fireEvent(evnt.formEvent, this.get('value'));
      });
    }
  }

});

Ngn.Dialog.RequestForm.Static = new Class({
  Extends: Ngn.Dialog.RequestForm,

  // options: {
  //   staticResponse: {
  //     title: text
  //     submitTitle: text
  //     form: html
  //   }
  // }

  initFormResponse: function() {
    this.urlResponse(Ngn.json.process(this.options.staticResponse));
  }

});