/**
 *
 * <div id="mainElement">
 *   <div class="rowElement">
 *     <input type="" name="k[0]" value="gg" size="40" id="k[0]i" />
 *     <input type="" name="v[0]" value="gggg" size="40" id="v[0]i" />
 *     <div class="drag"></div>
 *     <a href="#" class="smIcons delete bordered"><i></i></a>
 *     <div class="clear"><!-- --></div>
 *   </div>
 *   <div class="element">
 *     ...
 *   </div>
 *   <a href="#" class="add">Добавить</a>
 * </div>
 *
 */
Ngn.FieldSet = new Class({
  Implements: [Options, Events],

  options: {
    fields: [],
    data: [],
    rowElementSelector: 'div[class~=rowElement]',
    elementContainerSelector: '.element',
    cleanOnCloneSelector: '.type_image .iconsSet',
    addRowBtnSelector: 'a[class~=add]',
    deleteBtnSelector: 'a[class~=delete]',
    dragBoxSelector: 'div[class=dragBox]',
    removeExceptFirstRow: 'p.label',
    moveElementToRowStyles: ['border-bottom', 'padding-left'],
    addTitle: 'Добавить',
    cleanupTitle: 'Очистить поля строки',
    deleteTitle: 'Удалить строку',
    addRowNumber: false
  },

  changed: false,
  eSampleRow: null,
  buttons: [], // array of Ngn.Btn objects

  toggleDisabled: function(flag) {
    for (var i = 0; i < this.buttons.length; i++) {
      this.buttons[i].toggleDisabled(flag);
    }
  },

  getContainer: function() {
    var eContainer = Elements.from('<div class="fieldSet"></div>')[0];
    if (!this.options.data) this.options.data = [false];
    var n = this.options.data.length;
    var eRowProto = Elements.from('<div class="rowElement"><div class="drag"></div><div class="clear"><!-- --></div></div>')[0];
    for (var j = 0; j < n; j++) {
      var eRow = eRowProto.clone();
      eRow.store('n', j + 1);
      for (var i = 0; i < this.options.fields.length; i++) {
        var el = new Element('div', {'class': 'element'});
        new Element('input', {
          name: this.options.fields[i].name + '[' + j + ']',
          value: this.options.data[j] ? this.options.data[j].name : ''
        }).inject(el);
        el.inject(eRow, 'top');
      }
      eRow.inject(eContainer);
    }
    return eContainer.inject(this.eParent);
  },

  initialize: function(eParent, options) {
    this.eParent = eParent;
    this.setOptions(options);
    this.eContainer = this.getContainer();
    this.eAddRow = this.eContainer.getElement(this.options.addRowBtnSelector);
    if (!this.eAddRow) {
      var eBbtns = new Element('div', {'class': 'bbtns'}).inject(this.eContainer, 'bottom');
      this.eAddRow = Ngn.btn1(this.options.addTitle, 'add dgray').inject(eBbtns);
      Elements.from('<div class="heightFix"></div>')[0].inject(this.eContainer, 'bottom');
    }
    this.buttons.push(new Ngn.Btn(this.eAddRow, function(btn) {
      this.buttons.push(btn);
      this.addRow();
    }.bind(this)));
    this.initRows();
    //this.initSorting();
    this.checkDeleteButtons();
  },

  /*
   inputsEmpty: function(container) {
   var elements = container.getElements('input')
   for (var i = 0; i < elements.length; i++) {
   if (elements[i].get('value')) return false;
   }
   return true;
   },
   */

  initRows: function() {
    if (!this.options.rowElementSelector) {
      this.eContainer.getElements('input').each(function(eInput) {
        var eRowDiv = new Element('div', {'class': 'genRow'})
        eRowDiv.inject(eInput, 'after');
        eInput.inject(eRowDiv);
      });
      this.options.rowElementSelector = 'div[class=genRow]';
    }
    // Переносим стили элементов в стили контейнеров элементов, а у элементов их удаляем
    this.esRows = this.eContainer.getElements(this.options.rowElementSelector);
    this.eSampleRow = this.esRows[0].clone();
    this.eSampleRow.getElements(this.options.cleanOnCloneSelector).dispose();
    this.createCleanupButton(this.esRows[0]);
    this.removeTrash(this.eSampleRow);
    for (var i = 0; i < this.esRows.length; i++) {
      if (this.options.addRowNumber) this.addRowNumber(this.esRows[i]);
      this.moveStyles(this.esRows[i]);
    }
    if (this.esRows.length > 0) {
      for (var i = 1; i < this.esRows.length; i++) {
        this.removeTrash(this.esRows[i]);
        this.createDeleteButton(this.esRows[i]);
      }
    }
  },

  firstIndex: function(name) {
    return name.replace(/[^[]+\[(\d)+\].*/, '$1').toInt();
  },

  addRowNumber: function(eRow) {
    var index = this.firstIndex(eRow.getElement(Ngn.frm.selector).get('name'));
    new Element('span', {
      html: index + ' — ',
      'class': 'rowNumber'
    }).inject(eRow.getElement('.field-wrapper'), 'top');
  },

  moveStyles: function(eRow) {
    return;
    var style;
    esEls = eRow.getElements(this.options.elementContainerSelector);
    for (var j = 0; j < this.options.moveElementToRowStyles.length; j++) {
      style = this.options.moveElementToRowStyles[j];
      eRow.setStyles(esEls[0].getStyles(style));
      for (var k = 0; k < esEls.length; k++)
        esEls[k].setStyle(style, '0');
    }
  },

  checkDeleteButtons: function() {
    return;
    // Удаляем кнопку "Удалить", если элемент 1 в списке и значения полей пустые
    if (this.eRows.length == 1) {
      var eRow = this.eContainer.getElement(this.options.rowElementSelector);
    }
  },

  removeTrash: function(eRow) {
    eRow.getElements(this.options.removeExceptFirstRow).each(function(el) {
      el.dispose();
    });
  },

  createRowButton: function(eRow, btn, action, options) {
    var els = eRow.getElements(this.options.elementContainerSelector);
    var fieldSet = this;
    this.buttons.push(new Ngn.Btn(// Вставляем кнопку после последнего элемента формы в этой строке
      //Ngn.addTips(Ngn.btn(btn)).inject(els[els.length - 1], 'after'), function() {
      Ngn.btn(btn).inject(els[els.length - 1], 'after'), function() {
        fieldSet.fireEvent(btn.cls);
        action.bind(this)();
      }, options || {}));
  },

  createDeleteButton: function(eRow) {
    var fieldSet = this;
    this.createRowButton(eRow, {
      caption: this.options.deleteTitle,
      cls: 'delete'
    }, function() {
      eRow.dispose();
      fieldSet.regenInputNames();
      fieldSet.buttons.erase(this);
    });
  },

  createCleanupButton: function(eRow) {
    var els = eRow.getElements(this.options.elementContainerSelector);
    //реализовать через css
    //var eLabel = eRow.getElement(this.options.removeExceptFirstRow);
    //if (eLabel) eBtn.setStyle('margin-top', (eBtn.getStyle('margin-top').toInt() + eLabel.getSizeWithMargin().y) + 'px');
    this.createRowButton(eRow, {
      caption: this.options.cleanupTitle,
      cls: 'cleanup'
    }, function() {
      eRow.getElements(Ngn.frm.selector).set('value', '');
    });
  },

  addRow: function() {
    var eLastRow = this.eContainer.getLast(this.options.rowElementSelector);
    var eNewRow = this.eSampleRow.clone();
    var lastRowN = this.getN(eLastRow);
    var nextRowN = this.getNextN(eLastRow);
    var eLabel;
    var lastRowElements = eLastRow.getElements(Ngn.frm.selector);
    eNewRow.getElements('.element').each(function(eElement, i) {
      //c(eElement.get('class').replace('-' + curN + '-', '-' + nextN + '-'));
      //c('(.*)-' + lastRowN + '-(.*)');
      eElement.set('class', eElement.get('class').replace(new RegExp('(.*)-0-(.*)'), '$1-' + nextRowN + '-$2'));
    });
    eNewRow.getElements(Ngn.frm.selector).each(function(eInput, i) {
      Ngn.frm.emptify(eInput);
      //if (eInput.get('value')) eInput.set('value', '');
      //if (eInput.get('checked')) eInput.set('checked', false);
      c(nextRowN);
      eInput.set('name', this.getInputName(eInput, nextRowN));
      eInput.set('id', lastRowElements[i].get('id').replace('-' + lastRowN + '-', '-' + nextRowN + '-'));
      eLabel = eInput.getNext('label');
      if (eLabel) eLabel.set('for', eInput.get('id'));
      this.initInput(eInput);
    }.bind(this));
    eNewRow.injectAfter(eLastRow);
    this.createDeleteButton(eNewRow);
    this.fireEvent('addRow');
    if (this.options.addRowNumber) this.addRowNumber(eNewRow, nextRowN);
    this.moveStyles(eNewRow);
    this.afterAddRow(eNewRow);
    // this.initSorting();
  },

  initInput: function(eInput) {
  },
  afterAddRow: function(eNewRow) {
  },

  getNextN: function(eRow) {
    return this.getN(eRow, 1);
  },

  getN: function(eRow, plus) {
    plus = plus || 0;
    var els = eRow.getElements(Ngn.frm.selector);
    var name;
    for (var i = 0; i < els.length; i++) {
      name = els[i].get('name');
      if (name) break;
    }
    return this.firstIndex(name) + plus;
  },

  getInputName: function(eInput, n) {
    var name = eInput.get('name');
    if (!name) return;
    return name.replace(/([a-z0-9]+)\[([0-9]+)\](.*)/i, '$1[' + n + ']$3');
  },

  regenInputNames: function() {
    this.eContainer.getElements(this.options.rowElementSelector).each(function(eRow, n) {
      eRow.getElements(Ngn.frm.selector).each(function(eInput) {
        eInput.set('name', this.getInputName(eInput, n));
      }.bind(this));
    }.bind(this));
  },

  initSorting: function() {
    var ST = new Sortables(this.eContainer, {
      handle: this.options.dragBoxSelector
    });
    ST.addEvent('start', function(el, clone) {
      el.addClass('move');
    });
    ST.addEvent('complete', function(el, clone) {
      el.removeClass('move');
    }.bind(this));

    this.eContainer.getElements(this.options.dragBoxSelector).each(function(el) {
      el.addEvent('mouseover', function() {
        el.addClass('over');
      });
      el.addEvent('mouseout', function() {
        el.removeClass('over');
      });
    });
  }

});
