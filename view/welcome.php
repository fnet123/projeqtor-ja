<?php
/* ============================================================================
 * Welcom screen (replacing Today if no access right)
 */
  require_once "../tool/projeqtor.php";
?>  
<table style="width:100%;height:100%;">
    <tr style="height:100%; vertical_align: middle;">
      <td style="width:100%;text-align: center;">
        <div style="position:relative;width:100%;height:100%;left:0px;;">        
          <div style="position:absolute;width:100%;height:100%; top:25%;">
            <img style="height:50%;top:25%;left:25%;opacity:0.10;filter:alpha(opacity=10);" src="img/logoBig.png" />
          </div>
          <div id="welcomeTitle" style="position:absolute;width:100%;height:100%;top:5%;left:-30%" >
            <?php $logo="img/title.png"; 
                  if (file_exists("../logo.gif")) $logo="../logo.gif"; 
                  if (file_exists("../logo.png")) $logo="../logo.png"; ?> 
            <img src="<?php echo $logo;?>" style="width: 300px; height:54px"/>
          </div>
        </div>
      </td>
    </tr>
</table>