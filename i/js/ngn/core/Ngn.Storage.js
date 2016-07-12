Ngn.Storage = {
  get: function(key) {
    if (localStorage) {
      var v = localStorage.getItem(key);
    } else {
      var v = Cookie.read(key);
    }
    if (v == 'false') {
      return false;
    } else if (v == 'true') {
      return true;
    } else {
      return v;
    }
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
