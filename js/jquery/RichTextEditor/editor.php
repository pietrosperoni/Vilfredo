<div id="addtable_div" style="display:none" title="<?=getLabel("Add Table",$language);?>">
   <table>
      <tr>
         <td><?=getLabel("Rows",$language);?></td>
         <td><input type="text" id="addtable_row" name="table_row" value="2" size="10"/></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?=getLabel("Columns",$language);?></td>
         <td><input type="text" id="addtable_column" name="table_column" value="2" size="10"/></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?=getLabel("Width",$language);?></td>
         <td><input type="text" id="addtable_width" name="table_width" value="100" size="10"/></td>
         <td>
            <select name="table_width_format" id="addtable_format">
               <option value="%">%</option>
               <option value=""><?=getLabel("pixels",$language);?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td><?=getLabel("Border",$language);?></td>
         <td><input type="text" id="addtable_border" name="table_border" value="1" size="10"/></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?=getLabel("Cellspacing",$language);?></td>
         <td><input type="text" id="addtable_cellspacing" name="table_cellspacing" value="0" size="10"/></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?=getLabel("Cellpadding",$language);?></td>
         <td><input type="text" id="addtable_cellpadding" name="table_cellpadding" value="0" size="10"/></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?=getLabel("Alignment",$language);?></td>
         <td>
            <select id="addtable_alignment" name="table_alignment">
               <option value=""><?=getLabel("default",$language);?></option>
               <option value="left"><?=getLabel("left",$language);?></option>
               <option value="right"><?=getLabel("right",$language);?></option>
               <option value="center"><?=getLabel("center",$language);?></option>
            </select>
         </td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addtable_btn" value="<?=getLabel("Submit",$language);?>"/>
         </td>
      </tr>
   </table>
</div>


<div id="addlink_div" style="display:none"  title="<?=getLabel("Add Link",$language);?>">
   <table>
      <tr>
         <td><?=getLabel("Site Name",$language);?></td>
         <td><input type="text" id="addlink_name" name="link_name" size="20"/></td>
      </tr>
      <tr>
         <td><?=getLabel("URL",$language);?></td>
         <td><input type="text" id="addlink_url" name="link_url"/></td>
      </tr>
      <tr>
         <td><?=getLabel("Target",$language);?></td>
         <td>
            <select name="link_target" id="addlink_target">
               <option value=""></option>
               <option value="_blank"><?=getLabel("_blank",$language);?></option>
               <option value="_parent"><?=getLabel("_parent",$language);?></option>
               <option value="_self"><?=getLabel("_self",$language);?></option>
               <option value="_top"><?=getLabel("_top",$language);?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addlink_btn" value="<?=getLabel("Submit",$language);?>"/>
         </td>
      </tr>
   </table>
</div>

<div id="addimage_div" style="display:none" title="<?=getLabel("Add Image",$language);?>">
   <table>
      <tr>
         <td><?=getLabel("Image URL",$language);?></td>
         <td><input type="text" id="addimage_url" name="image_url"/></td>
      </tr>
      <tr>
         <td><?=getLabel("Image Description",$language);?></td>
         <td><input type="text" id="addimage_desc" name="image_desc"/></td>
      </tr>
      <tr>
         <td><?=getLabel("Alignment",$language);?></td>
         <td>
            <select name="image_alignment" id="addimage_alignment">
               <option value=""></option>
               <option value="left"><?=getLabel("left",$language);?></option>
               <option value="right"><?=getLabel("right",$language);?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td><?=getLabel("Border",$language);?></td>
         <td><input type="text" id="addimage_border" name="image_border" value="0" size="10"/></td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addimage_btn" value="<?=getLabel("Submit",$language);?>"/>
         </td>
      </tr>
   </table>
</div>


<div id="html_div" style="display:none" title="<?=getLabel("Html Content",$language);?>">
   <textarea id="html_content" rows="8" cols="50"></textarea><br/>
   <input type="button" id="html_btn" value="submit"/>
</div>