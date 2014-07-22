var require = patchRequire(require);
var Project = require('Project');

module.exports = new Class({
  Extends: Project,

  initialize: function(casper) {
    this.parent(casper);
    this.test = JSON.decode(require('system').stdin.readLine());
    if (!this.test) throw new Error('Wrong or empty json in stdin');
    //if (!this.casper.cli.options.test) throw new Error('option "test" is required');
    //this.test = eval(require('fs').read(this.casper.cli.options.test));
    this.casper.on('page.error', function(msg, trace) {
      var t = '';
      for (var i = 0; i < trace.length; i++) {
        t += trace[i].file + ':' + trace[i].line + "\n";
      }
      console.debug(msg + "\n" + t + "--");
      this.exit();
    });
    this.casper.waitForPageLoaded = function(callback) {
      this.wait(100, function() {
        this.waitFor(function() {
          return this.evaluate(function() {
            return Ngn.requestLoaded;
          });
        }, callback);
      });
    };
  },

  testUrl: function(url) {
    console.debug('testUrl: ' + url);
    this.casper.thenOpen(url);
    this.casper.waitForPageLoaded();
  },

  beforeRun: function() {
    this.casper.thenOpen(this.baseUrl);
  },

  run: function() {
    for (var i = 0; i < this.test.length; i++) {
      if (typeof this.test[i] == 'string') {
        this.testUrl(this.baseUrl + '/' + this.test[i]);
      }
    }
  }

});