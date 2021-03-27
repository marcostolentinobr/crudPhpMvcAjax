<?

class Conexao {

    protected $pdo;

    public function __construct() {
        $this->pdo = new PDO(
                MYSQL_DBLIB . ':host=' . MYSQL_HOST . ';dbname=' . MYSQL_DBNAME,
                MYSQL_USERNAME, MYSQL_PASSWORD
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    protected function whereExecute($CONSULTA) {
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

}
