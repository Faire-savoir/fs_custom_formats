<?php

/**
 * Permet de retourner le widget TripAdvisor grace à un ID tripadvisor. Quelques options sont disponibles.
 *
 * @param  int    $widget_id    l'ID du widget TripAdvisor
 * @param  array  $options      les options du format
 *                  'nb_avis' => 5  // nb d'avis sur le widget
 *
 * @return string               le HTML à retourner
 */
function format_tripadvisor( $widget_id, $options = [] ){
  $html = '';
  //$html = ICL_LANGUAGE_CODE;
  if ( isset($widget_id) && is_numeric($widget_id) && !empty($widget_id) ) {
    //
    $uniq = rand ( 100 , 999 );
    $nb_avis = ( isset($options['nb_avis']) ) ? $options['nb_avis'] : 5 ;
    $html = '<div id="TA_selfserveprop'.$uniq.'" class="TA_selfserveprop">
      <a target="_blank" href="https://www.tripadvisor.fr/"><img src="https://www.tripadvisor.fr/img/cdsi/img2/branding/150_logo-11900-2.png" alt="TripAdvisor"/></a>
    </div>
    <script async src="https://www.jscache.com/wejs?wtype=selfserveprop&amp;uniq='.$uniq.'&amp;locationId='.$widget_id.'&amp;lang='.ICL_LANGUAGE_CODE.'&amp;rating=true&amp;nreviews='.$nb_avis.'&amp;writereviewlink=true&amp;popIdx=true&amp;iswide=false&amp;border=true&amp;display_version=2" data-loadtrk onload="this.loadtrk=true"></script>';
  }
  return $html;
}

/**
 * Permet de retourner une liste des reseaux sociaux avec une classe spécifiant le reseau social associé.
 *
 * @param  array  $field        le champ multi-valué
 * @param  array  $options      les options du format
 *
 * @return string               le HTML à retourner
 */
function format_list_reseaux_sociaux( $field = [], $field_link = '', $options = [] ){
  $html = '';

  if( is_array($field) && !empty($field) && is_string($field_link) && !empty($field_link) ) {

    // Initialisation
    $field_class = ( isset($options['field_class_label']) && !empty($options['field_class_label']) ) ? $options['field_class_label'] : '' ;
    $target = ( isset($options['target']) && !empty($options['target']) ) ? $options['target'] : '_blank' ;

    // Calcul
    $list = [];
    foreach( $field as $item) {
      if ( isset($item[$field_link]) && !empty($item[$field_link]) ) {
        // Classe specifique
        $label = '';
        $specific_class = '';
        if ( isset($item[$field_class]) && !empty($item[$field_class]) && function_exists('get_label_thesaurus_by_id') ) {
          if ( is_array($item[$field_class]) ){
            $item[$field_class] = reset($item[$field_class]);
          }
          $label = get_label_thesaurus_by_id($item[$field_class]);
          $specific_class = ' '.tis_build_system_name($label);
        }
        // ajout à la liste
        $list[] = '<li><a href="'.add_http_if_missed($item[$field_link]).'" target="'.$target.'" class="list-item-social'.$specific_class.'">'.$label.'</a></li>';
      }
    }

    // Rendu
    if ( !empty($list) ) {
      $html = '<ul>'.implode('',$list).'</ul>';
    }
  }

  return $html;
}
