<div id="table"></div>
<script>
new Ngn.Grid({
  basePath: Ngn.Url.getPath(3),
  fromDialog: true,
  toolActions: {
    edit: Ngn.Items.toolActions.inlineTextEdit
    //delete: Ngn.Items.toolActions.delete
  }

}).reload();
</script>
