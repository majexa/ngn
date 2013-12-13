Ngn.frm = {};
Ngn.frm.init = {}; // объект для хранения динамических функций иниыиализации
Ngn.frm.html = {};
Ngn.frm.selector = 'input,select,textarea';
Ngn.frm.textSelector = 'input[type=text],input[type=password],textarea';

Ngn.frm.getValueByName = function(name, parent) {
  return Ngn.frm.getValue(Ngn.frm.getElements(name, parent));
};

Ngn.frm.emptify = function(eInput) {
  if (eInput.get('type') == 'checkbox') eInput.set('checked', false);
  else eInput.get('value', '');
};

/**
 * @param Element|array of Element
 * @returns {*}
 */
Ngn.frm.getValue = function(el) {
  if (el.length === undefined) {
    var elements = el.getElements(Ngn.frm.selector);
  } else {
    var elements = el;
  }
  var r = null;
  elements.each(function(el){
    var type = el.get('type');
    if (type == 'radio' || type == 'checkbox') {
      if (el.get('checked'))
        r = el.get('value');
    } else {
      r = el.get('value');
    }
  });
  return r;
};

Ngn.frm.getValues = function(el) {
  if (el.length === undefined) {
    var elements = el.getElements(Ngn.frm.selector);
  } else {
    var elements = el;
  }
  var r = [];
  elements.each(function(el){
    var type = el.get('type');
    if (type == 'radio' || type == 'checkbox') {
      if (el.get('checked'))
        r.push(el.get('value'));
    } else {
      r = [el.get('value')];
    }
  });
  return r;
};

Ngn.frm.getElements = function(name, parent) {
  var elements = [];
  var n = 0;
  var _name;
  parent = parent || document;
  parent.getElements(Ngn.frm.selector).each(function(el) {
    _name = el.get('name');
    if (!_name) return;
    if (_name.replace('[]', '') != name) return;
    elements[n] = el;
    n++;
  });
  return elements;
};

Ngn.frm.disable = function(eForm, flag) {
  eForm.getElements(Ngn.frm.selector).each(function(el){
    el.set('disabled', flag);
  });
  //c(Ngn.frm.virtualElements);
  for (var i =0; i<Ngn.frm.virtualElements.length; i++) {
    var o = Ngn.frm.virtualElements[i];
    c([o, o.getForm()]);
    //if (o.getForm() && o.getForm().get('id') != eForm.get('id')) return;
    //o.toggleDisabled(!flag);
  };
};

Ngn.frm.virtualElements = [];
Ngn.frm.virtualElement = {
  // abstract toggleDisabled: function(flag) {},
  parentForm: null,
  initVirtualElement: function(el) {
    var eForm = el.getParent('form');
    if (!eForm) return;
    eForm.storeAppend('virtualElements', this);
  },
  getForm: function() {
  }
};

// формат callback ф-ии должен быть следующим:
// function (fieldValue, args) {}
Ngn.frm.addEvent = function(event, name, callback, args) {
  var elements = Ngn.frm.getElements(name);
  elements.each(function(el){
    el.addEvent(event, function(e){
      callback.run([Ngn.frm.getValue(elements), args], el);
    });
  });
}

Ngn.frm.VisibilityCondition = new Class({

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
    var toggleSection = function(v, isFx){
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
          flag ?
            (function () { this.fx.show(); }).delay(200, this) :
            (function () { this.fx.hide(); }).delay(200, this);
        else
          flag ? this.fx.slideIn() : this.fx.slideOut();
      } else {
        this.eSection.setStyle('display', flag ? 'block' : 'none');
        this.eSection.getElements(Ngn.frm.selector).each(function(el) {
          el.set('disabled', !flag);
        });
      }
    }.bind(this);
    toggleSection(Ngn.frm.getValueByName(condFieldName), false);
    Ngn.frm.addEvent('change', condFieldName, toggleSection, true);
    Ngn.frm.addEvent('focus', condFieldName, toggleSection, true);
  }
  
});

Ngn.frm.VisibilityCondition.Header = new Class({
  Extends: Ngn.frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.hgrp_' + this.sectionName;
  }
});

Ngn.frm.VisibilityCondition.Field = new Class({
  Extends: Ngn.frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.name_' + this.sectionName;
  }
});

Ngn.frm.headerToggleFx = function(btns) {
  btns.each(function(btn) {
    var eToggle = btn.getParent().getParent();
    btn.getParent().inject(eToggle, 'before');
    var setArrow = function(opened) {
      btn.set('value', '  '+(opened ? '↑' : '↓')+'  ');
    };
    var fx = new Fx.Slide(eToggle, {
      duration: 300,
      transition: Fx.Transitions.Pow.easeOut,
      onComplete: function() {
        setArrow(opened);
        Ngn.storage.set(btn.get('data-name'), opened ? 1 : 0);
      }
    });
    var opened = true;
    var saved = Ngn.storage.get(btn.get('data-name'));
    if (!saved || saved == 0) {
      fx.hide();
      opened = false;
    }
    if (saved != undefined) setArrow(opened);
    btn.addEvent('click', function(e) {
      e.preventDefault();
      opened ? fx.slideOut() : fx.slideIn();
      opened = !opened;
    });
  });
};

Ngn.frm.headerToggle = function(esBtns) {
  esBtns.each(function(el){
    new Ngn.frm.HeaderToggle(el);
  });
};

Ngn.frm.HeaderToggle = new Class({
  Implements: [Options, Events],
  
  opened: false,
  
  initialize: function(eBtn, options) {
    this.setOptions(options);
    this.eBtn = eBtn;
    this.eHeader = this.eBtn.getParent();
    this.eToggle = this.eBtn.getParent().getParent();
    this.eHeader.inject(this.eToggle, 'before');
    var saved = Ngn.storage.get(eBtn.get('data-name'));
    if (saved == undefined) this.toggle(this.opened); 
    else this.toggle(saved);
    this.eBtn.addEvent('click', function(e) {
      e.preventDefault();
      this.toggle(!this.opened);
      Ngn.storage.set(this.eBtn.get('data-name'), this.opened);
    }.bind(this));
  },
  
  toggle: function(opened) {
    opened ?
      this.eHeader.removeClass('headerToggleClosed') :
      this.eHeader.addClass('headerToggleClosed');
    if (this.eBtn.get('tag') == 'input') this.eBtn.set('value', '  '+(opened ? '↑' : '↓')+'  ');
    this.eToggle.setStyle('display', opened ? 'block' : 'none');
    this.opened = opened;
    this.fireEvent('toggle', opened);
  }
  
});

Ngn.enumm = function(arr, tpl, glue) {
  if (!$defined(glue)) glue = '';
  for (var i=0; i<arr.length; i++)
    arr[i] = tpl.replace('{v}', arr[i]);
  return arr.join(glue);
};

Ngn.frm.getPureName = function($bracketName) {
  return $bracketName.replace(/(\w)\[.*/, '$1');
};

Ngn.frm.getBracketNameKeys = function(name) {
  var m;
  m = name.match(/([^[]*)\[/);
  if (!m) return [name];
  var keys = [];
  keys.extend([m[1]]);
  var re = /\[([^\]]*)\]/g;
  while (m = re.exec(name)) {
    keys.extend([m[1]]);
  }
  return keys;
};

Ngn.frm.fillEmptyObject = function(object, keys) {
  for (var i=0; i<keys.length-1; i++) {
    var p = 'object'+(Ngn.enumm(keys.slice(0, i+1), "['{v}']"));
    eval('if (!$defined('+p+')) '+p+' = {}');
  }
};

Ngn.frm.setValueByBracketName = function(o, name, value) {
  var _name = name.replace('[]', '');
  if (!(o instanceof Object)) throw new Error('o is not object');
  var keys = Ngn.frm.getBracketNameKeys(_name);
  Ngn.frm.fillEmptyObject(o, keys);
  var p = 'o';
  for (var i=0; i<keys.length; i++) p += "['"+keys[i]+"']";
  if (name.contains('[]')) {
    eval(p+' = $defined('+p+') ? '+p+'.concat(value) : [value]');
  } else {
    //eval(p+' = $defined('+p+') ? [].concat('+p+', value) : value');
    eval(p+' = value');
  }
  return o;
};

Ngn.frm.objTo = function(eContainer, obj) {
  for (var i in obj) {
    eContainer.getElement('input[name='+i+']').set('value', obj[i]);
  }
};

Ngn.frm.toObj = function(eContainer, except) {
  var rv = {};
  var name;
  except = except || [];
  eContainer = $(eContainer);
  var typeMatch =
    'text' + 
    (!except.contains('hidden') ? '|hidden' : '') + 
    (!except.contains('password') ? '|password' : '');
  var elements = eContainer.getElements(Ngn.frm.selector);
  for (var i = 0; i < elements.length; i++) {
    var el = elements[i];
    if (!el.name) continue;
    var pushValue = undefined;
    if (el.get('tag') == 'textarea' && el.get('aria-hidden')) {
      // Значит из этой texarea был сделан tinyMce
      pushValue = tinyMCE.get(el.get('id')).getContent();
    } else if (
      (el.get('tag') == 'input' && 
         el.type.match(new RegExp('^'+typeMatch+'$', 'i'))) ||
       el.get('tag') == 'textarea' ||
      (el.get('type').match(/^checkbox|radio$/i) && el.get('checked'))
    ) {
      pushValue = el.value;
    } else if (el.get('tag') == 'select') {
      if (el.multiple) {
        var pushValue = [];
        for (var j = 0; j < el.options.length; j++)
          if (el.options[j].selected)
            pushValue.push(el.options[j].value);
        if (pushValue.length == 0) pushValue = undefined;
      } else {
        pushValue = el.options[el.selectedIndex].value;
      }
    }
    if (pushValue != undefined) {
      Ngn.frm.setValueByBracketName(rv, el.name, pushValue);
    }
  }
  return rv;
};

Ngn.frm.initTranslateField = function(eMasterField, eTranslatedField) {
  var eMasterField = $(eMasterField);
  var eTranslatedField = $(eTranslatedField);
  //if (!eMasterField || !eTranslatedField) return;
  var translatedValueExists = eTranslatedField.get('value') ? true : false;
  var translatedFieldEdited = false;
  var translateField = function() {
    if (translatedValueExists || translatedFieldEdited) return;
    eTranslatedField.set('value', translate(trim(eMasterField.get('value'))));
  };
  eMasterField.addEvent('keyup', translateField);
  eMasterField.addEvent('blur', translateField);
  eMasterField.addEvent('click', translateField);
  eTranslatedField.addEvent('keyup', function(e){
    translatedFieldEdited = true;
  });
};

Ngn.frm.initCopySelectValue = function(eSelectField, eSlaveField, param) {
  if (!$defined(param)) param = 'value';
  var eSelectField = $(eSelectField);
  var eSlaveField = $(eSlaveField);
  eSlaveField.addEvent('keyup', function(){
    eSlaveField.store('edited', true);
  });
  eSelectField.addEvent('change', function(){
    if (eSlaveField.retrieve('edited')) return;
    eSlaveField.set('value', eSelectField.options[eSelectField.selectedIndex].get(param));
    eSlaveField.fireEvent('blur');
  });
};

Ngn.frm.initCopySelectTitle = function(eSelectField, eSlaveField) {
  Ngn.frm.initCopySelectValue(eSelectField, eSlaveField, 'text');
};

Ngn.frm.makeDialogabble = function(eLink, action, options) {
  eLink.addEvent('click', function(e) {
    e.preventDefault();
    new Ngn.Dialog.RequestForm(Object.merge({
      url: eLink.get('href').replace(action, 'json_'+action),
      onSubmitSuccess: function() {
        window.location.reload();
      }
    }, options || {}));
 });
};

Ngn.frm.storable = function(eInput) {
  if (!eInput.get('id')) throw new Error('ID param mast be defined');
  var store = function() {
    Ngn.storage.set(eInput.get('id'), eInput.get('value'));
  };
  var restore = function() {
    eInput.set('value', Ngn.storage.get(eInput.get('id')));
  };
  restore();
  eInput.addEvent('keypress', function() {
    (function() {
      store();
    }).delay(100);
  });
  eInput.addEvent('blur', function() {
    store();
  });
}