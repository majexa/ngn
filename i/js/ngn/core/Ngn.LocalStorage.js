Ngn.LocalStorage = {

  clean: function() {
    if (!localStorage) return;
    try {
      for (var k in localStorage) {
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
