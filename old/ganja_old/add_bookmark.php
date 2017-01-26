<?php
    require_once 'ganja_core.php';
    require_once 'ganja_order.php';

    echo ('
<form name="newBookmark" id="idNewBookmark" method="post" enctype="multipart/form-data" target="_self" title="Новая закладка" class="newBookmark">
<table>
   <tr>
      <td>
         Items quantity:
      </td>
      <td>
        <input type="text" name="quantity" id="idQuantity" value="2" size="5" maxlength="3"   title="Quantity:" >
      </td>
   </tr>
   <tr>
      <td>
        Main link:
      </td>
      <td>
        <input type="text" name="Link" id="idLink" size="45" maxlength="512" value="' . @$_POST['Link'] . '">
      </td>
   </tr>
   <tr>
      <td>
         Description:
      </td>
      <td>
        <textarea name="Description" id="idDescription" rows="3" cols="50" wrap="virtual"   title="Description">Password:</textarea>

      </td>
   </tr>
   <tr>
      <td>
         RegionTitle (ukr):
      </td>
      <td>
         &nbsp;
      </td>
   </tr>
   <tr>
      <td>
         RegionTitle (rus):
      </td>
      <td>
         &nbsp;
      </td>
   </tr>
   <tr>
      <td>
        Location link:
      </td>
      <td>
        <input type="text" name="LocationLink" id="idLocationLink" size="45" maxlength="512"> 
      </td>
   </tr>
   <tr>
      <td colspan="2"> 
        <input type="submit" name="btnAddBookmark" id="idBtnAddBookmark" value="Create bookmark" align="center" size="10"   class="btn">
      </td>
   </tr>
</table>

</form>');    
?>
