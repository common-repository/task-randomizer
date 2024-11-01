<?php

//
// Рандомайзер
// 
//

defined( 'ABSPATH' ) || exit;

class mif_tr_randomizer extends mif_tr_core { 

    private $arr = array();
    private $raw = array();
    private $param = array();

    
    function __construct( $arr, $param )
    {
        parent::__construct();

        $this->arr = $this->arr_init( $arr, $param );
        $this->param = $param;
        $this->raw = $arr;

    }

    

    // 
    // Инициализация массива выбранных заданий
    // 

    private function arr_init( $arr, $param )
    {
        $arr2 = array();
        
        if ( $param['history'] == 'history' ) {
            
            $arr2 = $this->get_history( $arr, $param );

            if ( $arr2 ) return $arr2;
            
        }

        if ( $param['unique'] == 'unique' ) $arr = $this->get_unique( $arr );

        
        if ( $param['choice'] == 'random' ) {
                
            shuffle( $arr );
            $arr2 = array_slice( $arr, 0, $param['num'] );
            
        }
        

        if ( $param['choice'] == 'exam' || $param['choice'] == 'choice' ) {
                
            $arr2 = $this->get_choice( $arr, $param );
            
        }
        


        if ( $param['history'] == 'history' ) $this->set_history( $arr2 );
        if ( $param['unique'] == 'unique' ) $this->set_unique( $arr2 );
        
        return $arr2;
    }

    

    
    // 
    // Выбор вручную
    // 

    public function get_choice( $arr, $param )
    {
        $arr2 = array();

        $index = array();
        $user_id = get_current_user_id();

        foreach ( $arr as $item ) $index[md5( $item . $user_id )] = $item;

        if ( $_REQUEST['choices'] ) {

            $choices = array_map( 'sanitize_key', array_keys( $_REQUEST['choices'] ) );

            $arr3 = array();

            foreach ( $choices as $key ) {

                if ( isset( $index[$key] ) ) $arr3[] = $index[$key];

            }

            $arr4 = array_intersect( $arr, $arr3 );
            if ( count( $arr4 ) == $param['num'] ) $arr2 = $arr3;

        }

        return $arr2;
    }


    
    // 
    // Запомнить выбор задачи
    // 

    public function set_unique( $arr )
    {
        global $post;

        $user_id = get_current_user_id();

        $ret = true;

        foreach ( $arr as $item ) {
            
            $data = array(
                'user' => $user_id,
                'time' => time(),
                'task' => $item,
            );
            
            $ret2 = add_post_meta( $post->ID, 'task-unique', $data );

            if ( ! $ret2 ) $ret = false;

        } 

        return $ret;
    }
    

    
    // 
    // Получить список уникальных задач
    // 

    public function get_unique( $arr )
    {
        global $post;

        $arr2 = get_post_meta( $post->ID, 'task-unique' );

        $arr3 = array();

        foreach ( $arr2 as $data ) {

            // Здесь можно учитывать время и пользователей

            $arr3[] = $data['task'];

        }

        $arr4 = array_diff( $arr, $arr3 );
        
        return $arr4;
    }


    
    // 
    // Извлечь задачи из истории
    // 

    public function get_history( $arr, $param )
    {
        global $post;
        
        if ( ! is_user_logged_in() ) return false;

        $user_id = get_current_user_id();

        $arr2 = get_post_meta( $post->ID, 'task-history-' . $user_id );

        $ret = false;

        foreach ( $arr2 as $item ) {

            $arr3 = array_intersect( $arr, $item );
            if ( count( $arr3 ) == $param['num'] ) $ret = $arr3;

        }

        return $ret;
    }



    // 
    // Записать в историю выбора
    // 

    public function set_history( $arr )
    {
        global $post;
        
        if ( ! is_user_logged_in() ) return false;

        $user_id = get_current_user_id();

        $ret = add_post_meta( $post->ID, 'task-history-' . $user_id, $arr );
        
        return $ret;
    }
    

    // 
    // Выводит все задачи
    // 

    public function get_raw()
    {
        return $this->raw;
    }
    

    // 
    // Выводит выбранные задачи в виде массива
    // 

    public function get_arr()
    {
        return $this->arr;
    }
    

    // 
    // Получает параметры выбора
    // 

    public function get_param()
    {
        return $this->param;
    }
    

    // 
    // Форма выбора заданий
    // 

    public function get_form()
    {
        $out = '';

        $raw = $this->get_raw();
        $param = $this->get_param();

        if ( $param['unique'] == 'unique' ) $raw = $this->get_unique( $raw );

        $out .= '<form method="POST">';
        $out .= '<div class="bg-light p-3 mt-3 mb-3">';
        
        $user_id = get_current_user_id();

        $n = 1;

        if ( $param['choice'] == 'exam' ) shuffle( $raw );

        foreach ( $raw as $item ) {
            
            $key = md5( $item . $user_id );

            $text = trim( $item );

            if ( $param['choice'] == 'exam' ) $text = 'Задание ' . $n++;

            $out .= '<label><input type="checkbox" name="choices[' . $key . ']"> ' . $text . '</label><br />';
            
        }
        
        
        $out .= '<input type="submit" value="Выбрать">';
        $out .= '</div>';
        $out .= '</form>';

        return $out;
    }


    // 
    // Выводит выбранные задачи
    // 

    public function get_text()
    {
        $arr = $this->get_arr();
        $param = $this->get_param();

        if ( ( $param['choice'] == 'exam' || $param['choice'] == 'choice' ) && ! $arr ) {

            $out = $this->get_form();
            return $out;
        }
        
        if ( ! $arr ) $arr[] = 'Нет доступных заданий';

        foreach ( $arr as $key => $item ) {

            $arr[$key] = '<div class="bg-light p-3 mt-3 mb-3">' . $item . '</div>';

        }

        $out = implode( "\n", $arr );

        return $out;
    }

}

?>
