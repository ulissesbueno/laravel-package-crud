<?php
namespace Ubs\Crud;

use Illuminate\Console\Command;

class AppCrudCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar Crud';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $askData = [];

        $this->info('Dados Necessários!');

        $askData['title'] = $this->ask('Título da Lista');
        $askData['model_name'] = $this->ask('Nome da Model');
        $askData['table_name'] = $this->ask('Nome da Tabela');
        $askData['route_name'] = $this->ask('Nome da Rota');

        $this->info('Botão Adicionar!');
        $askData['button_add'] = $this->ask('Título botão') ?? 'Adicionar';

        $this->info('Botão Deletar Registro!');
        $askData['button_delete'] = $this->ask('Título botão') ?? 'Excluir';

        $this->info('Formulário de Cadastro!');

        $askData['id_form'] = $this->ask('ID do formulário');
        $askData['title_form'] = $this->ask('Título do formulário');
        $askData['callback_form'] = $this->ask('Nome da função (js) após envio do formulário');
        $askData['button_save'] = $this->ask('Botão do formulário') ?? "Gravar";

        // Criar Model
        $new_file_model = 'app/Models/'.$askData['model_name'].".php";
        $model_file_model = __DIR__.'/CrudCreateFiles/Model.php';
        $content_file_model = file_get_contents( $model_file_model );

        // settingDF
        $columns = \DB::select('show columns from ' . $askData['table_name']);
        $df = [];
        $fillable = [];
        $data_table_columns = [];

        foreach( $columns as $col ){
            $fillable[] = "'".$col->Field."'";
            if( $col->Key == 'PRI' ){
                $df[]= " '". $col->Field ."' => [ 'type' => 'hidden' ] ";
            } else {
                $df[]= " '". $col->Field ."' => [ 'label' => '". $col->Field ."' ] ";
            }

            $data_table_columns[] = "[ 'data' => '". $col->Field ."', 'title' => '". $col->Field ."' ]";
        }

        $askData['settingDF'] = implode(',',$df);
        $askData['fillable'] = implode( ",", $fillable );
        $askData['data_table_columns'] = implode(',',$data_table_columns);

        foreach( $askData as $index => $value ){
            $content_file_model = preg_replace( "/\{$index\}/", $value, $content_file_model );
        }
        file_put_contents( $new_file_model , $content_file_model );


        // Criar Controller
        $new_file_controller = 'app/Http/Controllers/'.$askData['model_name']."Controller.php";
        $model_file_controller = __DIR__.'/CrudCreateFiles/Controller.php';
        $content_file_controller = file_get_contents( $model_file_controller );
        foreach( $askData as $index => $value ){
            $content_file_controller = preg_replace( "/\{$index\}/", $value, $content_file_controller );
        }
        file_put_contents( $new_file_controller , $content_file_controller );

        // Criar Routes
        $new_file_route = 'routes/'.$askData['route_name'].".php";
        $model_file_route = __DIR__.'/CrudCreateFiles/route.php';
        $content_file_route = file_get_contents( $model_file_route );
        foreach( $askData as $index => $value ){
            $content_file_route = preg_replace( "/\{$index\}/", $value, $content_file_route );
        }
        file_put_contents( $new_file_route , $content_file_route );

        // Criar Arquivo custom init
        $new_file_custom = 'public/js/'.$askData['route_name'].'.init.js';
        $model_file_custom = __DIR__.'/CrudCreateFiles/custom.init.js';
        copy( $model_file_custom, $new_file_custom );
        if( $askData['callback_form'] ){
            file_put_contents( $new_file_custom, "\nfunction ".$askData['callback_form']."(){}", FILE_APPEND );
        }

        $this->info('Crud Criado!');
    }
}
