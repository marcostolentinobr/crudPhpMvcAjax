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

require_once 'Controller.php';

class Pessoa extends Controller {

    public function __construct() {
        parent::__construct('PessoaModel');

        try {

            //LISTAR
            if (@$this->post['ACAO'] == 'Listar') {
                $this->retorno['status'] = 'ok';
                $this->retorno['mensagem'] = 'Pessoas listadas';

                $DADOS = $this->Model->listar();
                $this->retorno['lista'] = $DADOS;
            }
            //INCLUIR
            elseif (@$this->post['ACAO'] == 'Incluir') {
                $this->retorno['status'] = 'erro';
                $this->retorno['mensagem'] = $this->post['NOME'] . ' já existe';

                $existeDado = $this->Model->listar(['NOME' => $this->post['NOME']]);
                if (!$existeDado) {
                    $DADOS = $this->dados();
                    $execute = $this->Model->incluir($DADOS);

                    $this->retorno['status'] = 'ok';
                    $this->retorno['mensagem'] = $this->post['NOME'] . ' incluído(a)';
                }
            }
            //EXCLUIR
            elseif (@$this->post['ACAO'] == 'Excluir') {
                $this->retorno['status'] = 'ok';
                $this->retorno['mensagem'] = $this->post['descricao'] . ' excluído(a)';
                $execute = $this->Model->excluir(['ID_PESSOA' => $this->post['ID_PESSOA']]);
            }
            //Consulta
            elseif (@$this->post['ACAO'] == 'Buscar') {
                $this->retorno['status'] = 'ok';
                $this->retorno['mensagem'] = 'Pessoa listada';

                $DADO = $this->Model->listar(['ID_PESSOA' => $this->post['ID_PESSOA']]);
                $this->retorno['dado'] = $DADO[0];

                if (!$DADO) {
                    $this->retorno['status'] = 'erro';
                    $this->retorno['mensagem'] = 'Pessoa não localizada';
                }
            }
            //ALTERAR
            elseif (@$this->post['ACAO'] == 'Alterar') {
                $this->retorno['status'] = 'erro';
                $this->retorno['mensagem'] = $this->post['NOME'] . ' já existe';

                $DADO = @$this->Model->listar(['NOME' => $this->post['NOME']])[0];
                if (!$DADO || $DADO['ID_PESSOA'] == $this->post['ID_PESSOA']) {
                    $this->retorno['status'] = 'ok';
                    $this->retorno['mensagem'] = $this->post['NOME'] . ' alterado(a)';

                    $DADOS = $this->dados();
                    $DADOS['ID_PESSOA'] = $this->post['ID_PESSOA'];
                    $execute = $this->Model->alterar($DADOS);
                }
            }
        } catch (Exception $ex) {
            $this->retorno = [
                'status' => 'erro',
                'mensagem' => $ex->getMessage()
            ];
        }

        exit(json_encode($this->retorno));
    }

    private function dados() {
        return [
            'NOME' => $this->post['NOME'],
            'UF' => $this->post['UF'],
            'OBSERVACAO' => $this->post['OBSERVACAO']
        ];
    }

}

$Pessoa = new Pessoa();
