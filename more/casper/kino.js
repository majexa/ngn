var require = patchRequire(require);
require('mootools');
var Rumax = require('Rumax');

var kino = new Class({
  Implements: [Rumax],
  initialize: function() {
    this.casper = require('casper').create({
      verbose: true, logLevel: "info" // uncomment for debug
    });
    this.casper.options.viewportSize = {
      width: 950,
      height: 500
    };
    this.casper.start('http://www.kinopoisk.ru/film/341/', function() {
      this.capture();
    }.bind(this));
    this.casper.run();
  }
});

new kino();