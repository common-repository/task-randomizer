<?php
/**
 * Plugin Name: Task Randomizer
 * Description: Составление базы заданий и предъявление задания обучающемуся
 * Plugin URI:  https://github.com/alexey-sergeev/task-randomizer
 * Author:      Alexey N. Sergeev
 * Version:     1.0.0
 */


include_once dirname( __FILE__ ) . '/inc/tr-init.php';


// Инициализация плагина

add_action( 'init', 'mif_tr_init' );

function mif_tr_init()
{
    global $tr;
    
    $tr = new mif_tr_init();
    
}



// Подключение стилей

add_action( 'wp_enqueue_scripts', 'mif_tr_customizer_styles' );

function mif_tr_customizer_styles() 
{
	wp_enqueue_style( 'bootstrap', plugins_url( 'lib/bootstrap/css/bootstrap.min.css', __FILE__ ) );
}



// Служебные функции

if ( ! function_exists( p ) ) {
    
    function p( $txt )
    {
        
        print_r( '<pre>' );
        print_r( $txt );
        print_r( '</pre>' );
        
    }

}

?>