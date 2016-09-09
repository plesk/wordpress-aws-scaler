<?php

class Parallax_One_Customize_Alpha_Color_Control extends WP_Customize_Control {
    
    public $type = 'alphacolor';
    public $palette = true;
    public $default = '';
    
    
    public function __construct( $manager, $id, $args = array() ) {
        parent::__construct( $manager, $id, $args );
        $this->default = $this->setting->default;
    }
    
    
    protected function render() {
        $id = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
        $class = 'customize-control customize-control-' . $this->type; ?>
        <li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
            <?php $this->render_content(); ?>
        </li>
    <?php 
    }

    public function render_content() { ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <input type="text" data-palette="<?php echo $this->palette; ?>" data-default-color="<?php echo $this->default; ?>" value="<?php echo intval( $this->value() ); ?>" class="pluto-color-control" <?php $this->link(); ?>  />
        </label>
    <?php 
    }
}

?>