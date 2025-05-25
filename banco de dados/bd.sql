
CREATE DATABASE Zero1Piscinas;
USE Zero1Piscinas;


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(20),
    endereco TEXT,
    senha VARCHAR(255),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_usuario ENUM('cliente', 'profissional') NOT NULL

);

CREATE TABLE clientes (
    id INT PRIMARY KEY,
    FOREIGN KEY (id) REFERENCES usuarios(id)
);

CREATE TABLE profissionais (
    id INT PRIMARY KEY,
    especialidades TEXT,
    experiencia_anos INT,
    FOREIGN KEY (id) REFERENCES usuarios(id) 
);

CREATE TABLE piscinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    tamanho VARCHAR(10),
    tipo VARCHAR(20),
    profundidade VARCHAR(10),
    data_instalacao DATE,
    servico_desejado VARCHAR(100),
    foto_piscina VARCHAR(255),
    status ENUM('pendente', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'pendente',
    resposta TEXT,
    data_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    piscina_id INT,
    profissional_id INT,
    tipo_servico ENUM('limpeza', 'manutencao', 'reparo','aquecimento_piscinas', 
    'acabamentos', 'construcao_reforma', 'instalacao_de_capas','automacao','tratamento_agua') NOT NULL,
    descricao TEXT,
    estatus ENUM('pendente', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'pendente',
    data_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_execucao DATETIME,
    preco DECIMAL(10,2),
    FOREIGN KEY (piscina_id) REFERENCES piscinas(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servico_id INT,
    estatus ENUM('pago', 'pendente', 'falhou') DEFAULT 'pendente',
    transacao_id VARCHAR(100),
    data_pagamento DATETIME,
    valor_pago DECIMAL(10,2),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);


INSERT INTO clientes (id) 
SELECT id FROM usuarios WHERE tipo_usuario = 'cliente';

select*from profissionais;
select*from clientes;

select*from usuarios;

$query_piscinas = "
    SELECT piscina.id, cliente.nome AS cliente_nome, cliente.email AS cliente_email, cliente.endereco AS cliente_endereco, 
           piscina.tamanho, piscina.tipo, piscina.profundidade, piscina.data_instalacao, piscina.servico_desejado, piscina.data_solicitacao
    FROM piscinas
    JOIN usuarios AS cliente ON piscina.id_cliente = cliente.id
    WHERE piscina.status = 'pendente'
    ORDER BY piscina.data_solicitacao DESC
";
