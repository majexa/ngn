var require = patchRequire(require);
require('mootools');

module.exports = new Class({

  initialize: function(casper) {
    casper.options.pageSettings = {
      loadImages: false
    };
    //require('utils').dump(casper.cli);
    if (!casper.cli.options.projectDir) throw new Error('option "projectDir" is required');
    this.projectDir = casper.cli.options.projectDir;
    if (!require('fs').exists(this.projectDir)) throw new Error('folder "' + this.projectDir + '" does not exists');
    //
    //casper.log(casper.cli.args);
    //this.casper = casper;
    // require('fs').casper-path
    //if (this.casper.cli.substr(0, 1) != '/');
  },

  configConstToJson: function(onComplete) {
    /*
    require('child_process').execFile('php', _params, null, function(err, stdout, stderr) {
      done = true;
      result = parseJSON(stdout);
    });
    */
  },

  configVarToJson: function(onComplete) {

  }

});