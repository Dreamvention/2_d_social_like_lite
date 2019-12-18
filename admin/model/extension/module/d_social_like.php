<?php
class ModelExtensionModuleDSocialLike extends Model {

    public function addToLayoutFromSetup($module_id)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` (
                            `layout_id`, 
                            `code`, 
                            `position`, 
                            `sort_order`)
                          VALUES (
                              (SELECT `layout_id` FROM `" . DB_PREFIX . "layout_route` WHERE `route` LIKE '%home%' LIMIT 1), 
                              'd_social_like." . $module_id."', 
                              'content_top', 
                              0)
                        ");
        return true;
    }

    /*
    *   Return list of stores.
    */
    public function getStores()
    {
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
}