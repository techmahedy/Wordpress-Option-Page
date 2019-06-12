<?php

/**
 * Theme option page 
 */

class Theme_Options{

    public $options;

    public function __construct(){

       //delete_option('keen_options');
        $this->options = get_option('keen_options');
        $this->register_settings_and_fields();
    }

    public static function add_menu_page(){
        add_options_page( 'Theme Options', 'Theme Options', 'administrator', 'theme_option', array('Theme_Options','display_option_page') );
    }

    public static function display_option_page(){
        ?>
        <div class="wrap">
           <h2>My Theme Options</h2>
           <form action="options.php" method="post" enctype="multipart/form-data">
                <?php
                settings_fields( 'keen_options' );
                do_settings_sections( 'theme_option' );
                ?>
                <p class="submit">
                  <input name="submit" type="submit" class="button-primary" value="Save changes"/>
                </p>
           </form>
        </div>

        <?php 
    }

    public function register_settings_and_fields(){

        register_setting('keen_options', 'keen_options' , array($this,'validate_settings')); //3rd @param =  optional callback

        add_settings_section( 'keen_section', 'General Settings', array($this,'keen_section_callback'), 'theme_option' );

        add_settings_field( 'keen_banner_heading', 'Banner Heading', array($this,'keen_banner_heading_setting'), 'theme_option', 'keen_section' );

        add_settings_field( 'keen_logo', 'Your Logo', array($this,'keen_logo'), 'theme_option', 'keen_section' );

        add_settings_field( 'keen_color_scheme', 'Your Color', array($this,'keen_color'), 'theme_option', 'keen_section' );
        
    }
    
    public function validate_settings($keen_options){
        if(!empty($_FILES['keen_logo_upload']['tmp_name'])){
            $override = array('test_form' => false);
            $file = wp_handle_upload($_FILES['keen_logo_upload'],$override);
            $keen_options['keen_logo'] = $file['url'];
        }else{
            $keen_options['keen_logo'] = $this->options['keen_logo'];
        }
        return $keen_options;
    }
    public function keen_section_callback(){

    }
    public function keen_banner_heading_setting(){
        echo "<input name='keen_options[keen_banner_heading]' type='text' value='{$this->options['keen_banner_heading']}' />";
    }
    public function keen_logo(){
        echo '<input type="file" name="keen_logo_upload" /> <br/> <br/>';
        if(isset($this->options['keen_logo'])){
            echo "<img src='{$this->options['keen_logo']}' >";
        }
    }
    public function keen_color(){
        $color = array('Red', 'Green', 'Blue', 'Yellow');
        echo "<select name='keen_options[keen_color_scheme]' >";
        foreach($color as $item){
            $selected = ($this->options['keen_color_scheme'] === $item) ? 'selected = "selected" ' : '';
            echo "<option value='$item' $selected>$item</option>";
        }
        echo "</select>";
    }
}


add_action('admin_menu',function(){
    Theme_Options::add_menu_page();
});

add_action('admin_init' , function(){
   new Theme_Options();
});

