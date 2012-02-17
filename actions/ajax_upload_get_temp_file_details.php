<?php
class actions_ajax_upload_get_temp_file_details {
	function handle($params){
	
	
		$app = Dataface_Application::getInstance();
		$query = $app->getQuery();
		
		if ( !@$query['--fileid'] ){
			throw new Exception("No file id specified");
		}
		
		if ( !@$query['--field'] ){
			throw new Exception("No field specified");
		}
		
		if ( !@$query['-table'] ){
			throw new Exception("No table specified");
		}
		
		$table = Dataface_Table::loadTable($query['-table']);
		$field =& $table->getField($query['--field']);
		
		$path = $field['savepath'];
		$uploadsDir = $path.DIRECTORY_SEPARATOR.'uploads';
		$filePath = $uploadsDir.DIRECTORY_SEPARATOR.basename($query['--fileid']);
		if ( !file_exists($filePath) ){
			throw new Exception("File does not exist: ".$filePath);
		}
		$infoPath = $filePath.'.info';
		if ( !file_exists($infoPath) ){
			throw new Exception("Info file does not exist");
		}
		
		$serializedData = trim(file_get_contents($infoPath));
		if ( !$serializedData ){
			throw new Exception("No info found in info file.");
		}
		$data = unserialize($serializedData);
		
		$out = array(
			'name' => $data['name'],
			'type' => $data['type'],
			'size' => $data['size']
		);
		
		header('Content-type: text/json; charset="'.$app->_conf['oe'].'"');
		echo json_encode($out);
		
		
	}
}