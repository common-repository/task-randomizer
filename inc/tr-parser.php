<?php

//
// Парсер
// 
//

defined( 'ABSPATH' ) || exit;

class mif_tr_parser extends mif_tr_core { 

    private $arr = array();
    private $param = array();

    private $allowable_params = array(

        'choice' => array( 'random', 'exam', 'choice' ),
        'history' => array( 'history', 'simple' ),
        'unique' => array( 'overlapping', 'unique' )

    );




    function __construct( $text )
    {
        parent::__construct();

        $this->parse( $text );
        
    }


    // 
    // Парсинг
    // 

    private function parse( $text )
    {
        $arr = array();
        $arr = explode( "\n", $text );

        $param_raw = array();
        $arr_raw = array();
        $n = 0;

        foreach ( $arr as $item ) {
            
            if ( preg_match( "/^#/", $item ) ) continue;
            if ( preg_match( "/\[tasks\]/", $item ) ) continue;
            if ( preg_match( "/\[\/tasks\]/", $item ) ) continue;
            
            if ( preg_match( "/^@/", $item ) ) {
                
                $param_raw[] = $item;
                continue;

            }
            
            if ( preg_match( "/^---/", $item ) ) {
                
                $n++;
                continue;

            }
            
            if ( ! isset( $arr_raw[$n] ) ) $arr_raw[$n] = '';

            $arr_raw[$n] .= trim( $item ) . "\n";
            
        }

        $this->param = $param_raw;
        $this->arr = $arr_raw;

    }




    // 
    // Вернуть параметры
    // 

    public function get_param()
    {
        $num = 1;
        $params = array( 'random', 'history', 'overlapping' );

        foreach ( $this->param as $item ) {

            if ( preg_match( '/^@n/', $item ) ) {

                $arr = explode( ' ', $item );
                $num = (int) $arr[1];
                
            }

            if ( preg_match( '/^@s/', $item ) ) {

                $params = array_merge( $params, explode( ' ', $item ) );
                
            }
            
        }

        $arr2 = array();

        foreach ( $params as $item ) {

            foreach ( $this->allowable_params as $key => $value ) {

                if ( in_array( $item, $value ) ) $arr2[$key] = $item;

            }

        }
        
        $arr2['num'] = $num;
        
        return $arr2;
    }




    // 
    // Вернуть массив
    // 

    public function get_arr()
    {
        return $this->arr;
    }


}

?>
