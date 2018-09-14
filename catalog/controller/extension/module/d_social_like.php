<?php
class ControllerExtensionModuleDSocialLike extends Controller {

    private $codename = 'd_social_like';
    private $route = 'extension/module/d_social_like';
    private $config_file = 'd_social_like';

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->model($this->route);
        $this->load->language($this->route);
        $this->load->model('extension/d_opencart_patch/load');
    }

    public function index($setting)
    {
        $setting['language_id'] = -1;
        $setting['store_id'] = -1;
        if ((($setting['language_id'] == (int)$this->config->get('config_language_id')) || ($setting['language_id'] == -1))
        && (($setting['store_id'] == (int)$this->config->get('config_store_id')) || ($setting['store_id'] == -1))) {
         
            $setting = $this->getSetting($setting);

            $this->document->addScript('//s7.addthis.com/js/300/addthis_widget.js#pubid='.$setting['addthis_id']);

            $data['heading_like_us'] = $this->language->get('heading_like_us');
            $data['button_aready_liked'] = $this->language->get('button_aready_liked');
            $data['button_like_us'] = $this->language->get('button_like_us');

            

            $data['view'] = $setting['view_id'];
            $data['url'] = $setting['url'];

            $sort_order = array();


            foreach ($setting['social_likes'] as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $setting['social_likes']);

            $data['social_likes'] = array();
            $data['count'] = 0;
            $data['design'] = $setting['design'];

            if (isset($setting['social_likes']['stumbleupon'])) {
                if($setting['social_likes']['stumbleupon']['enabled'] && !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) || $setting['social_likes']['stumbleupon']['enabled'] && !empty($_SERVER['HTTPS'])){
                    unset($setting['social_likes']['stumbleupon']);
                }
            }

            foreach ($setting['social_likes'] as $social_like){
                if($social_like['enabled']){
                    $data['count']++;
                    $social_like['text_like'] = $this->language->get('text_like');
                    $data['social_likes'][$social_like['id']] = $social_like;
                    $data['social_likes'][$social_like['id']]['code'] = $this->load->view('extension/module/d_social_like/'.$social_like['id'], $social_like);
                }
            }

            if (isset($this->request->get['store_id'])) {
                $store_id = $this->request->get['store_id'];
            } else {
                $store_id = 0;
            }

            // $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->codename . '/icons/'.$setting['design']['icon_theme'].'/styles.css');

            return $this->model_extension_d_opencart_patch_load->view($this->route, $data);
        }
    }

    public function getSetting($setting){
        $key = $this->codename.'_setting';

        if ($this->config_file) {
            $this->config->load($this->config_file);
        }

        $config = ($this->config->get($key)) ? $this->config->get($key) : array();

        $setting = array_replace_recursive($config, $setting);
        // //get social like settings
        foreach(glob(DIR_CONFIG.'d_social_like/*.php') as $file) {
            $social_like_id = substr(basename($file), 0, -4);
            if(isset($setting['social_likes'][$social_like_id])){
                $this->config->load('d_social_like/'.$social_like_id);
                if($this->config->get('d_social_like_'.$social_like_id)){
                    $setting['social_likes'][$social_like_id] = array_replace_recursive($this->config->get('d_social_like_'.$social_like_id), $setting['social_likes'][$social_like_id]); 
                }
            }
        }
        return $setting;
    }
}
?>
