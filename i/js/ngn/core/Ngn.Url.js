Ngn.Url = {};

Ngn.Url.getPath = function(n) {
  if (n === 0) return './';
  var p = window.location.pathname.split('/');
  var s = '';
  if (!n) n = p.length - 1;
  for (var i = 1; i <= n; i++) {
    s += '/' + (p[i] ? p[i] : 0);
    if (n === i) break;
  }
  return s;
};


Ngn.Url.getParam = function(n, zeroOnUndefined) {
  return Ngn.Url._getParam(window.location.pathname, n + 1, zeroOnUndefined);
};

Ngn.Url._getParam = function(url, n, zeroOnUndefined) {
  var p = url.split('/');
  return p[n] != undefined ? p[n] : (zeroOnUndefined != undefined ? 0 : false);
};
