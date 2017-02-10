<div id="table"></div>
<script>
new Ngn.Grid({
  basePath: Ngn.Url.getPath(3),
  toolActions: {
    edit: Ngn.Items.toolActions.inlineTextEdit
    //delete: Ngn.Items.toolActions.delete
  }

}).reload();
</script>
