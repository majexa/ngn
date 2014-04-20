Ngn.regNamespace('Ngn.site.top.briefcase.btns', true);

Ngn.site.top.briefcase.btns.unshift(['users', 'Профиль', function() {
  window.location = Ngn.tpls.userPath.replace('{id}', this.userId);
}]);

Ngn.site.top.briefcase.Menu = new Class({

  Implements: [Options],

  initialize: function(userId, options) {
    this.setOptions(options);
    this.userId = userId;
    var obj = this;
    new Ngn.DropdownWin($('personal').getElement('.briefcase'), {
      winClass: 'briefcaseWin',
      onDomReady: function() {
        var eCont = Elements.from('<div class="iconsSet"></div>')[0];
        for (var i=0; i<Ngn.site.top.briefcase.btns.length; i++) {
          var btn = Ngn.site.top.briefcase.btns[i];
          new Ngn.Btn(Ngn.btn1(btn[0], btn[1]).inject(eCont), btn[2].bind(obj));
        }
        eCont.inject(this.eBody)
      }
    });
  }
  
});

/*
'<a href="<?= Tt()->getUserPath(Auth::get('id')) ?>" class="users"><i></i>Инфо</a>'+
'<a href="#" class="profile"><i></i>Изменить профиль</a>'+
'<a href="#" class="settings"><i></i>Настройки аккаунта</a>'+
'<a href="#" class="cart"><i></i>Параметры магазина</a>'+
*/