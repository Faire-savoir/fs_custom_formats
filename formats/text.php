<?php

/**
 * Permet de renvoyer un texte selon les options de mise en forme
 *
 * @param  array $field     le champ texte
 * @param  array $options   les options de mise en forme
 *                 'trim' => 'word',
 *                 'trim_count' => '55',
 *                 'trim_char' => '...',
 *                 'new_line' => 'p',
 *
 * @return string           le HTML à retourner
 */
function format_text( $field, $options = [] ) {
  $html = '';
  if ( isset($field) && is_string($field) && !empty($field) ) {
    $html = $field;

    // TRIM FUNCTION
    if ( isset($options['trim']) && !empty($options['trim']) ) {
      $trim_count = ( isset($options['trim_count']) && is_numeric($options['trim_count']) && $options['trim_count'] > 0 ) ? $options['trim_count'] : 55 ;
      $trim_char = ( isset($options['trim_char']) ) ? $options['trim_char'] : '...' ;
      switch($options['trim']) {
        case 'word':
          $html = wp_trim_words( $field, $trim_count, $trim_char );
          break;
        case 'char':
          $html = mb_strimwidth( $field, 0, $trim_count ).$trim_char;
          break;
        case 'smart_char':
          $html = gen_string( $field, $trim_count, $trim_char);
          break;
      }
    }

    // NEW LINE
    if ( isset($options['new_line']) && !empty($options['new_line']) ) {
      switch($options['new_line']){
        case 'br':
          $html = format_nl_to_br($html);
          break;
        case 'p':
          $html = format_nl_to_p($html);
          break;
      }
    }


  }

  return $html;
}

function gen_string($string,$max=20, $trim_char = '...'){
    $tok=strtok($string,' ');
    $string='';
    while($tok!==false && mb_strlen($string)<$max)
    {
        if (mb_strlen($string)+mb_strlen($tok)<=$max)
            $string.=$tok.' ';
        else
            break;
        $tok=strtok(' ');
    }
    return trim($string).$trim_char;
}

/**
 * Permet de renvoyer un texte en remplacant les \n par des <br>
 *
 * @param  array $field     le champ texte
 *
 * @return string           le HTML à retourner
 */
function format_nl_to_br( $field ){
  $html = '';

  if( isset($field) && !empty($field) ) {
    $html = nl2br($field);
  }

  return $html;
}

/**
 * Permet de renvoyer un texte en remplacant les \n par des <p>
 *
 * @param  array $field     le champ texte
 *
 * @return string           le HTML à retourner
 */
function format_nl_to_p( $field ){
  $html = '';

  if( isset($field) && !empty($field) ) {
    $html = nl2p($field);
  }

  return $html;
}

function nl2p($string, $line_breaks = true, $xml = true) {

  $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

  if ($line_breaks == true) {
    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
  }
  else {
    return '<p>'.preg_replace(
        array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
        array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),
        trim($string)
    ).'</p>';
  }
}
