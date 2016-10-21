<?php
class ModelModuleDSocialLike extends Model {
		
	public function getThumb($image, $width, $height) {
		$this->load->model('tool/image');

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}
		
		if ($image) {
			$image_info = @getimagesize(DIR_IMAGE . $image);
			if ($image_info) {
				$thumb = $this->model_tool_image->resize($image, $width, $height);
			} else {
				$thumb = $server . 'image/' . $image;
			}
		} else {
			$thumb = $this->model_tool_image->resize("no_image.png", $width, $height);
		}
		
		return $thumb;
	}
	
	/*
	*	Return list of languages.
	*/
	public function getLanguages(){
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		$result = array();
		if (count($languages)>1) {
		$result[] = array(
			'language_id' => -1, 
			'name' => $this->language->get('text_all_languages')
		);
		}
		if($languages){
			foreach ($languages as $language) {
				$result[] = array(
					'language_id' => $language['language_id'],
					'name' => $language['name']	
				);
			}	
		}
		return $result;
	}
	
	/*
	*	Return list of stores.
	*/
	public function getStores(){
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();
		$result = array();
		if ($stores) {
		$result[] = array(
			'store_id' => -1, 
			'name' => $this->language->get('text_all_stores')
		);
		}
		$result[] = array(
			'store_id' => 0, 
			'name' => $this->config->get('config_name')
		);
		if($stores){
			foreach ($stores as $store) {
				$result[] = array(
					'store_id' => $store['store_id'],
					'name' => $store['name']	
				);
			}	
		}
		return $result;
	}
	
	/*
	*	Return name of config file.
	*/
	public function getConfigFile($id, $sub_versions = array()){
		
		if(isset($this->request->post['config'])){
			return $this->request->post['config'];
		}

		$setting = $this->config->get($id.'_setting');

		if(isset($setting['config'])){
			return $setting['config'];
		}

		$full = DIR_SYSTEM . 'config/'. $id . '.php';
		if (file_exists($full)) {
			return $id;
		} 

		foreach ($sub_versions as $lite){
			if (file_exists(DIR_SYSTEM . 'config/'. $id . '_' . $lite . '.php')) {
				return $id . '_' . $lite;
			}
		}
		
		return false;
	}
	
	/*
	*	Return list of config files that contain the id of the module.
	*/
	public function getConfigFiles($id){
		$files = array();
		$results = glob(DIR_SYSTEM . 'config/'. $id .'*');
		foreach($results as $result){
			$files[] = str_replace('.php', '', str_replace(DIR_SYSTEM . 'config/', '', $result));
		}
		return $files;
	}
		
	/**

	 MBooth functions

	**/

	/*
	*	Return mbooth file.
	*/
	public function getMboothFile($id, $sub_versions = array()){
		$full = DIR_SYSTEM . 'mbooth/xml/mbooth_'. $id .'.xml';
		if (file_exists($full)) {
			return 'mbooth_'. $id . '.xml';
		} else{
			foreach ($sub_versions as $lite){
				if (file_exists(DIR_SYSTEM . 'mbooth/xml/mbooth_'. $id . '_' . $lite . '.php')) {
					$this->prefix = '_' . $lite;
					return $id . '_' . $lite . '.xml';
				}
			}
		}
		return false;
	}

	/*
	*	Return mbooth file.
	*/
	public function getMboothInfo($mbooth_file){
		if(file_exists(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_file)){
			$xml = new SimpleXMLElement(file_get_contents(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_file));
			return $xml;
		}else{
			return false;
		}
	}
	
	/*
	*	Get the version of this module
	*/
	public function getVersion($mbooth_file){
		if(file_exists(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_file)){
			$xml = new SimpleXMLElement(file_get_contents(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_file));
			return $xml->version;
		}else{
			return false;
		}
	}

	/*
	*	Check if another extension/module is installed.
	*/
	public function isInstalled($code) {
		$extension_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = '" . $this->db->escape($code) . "'");
		if($query->row) {
			return true;
		}else{
			return false;
		}	
	}

	/*
	*	Get extension info by mbooth from server (Check for update)
	*/
	public function getUpdateInfo($mbooth_file, $status = 1){
		$result = array();

		$current_version = $this->getVersion($mbooth_file);
		$customer_url = HTTP_SERVER;
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE language_id = " . (int)$this->config->get('config_language_id') ); 
		$language_code = $query->row['code'];
		$ip = $this->request->server['REMOTE_ADDR'];

		$request = 'http://opencart.dreamvention.com/api/1/index.php?route=extension/check&mbooth=' . $mbooth_file . '&store_url=' . $customer_url . '&module_version=' . $current_version . '&language_code=' . $language_code . '&opencart_version=' . VERSION . '&ip='.$ip . '&status=' .$status;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result['data'] = curl_exec($curl);
		$result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $result;
	}
	
	/**
	 
	*	Get the version of this module
	*	Use this function to install dependencies, which are set in your mbooth xml file 
	*	under the tag required;
	
	*/
		
	public function installDependencies($mbooth_file){
        if(!defined('DIR_ROOT')) { define('DIR_ROOT', substr_replace(DIR_SYSTEM, '/', -8)); }

        foreach($this->getDependencies($mbooth_file) as $extension){
            if(isset($extension['codename'])){
                if(!$this->getVersion('mbooth_'.$extension['codename'].'.xml') || ($extension['version'] > $this->getVersion('mbooth_'.$extension['codename'].'.xml'))){
                    $this->downloadExtension($extension['codename'], $extension['version']);
                    $this->extractExtension();
                    if(file_exists(DIR_SYSTEM . 'mbooth/xml/'.$mbooth_file)){
                        $result = $this->backupFilesByMbooth($mbooth_file, 'update');
                    }
                    $this->move_dir(DIR_DOWNLOAD . 'upload/', DIR_ROOT, $result);
                }
            }
        }
    }

    public function getDependencies($mbooth_xml){
        if(file_exists(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_xml)){
            $xml = new SimpleXMLElement(file_get_contents(DIR_SYSTEM . 'mbooth/xml/'. $mbooth_xml));
            $result = array();
            $version = false;
            
            foreach($xml->required->require as $require){

                foreach($require->attributes() as $key => $value){
                    $version = false;
                    if($key == 'version'){
                        $version = $value;
                    }
                }
                $result[] = array(
                    'codename' => (string)$require,
                    'version' => (string)$version
                    );
            }
            return $result;
        }else{
            return false;
        }
    }

    public function downloadExtension($codename, $version, $filename  = false ) {

        if(!$filename){
            $filename = DIR_DOWNLOAD . 'archive.zip';
        }

        $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';  
        $ch = curl_init();  
        $fp = fopen($filename, "w");  
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  
        curl_setopt($ch, CURLOPT_URL, 'http://opencart.dreamvention.com/api/1/extension/download/?codename=' . $codename.'&opencart_version='.VERSION.'&extension_version='. $version);  
        curl_setopt($ch, CURLOPT_FAILONERROR, true);  
        curl_setopt($ch, CURLOPT_HEADER,0);  
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);  
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);   
        curl_setopt($ch, CURLOPT_FILE, $fp);  
        $page = curl_exec($ch);  
        if (!$page) {  
            exit;  
        }
        curl_close($ch);

    }

    

    public function extractExtension($filename = false, $location = false ) {
        if(!$filename){
            $filename = DIR_DOWNLOAD . 'archive.zip';
        }
        if(!$location){
            $location = DIR_DOWNLOAD;
        }

        $result = array();
        $zip = new ZipArchive;
        if (!$zip) {  
            $result['error'][] = 'ZipArchive not working.'; 
        }

        if($zip->open($filename) != "true") {  
            $result['error'][] = $filename;
        }
        $zip->extractTo($location);  
        $zip->close();

        unlink($filename);

        return $result;

    }
	
	public function getMboothFiles($mbooth_url) {

        $xml = new SimpleXMLElement(file_get_contents($mbooth_url));

        if(isset($xml->id)){
            $result['file_name'] =   basename($mbooth_url, '');
            $result['id'] = isset($xml->id) ? (string)$xml->id : '';
            $result['name'] = isset($xml->name) ? (string)$xml->name : '';
            $result['description'] = isset($xml->description) ? (string)$xml->description : '';
            $result['type'] = isset($xml->type) ? (string)$xml->type : '';
            $result['version'] = isset($xml->version) ? (string)$xml->version : '';
            $result['license'] = isset($xml->license) ? (string)$xml->license : '';
            $result['opencart_version'] = isset($xml->opencart_version) ? (string)$xml->opencart_version : '';
            $result['mbooth_version'] = isset($xml->mbooth_version) ? (string)$xml->mbooth_version : '';
            $result['author'] = isset($xml->author) ? (string)$xml->author : '';
            $result['support_email'] = isset($xml->support_email) ? (string)$xml->support_email : '';
            $files = $xml->files;
            $dirs = $xml->dirs;
            $required = $xml->required;
            $updates = $xml->update;

            foreach ($files->file as $file){
				$result['files'][] = (string)$file; 
			} 

			if (!empty($dirs)) {

				$dir_files = array();
            
				foreach ($dirs->dir as $dir) {
					$this->scan_dir(DIR_ROOT . $dir, $dir_files);
				}

				foreach ($dir_files as $file) {
					$file = str_replace(DIR_ROOT, "", $file);
					$result['files'][] = (string)$file;
				}
			}

			return $result;  
		} else {
			return false;
		}

	}

	public function backupFilesByMbooth($mbooth_xml, $action = 'install'){

		$zip = new ZipArchive();

		if (!file_exists(DIR_SYSTEM . 'mbooth/backup/')) {
			mkdir(DIR_SYSTEM . 'mbooth/backup/', 0777, true);
		}

		$mbooth = $this->getMboothFiles(DIR_SYSTEM . 'mbooth/xml/' . $mbooth_xml);
		$files = $mbooth['files'];

		$zip->open(DIR_SYSTEM . 'mbooth/backup/' . date('Y-m-d.h-i-s'). '.'. $action .'.'.$mbooth_xml.'.v'.$mbooth['version'].'.zip', ZipArchive::CREATE);


		foreach ($files as $file) {

			if(file_exists(DIR_ROOT.$file)){
	
				if (is_file(DIR_ROOT.$file)) {
					$zip->addFile(DIR_ROOT.$file, 'upload/'.$file);
					$result['success'][] = $file;
				} else {
					$result['error'][] = $file;
				}
			
			} else {
				$result['error'][] = $file;
			}
		}
		
		$zip->close();
		return $result; 

	}

    /*
    *   Dir function
    */
    public function scan_dir($dir, &$arr_files){

        if (is_dir($dir)){
            $handle = opendir($dir);
            while ($file = readdir($handle)){
                if ($file == '.' or $file == '..') continue;
                if (is_file($file)) $arr_files[]="$dir/$file";
                else $this->scan_dir("$dir/$file", $arr_files);
            }
            closedir($handle);
        } else {
            $arr_files[]=$dir;
        }
    }

    public function move_dir($souce, $dest, &$result) {

        $files = scandir($souce);

        foreach($files as $file){

            if($file == '.' || $file == '..' || $file == '.DS_Store') continue;
            
            if(is_dir($souce.$file)){
                if (!file_exists($dest.$file.'/')) {
                    mkdir($dest.$file.'/', 0777, true);
                }
                $this->move_dir($souce.$file.'/', $dest.$file.'/', $result);
            }elseif (rename($souce.$file, $dest.$file)) {
                $result['success'][] = str_replace(DIR_ROOT, '', $dest.$file);
            }else{
                $result['error'][] = str_replace(DIR_ROOT, '', $dest.$file);
            }
        }

        $this->delete_dir($souce);
    }

    public function delete_dir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->delete_dir($dir."/".$object); 
                    else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
?>