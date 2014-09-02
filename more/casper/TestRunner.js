var require = patchRequire(require);
var Project = require('Project');
var c = function(v) {
  require('utils').dump(v);
};

module.exports = new Class({
  Extends: Project,

  //logLevel: 3,
  i: 0,
  callbackPrefixes: [
    'then', 'wait'
  ],

  initialize: function() {
    this.parent();
    var data = require('system').stdin.readLine();
    if (!data.replace(new RegExp('\\s', 'g'), '')) throw new Error('Wrong or empty json in stdin');
    data = JSON.decode(data);
    this.steps = data.steps;
    if (data.extension) this.casper = Object.merge(this.casper, require(data.extension));
    this.casper.on('page.error', function(msg, trace) {
      var t = '';
      for (var i = 0; i < trace.length; i++) {
        t += trace[i].file + ':' + trace[i].line + "\n";
      }
      console.debug(msg + "\n" + t + "--");
      this.exit();
    });
    this.casper.setFilter("page.confirm", function(msg) {
      return true;
    });
    var runner = this;
    this.casper.thenUrl = function(url, callback) {
      this.thenOpen(runner.baseUrl + '/' + url, function() {
        this.evaluate(function() {
        });
        this.wait(10, callback);
        //callback.delay(2000);
      });
    };
    this.casper.waitForPageLoaded = function(callback) {
      this.wait(100, function() {
        this.waitFor(function() {
          return this.evaluate(function() {
            return Ngn.requestLoaded;
          });
        }, callback);
      });
    };
    this.casper.waitForDialog = function(callback) {
      this.waitForSelector('.dialog .apeform', function() {
        this.evaluate(function() {
        });
        callback();
      });
    };
    this.casper.waitForDialogClose = function(callback) {
      this.waitWhileSelector('.dialog .apeform', function() {
        this.evaluate(function() {
        });
        callback();
      });
    };
    this.casper.selectOption = function(selector, value) {
      this.evaluate(function(selector, value) {
        document.querySelector(selector).value = value;
      }, {
        selector: selector,
        value: value
      });
    };
    this.casper.checkExistence = function(selector) {
      return [
        this.exists(selector),
        '"' + selector + '" selector does not exists'
      ];
    };
    this.casper.checkText = function(selector, textToCompare) {
      var text = this.fetchText(selector);
      return [
        text == textToCompare,
        'text and textToCompare are not identical. Selector "' + selector + '" value: ' + text + '; text to compare: ' + textToCompare
      ];
    };
    this.casper.printText = function(selector) {
      console.debug(this.fetchText(selector));
    };
  },

  run: function() {
    this.runStep();
  },

  isCallbackMethod: function(name) {
    for (var i = 0; i < this.callbackPrefixes.length; i++) {
      if (name.substr(0, this.callbackPrefixes[i].length) === this.callbackPrefixes[i]) {
        return true;
      }
    }
    return false;
  },

  processStep: function(cmd) {
    var methodName = cmd[0];
    var negativeCheck = false;
    if (methodName.substr(0, 1) == '!') {
      methodName = methodName.substr(1, methodName.length);
      negativeCheck = true;
    }
    var nextMethod, methodBind, method, methodType;
    if (this[methodName]) {
      method = this[methodName];
      methodBind = this;
      methodType = 'TestRunner';
    } else if (this.casper[methodName]) {
      method = this.casper[methodName];
      methodBind = this.casper;
      methodType = 'Casper';
    } else if (this.casper.page[methodName]) {
      method = this.casper.page[methodName];
      methodBind = this.casper;
      methodType = 'Page';
    } else {
      throw new Error('Casper or TestRunner method "' + methodName + '" is absent');
    }
    var params = null;
    this.log('Running ' + methodType + '::' + methodName, 2);
    if (this.isCallbackMethod(methodName)) {
      nextMethod = this.getNextMethod(methodName);
      params = cmd[1] !== undefined ? cmd.slice(1, cmd.length) : [];
      if (method.length - 1 != params.length) {
        throw new Error('method "' + methodName + '" must have ' + (method.length - 1) + ' params. ' + params.length + ' passed instead');
      }
      params.push(nextMethod); // last param is always casper callback fn
      method = method.pass(params, methodBind);
      this.callMethod(methodName, method);
    } else {
      if (cmd[1] !== undefined) {
        params = cmd.slice(1, cmd.length);
        if (method.length != params.length) throw new Error('method "' + methodName + '" must have ' + method.length + ' params. ' + params.length + ' passed instead');
        method = method.pass(params, methodBind);
      } else {
        method = method.bind(methodBind);
      }
      this.callMethod(methodName, method, negativeCheck);
      this.getNextMethod(methodName)();
    }
  },

  callMethod: function(methodName, method, negativeCheck) {
    if (methodName.substr(0, 5) == 'check') {
      var r = method();
      if (r[0] !== !negativeCheck) throw new Error(r[1]);
      this.log((negativeCheck ? '!' : '') + methodName + ' success');
      return;
    }
    method();
  },

  getNextMethod: function(currentMethodName) {
    if (!this.steps[this.i + 1]) {
      return function() {
        if (this.isCallbackMethod(currentMethodName)) this.capture(currentMethodName + ' (step: ' + this.i + ')');
        this.log('There are no more steps', 2);
      }.bind(this);
    }
    return function() {
      if (this.isCallbackMethod(currentMethodName)) this.capture(currentMethodName + ' (step: ' + this.i + ')');
      this.nextStep();
    }.bind(this);
  },

  runStep: function() {
    this.processStep(this.steps[this.i]);
  },

  nextStep: function() {
    this.log('Running next cmd (' + this.steps[this.i + 1][0] + ')', 2);
    this.i++;
    this.runStep();
  }

});