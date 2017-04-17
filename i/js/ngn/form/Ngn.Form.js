/**
 * Класс `Ngn.Form` в паре с серверным PHP классом `Form` образует свзяку для работы с HTML-формами
 *
 * ###Основные задачи###
 *
 *  - Инициализацию динамически сгенерированого на сервере JavaScript'а
 *  - Валидацию полей
 *  - Сабмит
 *  - Интерфейс колонок, свёртываемых блоков, прикрепленных файлов
 *  - Активацию/дезактивацию полей
 *  - Инициализацию загрузчика файлов
 */
Ngn.Form = new Class({
  Implements: [Options, Events, Class.Occlude],

  options: {
    equalElementHeights: false, // [boolean] Уравнивать высоты элементов формы
    dialog: null, // [null|Ngn.Dialog] Диалог, из которого была создана форма
    focusFirst: false, // [boolean] Делать фокус на первом элементе
    ajaxSubmit: false, // [boolean] Сабмитить форму ajax-ом
    disableInit: false, // [boolean] Не производить инициализацию в формы в конструкторе
    requestOptions: {} // [Object] Опции AJAX запроса
  },

  els: {},

  initialize: function(eForm, options) {
    this.eForm = eForm;
    this.eOutsideContainer = new Element('div', {styles: {'display': 'none'}}).inject(this.eForm, 'after');
    if (this.eForm.get('data-init')) throw new Error('This form already initialized');
    this.eForm.set('data-init', true);
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
        var f = this.eForm.getElement(Ngn.Frm.textSelector);
        if (f) f.focus();
      }
    }
    // Если у первого элемента есть плейсхолдер, значит и у всех остальных. Инициализируем кроссбрауузерные плейсхолдеры (для IE9)
    var eFirstTextInput = this.eForm.getElement(Ngn.Frm.textSelector);
    if (eFirstTextInput && eFirstTextInput.get('placeholder')) new Ngn.PlaceholderSupport();
    this.eForm.getElements('input[type=text],input[type=password]').each(function(el) {
      el.addEvent('keypress', function(e) {
        if (e.key == 'enter') this.submit();
      }.bind(this));
    }.bind(this));
  },

  initValidation: function() {
    var opts = {};
    opts.evaluateOnSubmit = false;
    if (this.options.dialog) {
      opts.scrollToErrorsOnSubmit = false;
      // opts.scrollElement = this.options.dialog.message;
    }
    this.validator = new Ngn.Form.Validator(this, opts);
  },

  initDynamicJs: function() {
    console.log('init_Dynamic_Js');
    var js = $(this.eForm.get('id') + 'js');
    if (js) {
      Asset.javascript(js.get('html'), {
        onLoad: function() {
          var func = eval('Ngn.Frm.init.' + this.eForm.get('id'));
          if (func) func();
          this.fireEvent('jsComplete');
        }.bind(this)
      });
    }
  },

  initInlineJs: function() {
    var js = $(this.eForm.get('id') + 'jsInline');
    console.log(js.get('html'));//
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
      var eFileNav = el.getElement('.fileNav');
      if (!eFileNav) return;
      eFileNav.inject(el.getElement('.label'), 'top');
    });
    Ngn.Milkbox.add(this.eForm.getElements('a.lightbox'));
  },

  initActive: function() {
    this.eForm.getElements(Ngn.Frm.textSelector).each(function(el) {
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
      Ngn.Frm.disable(this.eForm, flag);
    } else {
      var eSubmit = this.eForm.getElement('input[type=submit]');
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
    } else if (this.uploadType == 'default' && !this.options.ajaxSubmit) {
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
    console.log('initUpload');
    if (!this.hasFilesFields()) return;
    if (!opt || !opt.url) throw Error("$options['uploadOptions']['url'] of php Form object must be defined. Use UploadTemp::extendFormOptions to add this option to Form object");
    this.uploadOptions = opt;
    if ('FormData' in window) this.initHtml5Upload();
    if (this.uploadType == 'default') {
      this.uploadType = 'iframe';
      this.initIframeRequest();
    }
  },

  uploads: [],

  submitHtml5: function() {
    this.uploads.each(function(upload) {
      upload.send(false);
    }.bind(this));
  },

  initHtml5Upload: function() {
    if (!this.hasFilesFields()) return;
    this.uploadType = 'html5';
    this.eForm.getElements('input[type=file]').each(function(eInput) {
      if (eInput.retrieve('uploadInitialized')) return;
      eInput.store('uploadInitialized', true);
      var cls = eInput.get('multiple') ? 'multiUpload' : 'upload';
      var eInputValidator = new Element('input', {
        type: 'hidden'
        //name: eInput.get('name') + '_helper'
      }).inject(eInput, 'after');
      var fileSaved = eInput.getParent('.element').getElement('.fileSaved');
      if (!fileSaved) eInputValidator.addClass(eInput.hasClass('required') ? 'validate-' + cls + '-required' : 'validate-' + cls);
      if (eInput.get('data-file')) eInputValidator.set('value', 1);
      var name = eInput.get('name');
      this.oneFileCompleteEventFired = false;
      var uploadOptions = {
        url: this.uploadOptions.url.replace('{fn}', name),
        loadedFiles: this.uploadOptions.loadedFiles,
        fileEvents: {
          change: function() {
            eInputValidator.set('value', 1);
          },
          empty: function() {
            eInputValidator.set('value', '');
          }
        },
        onComplete: function(r) {
          if (this.allUploadsIsEmpty() && this.oneFileCompleteEventFired) {
            // try to submit if no uploads, but one complete event already fired
            return;
          }
          this.oneFileCompleteEventFired = true;
          if (this.hasUploadsInProgress()) {
            // try to submit, but there are uploads in progress
            return;
          }
          this.submitedAndUploaded(r);
        }.bind(this)
      };
      if (!eInput.get('multiple')) {
        this.addUpload(new Ngn.Form.Upload.Single(this, eInput, uploadOptions));
      } else {
        uploadOptions.url += '&multiple=1';
        this.addUpload(new Ngn.Form.Upload.Multi(this, eInput, uploadOptions));
      }
    }.bind(this));
  },

  submitedAndUploaded: function() {
    this.submitAjax();
  },

  /**
   * @property upload Ngn.Form.Upload
   */
  addUpload: function(upload) {
    this.uploads.push(upload);
  },

  allUploadsIsEmpty: function() {
    for (var i = 0; i < this.uploads.length; i++) {
      if (this.uploads[i].file) return false;
    }
    return true;
  },

  hasUploadsInProgress: function() {
    for (var i = 0; i < this.uploads.length; i++) {
      if (this.uploads[i].inProgress) return true;
    }
    return false;
  },

  hasFilesFields: function() {
    return this.eForm.getElements('input[type=file]').length != 0;
  },

  initHeaderToggle: function() {
    var htBtns = this.eForm.getElements('.type_headerToggle .toggleBtn');
    var ht = [];
    if (htBtns) {
      for (var i = 0; i < htBtns.length; i++)
        ht.push(new Ngn.Frm.HeaderToggle(htBtns[i]));
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

  // @requiresBefore Ngn.Frm.VisibilityCondition.Header
  initVisibilityConditions: function() {
    var vc = this.eForm.getElement('.visibilityConditions');
    if (!vc) return;
    vc = JSON.decode(vc.get('html'));
    for (var i = 0; i < vc.length; i++) {
      var cls = Ngn.Frm.VisibilityCondition[Ngn.String.ucfirst(vc[i][3])];
      this.visibilityConditions[vc[i][0]] = new cls(this.eForm, vc[i][0], vc[i][1], vc[i][2]);
    }
  },

  resetVisibilityConditionOfFieldSection: function(eInput) {
    var eHgrp = eInput.getParent().getParent('.hgrp');
    if (!eHgrp) return;
    var headerName = eHgrp.get('class').replace(/.* hgrp_(\w+) .*/, '$1');
    if (headerName && this.visibilityConditions[headerName])
      (function() {
        this.visibilityConditions[headerName].show();
      }).delay(500, this);
  },

  initValues: {},

  initAutoGrow: function() {
    this.eForm.getElements('textarea').each(function(el) {
      new AutoGrow(el);
    });
  },

  initIframeRequest: function() {
    this.iframeRequest = new Ngn.IframeFormRequest.JSON(this.eForm);
    return this.iframeRequest;
  },

  addElements: function(eRow) {
    eRow.getElements('.element').each(function(el) {
      Ngn.Form.ElInit.factory(this, Ngn.Form.getElType(el));
    }.bind(this));
    this.initHtml5Upload();
  },

  initFileNav: function() {
    this.eForm.getElements('.fileNav').each(function(eFileNav) {
      Ngn.Btn.addAjaxAction(eFileNav.getElement('.delete'), 'delete', function() {
        eFileNav.dispose();
      });
    });
  },

  submitAjax: function() {
    this.options.ajaxSubmit ? this._submitAjax() : this._submit();
  },

  _submitAjax: function() {
    var failed = false;
    new Ngn.Request.JSON(Object.merge({
      url: this.options.ajaxSubmitUrl || this.eForm.get('action'),
      onFailure: function(r) {
        this.fireEvent('failed', JSON.decode(r.responseText));
        failed = true;
      }.bind(this),
      onComplete: function(r) {
        setTimeout(function() {
          this.disable(false);
          this.submiting = false;
          if (failed) return;
          if (r && (r.error || r.form)) {
            this.fireEvent('failed', r);
            return;
          }
          this.fireEvent('complete', r);
        }.bind(this), 1);
      }.bind(this)
    }, this.options.requestOptions)).post(Ngn.Frm.toObj(this.eForm));
  },

  _submit: function() {
    this.eForm.submit();
  },

  showGlobalError: function(message) {
    if (this.eGlobalError) this.eGlobalError.dispose();
    var html = '<div class="element errorRow padBottom"><div class="validation-advice">' + message + '</div></div>';
    this.eGlobalError = Elements.from(html)[0].inject(this.eForm, 'top');
  }

});


Ngn.Form.factories = {};
Ngn.Form.registerFactory = function(id, func) {
  Ngn.Form.factories[id] = func;
};

Ngn.Form.factory = function(eForm, opts) {
  eForm = document.id(eForm, true);
  if (Ngn.Form.factories[eForm.get('id')]) {
    return Ngn.Form.factories[eForm.get('id')](eForm, opts);
  }
  var name = 'Ngn.' + (eForm.get('data-class') || 'Form');
  var cls = eval(name);
  if (!cls) throw new Error('class ' + name + ' not found');
  return new cls(eForm, opts);
};

Ngn.Form.forms = {};
Ngn.Form.elOptions = {};

Ngn.Form.ElInit = new Class({

  initialize: function(form, type) {
    this.form = form;
    this.type = type;
    this.init();
  },

  init: function() {
    var els = this.form.eForm.getElements('.type_' + this.type);
    if (!els.length) throw new Error('No ".type_' + this.type + '" elements was found. Maybe use FieldEAbstract::_html() instead of html()');
    els.each(function(eRow) {
      if (!eRow.get('data-typejs')) return;
      var clsName = 'Ngn.Form.El.' + Ngn.String.ucfirst(this.type)
      var cls = eval(clsName);
      if (cls === undefined) throw new Error('Class "' + clsName + '" is not defined');
      if (eRow.retrieve('initialized')) return;
      new cls(this.type, this.form, eRow);
      eRow.store('initialized', true);
    }.bind(this));
  }

});

// ------------------- Form Elements Framework ----------------------

Ngn.Form.ElInit.factory = function(form, type) {
  var cls = eval('Ngn.Form.ElInit.' + Ngn.String.ucfirst(type));
  if (cls) return new cls(form, type);
  return new Ngn.Form.ElInit(form, type);
};

Ngn.Form.getElType = function(el) {
  return el.get('class').replace(/.*type_(\w+).*/, '$1');
};

Ngn.Form.elN = 0;
Ngn.Form.El = new Class({
  options: {},
  initialize: function(type, form, eRow) {
    this.type = type;
    this.form = form;
    Ngn.Form.elN++;
    this.eRow = eRow;
    this.eRow.n = Ngn.Form.elN;
    this.name = eRow.get('data-name');
    this.form.els[this.name] = this;
    if (Ngn.Form.elOptions[this.name]) this.options = Ngn.Form.elOptions[this.name];
    this.init();
  },
  fireFormElEvent: function(event, value) {
    this.form.fireEvent('el' + Ngn.String.ucfirst(this.name) + Ngn.String.ucfirst(event), value);
  },
  init: function() {
  }
});

// ------------------- Form Elements Framework End -------------------

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
        this.resetVisibilityConditionOfFieldSection(eInput);
      }.bind(form),
      elementPass: function(eInput, name) {
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
    // убираем все эдвайсы при фокусе на поле
    this.getFields().each(function(field) {
      field.addEvent('focus', this.reset.bind(this));
    }.bind(this));
  },

  lastAdvices: {},

  makeAdvice: function(className, field, error, warn) {
    var advice;
    var errorMsg = (warn) ? this.warningPrefix : this.errorPrefix;
    errorMsg += (this.options.useTitles) ? field.title || error : error;
    var cssClass = (warn) ? 'warning-advice' : 'validation-advice';
    var adviceWrapper = this.getAdvice(className, field);
    if (!adviceWrapper) {
      advice = new Element('div', {
        html: errorMsg
      }).addClass('advice').addClass(cssClass);
      adviceWrapper = new Element('div', {
        styles: {display: 'none'},
        id: 'advice-' + className.split(':')[0] + '-' + this.getFieldId(field)
      }).addClass('advice-wrapper').grab(advice);
      adviceWrapper.grab(new Element('div', {'class': 'corner'}), 'top').setStyle('z-index', 300);
      field.store('$moo:advice-' + className, adviceWrapper);
    } else {
      advice = adviceWrapper.getElement('.advice');
      advice.set('html', errorMsg);
    }
    this.lastAdvices[field.get('name')] = className;
    return adviceWrapper;
  },

  showNewAdvice: function(className, field, error) {
    var advice = this.getAdvice(className, field);
    if (!advice) {
      advice = this.makeAdvice(className, field, error);
      this.insertAdvice(advice, field);
    }
    this.showAdvice(className, field);
    field.addEvent('keypress', function() {
      this.hideAdvice(className, field);
    }.bind(this));
    field.addEvent('change', function() {
      this.hideAdvice(className, field);
    }.bind(this));
    field.focus();
  },

  hideLastAdvice: function(field) {
    if (!this.lastAdvices[field.get('name')]) return;
    this.hideAdvice(this.lastAdvices[field.get('name')], field);
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
    if (element.type == 'select-one' || element.type == 'select') {
      return !(element.selectedIndex >= 0 && element.options[element.selectedIndex].value != '');
    } else if (element.type == 'file') {
      return element.get('data-file') == null;
    } else {
      return ((element.get('value') == null) || (element.get('value').length == 0));
    }
  }
});

Ngn.getReadableFileSizeString = function(fileSizeInBytes) {
  var i = -1;
  var byteUnits = [' Кб', ' Мб', ' Гб'];
  do {
    fileSizeInBytes = fileSizeInBytes / 1024;
    i++;
  } while (fileSizeInBytes > 1024);
  return Math.max(fileSizeInBytes, 0.1).toFixed(0) + byteUnits[i];
};

// @requiresBefore s2/js/locale?key=form

Form.Validator.addAllThese([['should-be-changed', {
  errorMsg: 'значение этого поля должно быть изменено',
  test: function(element) {
    if (Ngn.Form.forms[element.getParent('form').get('id')].initValues[element.get('name')] == element.get('value'))
      return false; else
      return true;
  }
}], ['validate-num-min', {
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
}], ['validate-num-max', {
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
}], ['validate-name', {
  errorMsg: 'должно содержать только латинские символы, тире, подчеркивание и не начинаться с цифры',
  test: function(element) {
    if (!element.value) return true;
    if (element.value.match(/^[a-z][a-z0-9-_]*$/i)) return true; else return false;
  }
}], ['validate-fullName', {
  errorMsg: 'неправильный формат имени',
  test: function(element) {
    //return true;
    if (!element.value) return true;
    if (element.value.match(/^\S+\s+\S+\s+\S+.*$/i)) return true; else return false;
  }
}], ['validate-domain', {
  errorMsg: 'неправильный формат',
  test: function(element) {
    if (!element.value) return true;
    if (element.value.match(/^[a-z][a-z0-9-.]*[a-z]$/i)) return true; else return false;
  }
}], ['validate-phone', {
  errorMsg: 'неправильный формат',
  test: function(element) {
    if (!element.value) return true;
    element.value = element.value.trim();
    element.value = element.value.replace(/[\s\-\(\)]/g, '');
    element.value = element.value.replace(/^8(.*)/g, '+7$1');
    return /^\+\d{11}$/g.test(element.value);
  }
}], ['validate-procent', {
  errorMsg: 'введите число от 0 до 100',
  test: function(element) {
    if (!element.value) return true;
    element.value = parseInt(element.value);
    return (element.value >= 0 && element.value <= 100);
  }
}], ['validate-skype', {
  errorMsg: 'неправильный формат',
  test: function(element) {
    if (!element.value) return true;
    if (element.value.length > 32 || element.value.length < 6) return false;
    if (element.value.match(/^[a-z][a-z0-9._]*$/i)) return true; else return false;
  }
}], ['required-wisiwig', {
  errorMsg: 'поле обязательно для заполнения',
  test: function(element) {
    return !!Ngn.clearParagraphs(tinyMCE.get(element.get('id')).getContent());
  }
}], ['validate-request', {
  errorMsg: 'Дождитесь загрузки',
  test: function(element) {
    return element.get('value') == 'complete' ? true : false;
  }
}], ['validate-upload-required', {
  errorMsg: Ngn.Locale.get('Form.fileNotChosen'),
  test: function(element) {
    return element.get('value') ? true : false;
  }
}], ['validate-multiUpload-required', {
  errorMsg: 'Файлы не выбраны',
  test: function(element) {
    return element.get('value') ? true : false;
  }
}], ['maxFileSizeExceeded', {
  errorMsg: 'Превышен максимальный размер файла ' + Ngn.getReadableFileSizeString(Ngn.fileSizeMax),
  test: function() {
    return false;
  }
}]]);
