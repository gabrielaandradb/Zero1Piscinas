drop database Zero1Piscinas;
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
    tamanho ENUM('pequena', 'media', 'grande'),
    tipo VARCHAR(20),
    profundidade VARCHAR(10),
    data_instalacao DATE,
    servico_desejado VARCHAR(100),
    status ENUM('pendente', 'concluido', 'cancelado') DEFAULT 'pendente',
    resposta TEXT,
    data_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    preco DECIMAL(10, 2),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);



SET SQL_SAFE_UPDATES = 0;
UPDATE piscinas
SET preco = 
    CASE 
        WHEN tamanho = 'pequena' AND servico_desejado = 'Limpeza de Piscinas' THEN 150.00
        WHEN tamanho = 'media' AND servico_desejado = 'Limpeza de Piscinas' THEN 250.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Limpeza de Piscinas' THEN 350.00

        WHEN tamanho = 'pequena' AND servico_desejado = 'Manutenção' THEN 200.00
        WHEN tamanho = 'media' AND servico_desejado = 'Manutenção' THEN 300.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Manutenção' THEN 400.00

        WHEN tamanho = 'pequena' AND servico_desejado = 'Reparos' THEN 300.00
        WHEN tamanho = 'media' AND servico_desejado = 'Reparos' THEN 450.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Reparos' THEN 600.00

        WHEN tamanho = 'pequena' AND servico_desejado = 'Aquecimento de Piscinas' THEN 600.00
        WHEN tamanho = 'media' AND servico_desejado = 'Aquecimento de Piscinas' THEN 700.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Aquecimento de Piscinas' THEN 800.00

        WHEN tamanho = 'pequena' AND servico_desejado = 'Instalação de Capas Protetoras' THEN 100.00
        WHEN tamanho = 'media' AND servico_desejado = 'Instalação de Capas Protetoras' THEN 150.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Instalação de Capas Protetoras' THEN 200.00

        WHEN tamanho = 'pequena' AND servico_desejado = 'Tratamento da Água' THEN 250.00
        WHEN tamanho = 'media' AND servico_desejado = 'Tratamento da Água' THEN 350.00
        WHEN tamanho = 'grande' AND servico_desejado = 'Tratamento da Água' THEN 450.00
        ELSE 0.00
    END;


CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    piscina_id INT,
    profissional_id INT,
    tipo_servico ENUM('limpeza', 'manutencao', 'reparo','aquecimento_piscinas', 
    'instalacao_de_capas','tratamento_agua') NOT NULL,
    descricao TEXT,
    estatus ENUM('pendente', 'concluido', 'cancelado') DEFAULT 'pendente',
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

INSERT INTO profissionais (id) 
SELECT id FROM usuarios WHERE tipo_usuario = 'profissional';

select*from profissionais;
select*from clientes;

SELECT 
    s.id, 
    s.tipo_servico, 
    s.descricao, 
    s.estatus, 
    s.preco, 
    s.data_solicitacao, 
    s.data_execucao, 
    p.tamanho, 
    p.tipo AS tipo_piscina, 
    u.nome AS cliente_nome, 
    u.email AS cliente_email
FROM servicos s
JOIN piscinas p ON s.piscina_id = p.id
JOIN usuarios u ON p.cliente_id = u.id
WHERE s.id = 1 
AND s.profissional_id = 2;

SELECT * FROM piscinas WHERE id;

SELECT * FROM piscinas;

SELECT 
    p.id,
    u.nome AS cliente_nome,
    u.email AS cliente_email,
    u.endereco AS cliente_endereco,  -- endereço vem da tabela usuarios
    p.tamanho,
    p.tipo,
    p.profundidade,
    p.data_instalacao,
    p.servico_desejado
FROM piscinas p
JOIN usuarios u ON p.cliente_id = u.id
WHERE p.status = 'pendente'
ORDER BY p.data_solicitacao DESC;

SET SQL_SAFE_UPDATES = 1;

