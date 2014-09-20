var require = patchRequire(require);
require('mootools');
var LogLevel = require('LogLevel');
var PhpCmd = require('PhpCmd');

module.exports = new Class({
  Implements: [LogLevel, PhpCmd],

  logLevel: 1,

  thenOpen: function(url, callback) {
    this.log('open url: ' + url, 2);
    this.casper.thenOpen(url, function(page) {
      this.log('url opened: ' + url, 2);
      this.wrapCallback(callback, page);
    }.bind(this));
  },

  waitForSelector: function(selector, callback) {
    this.casper.waitForSelector(selector, function() {
      this.wrapCallback(callback);
    }.bind(this));
  },

  makeCapture: function(caption, id) {
    if (!id) id = new Date().getTime() + '-' + parseInt(Math.random() * 100000000);
    this.casper.capture('/home/user/ngn-env/rumax/web/captures/' + id + '.png', {
      top: 0,
      left: 0,
      width: 950,
      height: 500
    });
    this.log('captured on ' + caption, 3);
    return id;
  },

  /**
   * @param {string} runner - Path to running program
   * @param {string} options - Look at NgnCl::strParamsToArray for options format
   */
  afterCaptureCmd: function(runner, options) {
    this.execFile(runner, options, function(stdout) {
      this.log('capture result:\n' + stdout, 3);
    }.bind(this));
  },

  capture: function(caption, id) {
    id = this.makeCapture(caption, id);
    this.afterCaptureCmd('rumax/ping', 'id=' + id);
  },

  wrapCallback: function(callback, arg1) {
    this.capture();
    callback(arg1);
  }

});