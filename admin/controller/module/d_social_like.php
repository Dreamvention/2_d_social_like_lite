<?php
class ControllerModuleDSocialLike extends Controller {
	private $id = 'd_social_like';
	private $codename = 'd_social_like_lite';
	private $route = 'module/d_social_like';
	private $mbooth = '';
	private $config_file = '';
	private $prefix = '';
	private $sub_versions = array();
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model($this->route);
		$this->d_shopunity = (file_exists(DIR_SYSTEM.'mbooth/extension/d_shopunity.json'));
		$this->mbooth = $this->{'model_module_' . $this->id}->getMboothFile($this->id, $this->sub_versions);
		$this->config_file = $this->{'model_module_' . $this->id}->getConfigFile($this->id, $this->sub_versions);
	}

	public function required(){
		$this->load->language($this->route);

		$this->document->setTitle($this->language->get('heading_title_main'));
		$data['heading_title'] = $this->language->get('heading_title_main');
		$data['text_not_found'] = $this->language->get('text_not_found');
		$data['breadcrumbs'] = array();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

 		$this->response->setOutput($this->load->view('error/not_found.tpl', $data));
	}

	public function index(){
		if(!$this->d_shopunity){
			$this->response->redirect($this->url->link($this->route.'/required', 'codename=d_shopunity&token='.$this->session->data['token'], 'SSL'));
		}
		$this->load->language($this->route);

		$this->load->model($this->route);
		$this->load->model('setting/setting');
		$this->load->model('extension/module');

		$this->load->model('d_shopunity/setting');
		$this->load->model('d_shopunity/mbooth');

		$this->model_d_shopunity_mbooth->validateDependencies($this->codename);

		if (isset($this->request->get['module_id'])) {
			$module_id = $this->request->get['module_id'];
		}else{
			$module_id = 0;
		}

		// Shopunity (requred)
		$this->document->addLink('//fonts.googleapis.com/css?family=PT+Sans:400,700,700italic,400italic&subset=latin,cyrillic-ext,latin-ext,cyrillic', "stylesheet");
		$this->document->addStyle('view/stylesheet/shopunity/bootstrap.css');
		$this->document->addScript('view/javascript/shopunity/tinysort/jquery.tinysort.min.js');
		$this->document->addScript('view/javascript/shopunity/rubaxa-sortable/sortable.js');
		$this->document->addStyle('view/stylesheet/shopunity/rubaxa-sortable/sortable.css');
		$this->document->addStyle('view/stylesheet/shopunity/bootstrap-switch/bootstrap-switch.css');
		$this->document->addScript('view/javascript/shopunity/bootstrap-switch/bootstrap-switch.min.js');
		$this->document->addStyle('view/javascript/shopunity/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/shopunity/colorpicker/js/bootstrap-colorpicker.js');
		$this->document->addStyle('view/stylesheet/d_social_like.css');

		$url = '';

		if(isset($this->request->get['module_id'])){
			$url .=  '&module_id='.$module_id;
		}
		if(isset($this->request->get['config'])){
			$url .=  '&config='.$this->request->get['config'];
		}

		// Heading
		$this->document->setTitle($this->language->get('heading_title_main'));
		$data['heading_title'] = $this->language->get('heading_title_main');
		$data['text_edit'] = $this->language->get('text_edit');

		// Variable
		$data['id'] = $this->id;
		$data['route'] = $this->route;
		$data['module_id'] = $module_id;
		$data['mbooth'] = $this->mbooth;
		$data['config'] = $this->config_file;
		$data['token'] =  $this->session->data['token'];
		$data['stores'] = $this->{'model_module_' . $this->id}->getStores();
		$data['languages'] = $this->{'model_module_' . $this->id}->getLanguages();
		$data['version'] = $this->{'model_module_' . $this->id}->getVersion($data['mbooth']);
		$data['no_image'] = $this->{'model_module_' . $this->id}->getThumb('', 100, 100);

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['server'] = HTTPS_SERVER;
			$data['catalog'] = HTTPS_CATALOG;
		} else {
			$data['server'] = HTTP_SERVER;
			$data['catalog'] = HTTP_CATALOG;
		}

		// Action
		if(VERSION < '2.3.0.0') {
			$data['module_link'] = $this->url->link($this->route, 'token=' . $this->session->data['token'], 'SSL');
			$data['action'] = $this->url->link($this->route . '/save', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
			$data['get_cancel'] = str_replace('&amp;', '&', $this->url->link('extension/module', 'token=' . $this->session->data['token'] . '&type=module', 'SSL'));
		} else {
			$data['module_link'] = $this->url->link('extension' . $this->route, 'token=' . $this->session->data['token'], 'SSL');
			$data['action'] = $this->url->link($this->route . '/save', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', 'SSL');
			$data['get_cancel'] = str_replace('&amp;', '&', $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', 'SSL'));
		}

		// Tab
		$data['text_settings'] = $this->language->get('text_settings');
		$data['text_design'] = $this->language->get('text_design');
		$data['text_instructions'] = $this->language->get('text_instructions');
		$data['text_instructions_full'] = $this->language->get('text_instructions_full');

		// Button
		$data['button_save'] = $this->language->get('button_save');
		$data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_get_update'] = $this->language->get('button_get_update');

		// Entry
		$data['entry_get_update'] = sprintf($this->language->get('entry_get_update'), $data['version']);
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_language'] = $this->language->get('entry_language');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_config_files'] = $this->language->get('entry_config_files');
		$data['entry_view'] = $this->language->get('entry_view');
		$data['entry_url'] = $this->language->get('entry_url');

		$data['entry_icon_theme'] = $this->language->get('entry_icon_theme');
		$data['entry_icon_color'] = $this->language->get('entry_icon_color');
		$data['entry_icon_color_active'] = $this->language->get('entry_icon_color_active');
		$data['entry_background_color'] = $this->language->get('entry_background_color');
		$data['entry_background_color_active'] = $this->language->get('entry_background_color_active');
		$data['entry_api'] = $this->language->get('entry_api');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_border'] = $this->language->get('entry_border');
		$data['entry_border_color'] = $this->language->get('entry_border_color');
		$data['entry_box_shadow_color'] = $this->language->get('entry_box_shadow_color');
		$data['entry_box_shadow'] = $this->language->get('entry_box_shadow');
		$data['entry_border_radius'] = $this->language->get('entry_border_radius');
		$data['entry_popup_mobile'] = $this->language->get('entry_popup_mobile');
		$data['entry_custom_style'] = $this->language->get('entry_custom_style');

		// Help
		$data['help_width'] = $this->language->get('help_width');
		$data['help_config_files'] = $this->language->get('help_config_files');
		$data['help_icon_theme'] = $this->language->get('help_icon_theme');
		$data['help_api'] = $this->language->get('help_api');
		$data['help_custom_style'] = $this->language->get('help_custom_style');
		$data['help_url'] = $this->language->get('help_url');

		// Text
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['text_sort_order'] = $this->language->get('text_sort_order');
		$data['text_facebook'] = $this->language->get('text_facebook');
		$data['text_google'] = $this->language->get('text_google');
		$data['text_twitter'] = $this->language->get('text_twitter');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_linkedin'] = $this->language->get('text_linkedin');
		$data['text_vkontakte'] = $this->language->get('text_vkontakte');
		$data['text_odnoklassniki'] = $this->language->get('text_odnoklassniki');
		$data['text_mailru'] = $this->language->get('text_mailru');
		$data['text_stumbleupon'] = $this->language->get('text_stumbleupon');
		$data['text_foursquare'] = $this->language->get('text_foursquare');
		$data['text_amazon'] = $this->language->get('text_amazon');
		$data['text_pinterest'] = $this->language->get('text_pinterest');
		$data['text_addthis'] = $this->language->get('text_addthis');

 		// Notification
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		// Breadcrumbs
		$data['breadcrumbs'] = array();
   		$data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		if(VERSION < '2.3.0.0') {
	        $data['breadcrumbs'][] = array(
	       		'text'      => $this->language->get('text_modules'),
				'href'      => $this->url->link('extension/modules', 'token=' . $this->session->data['token'], 'SSL')
	   		);
	    } else {
	    	$data['breadcrumbs'][] = array(
	       		'text'      => $this->language->get('text_modules'),
				'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', 'SSL')
	   		);
	    }
   		$data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title_main'),
			'href' => $this->url->link($this->route, 'token=' . $this->session->data['token'] . $url, 'SSL')
   		);

		//setting
		$setting = $this->model_extension_module->getModule($module_id);

		$this->config->load($data['config']);
		$data['setting'] = ($this->config->get($this->id)) ? $this->config->get($this->id) : array();

		if (!empty($setting)) {
			$data['setting'] = array_replace_recursive($data['setting'], $setting);
		}

		//get config
		$data['config_files'] = $this->{'model_module_' . $this->id}->getConfigFiles($this->id);

		//Get views
		$data['views'] = array(
			0 => array('view_id' => 'left', 'name' => $this->language->get('text_view_left')),
			1 => array('view_id' => 'top', 'name' => $this->language->get('text_view_top')),
			2 => array('view_id' => 'right', 'name' => $this->language->get('text_view_right')),
			3 => array('view_id' => 'bottom', 'name' => $this->language->get('text_view_bottom')),
			4 => array('view_id' => 'inline', 'name' => $this->language->get('text_view_inline'))
		);

		//Get icon designes
		$dir    = DIR_CATALOG.'/view/theme/default/stylesheet/d_social_like/icons';
		$files = glob($dir . '/*', GLOB_ONLYDIR);
		$data['icon_themes'] = array();
		foreach($files as $file){
			if(strlen($file) > 6){
				$data['icon_themes'][] = basename($file);
			}
		}

		foreach(glob(DIR_CONFIG.'/d_social_like*.*') as $file) {
		    $data['config_settings'][] = substr(basename($file), 0, -4);
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->route.'.tpl', $data));
	}

	public function save() {
		$this->load->language($this->route);

		$this->load->model($this->route);
		$this->load->model('setting/setting');
		$this->load->model('extension/module');

		if (isset($this->request->get['module_id'])) {
			$module_id = $this->request->get['module_id'];
		} else {
			$module_id = 0;
		}

		$json['success'] = '';

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (!$module_id) {
				$this->model_extension_module->addModule($this->id, $this->request->post[$this->id]);
			} else {
				$this->model_extension_module->editModule($module_id, $this->request->post[$this->id]);
			}

			$json['success'] = $this->language->get('text_success');

		}

		if (isset($this->request->get['exit'])) {
			if ($json['success']) {
				$this->session->data['success'] = $json['success'];
			} else {
				unset($this->session->data['success']);
			}
		}

		$json['error'] = $this->error;

		$this->response->setOutput(json_encode($json));
	}

	private function validate($permission = 'modify') {
		if (isset($this->request->post['config'])) {
			return false;
		}

		$this->language->load($this->route);

		if (!$this->user->hasPermission($permission, $this->route)) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		$setting = $this->request->post[$this->id];

		if ((utf8_strlen($setting['name']) < 3) || (utf8_strlen($setting['name']) > 64)) {
			$this->error['warning'] = $this->language->get('error_warning');
			$this->error['name'] = $this->language->get('error_name');
			return false;
		}

		return true;
	}

	public function install() {
		$this->load->model($this->route);
		$this->load->model('d_shopunity/mbooth');
		$this->model_d_shopunity_mbooth->installDependencies($this->codename); 
	}
}
?>
