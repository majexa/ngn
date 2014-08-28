var casper = require('casper').create({
  verbose: true, logLevel: "debug"
});

casper.start();

casper.thenOpen('http://giport.ru', function() {

});
