Ngn.Frm = {};
Ngn.Frm.init = {}; // объект для хранения динамических функций иниыиализации
Ngn.Frm.html = {};
Ngn.Frm.selector = 'input,select,textarea';
Ngn.Frm.textSelector = 'input[type=text],input[type=password],textarea';

Ngn.Frm.getValueByName = function(name, parent) {
  return Ngn.Frm.getValue(Ngn.Frm.getElements(name, parent));
};

Ngn.Frm.emptify = function(eInput) {
  if (eInput.get('type') == 'checkbox') eInput.set('checked', false); else eInput.get('value', '');
};

/**
 * @param Element|array of Element
 * @returns {*}
 */
Ngn.Frm.getValue = function(el) {
  if (el.length === undefined) {
    var elements = el.getElements(Ngn.Frm.selector);
  } else {
    var elements = el;
  }
  var r = null;
  var res = [];
  var i = 0;
  elements.each(function(el) {
    var type = el.get('type');
    if (type == 'checkbox') {
      if (el.get('checked')) res[i] = el.get('value');
      i++;
    } else if (type == 'radio') {
      if (el.get('checked'))
        r = el.get('value');
    } else {
      r = el.get('value');
    }
  });
  if (res.length != 0) r = res;
  return r;
};

Ngn.Frm.getValues = function(el) {
  if (el.length === undefined) {
    var elements = el.getElements(Ngn.Frm.selector);
  } else {
    var elements = el;
  }
  var r = [];
  elements.each(function(el) {
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

Ngn.Frm.getElements = function(name, parent) {
  var elements = [];
  var n = 0;
  var _name;
  parent = parent || document;
  parent.getElements(Ngn.Frm.selector).each(function(el) {
    _name = el.get('name');
    if (!_name) return;
    if (_name.replace('[]', '') != name) return;
    elements[n] = el;
    n++;
  });
  return elements;
};

Ngn.Frm.virtualElements = [];
Ngn.Frm.disable = function(eForm, flag) {
  eForm.getElements(Ngn.Frm.selector).each(function(el) {
    el.set('disabled', flag);
  });
  // c(Ngn.Frm.virtualElements);
  for (var i = 0; i < Ngn.Frm.virtualElements.length; i++) {
    // var o = Ngn.Frm.virtualElements[i];
    // c([o, o.getForm()]);
    // if (o.getForm() && o.getForm().get('id') != eForm.get('id')) return;
    // o.toggleDisabled(!flag);
  }
};

// формат callback ф-ии должен быть следующим:
// function (fieldValue, args) {}
Ngn.Frm.addEvent = function(event, name, callback, args) {
  var elements = Ngn.Frm.getElements(name);
  elements.each(function(el) {
    el.addEvent(event, function(e) {
      callback.run([Ngn.Frm.getValue(elements), args], el);
    });
  });
}

Ngn.enumm = function(arr, tpl, glue) {
  if (!$defined(glue)) glue = '';
  for (var i = 0; i < arr.length; i++)
    arr[i] = tpl.replace('{v}', arr[i]);
  return arr.join(glue);
};

Ngn.Frm.getPureName = function($bracketName) {
  return $bracketName.replace(/(\w)\[.*/, '$1');
};

Ngn.Frm.getBracketNameKeys = function(name) {
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

Ngn.Frm.fillEmptyObject = function(object, keys) {
  for (var i = 0; i < keys.length - 1; i++) {
    var p = 'object' + (Ngn.enumm(keys.slice(0, i + 1), "['{v}']"));
    eval('if (!$defined(' + p + ')) ' + p + ' = {}');
  }
};

Ngn.Frm.setValueByBracketName = function(o, name, value) {
  var _name = name.replace('[]', '');
  if (!(o instanceof Object)) throw new Error('o is not object');
  var keys = Ngn.Frm.getBracketNameKeys(_name);
  Ngn.Frm.fillEmptyObject(o, keys);
  var p = 'o';
  for (var i = 0; i < keys.length; i++) p += "['" + keys[i] + "']";
  if (name.contains('[]')) {
    eval(p + ' = $defined(' + p + ') ? ' + p + '.concat(value) : [value]');
  } else {
    //eval(p+' = $defined('+p+') ? [].concat('+p+', value) : value');
    eval(p + ' = value');
  }
  return o;
};

Ngn.Frm.objTo = function(eContainer, obj) {
  for (var i in obj) {
    eContainer.getElement('input[name=' + i + ']').set('value', obj[i]);
  }
};

Ngn.Frm.toObj = function(eContainer, except) {
  var rv = {};
  var name;
  except = except || [];
  eContainer = $(eContainer);
  var typeMatch = 'text' + (!except.contains('hidden') ? '|hidden' : '') + (!except.contains('password') ? '|password' : '');
  var elements = eContainer.getElements(Ngn.Frm.selector);
  for (var i = 0; i < elements.length; i++) {
    var el = elements[i];
    if (!el.name) continue;
    var pushValue = undefined;
    if (el.get('tag') == 'textarea' && el.get('aria-hidden')) {
      // Значит из этой texarea был сделан tinyMce
      pushValue = tinyMCE.get(el.get('id')).getContent();
      //} else if ((el.get('tag') == 'input' && el.type.match(new RegExp('^' + typeMatch + '$', 'i'))) || el.get('tag') == 'textarea' || (el.get('type').match(/^checkbox|radio$/i) && el.get('checked'))) {
    } else if ((el.get('tag') == 'input' && el.type.match(new RegExp('^' + typeMatch + '$', 'i'))) || el.get('tag') == 'textarea' || (el.get('type').match(/^radio$/i) && el.get('checked'))) {
      pushValue = el.value;
    } else if ((el.get('type').match(/^checkbox$/i) && el.get('checked'))) {
      var pushValue = [];
      eContainer.getElement('.name_'+el.name).getElements('input').each(function(checkbox){
        if(checkbox.get('checked'))  pushValue.push(checkbox.value);
      });
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
      Ngn.Frm.setValueByBracketName(rv, el.name, pushValue);
    }
  }
  return rv;
};

Ngn.Frm.initTranslateField = function(eMasterField, eTranslatedField) {
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
  eTranslatedField.addEvent('keyup', function(e) {
    translatedFieldEdited = true;
  });
};

Ngn.Frm.initCopySelectValue = function(eSelectField, eSlaveField, param) {
  if (!$defined(param)) param = 'value';
  var eSelectField = $(eSelectField);
  var eSlaveField = $(eSlaveField);
  eSlaveField.addEvent('keyup', function() {
    eSlaveField.store('edited', true);
  });
  eSelectField.addEvent('change', function() {
    if (eSlaveField.retrieve('edited')) return;
    eSlaveField.set('value', eSelectField.options[eSelectField.selectedIndex].get(param));
    eSlaveField.fireEvent('blur');
  });
};

Ngn.Frm.initCopySelectTitle = function(eSelectField, eSlaveField) {
  Ngn.Frm.initCopySelectValue(eSelectField, eSlaveField, 'text');
};

Ngn.Frm.storable = function(eInput) {
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

Ngn.Frm.virtualElement = {
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

Ngn.Frm.maxLength = function(eForm, defaultMaxLength) {
  eForm.getElements('textarea').each(function(eInput){
    var eLabel = eInput.getParent('.element').getElement('.label');
    var maxlength = eInput.get('maxlength');
    if (!eLabel || !maxlength) return;
    var init = function() {
      eRemained.set('html',
        ' (осталось ' + (maxlength-eInput.get('value').length) + ' знаков из ' + maxlength + ')'
      );
    };
    if (maxlength >= defaultMaxLength) return;
    var eRemained = new Element('small', {
      'class': 'remained gray'
    }).inject(eLabel, 'bottom');
    eInput.addEvent('keyup', init);
    init();
  });
};
