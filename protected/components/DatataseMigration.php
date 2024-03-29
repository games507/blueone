<?php
class DatataseMigration
{
	
	public static function addColumn($table_name='',$fields = array())
	{
		$stats = array();
		$table_cols = Yii::app()->db->schema->getTable($table_name);
		if(is_array($fields) && count($fields)>=1){
			foreach ($fields as $key=>$val) {
				if(!isset($table_cols->columns[$key])) {							
				   Yii::app()->db->createCommand()->addColumn($table_name,$key,$val);				   
				    $stats[]= "field $key [OK]";
				} else {
					$stats[]= "field $key already exist";
				}							
			}
		}			
		return $stats;																			
	}
	
		
	public static function checkFields($table_name='',$fields = array())
	{		
		$found = false;
		$table_cols = Yii::app()->db->schema->getTable($table_name);
		if (isset($table_cols->columns)){
			foreach ($table_cols->columns as $val) {				
				if(in_array($val->name,$fields)){
					$found = true;
				}
			}
			return $found;
		}		
		return false;		
	}
		
	public static function createTable($table_name='',$fields = array())
	{
		$stats = array();
		if(Yii::app()->db->schema->getTable($table_name)){
			$stats[]= "table $table_name already exist";
		} else {
			Yii::app()->db->createCommand()->createTable(
			 $table_name,
			  $fields,
			'ENGINE=InnoDB DEFAULT CHARSET=utf8');
			$stats[]= "table $table_name created";
		}
		return $stats;
	}
	
	public static function createIndex($table_name='', $fields = array())
	{	
		$stats = array();
		foreach ($fields as $val) {		   
		   try {
		      Yii::app()->db->createCommand()->createIndex($val,$table_name,$val);
		      $stats[]  = "index [$val] created";
		   } catch (Exception $e) {
			  $stats[]  = "index [$val] already";
		   }					
		}	
		return $stats;
	}
	
}
/*end class*/