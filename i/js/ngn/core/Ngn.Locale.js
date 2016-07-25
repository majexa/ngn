Ngn.Locale = {
  get: function(key) {
    return Locale.get(key) || Ngn.String.ucfirst(String(key).replace(/\w+\.(.+)/g, '$1').replace(/[A-Z]/g, function(match){
      return (' ' + match.charAt(0).toLowerCase());
    }));
  }
};