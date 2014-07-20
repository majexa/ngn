var require = patchRequire(require);
var ProjectBase = require('ProjectBase');

module.exports = new Class({
  Extends: ProjectBase,

  run: function() {
    this.casper.thenOpen(this.baseUrl + '/default/testUsers/dialogAuth');
    this.casper.wait(3000);
  }

});