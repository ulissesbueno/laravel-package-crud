<?php

namespace Ubs\Crud\Library;

use Illuminate\Support\Facades\DB;

class DynLibrary{

	public function parse( $typeText ){

		$ret = (Object) [ "type" => null, "size" => null, "zerofill" => 'no' ];

		if( preg_match( '/([a-z]+)\((.*)\)(.*)/i' , trim( $typeText ), $match) ){
			$ret->type = $match[1];
			$ret->zerofill = ( preg_match('/zerofill/i', $match[3]) ?'yes':'no' );
			if( preg_match('/^([0-9]+)$/', $match[2]) )	$ret->size = $match[2];	
		} else {
			$ret->type = $typeText;
		}

		return $ret;
	}

	public static function parse_( $typeText ){
		$ret = (Object) [ "type" => null, "size" => null, "zerofill" => 'no' ];

		if( preg_match( '/([a-z]+)\((.*)\)(.*)/i' , trim( $typeText ), $match) ){
			$ret->type = $match[1];
			$ret->zerofill = ( preg_match('/zerofill/i', $match[3]) ?'yes':'no' );
			if( preg_match('/^([0-9]+)$/', $match[2]) )	$ret->size = $match[2];	
		} else {
			$ret->type = $typeText;
		}

		return $ret;
	}

	public function convertType( $type ){

		switch ( mb_strtolower( $type ) ) {
			case 'int':
			case 'tinyint':
			case 'mediumint':
			case 'bigint':
			case 'bit':
			case 'smallint':
					return 'integer';
				break;	
			case 'float':
			case 'double':
			case 'decimal':
					return 'numeric';
				break;
			case 'varchar':
			case 'text':
			case 'char':
			case 'tinytext':
			case 'mediumtext':
			case 'longtext':
			case 'json':
			case 'enum':
					return 'string';
				break;	
			case 'date':
			case 'datetime':
			case 'timestamp':
					return 'date';
				break;
			default:
					return $type;
				break;
		}

	}


}
