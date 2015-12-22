Ngn.Window = {};

Ngn.Window.confirm = function(question) {
  if (!question) question = 'Вы уверены?';
  return confirm(question);
};
