Ngn.Dialog.Auth = {};

Ngn.Dialog.Auth.Vk = new Class({

  vkInitialized: false,

  vkLogin: function(vkResult) {
    return vkResult.first_name + ' ' + vkResult.last_name;
  },

  vkInit: function() {
    var eVkAuth = $('vkAuth');
    if (!eVkAuth) return;
    if (this.vkInitialized) return;
    if (!Ngn.vkApiId) throw new Error('VK API ID does not defined in config');
    var eVkApiTransport = new Element('div', {id: 'vk_api_transport'}).inject(eVkAuth);
    var vkResultReceived = false;
    window.vkAsyncInit = function() {
      VK.init({apiId: Ngn.vkApiId});
      VK.Widgets.Auth('vkAuth', {
        width: 398,
        onAuth: function(vkResult) {
          if (vkResultReceived) return;
          vkResultReceived = true;
          new Ngn.Request({
            url: '/c/vkAuth/ajax_exists',
            onComplete: function(r) {
              this.vkRequest(vkResult, !(r == 'success'))
            }.bind(this)
          }).post({login: this.vkLogin(vkResult)});
        }.bind(this)
      });
      this.vkInitialized = true;
    }.bind(this);
    (function() {
      var eScript = new Element('script', {
        type: 'text/javascript',
        src: 'http://vkontakte.ru/js/api/openapi.js',
        async: true
      }).inject(eVkApiTransport);
    }).delay(0);
  },

  vkRequest: function(vkResult, create) {
    var data = {
      login: this.vkLogin(vkResult),
      uid: vkResult.uid,
      hash: vkResult.hash
    };
    if (create) {
      // Регистрация нового
      data.pass = prompt('Введите пароль для вашего профиля на сайте ' + Ngn.siteTitle, '');
      if (vkResult.photo) data.image = vkResult.photo;
    }
    new Ngn.Request({
      url: '/c/vkAuth' + (create ? '/ajax_reg' : ''),
      onComplete: function(r) {
        if (r == 'success') {
          this.authComplete();
        } else {
          alert('Ошибка авторизации');
        }
      }.bind(this)
    }).post(data);
  }

});

if (!Ngn.sflmFrontend) throw new Error('Ngn.sflmFrontend not defined');

// @requiresBefore s2/js/common/Ngn

Ngn.Dialog.Auth = new Class({
  Extends: Ngn.Dialog.RequestFormTabs,
  Implements: [Ngn.Dialog.Auth.Vk],

  options: {
    closeOnComplete: false,
    onAuthComplete: Function.from(),
    reloadOnAuth: true, //dialogClass: 'dialog fieldFullWidth',
    selectedTab: 0,
    id: 'auth',
    url: '/default/auth/json_auth',
    width: 300,
    completeUrl: null,
    fromVkEnabled: false
  },

  initialize: function(opts) {
    this.parent(opts);
    if (this.options.completeUrl) this.options.reloadOnAuth = true;
    if (this.options.fromVkEnabled && Ngn.fromVk) this.options.selectedTab = 2;
  },

  urlResponse: function(_response) {
    this.parent(_response);
    this.tabs.addEvent('select', function(toggle, container, index) {
      if (container.get('id') == 'vkAuth') {
        this.vkInit();
        this.footer.setStyle('display', 'none');
      } else {
        this.footer.setStyle('display', 'block');
      }
    }.bind(this));
    if (Ngn.Dialog.Auth.requestActions.length)
      for (var i = 0; i < Ngn.Dialog.Auth.requestActions.length; i++)
        Ngn.Dialog.Auth.requestActions[i].bind(this)();
  },

  authComplete: function() {
    this.fireEvent('authComplete', this);
    this.close();
    if (this.options.reloadOnAuth) {
      new Ngn.Dialog.Loader.Simple({
        title: 'Подождите...',
        hasFaviconTimer: false
      });
      this.options.completeUrl ? window.location.assign(this.options.completeUrl) : window.location.reload(true);
    }
  },

  // eval methods

  submitSuccessAuth: function(r) {
    this.authComplete();
  },

  submitSuccessUserReg: function(r) {
    if (r.activation) {
      this.close();
      this.fireEvent('activation', r.activation);
    } else if (r.authorized) {
      this.authComplete();
    }
  },

  submitSuccessUserRegPhone: function(r) {
    console.debug('not realized');
    //this.authComplete();
  }

});

Ngn.Dialog.Auth.requestActions = [];