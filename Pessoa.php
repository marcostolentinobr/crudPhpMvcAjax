<?

//Configurações do banco de dados
define('MYSQL_DBLIB', 'mysql');
define('MYSQL_HOST', '127.0.0.1');
define('MYSQL_DBNAME', 'CRUD');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');

//PRINT_R PRE
function pr($dado, $print_r = true) {
    echo '<pre>';
    if ($print_r) {
        print_r($dado);
    } else {
        var_dump($dado);
    }
}

require_once 'PessoaModel.php';

class Pessoa extends PessoaModel {

    public function __construct() {
        parent::__construct();

        //RETORNO - inicio
        $retorno = [
            'status' => 'erro',
            'mensagem' => 'Ação não confirmada',
            'lista' => [],
            'dado' => ''
        ];

        //Json para Post
        $_POST = json_decode(file_get_contents('php://input'), true);

        try {

            //LISTAR
            if (@$_POST['ACAO'] == 'Listar') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Pessoas listadas';

                $DADOS = $this->listar();
                $retorno['lista'] = $DADOS;
            }
            //INCLUIR
            elseif (@$_POST['ACAO'] == 'Incluir') {
                $retorno['status'] = 'erro';
                $retorno['mensagem'] = "$_POST[NOME] já existe";

                $existeDado = $this->listar(['NOME' => $_POST['NOME']]);
                if (!$existeDado) {
                    $DADOS = $this->dados();
                    $execute = $this->incluir($DADOS);

                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = "$_POST[NOME] incluído(a)";
                }
            }
            //EXCLUIR
            elseif (@$_POST['ACAO'] == 'Excluir') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = "$_POST[descricao] excluído(a)";
                $execute = $this->excluir(['ID_PESSOA' => $_POST['ID_PESSOA']]);
            }
            //Consulta
            elseif (@$_POST['ACAO'] == 'Buscar') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Pessoa listada';

                $DADO = $this->listar(['ID_PESSOA' => $_POST['ID_PESSOA']]);
                $retorno['dado'] = $DADO[0];

                if (!$DADO) {
                    $retorno['status'] = 'erro';
                    $retorno['mensagem'] = 'Pessoa não localizada';
                }
            }
            //ALTERAR
            elseif (@$_POST['ACAO'] == 'Alterar') {
                $retorno['status'] = 'erro';
                $retorno['mensagem'] = "$_POST[NOME] já existe";

                $DADO = @$this->listar(['NOME' => $_POST['NOME']])[0];
                if (!$DADO || $DADO['ID_PESSOA'] == $_POST['ID_PESSOA']) {
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = "$_POST[NOME] alterado(a)";

                    $DADOS = $this->dados();
                    $DADOS['ID_PESSOA'] = $_POST['ID_PESSOA'];
                    $execute = $this->alterar($DADOS);
                }
            }
        } catch (Exception $ex) {
            $retorno = [
                'status' => 'erro',
                'mensagem' => $ex->getMessage()
            ];
        }

        exit(json_encode($retorno));
    }

    private function dados() {
        return [
            'NOME' => $_POST['NOME'],
            'UF' => $_POST['UF'],
            'OBSERVACAO' => $_POST['OBSERVACAO']
        ];
    }

}

$Pessoa = new Pessoa();
