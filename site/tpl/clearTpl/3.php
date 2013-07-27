<script>
  var ws = new WebSocket('ws://62.76.45.154:8000');
  ws.onopen = function(event) {
    ws.send(JSON.encode(['orderChanged', 177]));
  };
</script>