Ngn.Html = {};

Ngn.Html.fixEmptyTds = function(el) {
  var tds = el.getElements('td');
  for (var i = 0; i < tds.length; i++)
    if (!tds[i].get('html').trim()) tds[i].set('html', '&nbsp;');
};
