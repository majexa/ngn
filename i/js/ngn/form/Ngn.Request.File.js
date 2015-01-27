Ngn.progressSupport = ('onprogress' in new Browser.Request);

// Обёртка для Request с поддержкой FormData
Ngn.Request.File = new Class({
  Extends: Ngn.Request.JSON,

  options: {
    emulation: false, urlEncoded: false, allowDublicates: false, formData: null
  },

  initialize: function(options) {
    this.id = Ngn.randString(20);
    this.xhr = new Browser.Request();
    this.setOptions(options);
    this.clear();
    this.headers = this.options.headers;
    if (this.options.formData) for (var i in this.options.formData) this.append(i, this.options.formData[i]);
  },

  clear: function() {
    this.formData = new FormData();
    this._formData = {};
    return this;
  },

  bracketCount: {},

  append: function(key, value) {
    var hasStr = function(haystack, needle) {
      var pos = haystack.indexOf(needle);
      if (pos == -1) {
        return false;
      } else {
        return true;
      }
    };
    var baseKey;
    var multi = hasStr(key, '[]');
    if (!multi && !this.options.allowDublicates && this._formData[key]) return;
    if (multi) {
      baseKey = key.replace('[]', '');
      if (!this.bracketCount[baseKey]) this.bracketCount[baseKey] = 0;
      key = baseKey + '[' + this.bracketCount[baseKey] + ']';
      this.bracketCount[baseKey]++;
    }
    this.formData.append(key, value);
    this._formData[key] = value;
    return this.formData;
  },

  send: function(options) {
    if (!this.check(options)) return this;
    Ngn.Request.inProgress.push(this.id);
    this.options.isSuccess = this.options.isSuccess || this.isSuccess;
    this.running = true;
    var xhr = this.xhr;
    if (Ngn.progressSupport) {
      xhr.onloadstart = this.loadstart.bind(this);
      xhr.onprogress = this.progress.bind(this);
      xhr.upload.onprogress = this.progress.bind(this);
    }
    xhr.open('POST', this.options.url, true);
    xhr.onreadystatechange = this.onStateChange.bind(this);
    Object.each(this.headers, function(value, key) {
      try {
        xhr.setRequestHeader(key, value);
      } catch (e) {
        this.fireEvent('exception', [key, value]);
      }
    }, this);
    this.fireEvent('request');
    xhr.send(this.formData);
    if (!this.options.async) this.onStateChange();
    if (this.options.timeout) this.timer = this.timeout.delay(this.options.timeout, this);
    return this;
  }

});
