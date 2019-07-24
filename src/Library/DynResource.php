<?php

namespace Ubs\Crud\Library;

class DynResource{

	public $returnType = 'json';	
	public function Response( $data, $http = 200 ){
		if( $this->returnType == 'json' ){			
			return response()->json( $data , $http );	
		} else {
			return $data;
		}
	}


}
