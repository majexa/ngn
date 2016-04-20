Ngn.String = {};
Ngn.String.rand = function(len) {
  var allchars = 'abcdefghijknmpqrstuvwxyzABCDEFGHIJKLNMPQRSTUVWXYZ'.split('');
  var string = '';
  for (var i = 0; i < len; i++) {
    string += allchars[Ngn.Number.randomInt(0, allchars.length - 1)];
  }
  return string;
};

Ngn.String.ucfirst = function(str) {
  var f = str.charAt(0).toUpperCase();
  return f + str.substr(1, str.length - 1);
};

Ngn.String.hashCode = function(str) {
  var hash = 0, i, chr, len;
  if (str.length == 0) return hash;
  for (i = 0, len = str.length; i < len; i++) {
    chr = str.charCodeAt(i);
    hash = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

Ngn.String.trim = function(s) {
  return s.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
};

