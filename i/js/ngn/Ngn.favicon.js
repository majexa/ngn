// Favicon.js - Change favicon dynamically [http://ajaxify.com/run/favicon].
// Copyright (c) 2006 Michael Mahemoff. Only works in Firefox and Opera.
// Background and MIT License notice at end of file, see the homepage for more.

// USAGE:
// * favicon.change("/icon/active.ico");  (Optional 2nd arg is new title.)
// * favicon.animate(new Array("icon1.ico", "icon2.ico", ...));
//     Tip: Use "" as the last element to make an empty icon between cycles.
//     To stop the animation, call change() and pass in the new arg.
//     (Optional 2nd arg is animation pause in millis, overwrites the default.)
// * favicon.defaultPause = 5000;

Ngn.favicon = {

  // -- "PUBLIC" ----------------------------------------------------------------

  defaultPause: 1000,
  initIconUrl: '/favicon.ico',

  change: function(iconURL, optionalDocTitle) {
    clearTimeout(this.loopTimer);
    if (optionalDocTitle) {
      document.title = optionalDocTitle;
    }
    this.replaceLink(iconURL);
  },

  animate: function(iconSequence, optionalDelay) {
    var links = this.getAllLinks();
    if (links.length && links[0].href) this.initIconUrl = links[0].href;
    // --------------------------------------------------
    this.preloadIcons(iconSequence);
    this.iconSequence = iconSequence;
    this.sequencePause = (optionalDelay) ? optionalDelay : this.defaultPause;
    Ngn.favicon.index = 0;
    Ngn.favicon.change(iconSequence[0]);
    this.loopTimer = setInterval(function() {
      Ngn.favicon.index = (Ngn.favicon.index + 1) % Ngn.favicon.iconSequence.length;
      Ngn.favicon.replaceLink(Ngn.favicon.iconSequence[Ngn.favicon.index], false);
    }, Ngn.favicon.sequencePause);
  },

  stop: function() {
    clearTimeout(this.loopTimer);
    this.removeIconLinksIfExists();
    if (this.initIconUrl) {
      this.replaceLink(this.initIconUrl);
    }
  },

  // -- "PRIVATE" ---------------------------------------------------------------

  loopTimer: null,

  preloadIcons: function(iconSequence) {
    var dummyImageForPreloading = document.createElement("img");
    for (var i = 0; i < iconSequence.length; i++) {
      dummyImageForPreloading.src = iconSequence[i];
    }
  },

  replaceLink: function(iconURL) {
    var link = document.createElement("link");
    link.type = "image/x-icon";
    link.rel = "shortcut icon";
    link.href = iconURL;
    this.removeIconLinksIfExists();
    this.docHead.appendChild(link);
  },

  removeIconLinksIfExists: function() {
    var links = this.getAllLinks();
    for (var i = 0; i < links.length; i++) {
      this.docHead.removeChild(links[i]);
    }
  },

  getAllLinks: function() {
    var r = [];
    var esLink = this.docHead.getElementsByTagName("link");
    var n = 0;
    for (var i = 0; i < esLink.length; i++) {
      if (esLink[i].type == "image/x-icon"/* && esLink[i].rel == "shortcut icon"*/) {
        r[n] = esLink[i];
      }
    }
    return r;
  },

  docHead: document.getElementsByTagName("head")[0]
}