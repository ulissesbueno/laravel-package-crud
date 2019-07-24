<?php

namespace Ubs\Crud\Library;

use Illuminate\Support\Facades\DB;
use Ubs\Crud\Library\DynLibrary;

class DynFormLibrary extends DynLibrary{

	public static function FormData( $data = [],  $model, $range = null ){
		$schema = $model->schema();
        $options = $model->settingDF;
       	$hiddens = $model->hiddens;
       	$table = $model->getTable();

		$min = [ 
                    "label" => "",
                    "mask"  => "",
                    "type"	=> "text",
                    "options" => null,
                    "value"	=> "",
                    "hidden" => "no",
                    "readonly" => "no",
                    "noshow"=>'',
                    "name" => '',
                    "field" => '',
                    "col" => 12
                ];

		$formData = [];
		$count = 0;

		foreach ($schema as $key => $value) {

			$name = $value->Field;
			if( in_array($name, $model->noform ) ){
				continue;
			}

			if( array_key_exists( $name , $options ) ){
				$formData[ $name ] = $formData[ $name ] = array_merge( $min , $options[$name]);
			} else{
				$formData[ $name ] = $min;
			}

            $formData[ $name ]['field'] = trim($name);
			$formData[ $name ]['name'] = $table.'__'.$name;

			$vl = '';
			if($data) $vl = $data[$name] ;

			$ts = parent::parse_( $value->Type );
            if( in_array($name, $hiddens) ){
                $formData[ $name ]['hidden'] = 'yes';
                $ts->type = 'hidden';
            }

			switch ( $ts->type ) {
				case 'enum':
						$formData[ $name ]['type'] = 'radio';
					break;
				case 'text':
						$formData[ $name ]['type'] = 'textarea';
					break;
				case 'date':
				case 'datetime':
				case 'timestamp':
						//$vl = self::date( $vl );
			}	

			if( is_string($formData[ $name ]['options']) ){
				if( preg_match('/^call\:(.*)$/i', $formData[ $name ]['options'], $match ) ){
					$method = $match[1];
					$formData[ $name ]['options'] = $model->$method();
					if( $formData[ $name ]['type'] != 'radio' ) $formData[ $name ]['type'] = 'select' ;
				} 
			}	

			$label = $name;
			
			$formData[ $name ]['origin'] = $ts->type;
			$formData[ $name ]['value'] = $vl;
			$formData[ $name ]['required'] = ( $value->Null == 'NO' ? 'required' : '' ) ;
			$formData[ $name ]['size'] = $ts->size;			
		}

		if( $range ){
			if( count( $range ) == 1 ){
				$formData = array_slice( $formData , $range[0]);
			} else {
				$formData = array_slice( $formData , $range[0],$range[1]);	
			}			
		}
		return $formData;
	}
	
	private static function gd( $index , $data ){
		if( array_key_exists( $index , $data ) ){
			return $data[ $index ];
		} else {
			return "";
		}
	}

	private static function date( $str, $format = '' ){

		if( preg_match('/([0-9]{4})\-([0-9]{2})\-([0-9]{2})(\s([0-9]{2}\:[0-9]{2}\:[0-9]{2}))*/', $str , $m) ){
			
			$year = $m[1];
			$month = $m[2];
			$day = $m[3];

			$hour = "";
			if( isset( $m[5] ) ){
				$hour = $m[5];
			}	

		} else  if( preg_match('/([0-9]{2})\/([0-9]{2})\/([0-9]{4})(\s([0-9]{2}\:[0-9]{2}\:[0-9]{2}))*/', $str , $m) ){
			
			$year = $m[1];
			$month = $m[2];
			$day = $m[3];

			$hour = "";
			if( isset( $m[5] ) ){
				$hour = $m[5];
			}
		} else {
			return $str;
		}

		switch ( $format ) {
			case 'db':
					return $year."-".$month."-".$day.( $hour ? " ".$hour : "" ) ;
				break;			
			default:
					return $day."/".$month."/".$year.( $hour ? " ".$hour : "" ) ;
				break;
		}

	} 

}
