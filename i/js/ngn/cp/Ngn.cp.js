Ngn.cp = {
  init: function() {
    if (Browser.ie) {
      alert('Броузер Internet Explorer не боддерживается Панелью управления.');
      window.location = '/';
      return;
    }
    this.initConfirms();
    this.initSaveAndReturn();
    //this.repairEmptyTds();
    this.initTooltips();
    //this.initHelp();
    this.initFormBases();
    this.colorizeSelects();
    //new Ngn.HidebleBar('bottom', 'down');
    //new Ngn.HidebleBar.H('header', 'up');
    //Ngn.Lightbox.add(document.getElements('a.lightbox'));
  },
  initHelp: function() {
    $$('a[class=help]').each(function(el){
      el.addEvent('click', function() {
        new Ngn.Dialog.Alert({
          force: true,
          title: el.get('text'),
          url: el.get('href')
        });
        return false;
      })
    });
    //
  },
  initTooltips: function() {
    new Ngn.Tips('.tools a,.tooltips a,.tooltip');
  },
  repairEmptyTds: function() {
    $('itemsTable').getElements('td').each(function(eTd, i) {
      if (eTd.get('html').trim() == '') 
        eTd.set('html', '&nbsp;');
    });
  },
  submitTitles: null,
  formSubmitTimeout: 600000,
  forms: {},
  addSubmitEvent: function(eForm) {
    var id = eForm.get('id');
    if (!id) {
      //c('Form with no id can not be initialized.');
      return;
    }
    if (this.forms[id]) {
      // If already exists
      return;
    }
    
    this.forms[id] = eForm;
    eForm.addEvent('submit', function(e) {
      // Проверяем валидность если существуют валидационные функции
      if (this.validations[id]) {
        if (!this.validations[id]()) {
          e.preventDefault();
          return;
        }
      }
      // Инициализируем возврат формы в активное положение
      this.returnSubmitTimeout.delay(this.formSubmitTimeout, this, eForm);
      // Делаем все INPUT элементы формы неактивными
      /*
      eForm.getElements('input').each(function(el, i) {
        el.setProperty('disabled', true);
      });
      */
      
      
      // Меняем названия кнопок на герундий
      eForm.getElements('input[type=submit]').each(function(btn, i) {
        btn.setProperty('disabled', true);
        var title = this.submitTitles[btn.get('value')];
        if (title) btn.set('value', title + '...');
      }.bind(this));
      
      ///c('submitting');
      
    }.bind(this));
  },
  validations: {},
  addFormValidation: function(eForm, validation) {
    var id = eForm.get('id');
    if (!id) // ID not defined in form tag
      return;
    this.validations[id] = validation;
  },
  removeFormValidations: function(eForm) {
    var id = eForm.get('id');
    if (!id) // ID not defined in form tag
      return;
    this.validations[id] = null;
  },
  returnSubmitTimeout: function(eForm) {
    alert('Нет ответа от сервера. Попробуйте отправить форму повторно.');
    this.returnSubmitTitles(eForm);
  },
  returnSubmitTitles: function(eForm) {
    if (!this.submitTitles) alert('this.submitTitles not defined');
    /*
    eForm.getElements('input').each(function(el, i) {
      el.setProperty('disabled', false);
    });
    */
    eForm.getElements('input[type=submit]').each(function(btn, i) {
      btn.setProperty('disabled', false);
      var newTitles = {};
      for (k in this.submitTitles)
        newTitles[this.submitTitles[k]] = k;
      var title = newTitles[btn.get('value').replace('...', '')];
      if (title) btn.set('value', title);
    }.bind(this));
  },
  initConfirms: function() {
    // Добавляем диалоговое окно подтверждения операции для ссылок с классом
    // "confirm"
    document.getElements('a[class~=confirm]').each(function(a, i) {
      a.addEvent('click', function(e){
        e.preventDefault();
        var title = this.get('text');
        if (!title) title = this.get('title');
        if (confirm(
          (title ? 'Вы действительно хотите ' + title.toLowerCase() : 'Вы уверены') + '?'))
          window.location = this.href;
      });
    });
  },
  initSaveAndReturn: function() {
    var eSaveAndReturn = $('saveAndReturn');
    if (eSaveAndReturn) {
      eSaveAndReturn.addEvent('change', function(){
        Cookie.write('saveAndReturn', eSaveAndReturn.get('checked') ? 1 : 0);
      });
      eSaveAndReturn.set('checked', Cookie.read('saveAndReturn') == 1 ? true : false);
    }
  },
  
  initFormBases: function() {
    var div = document.getElement('.apeform');
    if (!div) return;
    if (div.hasClass('forceDefaultInit')) return;
    var eForm = div.getElement('form');
    if (!eForm) throw new Error('.apeform tag has no form');
    Ngn.Form.factory(eForm);
  },
  
  colors: ['#FFFACE', '#FFE8CE', '#FFCEEA', '#EFCEFF', '#CFCEFF', '#CEEFFF', '#CEFFEE', '#CEFFD0', '#E8FFCE'],
  
  colorizeSelects: function() {
    var i = 0;
    $$('select').each(function(eSelect){
      eSelect.getElements('option').each(function(eOption, n){
        eOption.setStyle('background-color', this.colors[i]);
        i++;
        if (i == this.colors.length) i = 0;
      }.bind(this));
    }.bind(this));
  },
  
  getMainAreaHeight: function() {
    var b1 = $('bottom') ? $('bottom').getParent().getSize().y : 0;
    var b2 = $('bottom') ? $('bottom').getSize().y : 0;
    return window.getSize().y
      - (b1 == 0 ? 0 : b2)
      - ($('top').getParent().getSize().y == 0 ? 0 : $('top').getSize().y);
  }

};


Ngn.cp.modules = {};