<?php

add_action( 'wp_enqueue_scripts', 'add_date_format_scripts' );
function add_date_format_scripts(){
      wp_register_script('format_date_script', plugins_url('../assets/js/format_date_script.js',__FILE__), array('jquery'), false );
}

function format_date_in_template( $field = [], $options = [] ){
  $html = '';
  // Les params sont bien des tableaux
  if ( is_array($field) && !empty($field) && is_array($options) ) {

    // Le param "options" est bien formé sinon on l'initialise
    $options['field_start'] = (isset($options['field_start'])) ? $options['field_start'] : 'datedebut' ;
    $options['field_end'] = (isset($options['field_end'])) ? $options['field_end'] : 'datefin' ;
    $options['mode'] = (isset($options['mode'])) ? $options['mode'] : 'listing' ;
    $options['clean'] = (isset($options['clean'])) ? $options['clean'] : false ;
    $options['format_date'] = (isset($options['format_date'])) ? $options['format_date'] : 'j M' ;
    $options['format_date_plus'] = (isset($options['format_date_plus'])) ? $options['format_date_plus'] : 'j M Y' ;

    if ( $options['clean'] ) {
      clean_dates_with_recurrences($field);
    }

    // On tri le tableau $field avec le champ "date_début" spécifié dans l'option field_start.
    usort($field, date_asc_sorter($options['field_start']));

    $first_date = [];
    $other_dates = [];

    foreach ($field as $row){
      // Pour chaque date, on regarde si start_date existe.
      if ( isset($row[$options['field_start']]) && !empty($row[$options['field_start']]) ) {
        $date = [
          'start_date' => remove_hours_and_minutes_to_timestamp( $row[$options['field_start']] ),
          'end_date' => (isset($row[$options['field_end']])) ? remove_hours_and_minutes_to_timestamp( $row[$options['field_end']] ) : null,
        ];
        // On met la première date dans first_date
        if ( empty($first_date) ){
          $first_date[] = $date;
        }
        // Les autres dans other_dates
        else{
          $other_dates[] = $date;
        }
        unset($date);
      }
    }

    // Maintenant le rendu HTML
    if ( !empty($other_dates) ){
      wp_enqueue_script('format_date_script');
      if ( !empty($options['mode']) ){
        wp_add_inline_script( 'format_date_script', 'var format_date_mode = \'' . $options['mode'] . '\';', 'before' );
      }
    }
    $html = render_format_date($options, $first_date, $other_dates);

  }
  return $html;
}

/**
 * Fonction pour permettre le tri des occurences d'un champ contenant des dates de la plus proche à la plus longtaine
 * @param  string $key    la clé du tableau associatif pour faire un tri
 */
function date_asc_sorter($key) {
  return function ($a, $b) use ($key) {
    if ( isset($a[$key]) && isset($b[$key]) ){
      return $a[$key] > $b[$key];
    }
    return;
  };
}

/**
 * Cette fonction supprime les heures, minutes et secondes d'un timestamp passé en paramètre.
 * @param  integer $timestamp   Timestamp
 * @return integer              Le nouveau timestamp
 */
function remove_hours_and_minutes_to_timestamp($timestamp) {
    return DateTime::createFromFormat('U', $timestamp)->setTime(0,0)->format('U');
}

function transform_tis_format_date_to_timestamp($tis_datetime){
  $date = new DateTime($tis_datetime);
  return $date->format('U');
}

/**
 * Cette fonction renvoie le HTML à afficher dans les template
 * @param  array $options       Les options du format
 * @param  array $first_date    Le tableau contenant la premiere date
 * @param  array $other_dates   Le tableau contenant les autres dates
 * @return string               HTML
 */
function render_format_date( $options, $first_date, $other_dates ){
  $html = '';

  // Affichage de la div globale
  $html = '<div class="format_date_in_template">';
    // Ici la premiere date
    $html .= '<div class="fisrt_date">';
      $html .= '<div class="date">'.render_date(reset($first_date), $options['format_date']).'</div>';
    $html .= '</div>';
    // Ici les autres dates si il y en a
    if ( !empty($other_dates) ) :
      $nb_date = ( ($nb = count($other_dates)) > 1) ? '+ '.$nb.' dates' : '+ 1 date' ;
      $html .= '<div class="other_dates">';
        // avec un count
        $html .= '<span class="nb_other_dates">'.$nb_date.'</span>';
        // et la liste (cachée par défaut)
        if ( $options['mode'] == 'listing' ) :
          $html .= '<ul style="display: none;">';
          foreach ( $other_dates as $date ) :
            $html .= '<li>';
              $html .= '<span class="date">'.render_date($date, $options['format_date_plus']).'</span>';
            $html .= '</li>';
          endforeach;
          $html .= '</ul>';
        endif;
      $html .= '</div>';
    endif;
  $html .= '</div>';

  return $html;
}

/**
 * Fonction pour afficher la date dans un bon format
 * @param  integer $date    Timestamp
 * @param  string $format   Format
 * @return string           HTML
 */
function render_date( $date, $format ){
  // Si debut et fin existe et sont différents
  if ( isset($date['start_date']) && !empty($date['start_date']) && isset($date['end_date']) && !empty($date['end_date']) && $date['start_date']!=$date['end_date'] ) {
    return sprintf( __( 'Du %s au %s', 'format_date' ), date_i18n($format, $date['start_date']), date_i18n($format, $date['end_date']) );
  }
  // Si debut existe seulement
  else if ( isset($date['start_date']) && !empty($date['start_date']) ){
    return date_i18n($format, $date['start_date']);
  }
  // Sinon erreur
  else{
    return '';
  }
}

/**
 * This function allows to clean datesfma data by removing recurrences from render
 * @param  array  &$field                   the field to clean
 * @param  string $recurrence_field_name    the sub_field system_name of recurrence rules.
 *
 * @return n/a
 */
function clean_dates_with_recurrences( &$field = [], $recurrence_field_name = 'reglederecurrence' ){
  if ( isset($field) && !empty($field) && is_array($field) ){
    $recurrences_regles = [];
    foreach( $field as $num_row => $row ):
      if ( is_array($row) && isset($row[$recurrence_field_name]) && !empty($row[$recurrence_field_name]) ){
        if ( !array_key_exists($row[$recurrence_field_name],$recurrences_regles) ):
          $recurrences_regles[$row[$recurrence_field_name]] = $row;
          $recurrences_regles[$row[$recurrence_field_name]]['ROW_ID'] = $num_row;
        else:
          if ( $row['datedebut']<$recurrences_regles[$row[$recurrence_field_name]]['datedebut'] ) {
            unset($field[$recurrences_regles[$row[$recurrence_field_name]]['ROW_ID']]);
            $recurrences_regles[$row[$recurrence_field_name]] = $row;
            $recurrences_regles[$row[$recurrence_field_name]]['ROW_ID'] = $num_row;
          }
          else{
            unset($field[$num_row]);
          }
        endif;
      }
    endforeach;
  }
  return;
}

/**
 * This function allows to get readable dates by adding data to row.
 * @param  array  &$field                   the field to clean
 * @param  string $recurrence_field_name    the sub_field system_name of recurrence rules.
 *
 * @return n/a
 */
function get_readable_dates_with_recurrences($field = [], $recurrence_field_name = 'reglederecurrence', $exclude_field_name = 'exceptionalaregle'){
  if ( isset($field) && !empty($field) && is_array($field) ){
    // we clean the $field var
    clean_dates_with_recurrences($field, $recurrence_field_name);

    // now we add the readable data
    foreach ($field as $key => &$row) :
      if ( isset($row[$recurrence_field_name]) && !empty($row[$recurrence_field_name]) ) {
        parse_str(strtr($row[$recurrence_field_name], ";", "&"), $recurrence_data);
        if ( is_array($recurrence_data) && !empty($recurrence_data) ){
          if ( isset($recurrence_data['UNTIL']) && !empty($recurrence_data['UNTIL']) ) {
            $recurrence_data['UNTIL'] = remove_hours_and_minutes_to_timestamp(transform_tis_format_date_to_timestamp($recurrence_data['UNTIL']));
          }
          $row[$recurrence_field_name] = $recurrence_data;
        }
      }
      if ( isset($row[$exclude_field_name]) && !empty($row[$exclude_field_name]) ) {
        $row[$exclude_field_name] = explode(',',$row[$exclude_field_name]);
        foreach ($row[$exclude_field_name] as $it=>$date) {
          $row[$exclude_field_name][$it] = remove_hours_and_minutes_to_timestamp(transform_tis_format_date_to_timestamp($date));
        }
      }
    endforeach;
    return $field;
  }
  return [];
}

function get_min_occurence_of_dates( $field = [], $subfield = '', $return_value = false ){
  $min = [];
  if ( isset($field) && !empty($field) && is_array($field) ){
    foreach ($field as $key => $row) :
      if ( isset($row[$subfield]) && !empty($row[$subfield]) ) {
        if ( isset($min[$subfield]) ){
          if ( $row[$subfield] < $min[$subfield] ){
            $min = $row;
          }
        }else{
          $min = $row;
        }
      }
    endforeach;
    if ( is_bool($return_value) && $return_value ) {
      if ( isset($min[$subfield]) ) {
        return $min[$subfield];
      }
    }
  }
  return $min;
}



?>
