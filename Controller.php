<?

class Controller {

    protected $retorno;
    protected $Model;
    protected $post;

    public function __construct($model) {

        require_once "$model.php";
        $this->Model = new $model();

        $this->retorno = [
            'status' => 'erro',
            'mensagem' => 'Ação não confirmada',
            'lista' => [],
            'dado' => []
        ];

        $this->post = json_decode(file_get_contents('php://input'), true);
    }

}
