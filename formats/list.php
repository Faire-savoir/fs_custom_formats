<?php

/**
 * Permet de renvoyer une liste de type ul>li depuis un champ multi-valué.
 *
 * @param  array $field     le champ multi-valué
 * @param  array $options   les options du format
 *                 'class'        la ou les class à ajouter sur la balise <ul>
 *
 * @return string           le HTML à retourner
 */
function format_list_ul( $field, $options = array() ){
  $html = '';

  if( isset($field) && is_array($field) && !empty($field) ) {
    $list = '';
    foreach( $field as $choice) {

      if ( isset($choice) && !empty($choice) && !is_array($choice) ) {
        $final_choice = $choice;
      }
      else if ( isset($choice['label']) && !empty($choice['label']) && is_array($choice) ) {
        $final_choice = $choice['label'];
      }

      if ( isset($final_choice) && !empty($final_choice) ) {
        if ( function_exists('is_string_a_tis_id') && function_exists('get_label_thesaurus_by_id') ){
          if ( is_string_a_tis_id($final_choice) ){
            $tmp_choice = get_label_thesaurus_by_id($final_choice);
            if ( !empty($tmp_choice) ){
              $final_choice = $tmp_choice;
            }
          }
        }
        if ( isset($final_choice) && !empty($final_choice) ){ 
          $list .= '<li>'.$final_choice.'</li>';
        }
      }

    }
    if ( !empty($list) ) {
      $class = ( isset($options['class']) && !empty($options['class']) ) ? ' '.$options['class'] : '' ;
      $html = '<ul class="value_list'.$class.'">'.$list.'</ul>';
    }
  }

  return $html;
}

/**
 * Permet de renvoyer une liste de valeur séparée par un caractère spécifié.
 *
 * @param  array $field       le champ multi-valué
 * @param  array $separator   le caractère de séparation. Default : ''
 * @param  array $options     les options : 
 *                              - limit => integer to limit nb of return. Default : -1 (no limit)
 *
 * @return string           le HTML à retourner
 */
function format_list_separate( $field, $separator = '', $options = [] ){
  $html = '';

  $limit = ( isset($options['limit']) && is_numeric($options['limit']) ) ? $options['limit'] : -1 ;

  if( isset($field) && is_array($field) && !empty($field) ) {
    $list = [];
    foreach( $field as $choice ) {
      if ( isset($choice) && !empty($choice) && !is_array($choice) ) {
        $final_choice = $choice;
      }
      else if ( isset($choice['label']) && !empty($choice['label']) && is_array($choice) ) {
        $final_choice = $choice['label'];
      }

      if ( isset($final_choice) && !empty($final_choice) ) {
        if ( function_exists('is_string_a_tis_id') && function_exists('get_label_thesaurus_by_id') ){
          if ( is_string_a_tis_id($final_choice) ){
            $tmp_choice = get_label_thesaurus_by_id($final_choice);
            if ( !empty($tmp_choice) ){
              $final_choice = $tmp_choice;
            }
          }
        }
        if ( isset($final_choice) && !empty($final_choice) ){ 
          $list[] = $final_choice;
        }
      }
    }

    if ( !empty($list) ) {
      if ($limit > 0){
        $list = array_slice($list, 0, $limit);
      }
      $html = implode($separator, $list);
    }
  }

  return $html;
}

/**
 * Affiche une valeur d'une liste si elle est présente dans la liste.
 *
 * @param  array  $field      le champ multi-valué
 * @param  string $id_list    id à trouver dans la liste
 * @param  array  $options    les options du format
 *
 * @return string          le HTML à retourner
 */
function format_in_list($field, $id_list = '', $options = []) {
  $html = '';

  if( isset($field) && !empty($field) ){

    $wrapper_el = ( isset($options['wrapper_el']) && !empty($options['wrapper_el']) ) ? $options['wrapper_el'] : '' ;
    $wrapper_class = ( isset($options['wrapper_class']) && !empty($options['wrapper_class']) ) ? $options['wrapper_class'] : '' ;
    $search_field = ( isset($options['search_field']) && !empty($options['search_field']) ) ? $options['search_field'] : '' ;
    $display_field = ( isset($options['display_field']) && !empty($options['display_field']) ) ? $options['display_field'] : '' ;

    $list = [];
    foreach( $field as $item ){
      if ( (isset($search_field) && isset($item[$search_field]) && is_array($item[$search_field]) && in_array( $id_list , $item[$search_field])) OR (is_array($item) && in_array( $id_list , $item)) ) {
        if ( !empty($wrapper_el) ) {
          $value = ( isset($item[$display_field]) && !empty($item[$display_field]) ) ? $item[$display_field] : '' ;
          $list[] = '<'.$wrapper_el.' class="'.$wrapper_class.'">'.$value.'</'.$wrapper_el.'>';
        }
        else{
          $value = ( isset($item[$display_field]) && !empty($item[$display_field]) ) ? $item[$display_field] : '' ;
          $list[] = $value;
        }
      }
    }

    if ( !empty($list) ) {
      if ( !empty($wrapper_el) ){
        $html = implode('', $list);
      }
      else{
        $html = $list;
      }
    }


  }

  return $html;
}
