var require = patchRequire(require);
require('mootools');
var Rumax = require('Rumax');

module.exports = new Class({
  Implements: [Rumax, Options],

  initialize: function(options) {
    this.log('initializing', 3);
    this.setOptions(options);
    this.casper = require('casper').create({
      //verbose: true, logLevel: "info" // uncomment for debug
    });
    if (!this.casper.cli.options.projectDir) throw new Error('option "projectDir" is required');
    this.projectDir = this.casper.cli.options.projectDir;
    if (!require('fs').exists(this.projectDir)) throw new Error('folder "' + this.projectDir + '" does not exists');
    this.log('init casper', 3);
    this.initCasper();
    this.init();
    this.loadConstant('SITE_DOMAIN', function(domain) {
      if (!domain) throw new Error('domain not defined');
      this.baseUrl = 'http://' + domain;
      this.casper.start();
      this.beforeRun();
      this.run();
      this.casper.run();
    }.bind(this));
  },

  init: function() {},

  initCasper: function() {
    this.casper.options.viewportSize = {
      width: 950,
      height: 500
    };
    this.casper.options.pageSettings = {
      //loadPlugins: false,
      //loadImages: false
    };
  },

  beforeRun: function() {
  },

  run: function() {
    throw new Error('abstract');
  },

  ngnBasePaths: null,

  loadConstant: function(name, onComplete) {
    require('child_process').execFile('php', [this.projectDir + '/cmd.php', 'print json_encode(Ngn::$basePaths);'], null, function(err, stdout, stderr) {
      this.ngnBasePaths = JSON.decode(stdout);
      //require('utils').dump(this.ngnBasePaths);
      this.log('loading constant ' + name, 3);
      require('child_process').execFile('php', [this.projectDir + '/cmd.php', 'const', name], null, function(err, stdout, stderr) {
        this.log('constant ' + name + ' loaded: ' + stdout, 3);
        onComplete(stdout);
      }.bind(this));
    }.bind(this));
  }

});