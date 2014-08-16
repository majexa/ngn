var require = patchRequire(require);
var Project = require('Project');

module.exports = new Class({
  Extends: Project,

  //logLevel: 3,
  i: 0,
  callbackPrefixes: [
    'then', 'wait'
  ],

  initialize: function(casper) {
    this.parent(casper);
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
        runner.capture();
        callback();
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
        runner.capture();
        callback();
      });
    };
    this.casper.waitForDialogClose = function(callback) {
      this.waitWhileSelector('.dialog .apeform', function() {
        this.evaluate(function() {
        });
        runner.capture();
        callback();
      });
    };
    this.casper.checkExistence = function(selector) {
      if (!this.exists(selector)) throw new Error('"' + selector + '" selector does not exists');
    };
    this.casper.checkNonExistence = function(selector) {
      if (this.exists(selector)) throw new Error('"' + selector + '" selector has not to be present');
    };
  },

  run: function() {
    this.runCmd();
  },

  isCallbackMethod: function(name) {
    for (var i = 0; i < this.callbackPrefixes.length; i++) {
      if (name.substr(0, this.callbackPrefixes[i].length) === this.callbackPrefixes[i]) {
        return true;
      }
    }
    return false;
  },

  processCmd: function(cmd) {
    var method = cmd[0];
    var nextMethod, fnBind, fn;
    if (this[method]) {
      fn = this[method];
      fnBind = this;
    } else if (this.casper[method]) {
      fn = this.casper[method];
      fnBind = this.casper;
    } else {
      throw new Error('Casper or TestRunner method "' + method + '" is absent');
    }
    var params = null;
    this.log('Running ' + method, 2);
    if (this.isCallbackMethod(method)) {
      nextMethod = this.getNextMethod();
      params = cmd[1] !== undefined ? [cmd[1]] : [];
      if (fn.length - 1 != params.length) {
        throw new Error('method "' + method + '" must have ' + (fn.length - 1) + ' params. ' + params.length + ' passed instead');
      }
      params.push(nextMethod);
      fn = fn.pass(params, fnBind);
      fn();
    } else {
      if (cmd[1] !== undefined) {
        params = [cmd[1]];
        if (fn.length != params.length) throw new Error('method "' + method + '" must have ' + fn.length + ' params. ' + params.length + ' passed instead');
        fn = fn.pass(params, fnBind);
      } else {
        fn = fn.bind(fnBind);
      }
      fn();
      this.getNextMethod()();
    }
  },

  getNextMethod: function() {
    if (!this.steps[this.i + 1]) {
      return function() {
        this.log('There are no more steps', 2);
      }.bind(this);
    }
    return this.nextCmd.bind(this);
  },

  runCmd: function() {
    this.processCmd(this.steps[this.i]);
  },

  nextCmd: function() {
    this.log('Running next cmd (' + this.steps[this.i + 1][0] + ')', 2);
    this.i++;
    this.runCmd();
  }

  //beforeRun: function() {
  //this.casper.thenOpen(this.baseUrl);
  //}

});