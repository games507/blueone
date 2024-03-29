
<div id="layout_1">
<?php 
$this->renderPartial('/tpl/layout1_top',array(   
));
?> 
</div> <!--layout_1-->

<div class="parent-wrapper task-list-area">

 <div class="content_1 white">   
   <?php 
   $this->renderPartial('/tpl/menu',array(   
   ));
   ?>
 </div> <!--content_1-->
 
 <div class="content_main">

   <div class="nav_option">
      <div class="row">
        <div class="col-md-6 border">
         <b><?php echo t("Push Logs")?></b>
        </div> <!--col-->
        <div class="col-md-6 border text-right">
                       
           <a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh")?></a>
         
        </div> <!--col-->
      </div> <!--row-->
   </div> <!--nav_option-->
  
   <div class="inner">
   <form id="frm_table" class="frm_table">
   <?php echo CHtml::hiddenField('action','PushLogList')?>
   <?php 
   if (is_numeric($broadcast_id)){
   	  echo CHtml::hiddenField('broadcast_id',$broadcast_id);
   }
   ?>
   <table id="table_list" class="table table-hover">
   <thead>
    <tr>
      <th width="10%"><?php echo t("ID")?></th>      
      <th width="5%"><?php echo t("DriverID")?></th>
      <th><?php echo t("Title")?></th>
      <th><?php echo t("Message")?></th>
      <th><?php echo t("Type")?></th> 
      <th><?php echo t("Device")?></th> 
      <th><?php echo t("Date")?></th> 
      <th><?php echo Driver::t("Status")?></th>      
    </tr>
    </thead>
    <tbody>     
    </tbody>     
   </table>
   </form>
   </div>
 
 </div> <!--content_2-->

</div> <!--parent-wrapper-->