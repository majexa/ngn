Ngn.Arr = {};
Ngn.Arr.inn = function(needle, haystack, strict) {  // Checks if a value exists in an array
  var found = false, key, strict = !!strict;
  for (key in haystack) {
    if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
      found = true;
      break;
    }
  }
  return found;
};

Ngn.Arr.drop = function(array, value) {
  return array.splice(array.indexOf(value), 1);
};

