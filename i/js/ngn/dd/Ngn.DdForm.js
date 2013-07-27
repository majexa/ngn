Ngn.DdForm = new Class({
  Extends: Ngn.Form,
  
  eForm: null,
  
  initialize: function(eForm, options) {
    this.strName = eForm.get('data-strName');
    this.parent(eForm, options);
  }

  /*
  initDatePickers: function() {
    // Дата, дата-время
    this.eForm.getElements('.type_date, .type_datetime').each(function(el) {
      var hasTime = el.hasClass('type_datetime');
      new DatePicker(el.getElement('input'), {
        pickerClass: 'datepicker_jqui',
        positionOffset: { x: 0, y: 5 },
        format: hasTime ? 'd.m.Y H:i' : 'd.m.Y',
        inputOutputFormat: hasTime ? 'd.m.Y H:i' : 'd.m.Y',
        timePicker: hasTime
      });
    });

    // Дата рождения
    this.eForm.getElements('.type_birthDate').each(function(el) {
      new DatePicker(el.getElement('input'), {
        pickerClass: 'datepicker_jqui',
        positionOffset: { x: 0, y: 5 },
        format: 'd.m.Y',
        inputOutputFormat: 'd.m.Y',
        timePicker: false,
        allowEmpty: true,
        minDate: {
          //date: date('d.m.Y', mktime(0,0,0, 1, 1, new Date().getFullYear()-110)),
          //format: 'd.m.Y'
          date: Date.today().add({ years: -110 }).toString('dd.MM.yyyy'),
          format: 'd.m.Y'
        },
        maxDate: {
          date: Date.today().add({ years: -7 }).toString('dd.MM.yyyy'),
          format: 'd.m.Y'
        }
      });
    });
 
    // Время
    this.eForm.getElements('.type_time').each(function(el) {
      new DatePicker(el.getElement('input'), {
        pickerClass: 'datepicker_jqui',
        positionOffset: { x: 0, y: -5 },
        timePickerOnly: true,
        format: 'H:i',
        inputOutputFormat: 'H:i'
      });
    });
  }
  */
  
});

Ngn.Form.El.Dd = new Class({
  Extends: Ngn.Form.El,

  initialize: function(type, form, eRow) {
    if (!form.strName) throw new Error('form must be Ngn.DdForm instance');
    this.strName = form.strName;
    this.parent(type, form, eRow);
  }

});

Ngn.Form.El.DdTagsTreeSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var adviceId = 'ddTagsTreeSelectAdvice' + this.eRow.n;
    new Element('div', {id: adviceId}).inject(this.eRow, 'bottom');
    // this.eRow.getElement('input').addClass("validate-one-required2 msgPos:'" + adviceId + "'");
    this.initEls(this.eRow);
  },

  initEls: function(eParent) {
    eParent.getElements('a').each(function(el) {
      var eUl = this.eRow.getElement('.nodes_' + el.get('data-id'));
      eUl.setStyle('display', 'none');
      el.addEvent('click', function(e) {
        e.preventDefault();
        if (!!el.get('data-loadChildren') && !el.retrieve('loaded')) {
          new Ngn.Request.Loading({
            url: '/c/ddTagsTreeMultiselect/' + this.strName + '/' + Ngn.frm.getPureName(this.eRow.getElement('input').get('name')) + '/' + el.get('data-id'),
            onComplete: function(html) {
              el.store('loaded', true);
              eUl.set('html', Elements.from(html)[0].get('html'));
              eUl.setStyle('display', 'block');
              this.initEls(eUl);
              this.form.fireEvent('newElement', eUl);
            }.bind(this)
          }).send();
          return;
        }
        eUl.setStyle('display', eUl.getStyle('display') == 'block' ? 'none' : 'block');
      }.bind(this));
    }.bind(this));
    eParent.getElements('input').each(function(el) {
      if (el.get('checked')) this.openUp(el);
    }.bind(this));
  },

  openUp: function(el) {
    var eUl = el.getParent('ul');
    if (!eUl) return;
    eUl.setStyle('display', 'block');
    this.openUp(eUl);
  }

});

Ngn.Form.El.DdTagsTreeMultiselect = new Class({
  Extends: Ngn.Form.El.DdTagsTreeSelect
});

Ngn.Form.El.DdTagsConsecutiveSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    new Ngn.frm.DdConsecutiveSelect(this.eRow, this.strName, {
      onRequest: function(eSelect) {
        this.form.validator.resetField(eSelect);
      }.bind(this),
      onComplete: function() {
        this.form.validator.rewatchFields();
      }.bind(this)
    });
  }

});
