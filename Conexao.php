<?php
class Conexao {
    private static $instance = null;

    private function __construct() { }

    public static function getInstance() {
        if (self::$instance == null) {
            try {
                self::$instance = new PDO('mysql:host=localhost;dbname=Zero1Piscinas', 'root', '');
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Erro na conexão: " . $e->getMessage();
            }
        }
        return self::$instance;
    }
}
?>
