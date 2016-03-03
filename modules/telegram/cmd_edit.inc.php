<?php

if ($this->mode=='setvalue') {
   global $prop_id;
   global $new_value;
   global $id;
   $this->setProperty($prop_id, $new_value, 1);   
   $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
} 

if ($this->mode=='cmd') {
    global $data;
    $this->cmd($data);
}

if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}

$table_name='tlg_cmd';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
    
if ($this->mode=='update') { 
  $ok=1;
  if ($this->tab=='') {

    // NAME
    global $title;
    $rec['TITLE']=$title;
    global $description;
    $rec['DESCRIPTION']=$description;
    global $code;
    $rec['CODE']=$code;
    global $select_access;
    $rec['ACCESS']=$select_access;
    global $users_id;
    
    //UPDATING RECORD
    if ($ok) {
      if ($rec['ID']) {
        SQLUpdate($table_name, $rec); // update
        updateAccess($rec['ID'],$users_id);
      } else {
        $new_rec=1; 
        $rec['ID']=SQLInsert($table_name, $rec); // adding new record
        updateAccess($rec['ID'],$users_id);
        $id=$rec['ID'];
      } 
      $out['OK']=1;
    } else {
      $out['ERR']=1;
    }
  }
    $ok=1;
}

$res = SQLSelect("select ID,NAME,ADMIN, (SELECT count(*) FROM tlg_user_cmd where CMD_ID='$id' and tlg_user_cmd.USER_ID=tlg_user.ID) as ACCESS_USER from tlg_user");
if ($res[0]) {
    $out['LIST_ACCESS'] = $res;
}
outHash($rec, $out);

function updateAccess($cmd_id, $users_id) {
    SQLSelect("DELETE from tlg_user_cmd where CMD_ID=".$cmd_id);
    $users = explode(",", $users_id);
    foreach ( $users as $value ) {
        $recCU=array();
        $recCU['CMD_ID']=$cmd_id;
        $recCU['USER_ID']=$value;
        $recCU['ID']=SQLInsert('tlg_user_cmd', $recCU);
    }
}
  
?>
