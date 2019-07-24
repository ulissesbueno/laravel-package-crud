<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Ubs\Crud\Library\DynSchemaRequestLibrary;
use Validator;

use App\Models\{model_name} as Model;

class {model_name}Controller extends Controller
{
    public $page = [
        "title" => "{title}",
        "custom_js" => "{route_name}",
        "addButton" => [
            "title" => "{button_add}",
            "url" => "{route_name}.form"
        ],
        "dataTable" => [
            "column" => [
                {data_table_columns}
            ],
            "editable" => true,
            "delete_url" => '{route_name}/delete/{id}',
            "button_delete_title" => '{button_delete}'
        ],
        "Form" =>[  "title" => "{title_form}",
            "action" => "{route_name}.save",
            "callback" => "{callback_form}",
            "redirect" => "{route_name}.index",
            "id" => "{id_form}",
            "buttons" => [
                "submit" => [
                    "label" => "{button_save}",
                    "class" => "btn btn-primary"
                ]
            ],
            "view" => null
        ]

    ];

    public function __construct( Model $Model, DynSchemaRequestLibrary $DynSchemaRequestLibrary )
    {
        $this->Model = $Model;
        $this->DynSchemaRequest = $DynSchemaRequestLibrary;

        $this->page['dataTable']['render'] = function( $row, $index, $value = null ){

            if( method_exists( $this, 'render_'.$index ) ){
                return $this->{ 'render_'.$index }( $row, $index, $value );
            }

            return $value;
        };
    }

    public function getData( Request $request ){

        return $this->Model
            ->selectRaw( " * " )
            ->get()->toArray();
    }

}
