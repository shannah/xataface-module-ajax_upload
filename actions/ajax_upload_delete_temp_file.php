<?php
class actions_ajax_upload_delete_temp_file {
	
	const CODE_NO_SUCH_FILE = 404;
	

	function handle($params){
	
		$app = Dataface_Application::getInstance();
		$query = $app->getQuery();
		
		if ( !@$_POST['--field'] ) throw new Exception("No field specified");
		if ( !@$_POST['--fileId'] ) throw new Exception("No file id specified");
	
		$fieldName = $_POST['--field'];
		$tableName = $_POST['-table'];
		$fileId = $_POST['--fileId'];
		
		
		$table = Dataface_Table::loadTable($tableName);
		$field =& $table->getField($fieldName);
		try {
		
			$savepath = $field['savepath'];
			$uploadsPath = $savepath.DIRECTORY_SEPARATOR.'uploads';
			if ( !is_dir($uploadsPath) ){
				throw new Exception("Uploads directory for field $field of table $table does not exist.");
			}
			
			$filePath = $uploadsPath.DIRECTORY_SEPARATOR.basename($fileId);
			if ( !file_exists($filePath) ){
				throw new Exception("The file does not exist.", self::CODE_NO_SUCH_FILE);
			}
			
			if ( !@unlink($filePath) ){
				throw new Exception("Failed to delete file.  There is likely a permissions issue preventing the file from being deleted.");
				
			}
			
			$this->out(array(
				'code'=>200,
				'message' => 'Successfully deleted file.'
			));
		} catch (Exception $ex){
		
			if ( $ex->getCode() ){
				$this->out(array(
					'code'=>$ex->getCode(),
					'message' => $ex->getMessage()
				));
			} else {
				error_log('[ajax_upload] '.$ex->getMessage().' on line '.__LINE__.' or file '.__FILE__);
				
				$this->out(array(
					'code'=>500,
					'message' => 'Failed to delete file due to a server error.'
				));
			}
		}
	}
	
	function out($params){
		header('Content-type: text/json; charset="'.Dataface_Application::getInstance()->_conf['oe'].'"');
		echo json_encode($params);
	}
}