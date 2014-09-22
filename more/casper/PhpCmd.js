var require = patchRequire(require);
require('mootools');

module.exports = new Class({

  execFile: function(runner, options, callback) {
    //require('utils').dump([runner, options]);
    //console.log('****' + runner + ' ' + options);
    require('child_process').execFile('run', [runner, options], null, function(err, stdout, stderr) {
      if (stdout.indexOf('exception:') > -1 || stdout.indexOf('error:') > -1) throw new Error(stdout);
      callback(stdout);
    }.bind(this));
  }

});