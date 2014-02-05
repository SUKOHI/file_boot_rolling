<?php

class File_Boot_Rolling {

    private $_cache_path;
    private $_boot_dir;
    private $_boot_files;

    public function __construct($cache_dir, $cache_filename='BO.dat') {

        $this->_cache_path = $this->correctDirPath($cache_dir) . $cache_filename;

    }

    public function setBootFiles($boot_dir, $boot_files) {

        $this->_boot_dir = $this->correctDirPath($boot_dir);
        $this->_boot_files = $boot_files;

    }

    private function correctDirPath($dir) {

        if(substr($dir, -1) != '/') return $dir .'/';
        return $dir;

    }

    public function getCurrentFileInfo() {

        $boot_files = $this->_boot_files;
        $max_number = count($boot_files)-1;
        $last_number = intval(file_get_contents($this->_cache_path));
        $current_number = ($max_number == $last_number) ? 0 : ($last_number+1);
        $boot_file = $boot_files[$current_number];
        $boot_path = $this->_boot_dir . $boot_file;
        return array('current_number' => $current_number, 'boot_path' => $boot_path);

    }

    public function boot($require_once_flag=false) {

        $current_info = $this->getCurrentFileInfo();
        $current_number = $current_info['current_number'];
        $boot_path = $current_info['boot_path'];

        $this->saveNumber($current_number);

        if($require_once_flag) {

            require_once($boot_path);

        } else {

            require($boot_path);

        }

        return $current_info;

    }

    public function getCurrentNumber() {

        $current_info = $this->getCurrentFileInfo();
        $current_number = $current_info['current_number'];
        $this->saveNumber($current_number);
        return $current_number;
        
    }
    
    public function saveNumber($current_number) {

        @file_put_contents($this->_cache_path, $current_number);

    }
    
    public function setNumber($number) {
        
        $this->saveNumber($number);
        
    }
    
    public function refresh() {
        
        $this->saveNumber(0);
        
    }

}

/*** Sample Source

    require('file_boot_rolling.php');

    $cache_dir = '/cache_path/file_boot_rolling/';
    $cache_filename = 'file_boot_rolling.dat';
    $fbo = new File_Boot_rolling($cache_dir, $cache_filename);    // $cache_filename => omittable

    $boot_dir = '/boot_path/';
    $fbo->setBootFiles($boot_dir, array(

        'cron_mailzo_item.php',
        'cron_magzo_item.php',
        'cron_gekizou_item.php',
        'cron_muryoj_item.php',
        'cron_ikkiyomi_item.php',
        'cron_tadarepo_item.php',
        'cron_mrrp_item.php',
        'cron_sugowaza_item.php',
        'cron_lovewaza_item.php',
        'cron_magaichi_item.php',
        'cron_infozero_item.php',
        'cron_repomaga_item.php'

    ));
    $current_info = $fbo->boot();   // or echo $fbo->getCurrentNumber();
    print_r($current_info);

    $fbo->setNumber(0); // is the same mean with  $fbo->refresh();

[[OR]]

    $fbo = new File_Boot_rolling($cache_dir, $cache_filename);

    $fbo->setBootFiles('', array(

        'a', 'b', 'c', 'd', 'e'

    ));
    $current_number = $fbo->getCurrentNumber();
    $target_string = $target_strings[$current_number];

***/
