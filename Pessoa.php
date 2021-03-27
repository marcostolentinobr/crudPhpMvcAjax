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

//CONEXAO
$PDO = new PDO(
        MYSQL_DBLIB . ':host=' . MYSQL_HOST . ';dbname=' . MYSQL_DBNAME,
        MYSQL_USERNAME, MYSQL_PASSWORD
);
$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Json para Post
$_POST = json_decode(file_get_contents('php://input'), true);

function whereExecute($CONSULTA) {
    $retorno = [
        'where' => '',
        'execute' => [],
    ];

    $WHERE = [];
    foreach ($CONSULTA as $coluna => $valor) {
        $retorno['execute'][":$coluna"] = $valor;
        $WHERE[] = " $coluna = :$coluna";
    }

    if ($WHERE) {
        $retorno['where'] = ' WHERE ' . implode(' AND ', $WHERE);
    }

    return $retorno;
}

//LISCAR
function listar($CONSULTA = []) {
    global $PDO;

    $whereExecute = whereExecute($CONSULTA);
    $sql = "SELECT * FROM PESSOA $whereExecute[where] ORDER BY NOME";
    $prepare = $PDO->prepare($sql);
    $prepare->execute($whereExecute['execute']);

    $DADOS = $prepare->fetchAll(PDO::FETCH_ASSOC);
    return $DADOS;
}

//INCLUIR
function incluir($DADOS) {
    global $PDO;

    $prepare = $PDO->prepare('
        INSERT INTO PESSOA 
            ( NOME, UF, OBSERVACAO) VALUES 
            (:NOME,:UF,:OBSERVACAO)
    ');

    return $prepare->execute($DADOS);
}

//EXCLUIR
function excluir($DADOS) {
    global $PDO;

    $prepare = $PDO->prepare('
        DELETE FROM PESSOA WHERE ID_PESSOA = :ID_PESSOA
    ');

    return $prepare->execute($DADOS);
}

//ALTERAR
function alterar($DADOS) {
    global $PDO;

    $prepare = $PDO->prepare('
        UPDATE PESSOA SET NOME = :NOME, 
                            UF = :UF,
                    OBSERVACAO = :OBSERVACAO
         WHERE ID_PESSOA = :ID_PESSOA
    ');

    return $prepare->execute($DADOS);
}

function dados() {
    return [
        'NOME' => $_POST['NOME'],
        'UF' => $_POST['UF'],
        'OBSERVACAO' => $_POST['OBSERVACAO']
    ];
}

//RETORNO - inicio
$retorno = [
    'status' => 'erro',
    'mensagem' => 'Ação não confirmada',
    'lista' => [],
    'dado' => ''
];

class Pessoa {

    public function __construct() {
        global $PDO;

        try {

            //LISTAR
            if (@$_POST['ACAO'] == 'Listar') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Pessoas listadas';

                $DADOS = listar();
                $retorno['lista'] = $DADOS;
            }
            //INCLUIR
            elseif (@$_POST['ACAO'] == 'Incluir') {
                $retorno['status'] = 'erro';
                $retorno['mensagem'] = "$_POST[NOME] já existe";

                $existeDado = listar(['NOME' => $_POST['NOME']]);
                if (!$existeDado) {
                    $DADOS = dados();
                    $execute = incluir($DADOS);

                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = "$_POST[NOME] incluído(a)";
                }
            }
            //EXCLUIR
            elseif (@$_POST['ACAO'] == 'Excluir') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = "$_POST[descricao] excluído(a)";
                $execute = excluir(['ID_PESSOA' => $_POST['ID_PESSOA']]);
            }
            //Consulta
            elseif (@$_POST['ACAO'] == 'Buscar') {
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Pessoa listada';

                $DADO = listar(['ID_PESSOA' => $_POST['ID_PESSOA']]);
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

                $DADO = @listar(['NOME' => $_POST['NOME']])[0];
                if (!$DADO || $DADO['ID_PESSOA'] == $_POST['ID_PESSOA']) {
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = "$_POST[NOME] alterado(a)";

                    $DADOS = dados();
                    $DADOS['ID_PESSOA'] = $_POST['ID_PESSOA'];
                    $execute = alterar($DADOS);
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

}
$Pessoa = new Pessoa();