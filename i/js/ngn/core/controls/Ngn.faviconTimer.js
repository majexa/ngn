Ngn.FaviconTimer = {
  
  start: function() {
    Ngn.Favicon.animate([
      '/i/img/icons/l/loader1.ico',
      '/i/img/icons/l/loader2.ico',
      '/i/img/icons/l/loader3.ico',
      '/i/img/icons/l/loader4.ico'
    ]);
  },
  
  stop: function() {
    Ngn.Favicon.stop();
  }
  
};
