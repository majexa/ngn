var require = patchRequire(require);
var Project = require('Project');

module.exports = new Class({
  Extends: Project,

  i: 0,
  callbackPrefixes: [
    'then', 'wait'
  ],

  initialize: function(casper) {
    this.parent(casper);
    this.test = require('system').stdin.readLine();
    if (!this.test.replace(new RegExp('\\s', 'g'), '')) throw new Error('Wrong or empty json in stdin');
    this.test = JSON.decode(this.test);
    this.casper.on('page.error', function(msg, trace) {
      var t = '';
      for (var i = 0; i < trace.length; i++) {
        t += trace[i].file + ':' + trace[i].line + "\n";
      }
      console.debug(msg + "\n" + t + "--");
      this.exit();
    });
    var runner = this;
    this.casper.thenUrl = function(url, callback) {
      this.thenOpen(runner.baseUrl + '/' + url, callback);
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
        console.debug('DIALOG CALLBACK');
        callback();
      });
    };
    this.casper.waitForDialogClose = function(callback) {
      this.waitWhileSelector('.dialog .apeform', function() {
        this.evaluate(function() {
        });
        runner.capture();
        console.debug('DIALOG CLOSE CALLBACK');
        callback();
      });
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
    var nextMethod;
    var casperFn = this.casper[method];
    var params = null;
    if (typeof casperFn === 'function') {
      console.debug('RUNNING ' + method);
      if (this.isCallbackMethod(method)) {
        nextMethod = this.getNextMethod();
        params = cmd[1] !== undefined ? [cmd[1]] : [];
        if (casperFn.length - 1 != params.length) {
          throw new Error('method "' + method + '" must have ' + (casperFn.length - 1) + ' params. ' + params.length + ' passed instead');
        }
        params.push(nextMethod);
        casperFn = casperFn.pass(params, this.casper);
        casperFn();
      } else {
        if (cmd[1] !== undefined) {
          params = [cmd[1]];
          if (casperFn.length != params.length) throw new Error('method "' + method + '" must have ' + casperFn.length + ' params. ' + params.length + ' passed instead');
          casperFn = casperFn.pass(params, this.casper);
        } else {
          casperFn = casperFn.bind(this.casper);
        }
        casperFn();
        this.getNextMethod()();
      }
    } else {
      throw new Error('Casper method "' + method + '" is absent');
    }
  },

  getNextMethod: function() {
    if (!this.test[this.i + 1]) {
      return function() {
        console.debug('THERE ARE NO MORE STEPS');
      }.bind(this);
    }
    return this.nextCmd.bind(this);
  },

  runCmd: function() {
    this.processCmd(this.test[this.i]);
  },

  nextCmd: function() {
    console.debug('RUNNING NEXT CMD (' + this.test[this.i + 1][0] + ')');
    this.i++;
    this.runCmd();
  }

  //beforeRun: function() {
  //this.casper.thenOpen(this.baseUrl);
  //}

});