var require = patchRequire(require);

module.exports = new Class({

  logLevel: 1,

  log: function(text, level) {
    if (level === undefined) level = 0;
    if (level <= this.logLevel) console.debug(text);
  }

});