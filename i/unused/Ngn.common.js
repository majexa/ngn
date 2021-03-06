if (!Ngn) var Ngn = {};

MooTools.lang.setLanguage('ru-RU');

// ----------------------------------------------------------

Ngn.Class = new Class({

  className: function() {
    for (var i in Ngn) if (Ngn[i] == this.constructor) return i;
    return false;
  }

});

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

Ngn.initTips = function(els) {
  if (!Ngn.tips) Ngn.tips = new Tips(els);
};

Object.eq = function(obj1, obj2) {
  return JSON.encode(obj1) == JSON.encode(obj2);
};

Object.isEmpty = function(obj) {
  for (var i in Ngn.Object.fromArray(obj)) if (obj[i]) return false;
  return true;
};

Element.implement(Events);

Ngn.toObj = function(s, value) {
  var a = s.split('.');
  for (var i = 0; i < a.length; i++) {
    var ss = a.slice(0, i + 1).join('.');
    eval('var def = ' + ss + ' === undefined');
    if (def) eval((i == 0 ? 'var ' : '') + ss + ' = {}');
  }
  if (value) eval(s + ' = value');
};

Ngn.arrToObj = function(arr) {
  if (typeOf(arr) == 'object') return arr;
  var r = {};
  for (var i = 0; i < arr.length; ++i) r[i] = arr[i];
  return r;
};


Hash.implement({
  length: function() {
    var l = 0;
    this.each(function() {
      l++
    });
    return l;
  }
});

Array.prototype.max = function() {
  var max = this[0];
  var len = this.length;
  for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
  return max;
};

Array.prototype.get = function(k, v) {
  for (var i = 0; i < this.length; i++) {
    if (this[i][k] == v) return this[i];
  }
  return false;
};

//--------------------------------------------------------------------------

Ngn.checkboxesSelected = function(esCheckboxes) {
  var selected = false;
  esCheckboxes.each(function(el) {
    if (selected) return;
    if (el.get('checked')) selected = true;
  });
  return selected;
};

// --------------------------Common functions------------------------------

Ngn.debug = true;

function c(t) {
  if (console && console.log && Ngn.debug) {
    console.log(t);
  }
};

Ngn.name2id = function(name) {
  return name.replace(/-/g, '_').replace(/\[/g, '-').replace(/\]/g, '');
};

var mapRu = 'Ё|©|Й|Ц|У|К|Е|Н|Г|Ш|Щ|З|Х|Ъ|Ф|Ы|В|А|П|Р|О|Л|Д|Ж|Э|Я|Ч|С|М|И|Т|Ь|Б|Ю|ё|й|ц|у|к|е|н|г|ш|щ|з|х|ъ|ф|ы|в|а|п|р|о|л|д|ж|э|я|ч|с|м|и|т|ь|б|ю| |'.split('|');
var mapEn = 'E|N|Y|TS|U|K|E|N|G|SH|SCH|Z|H|-|F|I|V|A|P|R|O|L|D|ZH|E|JA|CH|S|M|I|T|-|B|JU|e|y|tc|u|k|e|n|g|sh|sch|z|h|-|f|i|v|a|p|r|o|l|d|zh|e|ja|ch|s|m|i|t|-|b|ju|-|'.split('|');

function translate(str) {
  for (i = 0; i < mapRu.length; ++i) {
    j = 0;
    if (!mapRu[i]) continue;
    while (str.indexOf(mapRu[i]) >= 0) {
      str = str.replace(mapRu[i], mapEn[i]);
      j++;
      if (j > 10) {
        break;
      }
    }
  }
  str = str.replace(/(\W)/g, '-').toLowerCase();
  return str;
};

function trim(s) {
  return s.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
};

function numerical(s) {
  if (s == 0) return true;
  var _s = s.toInt().toString();
  return _s.length == s.length && _s != 0;
};

function abbreviate(elements, n) {
  elements.each(function(el) {
    var t = el.get('text');
    if (t.length > n) {
      el.set('text', t.substr(0, n) + '...');
      el.set('title', t);
      el.addClass('tooltip');
    }
  });
};


Ngn.settings = function(name, callback) {
  Asset.javascript('/c2/jsSettings/' + name, {
    onLoad: function() {
      callback(eval('Ngn.settings.' + name.replace(/\//g, '.')));
    }
  });
};

Ngn.regNamespace = function(namespace, lastArray) {
  var parts = namespace.split('.');
  var brackets = '{}';
  for (var i = 0; i < parts.length; i++) {
    if (lastArray && i == parts.length - 1) brackets = '[]';
    var str = parts.slice(0, i + 1).join('.');
    eval('if (!' + str + ') window.' + str + ' = ' + brackets);
  }
};

Ngn.getPath = function(n) {
  if (n === 0) return './';
  var p = window.location.pathname.split('/');
  var s = '';
  if (!n) n = p.length - 1;
  for (var i = 1; i <= n; i++) {
    s += '/' + (p[i] ? p[i] : 0);
    if (n === i) break;
  }
  return s;
};

Ngn.getParam = function(n, zeroOnUndefined) {
  return Ngn._getParam(window.location.pathname, n + 1, zeroOnUndefined);
};

Ngn._getParam = function(url, n, zeroOnUndefined) {
  var p = url.split('/');
  return p[n] != undefined ? p[n] : (zeroOnUndefined != undefined ? 0 : false);
};

Ngn.getRandomInt = function(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
};

Ngn.randString = function(len) {
  var allchars = 'abcdefghijknmpqrstuvwxyzABCDEFGHIJKLNMPQRSTUVWXYZ'.split('');
  var string = '';
  for (var i = 0; i < len; i++) {
    string += allchars[Ngn.getRandomInt(0, allchars.length - 1)];
  }
  return string;
};

Ngn.setToCenter = function(element, eParent, offset) {
  if (!eParent != undefined) eParent = element.getParent();
  if (!offset) offset = {};
  offset = Object.merge({x: 0, y: 0}, offset);
  element.setStyles({
    'top': Math.round(eParent.getSize().y / 2 - element.getSize().y / 2 + offset.y),
    'left': Math.round(eParent.getSize().x / 2 - element.getSize().x / 2 + offset.x)
  });
};

Ngn.setToCenterHor = function(element, eParent) {
  if (!eParent != undefined) eParent = element.getParent();
  element.setStyles({
    'left': Math.round(eParent.getSize().x / 2 - element.getSize().x / 2)
  });
};

Ngn.setToCenterRelVer = function(element, eParent) {
  if (!eParent != undefined) eParent = element.getParent();
  element.setStyles({
    'margin-top': Math.round(eParent.getSize().y / 2 - element.getSize().y / 2) + 'px'
  });
};

Ngn.setToCenterRelHor = function(element, eParent) {
  if (!eParent != undefined) eParent = element.getParent();
  element.setStyles({
    'margin-left': Math.round(eParent.getSize().x / 2 - element.getSize().x / 2) + 'px'
  });
};

Ngn.setToTopRight = function(element, eParent, margin) {
  if (!eParent != undefined) eParent = element.getParent();
  if (!margin != undefined) margin = [0, 0];
  element.setStyles({
    'top': margin[1],
    'left': eParent.getSize().x - element.getSize().x - margin[0]
  });
};

Ngn.setToBottomRight = function(element, eParent, margin) {
  if (!eParent != undefined) eParent = element.getParent();
  if (!margin != undefined) margin = [0, 0];
  element.setStyles({
    'top': eParent.getSize().y - margin[1],
    'left': eParent.getSize().x - element.getSize().x - margin[0]
  });
};

Ngn.setToCenterRight = function(element, eParent, margin) {
  if (!eParent != undefined) eParent = element.getParent();
  if (!margin != undefined) margin = [0, 0];
  element.setStyles({
    'top': Math.round(eParent.getSize().y / 2 - element.getSize().y / 2) - margin[1],
    'left': eParent.getSize().x - element.getSize().x - margin[0]
  });
};

Ngn.setToCenterLeft = function(element, eParent, margin) {
  if (!eParent != undefined) eParent = element.getParent();
  if (!margin != undefined) margin = [0, 0];
  element.setStyles({
    'top': Math.round(eParent.getSize().y / 2 - element.getSize().y / 2) - margin[1],
    'left': margin[0]
  });
};

Ngn.setToCenterBlock = function(element, eWidth) {
  element.setStyles({
    'margin-left': Math.round(eWidth.getSize().x / 2 - element.getSize().x / 2)
  });
};

Ngn.tpl = function(tpl, data) {
  return tpl.replace(/\{(\w+)\}/g, function(str, name) {
    return data[name] ? data[name] : '';
  });
};

Ngn.initSubmit = function(eForm) {
  var btnSubmit = eForm.getElement('input[type=submit]');
  if (!btnSubmit) return;
  var submiting = false;
  btnSubmit.addEvent('click', function(e) {
    e.preventDefault();
    if (submiting) return;
    btnSubmit.disabled = true;
    btnSubmit.addClass('loading');
    if (this.validator.validate()) {
      submiting = true;
      eForm.submit();
    }
  });
};

Ngn.clearParagraphs = function(s) {
  return s.replace(/(<p>)(&nbsp;)?(<\/p>)/g, '').replace(/\n/g, '');
}

Ngn.LocalStorage = {
  clean: function() {
    if (!localStorage) return;
    try {
      for (k in localStorage) {
        localStorage.removeItem(k);
      }
    } catch (e) {
      for (var i = 0; i < localStorage.length; i++)
        localStorage.removeItem(localStorage[i]);
    }
  },
  remove: function(key) {
    if (!localStorage) return false;
    localStorage.removeItem(key);
  }
};
Ngn.LocalStorage.json = {
  get: function(key) {
    if (!localStorage) return false;
    return JSON.decode(localStorage.getItem(key));
  },
  set: function(key, data) {
    localStorage.setItem(key, JSON.encode(data));
  }
};

Ngn.Storage = {
  get: function(key) {
    if (localStorage) {
      var v = localStorage.getItem(key);
    } else {
      var v = Cookie.read(key);
    }
    if (v == 'false') return false; else if (v == 'true') return true; else return v;
  },
  set: function(key, value) {
    if (localStorage) {
      localStorage.setItem(key, value)
    } else {
      Cookie.write(key, value);
    }
  },
  remove: function(key) {
    localStorage.removeItem(key);
  },
  bget: function(key, value) {
    return !!this.get(key);
  }
};

Ngn.Storage.int = {

  get: function(key) {
    return parseInt(Ngn.Storage.get(key));
  }

};

Ngn.Storage.json = {
  get: function(key) {
    try {
      if (localStorage) {
        var r = Ngn.LocalStorage.json.get(key);
      } else {
        var r = JSON.decode(Cookie.read(key));
      }
    } catch (e) {
      var r = {};
    }
    return r;
  },
  set: function(key, data) {
    if (localStorage)
      Ngn.LocalStorage.json.set(key, data); else
      Cookie.write(key, JSON.encode(data));
  }
};

Ngn.addHover = function(el, hoverClass) {
  el.addEvent('mouseover', function() {
    this.addClass(hoverClass);
  });
  el.addEvent('mouseout', function() {
    this.removeClass(hoverClass);
  });
};

Ngn.loading = function(state) {
  var el = $('globalLoader');
  if (!el) {
    var el = Elements.from('<div id="globalLoader" class="globalLoader"></div>')[0].inject(document.getElement('body'), 'top');
    el.setStyle('top', window.getScroll().y);
    window.addEvent('scroll', function() {
      el.setStyle('top', window.getScroll().y);
    });
  }
  el.setStyle('visibility', state ? 'visible' : 'hidden');
};

Ngn.hHandler = function(eHandler, eContainer, wId, dragOptions) {
  var w = Ngn.Storage.get(wId);
  dragOptions = dragOptions || {};
  if (w) eContainer.setStyle('width', w);
  new Drag(eContainer, Object.merge({
    handle: eHandler,
    modifiers: {x: 'width', y: false},
    snap: 0,
    onComplete: function(el) {
      Ngn.Storage.set(wId, el.getStyle('width'));
    }
  }, dragOptions));
};

Ngn.addWrapper = function(el, wrapperClass) {
  var wrapper = new Element('div', {'class': wrapperClass}).inject(el, 'before');
  el.inject(wrapper);
  return wrapper;
};

function number_format(number, decimals, dec_point, thousands_sep) {  // Format a number with grouped thousands
  // 
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfix by: Michael White (http://crestidg.com)

  var i, j, kw, kd, km;

  // input sanitation & defaults
  if (isNaN(decimals = Math.abs(decimals))) {
    decimals = 2;
  }
  if (dec_point == undefined) {
    dec_point = ",";
  }
  if (thousands_sep == undefined) {
    thousands_sep = ".";
  }

  i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

  if ((j = i.length) > 3) {
    j = j % 3;
  } else {
    j = 0;
  }

  km = (j ? i.substr(0, j) + thousands_sep : "");
  kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
  kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

  return km + kw + kd;
}


Ngn.filesizeFormat = function(filesize) {
  if (filesize >= 1073741824) {
    filesize = number_format(filesize / 1073741824, 2, '.', '') + ' Gb';
  } else {
    if (filesize >= 1048576) {
      filesize = number_format(filesize / 1048576, 2, '.', '') + ' Mb';
    } else {
      if (filesize >= 1024) {
        filesize = number_format(filesize / 1024, 0) + ' Kb';
      } else {
        filesize = number_format(filesize, 0) + ' bytes';
      }
      ;
    }
    ;
  }
  ;
  return filesize;
};

Ngn.getElementJson = function(element) {
  return JSON.decode(element.get('html'));
};

Ngn.addUrlParam = function(url, k, v) {
  return url + (url.contains('?') ? '&' : '?') + k + '=' + v;
};

Ngn.fixEmptyTds = function(el) {
  var tds = el.getElements('td');
  for (var i = 0; i < tds.length; i++)
    if (!trim(tds[i].get('html'))) tds[i].set('html', '&nbsp;');
};

Ngn.addBtnAction = function(selector, action, parent) {
  var esBtn = (parent ? parent : document).getElements(selector);
  if (!esBtn) return;
  esBtn.each(function(eBtn) {
    action = action.pass(eBtn);
    eBtn.addEvent('click', function(e) {
      e.preventDefault();
      action(e);
    });
  });
};

Ngn.addBtnsAction = function(selector, action, parent) {
  (parent ? parent : document).getElements(selector).each(function(eBtn) {
    eBtn.addEvent('click', function(e) {
      e.preventDefault();
      action(eBtn);
    });
  });
};

Ngn.confirm = function(question) {
  if (!question) question = 'Вы уверены?';
  return confirm(question);
};

Ngn.addAjaxAction = function(eBtn, action, onComplete) {
  if (!eBtn) return;
  onComplete = onComplete ? onComplete : Function.from();
  eBtn.addEvent('click', function(e) {
    e.preventDefault();
    if (eBtn.hasClass('confirm') && !Ngn.confirm()) return;
    if (eBtn.hasClass('loading')) return;
    if (eBtn.retrieve('disabled')) return;
    eBtn.addClass('loading');
    new Ngn.Request({
      url: eBtn.get('href').replace(action, 'ajax_' + action),
      onComplete: function() {
        onComplete();
        eBtn.removeClass('loading');
      }
    }).send();
  });
};

Ngn.addBtnInit = function(selector, init) {
  eBtn = document.getElement(selector);
  if (!eBtn) return;
  init.pass(eBtn)();
}

Ngn.tpls = {};
Ngn.config = {};

Ngn.strReplace = function(search, replace, subject) {
  if (!(replace instanceof Array)) {
    replace = new Array(replace);
    if (search instanceof Array) {
      while (search.length > replace.length) {
        replace[replace.length] = replace[0];
      }
    }
  }
  if (!(search instanceof Array)) search = new Array(search);
  while (search.length > replace.length) {
    replace[replace.length] = '';
  }
  if (subject instanceof Array) {
    for (k in subject) {
      subject[k] = Ngn.strReplace(search, replace, subject[k]);
    }
    return subject;
  }
  for (var k = 0; k < search.length; k++) {
    var i = subject.indexOf(search[k]);
    while (i > -1) {
      subject = subject.replace(search[k], replace[k]);
      i = subject.indexOf(search[k], i);
    }
  }
  return subject;
};

Ngn.equalItemHeights = function(esItems, minHeight) {
  if (!esItems.length) return;
  var maxY = 0;
  var vPadding = esItems[0].getStyle('padding-top').toInt() + esItems[0].getStyle('padding-bottom').toInt() + esItems[0].getStyle('border-top-width').toInt() + esItems[0].getStyle('border-bottom-width').toInt();
  esItems.each(function(el) {
    var y = el.getSize().y;
    if (y > maxY) {
      maxY = y;
    }
  });
  if (!maxY) return;
  maxY = maxY - vPadding;
  esItems.each(function(el) {
    el.setStyle(minHeight ? 'min-height' : 'height', maxY);
  });
};

Ngn.cut = function(str, length) {
  return length < str.length ? str.substring(0, str.length) + '...' : str;
};

Ngn.cutElementText = function(el, length) {
  if (el.get('text').length <= length) return;
  var text = el.get('text');
  var initHtml = el.get('html');
  el.store('eCutTip', new Element('div', {
    'class': 'cutTip',
    html: initHtml,
    styles: {
      visibility: 'hidden',
      position: 'absolute'
    }
  }).inject(document.getElement('body'), 'bottom'));
  el.set('html', '<span class="pseudoLink">' + text.substr(0, length) + '...</span>');
  var show = function(el) {
    el.retrieve('eCutTip').setStyles({
      visibility: 'visible',
      top: el.getPosition().y + el.getSize().y - 1,
      left: el.getPosition().x
    });
  };
  el.retrieve('eCutTip').addEvent('mouseover', function() {
    show(el);
  });
  el.addEvent('mouseover', function() {
    show(el);
  });
  el.addEvent('mouseout', function() {
    this.retrieve('eCutTip').setStyle('visibility', 'hidden');
  });
  el.set('title', text);
};

Ngn.whenElPresents = function(eParent, selector, action) {
  return Ngn.Element._whenElPresents(function() {
    return eParent.getElement(selector);
  }, action);
};

function http_build_query(formdata, numeric_prefix, arg_separator) {
  var key, use_val, use_key, i = 0, tmp_arr = [];
  if (!arg_separator) arg_separator = '&';
  for (key in formdata) {
    use_key = escape(key);
    use_val = escape((formdata[key].toString()));
    use_val = use_val.replace(/%20/g, '+');
    if (numeric_prefix && !isNaN(key)) {
      use_key = numeric_prefix + i;
    }
    tmp_arr[i] = use_key + '=' + use_val;
    i++;
  }
  return tmp_arr.join(arg_separator);
}

function basename(str) {
  var base = new String(str).substring(str.lastIndexOf('/') + 1);
  if (base.lastIndexOf(".") != -1)
    base = base.substring(0, base.lastIndexOf("."));
  return base;
}

Ngn.clsToSelector = function(s) {
  return s.split(' ').map(function(v) {
    '.' + v
  }).join(' ');
};

String.prototype.hashCode = function() {
  var hash = 0, i, chr, len;
  if (this.length == 0) return hash;
  for (i = 0, len = this.length; i < len; i++) {
    chr   = this.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

Ngn.requestLoaded = true;
