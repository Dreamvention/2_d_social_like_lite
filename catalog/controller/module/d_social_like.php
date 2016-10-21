<?php
class ControllerModuleDSocialLike extends Controller {

	private $id = 'd_social_like';
	private $route = 'module/d_social_like';
	
	public function index($setting) {
		$this->load->language($this->route);
		
		if ((($setting['language_id'] == (int)$this->config->get('config_language_id')) || ($setting['language_id'] == -1)) && (($setting['store_id'] == (int)$this->config->get('config_store_id')) || ($setting['store_id'] == -1))) {

			$this->document->addScript('//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4d8b33027d80e2ce');

			$data['heading_like_us'] = $this->language->get('heading_like_us');
			$data['button_aready_liked'] = $this->language->get('button_aready_liked');
			$data['button_like_us'] = $this->language->get('button_like_us');
		
			$this->config->load($this->id);
			$config_setting = ($this->config->get($this->id)) ? $this->config->get($this->id) : array();

			if (!empty($setting)) {
				$setting = array_replace_recursive($config_setting, $setting);
			}
		
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
		
			foreach ($setting['social_likes'] as $social_like){
				if($social_like['enabled']){
					$data['count']++;
					$id = $social_like['id'];
					$data['social_likes'][$id] = $social_like;
					$data['social_likes'][$id]['code'] = $this->$id($social_like);
				}
			} 
			
			if (VERSION >= '2.2.0.0') {
				$this->document->addStyle('catalog/view/theme/' . $this->config->get($this->config->get('config_theme').'_directory') . '/stylesheet/' . $this->id . '/icons/'.$setting['design']['icon_theme'].'/styles.css');
			} elseif (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/' . $this->id . '/icons/'.$setting['design']['icon_theme'].'/styles.css')) {
				$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/' . $this->id . '/icons/'.$setting['design']['icon_theme'].'/styles.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->id . '/icons/'.$setting['design']['icon_theme'].'/styles.css');
			}
		
			if (VERSION >= '2.2.0.0') {
				return $this->load->view($this->route, $data);
			} elseif (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->route . '.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/' . $this->route . '.tpl', $data);
			} else {
				return $this->load->view('default/template/' . $this->route . '.tpl', $data);
			}
		
		}
	}

	public function facebook($social_like){
		$result ='<a class="addthis_button_facebook_like"></a>';
		return $result;
	}
	public function twitter($social_like){
		$result ='<a class="addthis_button_tweet"></a>';
		return $result;
	}
	public function google($social_like){
		$result ='<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>';
		return $result;
	}
	public function vkontakte($social_like){
		$result ='<script type="text/javascript" src="//vk.com/js/api/openapi.js?96"></script>
		
		<script type="text/javascript">
		  VK.init({apiId: '.$social_like['api']. ', onlyWidgets: true});
		</script>

		<div id="vk_like"></div>

		<script type="text/javascript">
			VK.Widgets.Like("vk_like", {type: "button"});
		</script>';
		return $result;
	}
	public function mailru($social_like){
		$result ='<a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share" data-mrc-config="{\'cm\' : \'1\', \'sz\' : \'20\', \'st\' : \'2\', \'tp\' : \'mm\'}" >'.$this->language->get('text_like').'</a>
		<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>';
		return $result;
	}
	public function odnoklassniki($social_like){
		
		$result ='<a id="ok_shareWidget"></a>
			<script>
			!function (d, id, did, st) {
			  var js = d.createElement("script");
			  js.src = "http://connect.ok.ru/connect.js";
			  js.onload = js.onreadystatechange = function () {
			  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
			    if (!this.executed) {
			      this.executed = true;
			      setTimeout(function () {
			        OK.CONNECT.insertShareWidget(id,did,st);
			      }, 0);
			    }
			  }};
			  d.documentElement.appendChild(js);
			}(document,"ok_shareWidget",$(location).attr("href"),"{width:145,height:30,st:\'rounded\',sz:20,ck:1}");
			</script>
			';
		return $result;
	}
		
	public function pinterest($social_like){
		$result ='<a class="addthis_button_pinterest_pinit"></a>';
		return $result;
	}
	public function linkedin($social_like){
		$result ='<a class="addthis_button_linkedin_counter"></a>';
		return $result;
	}
	public function stumbleupon($social_like){
		$result ='<a class="addthis_button_stumbleupon_badge"></a>';
		return $result;
	}
	public function foursquare($social_like){
		$result ='<a class="addthis_button_foursquare"></a>';
		return $result;
	}
		
	public function amazon($social_like){
		$result = false;
		if(isset($this->request->get['product_id'])){

			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);

			if ($product_info) {
				$this->load->model('tool/image');
				
				if (VERSION >= '2.2.0.0') {
                    $width = $this->config->get($this->config->get('config_theme') . '_image_thumb_width');
                    $height = $this->config->get($this->config->get('config_theme') . '_image_thumb_height');
                } else {
                    $width = $this->config->get('config_image_thumb_width');
                    $height = $this->config->get('config_image_thumb_height');
                }

				if ($product_info['image']) {
					$image= $this->model_tool_image->resize($product_info['image'], $width, $height);
				} else {
					$image = '';
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				$result ='<a class="addthis_button_amazonwishlist"></a>
				<script> 
					addthis_share = {
					     passthrough: {
					         amazonwishlist:{
					             p:"'.$price.'",
					             i:"'.$image.'",             
					             u:"",
					             t:"'.$product_info['name'].'"
					            }
					     }
					}
				</script> ';
			}
		}
		
		return $result;
	}	
	
	public function addthis($social_like){
		$result ='<a class="addthis_counter addthis_pill_style"></a>';
		return $result;
	}
}
?>