Ngn.Dialog.RequestFormBase = new Class({
  Extends: Ngn.Dialog,

  options: {
    okDestroy: false,
    jsonRequest: true,
    autoSave: false,
    getFormData: function() {
      return Ngn.frm.toObj(this.form.eForm);
    },
    onSubmitSuccess: $empty
  },

  initialize: function(options) {
    options = options || {};
    options.ok = this.submit.bind(this);
    if (!$defined(options.submitUrl)) {
      if (!$defined(options.jsonSubmit))
        options.jsonSubmit = false;
      options.submitUrl = options.url;
    }
    this.parent(options);
    this.toggle('ok', false);
    this.initFormResponse();
    this.iframeUpload = true;
    window.addEvent('keypress', function(e) {
      return;
      if (e.key != 'enter' || e.target.get('tag') == 'textarea') return;
      e.preventDefault();
      this.submit();
    }.bind(this));
  },

  initFormResponse: function() {
    this.addEvent('request', function(r) {
      if (r.sflJsDeltaUrl) {
        Ngn.Request.sflJsDeltaUrlOnLoad = function() {
          this.formResponse(r);
        }.bind(this);
      } else {
        this.formResponse(r);
      }
    }.bind(this));
  },

  form: null,
  response: null,

  formResponse: function(r) {
    this.response = r;
    this.toggle('ok', true);
    this.loading(false);
    if (r.title) this.setTitle(r.title);
    if (r.submitTitle) this.setOkText(r.submitTitle);
    if (r.jsOptions) {
      //this.setOptions(r.jsOptions);
      if (r.jsOptions.onOkClose)
        this.addEvent('okClose', r.jsOptions.onOkClose);
    }
    this.setMessage(r.form, false);
    this.form = Ngn.Form.factory(this.message.getElement('form'), {
      ajaxSubmit: true,
      disableInit: true
    });
    this.form.options.dialog = this; // Важно создавать передавать объект Диалога в объект 
    // Формы после выполнения конструктура, иначе объект
    // Даилога не будет содержать созданого объекта Формы
    this.form.init();
    this.fireEvent('formResponse');
    //this.form.addEvent('jsComplete', function(r) {
    //new Fx.Scroll(document.body).toElement(this.dialog, 'y');
    //}.bind(this));

    this.form.addEvent('submit', function(r) {
      this.fireEvent('formRequest');
      this.loading(true);
    }.bind(this));

    this.form.addEvent('failed', function(r) {
      this.formResponse(r);
      this.loading(false);
    }.bind(this));

    this.form.addEvent('complete', function(r) {
      this.response = r;
      this.okClose();
      this.fireEvent('submitSuccess', r);
    }.bind(this));

    this.resizeByCols();
    if (this.options.autoSave) {
      new Ngn.frm.Saver(this.form.eForm, {
        url: this.options.submitUrl,
        jsonRequest: true
      });
    }
    this._formInit();
    this.formInit();
    this.initPosition();
  },

  resizeByCols: function() {
    var cols = this.form.eForm.getElements('.type_col');
    if (!cols.length) return;
    var maxY = 0;
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
    if (this.isOkClose) {
      if (this.response.nextFormUrl) {
        new Request.JSON({
          url: this.response.nextFormUrl,
          onComplete: function(r) {
            if (!r.form) throw new Error('Form does not exists in next form url "' + this.response.nextFormUrl + '"');
            new Ngn.Dialog.RequestForm.Static({
              staticResponse: r
            });
          }
        }).send();
      }
    }
  }

  // abstract
  //_submit: {}

});

Ngn.Dialog.Form = new Class({
  Extends: Ngn.Dialog.RequestFormBase,

  options: {
    onSubmit: $empty
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
    formEvents: false,
    formRequest: $empty
  },

  _submit: function() {
    this.form.submit();
  },

  _formInit: function() {
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
    this.formResponse(Ngn.JSON.process(this.options.staticResponse));
  }

});