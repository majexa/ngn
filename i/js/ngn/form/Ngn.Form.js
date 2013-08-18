Ngn.Form = new Class({
  Implements: [Options, Events, Class.Occlude],

  options: {
    equalElementHeights: false,
    dialog: false,
    focusFirst: true,
    ajaxSubmit: false,
    disableInit: false
    //onNewElement: function(el) {}
    //onComplete: null
  },

  els: {},

  initialize: function(eForm, options) {
    this.eForm = eForm;
    if (this.eForm.get('data-init')) throw new Error('This form already initialized');
    this.eForm.set('data-init', true);
    this.initOptions = options;
    if ((options && !options.forceOcclude) && this.occlude(this.eForm.get('id'), this.eForm)) return this.occluded;
    Ngn.Form.forms[this.eForm.get('id')] = this;
    this.id = this.eForm.get('id');
    this.setOptions(options);
    if (!this.options.disableInit) this.init();
  },

  init: function() {
    // core
    this.initDynamicJs();
    this.initInlineJs();
    this.initValidation();
    this.initSubmit();
    // more
    this.initMooRainbow();
    this.initVisibilityConditions();
    this.initHeaderToggle();
    this.initFileNav();
    this.initActive();
    this.initCols();
    this.initImagePreview();
    if (this.options.focusFirst) {
      var focused = false;
      var eFirstAdvice = this.eForm.getElement('.static-advice');
      if (eFirstAdvice) {
        var eInput = eFirstAdvice.getParent('.element').getElement('input');
        if (eInput) {
          focused = true;
          eInput.focus();
        }
      }
      if (!focused) {
        var f = this.eForm.getElement(Ngn.frm.textSelector);
        if (f) f.focus();
      }
    }
    this.eForm.getElements('input[type=text],input[type=password]').each(function(el) {
      el.addEvent('keypress', function(e) {
        if (e.key == 'enter') this.submit();
      }.bind(this));
    }.bind(this));
  },

  initValidation: function() {
    var opts = {};
    if (this.options.dialog) {
      opts.scrollToErrorsOnSubmit = false;
      //opts.scrollElement = this.options.dialog.message;
    }
    this.validator = new Ngn.Form.Validator(this, opts);
    /*
     var eFirstError = this.eForm.getElement('.advice-wrapper');
     if (eFirstError) {
     (function() {
     this.validator.getScrollFx().toElement(eFirstError.getParent());
     }).delay(100, this);
     }
     */
  },

  initDynamicJs: function() {
    var js = $(this.eForm.get('id') + 'js');
    if (js) {
      Asset.javascript(js.get('html'), {
        onLoad: function() {
          var func = eval('Ngn.frm.init.' + this.eForm.get('id'));
          if (func) func();
          this.fireEvent('jsComplete');
        }.bind(this)
      });
    }
  },

  initInlineJs: function() {
    var js = $(this.eForm.get('id') + 'jsInline');
    if (js) {
      try {
        eval(js.get('html'));
      } catch (e) {
        throw new Error('Error in code: ' + js.get('html') + "\nerror:" + e.toString());
      }
    }
  },

  initImagePreview: function() {
    this.eForm.getElements('.elImagePreview').each(function(el) {
      eFileNav = el.getElement('.fileNav');
      if (!eFileNav) return;
      eFileNav.inject(el.getElement('.label'), 'top');
    });
    Ngn.lightbox.add(this.eForm.getElements('a.lightbox'));
  },

  initActive: function() {
    this.eForm.getElements(Ngn.frm.textSelector).each(function(el) {
      this.initActiveEl(el);
    }.bind(this));
  },

  initActiveEl: function(el) {
    el.addEvent('focus', function() {
      this.addClass('active');
    });
    el.addEvent('blur', function() {
      this.removeClass('active');
    });
  },

  initCols: function() {
    var cols = this.eForm.getElements('.type_col');
    for (var i = 0; i < cols.length; i++) {
      var children = cols[i].getChildren();
      var eColBody = new Element('div', {'class': 'colBody'}).inject(cols[i]);
      for (var j = 0; j < children.length; j++)
        children[j].inject(eColBody);
    }
  },

  disable: function(flag) {
    if (this.options.ajaxSubmit) {
      Ngn.frm.disable(this.eForm, flag);
    } else {
      eSubmit = this.eForm.getElement('input[type=submit]');
      if (eSubmit) {
        eSubmit.addClass('disabled');
        eSubmit.set('disabled', flag);
      }
    }
  },

  submit: function() {
    if (this.submiting) return false;
    if (!this.validator.validate()) return false;
    this.fireEvent('submit');
    this.disable(true);
    this.submiting = true;
    if (this.uploadType == 'html5') {
      this.submitHtml5();
    } else if (this.uploadType == 'default' && !this.options.dialog) {
      this.eForm.submit();
    } else {
      this.submitAjax();
    }
    return true;
  },

  initSubmit: function() {
    this.eForm.addEvent('submit', function(e) {
      e.preventDefault();
      this.submit();
    }.bind(this));
  },

  uploadType: 'default',
  uploadOptions: null,

  initUpload: function(opt) {
    if (!this.hasFilesFields()) return;
    if (!opt || !opt.url) throw Error("$options['uploadOptions']['url'] of php Form object must be defined. Use UploadTemp::extendFormOptions to add this option to Form object");
    this.uploadOptions = opt;
    if ('FormData' in window) this.initHtml5Upload();
    if (this.uploadType == 'default') {
      if (Browser.Plugins.Flash.version && opt && opt.url) {
        this.uploadType = 'fancy';
        this.initFancyUpload();
      } else if (this.options.dialog) {
        this.uploadType = 'iframe';
        this.initIframeRequest();
      }
    }
  },

  submitHtml5: function() {
    this.upload.send(this.multiUpload);
  },

  initHtml5Upload: function() {
    this.eForm.getElements('input[type=file]').each(function(eInput) {
      this.uploadType = 'html5';
      /*
       eInputValidator = new Element('input', {
       type: 'hidden',
       'class': eInput.hasClass('required') ?
       'validate-multiUpload-required' : 'validate-multiUpload'
       }).inject(eInput, 'after');
       */
      var Nname = eInput.get('name');
      var uploadOptions = {
        url: this.uploadOptions.url.replace('{fn}', name),
        loadedFiles: this.uploadOptions.loadedFiles,
        fileOptions: {
          onAdd: function() {
            //eInputValidator.set('value', 1);
          },
          onEmpty: function() {
            //eInputValidator.set('value', '');
          }
        },
        onComplete: function() {
          this.submitAjax();
        }.bind(this)
      };
      if (!eInput.get('multiple')) {
        this.upload = new Ngn.Form.Upload.Single(this.eForm, eInput, uploadOptions);
      } else {
        uploadOptions.url += '&multiple=1';
        //eInput.set('name', eInput.get('name')+'[]');
        this.upload = new Ngn.Form.Upload.Multi(this.eForm, eInput, uploadOptions);
      }
    }.bind(this));
  },

  hasFilesFields: function() {
    return this.eForm.getElements('input[type=file]').length != 0;
  },

  initHeaderToggle: function() {
    var htBtns = this.eForm.getElements('.type_headerToggle .toggleBtn');
    var ht = [];
    if (htBtns) {
      for (var i = 0; i < htBtns.length; i++)
        ht.push(new Ngn.frm.HeaderToggle(htBtns[i]));
    }
    if (this.options.equalElementHeights) {
      this.setEqualHeights();
      for (i = 0; i < ht.length; i++)
        ht[i].addEvent('toggle', function(open) {
          if (open) this.setEqualHeights();
        }.bind(this));
    }
  },

  visibilityConditions: [],

  setEqualHeights: function() {
    this.eForm.getElements('.hgrp').each(function(eHgrp) {
      Ngn.equalItemHeights(eHgrp.getElements('.element').filter(function(el) {
        return !el.hasClass('subElement');
      }));
    });
  },

  initVisibilityConditions: function() {
    var vc = this.eForm.getElement('.visibilityConditions');
    if (!vc) return;
    vc = JSON.decode(vc.get('html'));
    for (var i = 0; i < vc.length; i++) {
      var cls = eval('Ngn.frm.VisibilityCondition.' + ucfirst(vc[i][3]));
      this.visibilityConditions[vc[i][0]] = new cls(this.eForm, vc[i][0], vc[i][1], vc[i][2]);
    }
  },

  resetVisibilityConditionOfFieldSection: function(eInput) {
    var eHgrp = eInput.getParent().getParent('.hgrp');
    if (!eHgrp) return;
    var headerName = eHgrp.get('class').replace(/.* hgrp_(\w+) .*/, '$1');
    if (headerName && this.visibilityConditions[headerName])
      (function() {
        this.visibilityConditions[headerName].fx.show();
      }).delay(500, this);
  },

  initValues: {},

  initMooRainbow: function() {
    this.eForm.getElements('.type_color').each(function(el) {
      var eColor = el.getElement('div.color');
      var eInput = el.getElement('input').addClass('hexInput');
      eInput.addEvent('change', function() {
        eColor.setStyle('background-color', eInput.value);
      });
      new MooRainbow(eInput, {
        eParent: eInput.getParent(),
        id: 'rainbow_' + eInput.get('name'),
        //styles: { // и так работает
        //  'z-index': this.options.dialog.dialog.getStyle('z-index').toInt() + 1
        //},
        imgPath: '/i/img/rainbow/small/',
        wheel: true,
        startColor: eInput.value ? new Color(eInput.value).rgb : [255, 255, 255],
        onChange: function(color) {
          eColor.setStyle('background-color', color.hex);
          eInput.value = color.hex;
          eInput.fireEvent('change', color);
        },
        onComplete: function(color) {
          eColor.setStyle('background-color', color.hex);
          eInput.value = color.hex;
          eInput.fireEvent('change', color);
        }
      });
    }.bind(this));
  },

  initAutoGrow: function() {
    this.eForm.getElements('textarea').each(function(el) {
      new AutoGrow(el);
    });
  },

  fuOptions: null,

  initFancyUpload: function() {
    this.fuOptions = $merge({
      chooseBtnTitle: 'Выбрать',
      hideHelp: false
    }, this.uploadOptions);
    this._initFancyUpload(this.eForm);
  },

  /**
   * options: {
   *   url: 'http://asdasd'
   *   loadedFiles: [ ... ]   // $_FILES format
   * }
   */
  _initFancyUpload: function(eContainer) {
    var name, eDiv, eBtn, eList, eInputComplete, opts, options;
    eContainer.getElements('input[type=file]').each(function(eInput) {
      // Не обрабатываем файлы внутри fieldSet'а
      if (eInput.getParent('.fieldSet')) return;
      if (this.fuOptions.hideHelp)
        eInput.getParent().getElement('.help').setStyle('display', 'none');
      // Заменяем стандартный input элементами интерфейса FancyUpload
      name = eInput.get('name');
      eDiv = new Element('div', {'class': 'fu-item'});
      eList = new Element('ul', {'class': 'fu-list'}).inject(eDiv);
      eBtn = Ngn.btn1(this.fuOptions.chooseBtnTitle, 'btn2');
      eBtn.inject(eDiv, 'top');
      eDiv.inject(eInput, 'after');
      eInputComplete = Elements.from('<input type="hidden" class="' + (eInput.hasClass('required') ? 'validate-fancyUpload-required' : 'validate-fancyUpload') + '">')[0].inject(eDiv);
      // Если файл уже загружен (режим редактирования)
      if (eInput.get('data-file')) {
        eInputComplete.set('value', 'complete');
      }
      var eFileNav = eInput.getParent('.element').getElement('.fileNav');
      options = $merge(this.fuOptions, {
        onOpening: function() {
          if (eFileNav) eFileNav.setStyle('display', 'none');
        },
        onFileStart: function() {
          eInputComplete.set('value', 'uploading');
        },
        onComplete: function(data) {
          eInputComplete.set('value', 'complete');
          this.validator.validateField(eInputComplete, true);
        }.bind(this)
      });
      eInput.dispose();
      opts = $merge({}, options);
      opts.url = opts.url.replace('{fn}', name); // Заменяем строку {fn} на имя поля
      opts.loadedFiles = [];
      if (options.loadedFiles && options.loadedFiles[name])
        opts.loadedFiles[0] = options.loadedFiles[name];
      new Ngn.UploadAttach(eList, eBtn, opts);
    }.bind(this));
  },

  initIframeRequest: function() {
    this.iframeRequest = new Ngn.IframeFormRequest.JSON(this.eForm);
    return this.iframeRequest;
  },

  addElements: function(eRow) {
    if (this.uploadType == 'fancy') this._initFancyUpload(eRow);
    eRow.getElements('.element').each(function(el) {
      Ngn.Form.ElInit.factory(this, Ngn.Form.getElType(el));
    }.bind(this));
  },

  initFileNav: function() {
    this.eForm.getElements('.fileNav').each(function(eFileNav) {
      Ngn.addAjaxAction(eFileNav.getElement('.delete'), 'delete', function() {
        eFileNav.dispose();
      });
    });
  },

  submitAjax: function() {
    if (this.options.ajaxSubmit)
      this._submitAjax(); else
      this._submit();
  },

  _submitAjax: function() {
    new Ngn.Request.JSON({
      url: this.eForm.get('action'),
      onComplete: function(r) {
        if (r && r.form) {
          this.fireEvent('failed', r);
          return;
        }
        this.fireEvent('complete', r);
      }.bind(this)
    }).post(Ngn.frm.toObj(this.eForm));
  },

  _submit: function() {
    this.eForm.submit();
  }

});

Ngn.Form.factory = function(eForm, opts) {
  eForm = document.id(eForm, true);
  var cls = eval('Ngn.' + (eForm.get('data-class') || 'Form'));
  return new cls(eForm, opts);
};

Ngn.Form.forms = {};
Ngn.Form.ElOptions = {};

Ngn.Form.ElInit = new Class({

  initialize: function(form, type) {
    this.form = form;
    this.type = type;
    this.init();
  },

  init: function() {
    this.form.eForm.getElements('.type_' + this.type).each(function(eRow) {
      var clsName = 'Ngn.Form.El.' + ucfirst(this.type)
      var cls = eval(clsName);
      if (cls === undefined) {
        c('Init class "' + clsName + '" for element type "' + this.type + '" not found');
        //throw new Error('Class "'+clsName+'" is not defined');
        return;
      }
      if (eRow.retrieve('initialized')) return;
      new cls(this.type, this.form, eRow);
      eRow.store('initialized', true);
    }.bind(this));
  }

});

// ------------------- Form Elements Framework ----------------------

Ngn.Form.ElInit.factory = function(form, type) {
  var cls = eval('Ngn.Form.ElInit.' + ucfirst(type));
  if (cls) return new cls(form, type);
  return new Ngn.Form.ElInit(form, type);
};

Ngn.Form.getElType = function(el) {
  return el.get('class').replace(/.*type_(\w+).*/, '$1');
};

Ngn.Form.ElN = 0;
Ngn.Form.El = new Class({

  options: {},

  initialize: function(type, form, eRow) {
    this.type = type;
    this.form = form;
    Ngn.Form.ElN++;
    this.eRow = eRow;
    this.eRow.n = Ngn.Form.ElN;
    this.name = eRow.get('class').replace(/(.* )?name_(\w+)( .*)?/, '$2');
    this.form.els[this.name] = this;
    if (Ngn.Form.ElOptions[this.name]) this.options = Ngn.Form.ElOptions[this.name];
    this.init();
  },

  init: function() {
  }

});

// ------------------- Form Elements Framework End -------------------

Ngn.Form.El.Autocompleter = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    new Ngn.Autocompleter(this.eRow.getElement('input.fld'), this.form.options.dialog ? {zIndex: this.form.options.dialog.options.baseZIndex + 10 } : {});
  }

});

Ngn.Form.El.User = new Class({
  Extends: Ngn.Form.El.Autocompleter
});

Ngn.Form.El.Textarea = new Class({
  Extends: Ngn.Form.El,

  resizebleOptions: {},

  init: function() {
    this.initResize();
  },

  initResize: function() {
    if (this.form.options.dialog && this.form.options.dialog.vResize) {
      this.resizebleOptions = $merge(this.resizebleOptions, {
        handler: this.form.options.dialog.vResize.eHandler
      });
    }
    new Ngn.ResizableTextarea(this.eRow, this.resizebleOptions);
  }

});

Ngn.Form.El.Wisiwig = new Class({
  Extends: Ngn.Form.El.Textarea,

  resizebleOptions: {
    resizableElementSelector: 'iframe'
  },

  init: function() {
    this.form.options.dialog.setWidth(500);
    var eCol = this.eRow.getParent('.type_col');
    Ngn.whenElPresents(this.eRow, '.mceLayout', function(eMceLayout) {
      // this.initResize(); используется только как одно в диалоге. для ресайза используется соотв. класс Ngn.Dialog.VResize.Wisiwig
      if (!eCol) return;
      var eColBody = eCol.getElement('.colBody');
      if (eColBody.getSize().x < eMceLayout.getSize().x) eColBody.setStyle('width', eMceLayout.getSize().x + 'px');
      if (this.form.options.dialog) this.form.options.dialog.resizeByCols();
      // Если высота всех элементов колонки меньше
      var colH = eCol.getParent('.colSet').getSize().y;
      var els = eCol.getElements('.element');
      var allColElsH = 0;
      for (var i = 0; i < els.length; i++) allColElsH += els[i].getSize().y;
    }.bind(this));
  }

});

Ngn.Form.El.WisiwigSimple = new Class({
  Extends: Ngn.Form.El.Wisiwig,

  init: function() {
    this.parent();
    var settings = new (this.getTinySettingsClass())().getSettings();
    if (this.options.tinySettings) settings = $merge(settings, this.options.tinySettings);
    new Ngn.TinyInit({
      parent: this.form.eForm,
      selector: '.type_' + this.type + ' textarea',
      settings: settings
    });
  },

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple;
  }

});

Ngn.Form.El.WisiwigSimple2 = new Class({
  Extends: Ngn.Form.El.WisiwigSimple,

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple.Links;
  }

});

Ngn.Form.Validator = new Class({
  Extends: Form.Validator.Inline,

  options: {
    showError: function(errorElement) {
      errorElement.setStyle('display', 'block');
    },
    hideError: function(errorElement) {
      errorElement.setStyle('display', 'none');
    },
    ignoreHidden: false,
    evaluateFieldsOnBlur: false
  },

  initialize: function(form, options) {
    if (!options) options = {};
    options.scrollElement = document.body;
    this.parent(form.eForm, options);
    this.addEvents({
      elementFail: function(eInput, name) {
        //eInput.getParents('.element')[0].addClass('errorRow');
        this.resetVisibilityConditionOfFieldSection(eInput);
      }.bind(form),
      elementPass: function(eInput, name) {
        //eInput.getParents('.element')[0].removeClass('errorRow');
        this.resetVisibilityConditionOfFieldSection(eInput);
      }.bind(form)
    });
    // при инициализации формы происходит фокус на первое поле. так что сообщение об ошибке пропадает
    // так что добавляем задержку для инициализации этой фичи
    (function() {
      // Добавляем событие для элементов, имеющих статические ошибки (созданные жестко в html)
      this.element.getElements('.static-advice').each(function(eAdvice) {
        eAdvice.getParent('.element').getElement('input').addEvent('focus', function() {
          eAdvice.dispose();
        });
      });
    }).delay(2000, this);

    /*
     this.getFields().each(function(field) {
     field.addEvent('focus', this.hideLastAdvice.pass(field, this));
     }.bind(this));
     */

  },

  lastAdvices: {},

  makeAdvice: function(className, field, error, warn) {
    var errorMsg = (warn) ? this.warningPrefix : this.errorPrefix;
    errorMsg += (this.options.useTitles) ? field.title || error : error;
    var cssClass = (warn) ? 'warning-advice' : 'validation-advice';
    var adviceWrapper = this.getAdvice(className, field);
    if (!adviceWrapper) {
      var advice = new Element('div', {
        html: errorMsg
      }).addClass('advice').addClass(cssClass);
      adviceWrapper = new Element('div', {
        id: 'advice-' + className.split(':')[0] + '-' + this.getFieldId(field)
      }).addClass('advice-wrapper').grab(advice);
      adviceWrapper.grab(new Element('div', {'class': 'corner'}), 'top').setStyle('z-index', 300);
    } else {
      var advice = adviceWrapper.getElement('.advice');
      advice.set('html', errorMsg);
    }
    field.store('$moo:advice-' + className, adviceWrapper);
    this.lastAdvices[field.get('name')] = className;
    return adviceWrapper;
  },

  showNewAdvice: function(className, field, error) {
    var advice = this.getAdvice(className, field);
    if (!advice) {
      var advice = this.makeAdvice(className, field, error);
      this.insertAdvice(advice, field);
    }
    this.showAdvice(className, field);
    field.addEvent('keypress', function() {
      this.hideAdvice(className, field);
    }.bind(this));
    field.focus();
  },

  hideLastAdvice: function(field) {
    if (!this.lastAdvices[field.get('name')]) return;
    this.hideAdvice(this.lastAdvices[field.get('name')], field);
  },

  getAdvice: function(className, field) {
    return field.retrieve('$moo:advice-' + className);
  },

  getPropName: function(className) {
    return 'advice-' + className;
  },

  insertAdvice: function(advice, field) {
    advice.inject(field.getParent('.field-wrapper'), 'after');
  },

  rewatchFields: function() {
    this.watchFields(this.getFields());
  },

  getScrollFx: function() {
    var par = this.options.scrollElement || document.id(this).getParent();
    return new Fx.Scroll(par, this.options.scrollFxOptions);
  }

});

Form.Validator.add('IsEmpty', {
  errorMsg: false,
  test: function(element) {
    if (element.type == 'select-one' || element.type == 'select')
      return !(element.selectedIndex >= 0 && element.options[element.selectedIndex].value != ''); else if (element.type == 'file')
      return element.get('data-file') == null; else
      return ((element.get('value') == null) || (element.get('value').length == 0));
  }
});

Form.Validator.addAllThese([
  ['should-be-changed', {
    errorMsg: 'значение этого поля должно быть изменено',
    test: function(element) {
      if (Ngn.Form.forms[element.getParent('form').get('id')].initValues[element.get('name')] == element.get('value'))
        return false; else
        return true;
    }
  }],
  ['validate-num-min', {
    errorMsg: 'слишком маленькое число',
    test: function(element, props) {
      if (!element.get('value')) return true;
      var strict = typeOf(element.get('data-strict')) != 'null';
      if (typeOf(element.get('data-min')) != 'null') {
        var value = parseFloat(element.get('value').replace(/\s/g, ''));
        element.set('value', value);
        var min = parseFloat(element.get('data-min'));
        return strict ? value > min : value >= min;
      }
    }
  }],
  ['validate-num-max', {
    errorMsg: 'слишком большое число',
    test: function(element, props) {
      if (!element.get('value')) return true;
      var strict = typeOf(element.get('data-strict')) != 'null';
      if (typeOf(element.get('data-max')) != 'null') {
        var value = parseFloat(element.get('value').replace(/\s/g, ''));
        element.set('value', value);
        var max = parseFloat(element.get('data-max'));
        return strict ? value < max : value <= max;
      }
    }
  }],
  ['validate-name', {
    errorMsg: 'должно содержать только латинские символы и не начинаться с цифры',
    test: function(element) {
      if (!element.value) return true;
      if (element.value.match(/^[a-z][a-z0-9-_]*$/i)) return true; else return false;
    }
  }],
  ['validate-fullName', {
    errorMsg: 'неправильный формат имени',
    test: function(element) {
      //return true;
      if (!element.value) return true;
      if (element.value.match(/^\S+\s+\S+\s+\S+.*$/i)) return true; else return false;
    }
  }],
  ['validate-domain', {
    errorMsg: 'неправильный формат',
    test: function(element) {
      if (!element.value) return true;
      if (element.value.match(/^[a-z][a-z0-9-.]*[a-z]$/i)) return true; else return false;
    }
  }],
  ['validate-phone', {
    errorMsg: 'неправильный формат',
    test: function(element) {
      if (!element.value) return true;
      element.value = trim(element.value);
      element.value = element.value.replace(/[\s\-\(\)]/g, '');
      element.value = element.value.replace(/^8(.*)/g, '+7$1');
      return /^\+\d{11}$/g.test(element.value);
    }
  }],
  ['validate-procent', {
    errorMsg: 'введите число от 0 до 100',
    test: function(element) {
      if (!element.value) return true;
      element.value = parseInt(element.value);
      return (element.value >= 0 && element.value <= 100);
    }
  }],
  ['validate-skype', {
    errorMsg: 'неправильный формат',
    test: function(element) {
      if (!element.value) return true;
      if (element.value.length > 32 || element.value.length < 6) return false;
      if (element.value.match(/^[a-z][a-z0-9._]*$/i)) return true; else return false;
    }
  }],
  ['required-wisiwig', {
    errorMsg: 'поле обязательно для заполнения',
    test: function(element) {
      if (!Ngn.clearParagraphs(tinyMCE.get(element.get('id')).getContent()))
        return false;
      return true;
    }
  }],
  ['validate-request', {
    errorMsg: 'Дождитесь загрузки',
    test: function(element) {
      if (element.get('value') == 'complete') return true;
      return false;
    }
  }],
  ['validate-fancyUpload', {
    errorMsg: 'Файл ещё не загружен',
    test: function(element) {
      if (element.get('value') == 'uploading') return false;
      return true;
    }
  }],
  ['validate-fancyUpload-required', {
    errorMsg: 'Файл не загружен',
    test: function(element) {
      if (element.get('value') == 'complete') return true;
      return false;
    }
  }],
  ['validate-multiUpload-required', {
    errorMsg: 'Файлы не выбраны',
    test: function(element) {
      if (element.get('value')) return true;
      return false;
    }
  }]
]);
