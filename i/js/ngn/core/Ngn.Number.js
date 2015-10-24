Ngn.Number = {};
Ngn.Number.randomInt = function(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
};
