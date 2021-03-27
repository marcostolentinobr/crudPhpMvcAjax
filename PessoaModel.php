<?

require_once 'Conexao.php';
class PessoaModel extends Conexao {

    protected function listar($CONSULTA = []) {
        $whereExecute = $this->whereExecute($CONSULTA);
        $sql = "SELECT * FROM PESSOA $whereExecute[where] ORDER BY NOME";

        $prepare = $this->pdo->prepare($sql);
        $prepare->execute($whereExecute['execute']);

        $DADOS = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $DADOS;
    }

    protected function incluir($DADOS) {
        $prepare = $this->pdo->prepare('
            INSERT INTO PESSOA 
                ( NOME, UF, OBSERVACAO) VALUES 
                (:NOME,:UF,:OBSERVACAO)
        ');

        return $prepare->execute($DADOS);
    }

    protected function excluir($DADOS) {
        $prepare = $this->pdo->prepare('
            DELETE FROM PESSOA WHERE ID_PESSOA = :ID_PESSOA
        ');

        return $prepare->execute($DADOS);
    }

    protected function alterar($DADOS) {
        $prepare = $this->pdo->prepare('
            UPDATE PESSOA SET NOME = :NOME, 
                                UF = :UF,
                        OBSERVACAO = :OBSERVACAO
             WHERE ID_PESSOA = :ID_PESSOA
        ');

        return $prepare->execute($DADOS);
    }

}
