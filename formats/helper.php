<?php

/**
 * Initialiser une valeur provenant d'un flux
 *
 * @param  array  $field    Tableau de tous les champs retournés par la fonction get_fields()
 * @param  string $name     Nom du champ parmis le tableau de champ
 *
 * @return mixed            Le champ s'il existe, la valeur nulle '' sinon.
 */
function fs_init($field, $name){
	$return_field = '';
	if ( !empty($field[$name]) ){
		$return_field = $field[$name];
	}
	return $return_field;
}

function add_http_if_missed( $link ){
	if ( substr( $link, 0, 4 ) != 'http' ) {
		$link = 'http://'.$link;
	}
	return $link;
}
