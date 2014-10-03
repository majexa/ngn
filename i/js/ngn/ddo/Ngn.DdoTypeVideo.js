Ngn.DdoTypeSound = {};

// @requiresBefore i/js/jwplayer/jwplayer.js

jwplayer.key = "Mly7dc8S4bwy8h+wBjk4qOlr2ZgjAkDgqpHC9A==";

document.addEvent('domready', function() {
  document.getElements('.ddItems .t_video').each(function(el) {
    c(el.get('html'));
    /*
     jwplayer(el).setup({
     file: `.$v.`,
     width: 620,
     height: 340,
     image: "/uploads/myPoster.jpg",
     tracks: [{
     file: "/1.srt",
     label: "Russian",
     kind: "captions",
     "default": true
     }]
     });
     */
  });
});
