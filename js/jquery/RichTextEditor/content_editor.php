<div class="jqrte_body">
<table>
<tr>
   <td>
   <table class="jqrte_menu">
      <tr>
         <td colspan="4" title="<?=getLabel("Select Format",$language);?>">
            <select name="formatblock" id="content_rte_formatblock">
               <option value="" selected="selected"><?=getLabel("Select Format",$language);?></option>
               <option value="&lt;h2&gt;">Title</option>
               <option value="&lt;h3&gt;">Section Title</option>
               <option value="&lt;h4&gt;">SubSection Title</option>
               <option value="&lt;p&gt;">Paragraph</option>
               <option value="&lt;pre&gt;"><pre>code</pre></option>
            </select>
         </td>
         <td id="content_rte_bold" title="<?=getLabel("Bold",$language);?>"></td>
         <td id="content_rte_italic" title="<?=getLabel("Italic",$language);?>"></td>
         <td id="content_rte_underline" title="<?=getLabel("Underline",$language);?>"></td>
         <td id="content_rte_strikethrough" title="<?=getLabel("Strikethrough",$language);?>"></td>
         <td id="content_rte_superscript" title="<?=getLabel("Superscript",$language);?>"></td>
          <td id="content_rte_subscript" title="<?=getLabel("Subscript",$language);?>"></td>
         <td id="content_rte_indent" title="<?=getLabel("Indent",$language);?>"></td>
         <td id="content_rte_outdent" title="<?=getLabel("Outdent",$language);?>"></td>
         <td id="content_rte_insertorderedlist" title="<?=getLabel("Insert Ordered List",$language);?>"></td>
         <td id="content_rte_insertunorderedlist" title="<?=getLabel("Insert Unordered List",$language);?>"></td>
         <td id="content_rte_addlink" title="<?=getLabel("Add Link",$language);?>"></td>
         <td id="content_rte_unlink" title="<?=getLabel("Unlink",$language);?>"></td>
         <td id="content_rte_addimage" title="<?=getLabel("Add Image",$language);?>"></td>
         <td id="content_rte_addtable" title="<?=getLabel("Add Table",$language);?>"></td>
         <td id="content_rte_html" title="<?=getLabel("Html Content",$language);?>"></td>
         <td id="content_rte_copyright" title="<?=getLabel("Copyright",$language);?>"></td>
      </tr>
   </table>
   </td>
</tr>
<tr>
   <td>
      <iframe id="content_rte" src="about:blank" class="jqrte_iframebody"></iframe>
   </td>
</tr>
</table>
</div>