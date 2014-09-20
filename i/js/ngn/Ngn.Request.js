Ngn.Request = new Class({
  Extends: Request,

  success: function(text, xml) {
    Ngn.requestLoaded = true;
    if (text.contains('Error: ')) {
      return;
    }
    this.parent(text, xml);
  },

  send: function(options) {
    Ngn.requestLoaded = false;
    this.parent(options);
  }

});

Ngn.Request.Loading = new Class({
  Extends: Ngn.Request,

  success: function(text, xml) {
    Ngn.loading(false);
    this.parent(text, xml);
  },

  send: function(options) {
    Ngn.loading(true);
    this.parent(options);
  }

});

Ngn.json = {};
Ngn.json.decode = function(text, secure) {
  return Ngn.json.process(JSON.decode(text, secure));
};

Ngn.json.process = function(json) {
  if (!json) return json;
  for (var i in json) {
    if (typeof(json[i]) == 'object' || typeof(json[i]) == 'array') {
      json[i] = Ngn.json.process(json[i]);
    } else if (typeOf(json[i]) == 'string') {
      if (json[i].test(/^func: .*/)) {
        json[i] = json[i].replace(/^func: (.*)/, '$1');
        json[i] = eval('(function() {' + json[i] + '})');
      }
    }
  }
  return json;
};

Ngn.Request.JSON = new Class({
  Extends: Request.JSON,

  success: function(text) {
    Ngn.requestLoaded = true;
    try {
      this.response.json = Ngn.json.decode(text, this.options.secure);
    } catch (e) {
      throw new Error('non-json result by url ' + this.options.url + '. result:\n' + text);
    }

    if (this.response.json === null) {
      this.onSuccess({});
      return;
    }
    if (this.response.json.actionDisabled) {
      window.location.reload(true);
      return;
    }
    if (this.response.json.error) {
      Ngn.Request.JSON.throwServerError(this.response.json);
      return;
    }
    // sflm
    if (this.response.json.sflJsDeltaUrl) {
      Asset.javascript(this.response.json.sflJsDeltaUrl, {
        onLoad: function() {
          this.onSuccess(this.response.json, text);
        }.bind(this)
      });
    } else {
      this.onSuccess(this.response.json, text);
    }
    if (this.response.json.sflCssDeltaUrl) Asset.css(this.response.json.sflCssDeltaUrl);
  },

  send: function(options) {
    Ngn.requestLoaded = false;
    this.parent(options);
  }

});

Ngn.Request.JSON.throwServerError = function(r) {
  throw new Error(r.error.message + "\n----------\n" + r.error.trace)
};

Ngn.Request.sflJsDeltaUrlOnLoad = false;