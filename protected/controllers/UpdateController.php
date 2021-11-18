<?php
class UpdateController extends CController
{
	public function beforeAction($action)
	{
		require 'functions.php';
		
		Yii::app()->session;
		
		if(!AdminFunctions::islogin()){						
			Yii::app()->end();
		}
		return true;
	}
	
	public function actionIndex()
	{		
		$prefix=Yii::app()->db->tablePrefix;		
		$table_prefix=$prefix;
		$logger = array();		
				
		$date_default = "datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";		
		if($res = Yii::app()->db->createCommand("SELECT VERSION() as mysql_version")->queryRow()){				
			$mysql_version = (float)$res['mysql_version'];	
			if($mysql_version<=5.5){				
				$date_default="datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			}
		}
			
		Yii::app()->db->createCommand("ALTER TABLE ".$table_prefix."driver_task AUTO_INCREMENT = 100000;")->query();
		
		/*1.7*/
		$logger[] = DatataseMigration::addColumn("{{driver_assignment}}",array(
		  'request_interval'=>"int(14) NOT NULL DEFAULT '0'",
		  'customer_id'=>"int(14) NOT NULL DEFAULT '0'",
		  'timezone'=>"varchar(100) NOT NULL DEFAULT ''",
		));
		
		$logger[] = DatataseMigration::addColumn("{{push_broadcast}}",array(		  
		  'json_response'=>"text",
		));
				
		$logger[] = DatataseMigration::createTable("{{paystack_logs}}",array(		  
		  'id'=>'pk',
		  'transaction_type'=>"varchar(100) NOT NULL DEFAULT 'signup'",
		  'customer_id'=>"int(14) NOT NULL DEFAULT '0'",
		  'reference_number'=>"varchar(255) NOT NULL DEFAULT ''",
		  'params1'=>"varchar(255) NOT NULL DEFAULT ''",
		  'date_created'=>$date_default,
		  'ip_address'=>"varchar(50) NOT NULL DEFAULT ''"
		));
		
		$logger[] = DatataseMigration::createIndex("{{paystack_logs}}",array(
		  'customer_id'=>'customer_id',
		  'reference_number'=>'reference_number'
		));
		
		$logger[] = DatataseMigration::createTable("{{paystack_webhook}}",array(		  
		  'id'=>'pk',
		  'code'=>"int(1) NOT NULL DEFAULT '0'",
		  'message'=>"varchar(255) NOT NULL DEFAULT ''",
		  'receive_data'=>"text",
		  'date_created'=>$date_default,
		  'ip_address'=>"varchar(50) NOT NULL DEFAULT ''"
		));
		
		/*VIEW TABLES*/
		$stmt="
		Create OR replace view ".$table_prefix."driver_task_view as
		SELECT a.*,
		concat(b.first_name,' ',b.last_name) as driver_name,
		b.device_id,
		b.phone as driver_phone,
		b.email as driver_email,
		b.device_platform,
		b.enabled_push,
		b.location_lat as driver_location_lat,
		b.location_lng as driver_location_lng,
		b.last_login as driver_last_login ,
		b.transport_type_id as transport_type ,
		e.team_name
			
		FROM
		".$table_prefix."driver_task a
				
		LEFT JOIN ".$table_prefix."driver b
		ON
		b.driver_id=a.driver_id
		
		left join ".$table_prefix."driver_team e
		ON 
		e.team_id=a.team_id
		";
		if(Yii::app()->db->createCommand($stmt)->query()){
			$logger[] = array(
			  'view'=>"driver_task_view"
			);
		}
		
		
		$stmt="		
		Create OR replace view ".$table_prefix."view_task_history as
		select 
		a.id as history_id,
		a.task_id,
		DATE_FORMAT(a.date_created,'%Y%m%d%H%i%S') as date_created ,
		b.customer_id		
		
		FROM 
		".$table_prefix."task_history a
		left join ".$table_prefix."driver_task  b
		On
		a.task_id = b.task_id
		";
		if(Yii::app()->db->createCommand($stmt)->query()){
			$logger[] = array(
			  'view'=>"view_task_history"
			);
		}
		/*END VIEW TABLE*/
				
		dump($logger);
		
		?>
		<br/>
		<a href="<?php echo Yii::app()->createUrl("admin/")?>">
		 <?php echo AdminFunctions::t("Update done click here to go back")?>
		</a>
		<?php
		
	} /*end index*/	
	
} /*end class*/