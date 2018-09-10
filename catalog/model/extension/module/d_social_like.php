<?php
class ModelExtensionModuleDSocialLike extends Model {
    private $subversions = array('lite', 'light', 'free');

    public function getConfigFileName($codename)
    {
        if(isset($this->request->post['config'])){
            return $this->request->post['config'];
        }

        $setting = $this->config->get($codename.'_setting');

        if(isset($setting['config'])){
            return $setting['config'];
        }

        $full = DIR_SYSTEM . 'config/'. $codename . '.php';
        if (file_exists($full)) {
            return $codename;
        }

        foreach ($this->subversions as $subversion){
            if (file_exists(DIR_SYSTEM . 'config/'. $codename . '_' . $subversion . '.php')) {
                return $codename . '_' . $subversion;
            }
        }

        return false;
    }
}
