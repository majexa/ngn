var require = patchRequire(require);
require('mootools');

module.exports = new Class({

  initialize: function(casper) {
    this.casper = require('casper').create({
      //verbose: true
      //logLevel: "debug"
    });
    if (!this.casper.cli.options.projectDir) throw new Error('option "projectDir" is required');
    this.projectDir = this.casper.cli.options.projectDir;
    if (!require('fs').exists(this.projectDir)) throw new Error('folder "' + this.projectDir + '" does not exists');
    this.casper.options.pageSettings = {
      loadImages: false
    };
    this.loadConstant('SITE_DOMAIN', function(domain) {
      if (!domain) throw new Error('domain not defined');
      this.baseUrl = 'http://' + domain;
      this.casper.start();
      this.beforeRun();
      this.run();
      this.casper.run();
    }.bind(this));
  },

  beforeRun: function() {
  },

  run: function() {
    throw new Error('abstract');
  },

  loadConstant: function(name, onComplete) {
    require('child_process').execFile('php', [this.projectDir + '/cmd.php', 'const', name], null, function(err, stdout, stderr) {
      onComplete(stdout);
    });
  }

});