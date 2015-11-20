Ngn.Object = {};

Ngn.Object.fromString = function(s, value) {
  var a = s.split('.');
  for (var i = 0; i < a.length; i++) {
    var ss = a.slice(0, i + 1).join('.');
    eval('var def = ' + ss + ' === undefined');
    if (def) eval((i == 0 ? 'var ' : '') + ss + ' = {}');
  }
  if (value) eval(s + ' = value');
};

Ngn.Object.fromArray = function(arr) {
  if (typeOf(arr) == 'object') return arr;
  var r = {};
  for (var i = 0; i < arr.length; ++i) r[i] = arr[i];
  return r;
};
