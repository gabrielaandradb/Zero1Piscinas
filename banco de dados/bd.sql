
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
    status ENUM('pendente', 'respondido') DEFAULT 'pendente',
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


INSERT INTO usuarios (nome, email, telefone, endereco, senha, tipo_usuario) 
VALUES 
('Rafael Andrade', 'rafael@profissional.com', '6199901-1234', 'Rua 1, Brasília', 'raf1209', 'profissional'),
('João Oliveira', 'joaooliveira@profissional.com', '6198801-1234', 'Rua 2, Brasília', 'jo3008', 'profissional'),
('Kleber Andrade', 'kleber@profissional.com', '6199902-4321', 'Rua 3, Brasília', 'kb0300', 'profissional'),
('Gabriel Santos', 'gabriel@profissional.com', '6198802-4321', 'Rua 4, Brasília', 'gs1234', 'profissional'),
('Gabriela Andrade', 'gabrielaab@gmail.com', '6199664-1234', 'Rua 5, Brasília', '12345g', 'cliente'),
('Ilton Bento', 'iltonbento@gmail.com', '6197701-9876', 'Rua 6, Brasília', 'ie2526', 'cliente');

INSERT INTO clientes (id) 
SELECT id FROM usuarios WHERE tipo_usuario = 'cliente';

-- Inserir dados na tabela profissionais
INSERT INTO profissionais (id, especialidades, experiencia_anos)
VALUES 
(1, null, null),
(2, null, null),
(3, null, null),
(4, null, null);
select*from profissionais;
select*from clientes;

select*from usuarios;