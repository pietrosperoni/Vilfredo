      <textarea id="abstract" name="abstract" class="jqrte_popup" rows="250" cols="70"></textarea>
      <?php
	$RTE_TextLimit_abstract = 500;
	 include_once("js/jquery/RichTextEditor/content_editor_abstract.php");
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		 try{
			    $("#abstract_rte").jqrte();
			    $("#abstract_rte").jqrte_setIcon();
			    $("#abstract_rte").jqrte_setContent();
			    var limit = <?= empty($RTE_TextLimit_abstract) ? 'null' : $RTE_TextLimit_abstract; ?>;
			    if (limit) {
				$("#abstract_rte").data('maxlength', limit);
				$("#abstract_rte").data('content_length', 0);
			    }
		  }
		  catch(e){}
	 });
	 </script>
