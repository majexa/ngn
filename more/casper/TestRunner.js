var require = patchRequire(require);
var Project = require('Project');
var c = function(v) {
  require('utils').dump(v);
};

module.exports = new Class({
  Extends: Project,
  Implements: Options,

  logLevel: 1,
  i: 0,
  callbackPrefixes: [
    'then', 'wait'
  ],

  options: {
    captureFolder: null
  },

  initialize: function(options) {
    this.parent();
    var stdinOptions = require('system').stdin.readLine();
    if (!stdinOptions.replace(new RegExp('\\s', 'g'), '')) throw new Error('Wrong or empty json in stdin');
    stdinOptions = JSON.decode(stdinOptions);
    this.setOptions(Object.merge(stdinOptions, options));
    this.stepsPreparse();
    if (this.options.extension) this.casper = Object.merge(this.casper, require(this.options.extension));
    this.casper.on('page.error', function(msg, trace) {
      var t = '';
      for (var i = 0; i < trace.length; i++) {
        t += trace[i].file + ':' + trace[i].line + "\n";
      }
      console.debug(msg + "\n" + t + "--");
      this.exit();
    });
    this.casper.on('remote.message', function(message) {
      //this.echo('CLIENT: ' + message);
    });
    this.casper.setFilter('page.confirm', function(msg) {
      return true;
    });
    var runner = this;
    this.casper.thenUrl = function(url, callback) {
      this.thenOpen(runner.baseUrl + '/' + url, function() {
        this.evaluate(function() {
        });
        this.wait(100, callback);
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
    this.casper.closeDialog = function(callback) {
      this.click('.md-closer');
      this.waitForDialogClose(callback);
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
        this.exists(selector), //
        '"' + selector + '" selector does not exists', //
        '"' + selector + '" selector exists'
      ];
    };
    this.casper.checkText = function(selector, textToCompare) {
      var text = this.fetchText(selector);
      return [
        text == textToCompare, //
        'text and textToCompare are not identical. Selector "' + selector + '" value: ' + text + '; text to compare: ' + textToCompare, 'text and textToCompare are identical. Selector "' + selector + '" value: ' + text + '; text to compare: ' + textToCompare
      ];
    };
    this.casper.printText = function(selector) {
      console.debug(this.fetchText(selector));
    };
    this.casper.printProperty = function(selector, property) {
      var value = this.evaluate(function(selector, property) {
        return document.querySelector(selector)[property];
      }, {
        selector: selector,
        property: property
      });
      console.log(selector + ' [' + property + '] = "' + value + '"');
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

  disableCapturing: false,

  processStep: function(step) {
    var methodName = step[0];
    var negativeCheck = false;
    if (methodName.substr(0, 1) == '!') {
      methodName = methodName.substr(1, methodName.length);
      negativeCheck = true;
    }
    var nextMethod, methodBind, method;
    if (this[methodName]) {
      method = this[methodName];
      methodBind = this;
    } else if (this.casper[methodName]) {
      method = this.casper[methodName];
      methodBind = this.casper;
    } else if (this.casper.page[methodName]) {
      method = this.casper.page[methodName];
      methodBind = this.casper;
    } else {
      throw new Error('Casper or TestRunner method "' + methodName + '" is absent');
    }
    var params = null;
    if (this.isCallbackMethod(methodName)) {
      nextMethod = this.getNextMethod(methodName);
      params = step[1] !== undefined ? step.slice(1, step.length) : [];
      //if (method.length - 1 != params.length) throw new Error('method "' + methodName + '" must have ' + (method.length - 1) + ' params. ' + params.length + ' passed instead');
      params.push(nextMethod); // last param is always casper callback fn
      method = method.pass(params, methodBind);
      this.callMethod(methodName, method);
    } else {
      if (step[1] !== undefined) {
        params = step.slice(1, step.length);
        //if (method.length != params.length) throw new Error('method "' + methodName + '" must have ' + method.length + ' params. ' + params.length + ' passed instead');
        //require('utils').dump([methodName, params]);
        method = method.pass(params, methodBind);
      } else {
        method = method.bind(methodBind);
      }
      this.callMethod(methodName, method, negativeCheck);
      this.getNextMethod()();
    }
  },

  callMethod: function(methodName, method, negativeCheck) {
    if (methodName.substr(0, 5) == 'check') {
      var r = method();
      if (r[0] !== !negativeCheck) {
        throw new Error(r[negativeCheck ? 2 : 1]);
      }
      this.log((negativeCheck ? '!' : '') + methodName + ' success');
      return;
    }
    method();
  },

  getNextMethod: function() {
    if (!this.options.steps[this.i + 1]) {
      return function() {
        this.casper.wait(1000, function() {
          this.log('There are no more steps', 2);
        });
      }.bind(this);
    }
    return function() {
      this.nextStep();
    }.bind(this);
  },

  _capture: function(caption) {
    if (this.disableCapturing) return;
    if (this.options.captureFolder) {
      var id = this.makeCapture(caption);
      this.afterCaptureCmd('rumax/save', 'id=' + id + //
        '+folder=' + this.options.captureFolder + //
        '+n=' + (this.i + 1) + //
        '+caption=' + caption.replace(new RegExp(' ', 'g'), '_') //
      );
      return;
    }
    this.capture(caption, this.i + 1);
  },

  runStep: function() {
    this.processStep(this.options.steps[this.i]);
  },

  nextStep: function() {
    var step = this.options.steps[this.i + 1];
    var params = '';
    if (step.length > 1) params = ' (' + step.slice(1, step.length).join(', ') + ')';
    this.log('running> ' + step[0] + params, 2);
    this.i++;
    this.runStep();
  },

  stepsPreparse: function() {
    var steps = [];
    for (var i = 0; i < this.options.steps.length; i++) {
      var step = this.options.steps[i];
      if (step[0].substr(0, 1) == '~') {
        step[0] = step[0].substr(1, step[0].length);
        steps.push(step);
        steps.push(['wait', 1000]);
        steps.push(['_capture', 'after ' + step[0]]);
      } else {
        steps.push(step);
      }
    }
    this.options.steps = steps;
  }

});