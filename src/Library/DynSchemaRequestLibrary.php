<?php

namespace Ubs\Crud\Library;

use Illuminate\Support\Facades\DB;
use Ubs\Crud\Library\DynLibrary;

class DynSchemaRequestLibrary extends DynLibrary {
	
	private $last_schema ;

	public function request( $schema, $extra_validate = [] ){

		$rule = [] ;
		$columns = DB::select('show columns from ' . $schema);
		//print_r( $columns );

		$this->last_schema = [];

		$or = [];
		foreach ($columns as $value) {

			$this->last_schema[] = $value->Field;

			if( $value->Key == 'PRI' ) continue;
		   	$or[] = ( $value->Null == "NO" ? 'required' : 'nullable' );	

		   	$type = $this->parse( $value->Type  );
		   	if( array_key_exists( $value->Field, $extra_validate ) && 
		   		array_key_exists( 'type', $extra_validate[$value->Field] ) ){
		   		$type->type = $extra_validate[$value->Field]['type'];
		   	} else {
		   		$type->type = $this->convertType( ( $type->zerofill == 'yes' ? 'numeric' : $type->type ) );
		   	}

		   	if( $type->type ) $or[] = $type->type;
		   	$rule[$value->Field] = implode('|',$or);
		   	$or = [];
		   	//echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ;
		}


        return $rule;
	}


	public function clear( $request ){
		
		$new_req = [];
		foreach( $this->last_schema as $ls ){
			if( array_key_exists( $ls , $request ) ) $new_req[$ls] = $request[$ls];
		}
		return $new_req;
	}

}
