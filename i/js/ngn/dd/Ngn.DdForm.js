Ngn.DdForm = new Class({
  Extends: Ngn.Form,
  
  eForm: null,
  
  initialize: function(eForm, options) {
    this.strName = eForm.get('data-strName');
    if (!this.strName) throw new Error('"data-strName" is empty in form #' + eForm.get('id'));
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
