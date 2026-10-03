-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 03/10/2026 às 18:27
-- Versão do servidor: 11.8.9-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u839226731_farol`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acessos_mensagem`
--

CREATE TABLE `acessos_mensagem` (
  `id` int(11) NOT NULL,
  `cv` varchar(20) NOT NULL,
  `id_etiqueta` int(11) NOT NULL,
  `ip_acesso` varchar(45) DEFAULT NULL,
  `agente_usuario` text DEFAULT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `assinatura` text DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `total_acessos` int(11) DEFAULT 1,
  `hash_transacao` varchar(64) DEFAULT NULL,
  `entregue` tinyint(1) NOT NULL DEFAULT 0,
  `remetenteinverso` varchar(100) DEFAULT NULL,
  `mensagem_cad` varchar(50) DEFAULT NULL,
  `valor` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `acessos_transmissao`
--

CREATE TABLE `acessos_transmissao` (
  `id` int(11) NOT NULL,
  `peer_id` varchar(100) DEFAULT NULL,
  `cv` varchar(50) DEFAULT NULL,
  `id_programa` varchar(50) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `horario` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `last_activity` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `documento_cliente` varchar(14) DEFAULT NULL,
  `nome_cliente` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `consultorio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `idade` int(11) NOT NULL,
  `escola_id` int(11) DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `telefone` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `assentos`
--

CREATE TABLE `assentos` (
  `id` int(11) NOT NULL,
  `voo_id` int(11) NOT NULL,
  `numero_assento` varchar(10) NOT NULL,
  `pago` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `assinantenv3`
--

CREATE TABLE `assinantenv3` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nome_razao` varchar(255) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `assinaturas_pdf`
--

CREATE TABLE `assinaturas_pdf` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pix_id` varchar(50) NOT NULL,
  `assinatura_token` varchar(100) DEFAULT NULL,
  `token_devolucao_trator` varchar(255) DEFAULT NULL,
  `data_token_devolucao` datetime DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `auditoria_conversao`
--

CREATE TABLE `auditoria_conversao` (
  `id` int(11) NOT NULL,
  `uid_antigo` varchar(50) DEFAULT NULL,
  `uid_novo` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `datahora` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `auras`
--

CREATE TABLE `auras` (
  `id` int(11) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_confirmacao` datetime NOT NULL,
  `transacao_id` varchar(50) NOT NULL,
  `pagador_email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aura_balance`
--

CREATE TABLE `aura_balance` (
  `username` varchar(50) NOT NULL,
  `saldo_aura` int(11) DEFAULT 0,
  `saldo_total` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aura_compromisso`
--

CREATE TABLE `aura_compromisso` (
  `id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `criador` varchar(255) NOT NULL,
  `recebedor` varchar(100) DEFAULT NULL,
  `valor_aura` int(11) NOT NULL,
  `data_pagamento` datetime DEFAULT current_timestamp(),
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aura_envios`
--

CREATE TABLE `aura_envios` (
  `id` int(11) NOT NULL,
  `uid_confirmacao` varchar(64) DEFAULT NULL,
  `enviado_por` varchar(100) DEFAULT NULL,
  `data_envio` timestamp NULL DEFAULT current_timestamp(),
  `data_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aura_recebida`
--

CREATE TABLE `aura_recebida` (
  `id` int(11) NOT NULL,
  `de_usuario` varchar(100) NOT NULL,
  `para_usuario` varchar(100) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `data_recebida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `barcos`
--

CREATE TABLE `barcos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `valor_atual` decimal(10,2) DEFAULT 0.00,
  `valor_proxima_troca` decimal(10,2) DEFAULT 0.00,
  `data_proxima_troca` date DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `email_cadastrado` varchar(255) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `username` varchar(150) NOT NULL,
  `aprovacao` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bixos`
--

CREATE TABLE `bixos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `peso_arroba` decimal(5,2) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `blocos`
--

CREATE TABLE `blocos` (
  `id` int(11) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `codigo` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cadastro_produto`
--

CREATE TABLE `cadastro_produto` (
  `nome` text NOT NULL,
  `valor` text NOT NULL,
  `quantidade` text NOT NULL,
  `total` text NOT NULL,
  `id` int(11) NOT NULL,
  `imagem` text NOT NULL,
  `url_buy` text NOT NULL,
  `categoria` text NOT NULL,
  `idtrator` varchar(255) DEFAULT NULL,
  `eq_user` text NOT NULL,
  `leilao` varchar(250) NOT NULL,
  `nuvem` varchar(140) NOT NULL,
  `cidadetrator` varchar(140) NOT NULL,
  `estadotrator` varchar(140) NOT NULL,
  `destacar` int(11) NOT NULL,
  `numeroEtiqueta` varchar(255) DEFAULT NULL,
  `data_ultimo_destaque` datetime DEFAULT NULL,
  `aura` int(11) NOT NULL DEFAULT 0,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `estrelinhas` int(11) DEFAULT 0,
  `estoque` int(11) DEFAULT 0,
  `eq_tipo` varchar(50) DEFAULT NULL,
  `item_tipo` varchar(50) DEFAULT NULL,
  `contrato` varchar(50) DEFAULT NULL,
  `carrinho_id` varchar(50) DEFAULT NULL,
  `data_nota` date DEFAULT NULL,
  `peso` decimal(6,2) NOT NULL,
  `cepOrigem` varchar(8) NOT NULL,
  `cepDestino` varchar(8) NOT NULL,
  `servico` varchar(10) NOT NULL,
  `comprimento` decimal(6,2) NOT NULL,
  `altura` decimal(6,2) NOT NULL,
  `largura` decimal(6,2) NOT NULL,
  `diametro` decimal(6,2) NOT NULL,
  `assinantenv9` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caixa_postal`
--

CREATE TABLE `caixa_postal` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `documento` varchar(100) DEFAULT NULL,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caminhoes_heycar`
--

CREATE TABLE `caminhoes_heycar` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano` int(11) NOT NULL,
  `placa` varchar(50) NOT NULL,
  `capacidade` decimal(10,2) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `canecas_personalizadas`
--

CREATE TABLE `canecas_personalizadas` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `imagem` longtext NOT NULL,
  `rua` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `complemento` varchar(150) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT 70.00,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `status_pagamento` varchar(20) DEFAULT 'aguardando',
  `comprovante_hash` varchar(64) DEFAULT NULL,
  `entregue` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caronas`
--

CREATE TABLE `caronas` (
  `id` int(11) NOT NULL,
  `motorista` varchar(100) NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `partida` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `vagas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caronas_passageiros`
--

CREATE TABLE `caronas_passageiros` (
  `id` int(11) NOT NULL,
  `carona_id` int(11) NOT NULL,
  `passageiro` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caronas_passageiros_ufsm`
--

CREATE TABLE `caronas_passageiros_ufsm` (
  `id` int(11) NOT NULL,
  `carona_id` int(11) NOT NULL,
  `passageiro` varchar(100) NOT NULL,
  `codigo_validacao` varchar(50) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caronas_ufsm`
--

CREATE TABLE `caronas_ufsm` (
  `id` int(11) NOT NULL,
  `motorista` varchar(100) NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `partida` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `vagas` int(11) NOT NULL,
  `telefone` varchar(50) NOT NULL,
  `horario_saida` varchar(10) NOT NULL,
  `data_carona` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinhos`
--

CREATE TABLE `carrinhos` (
  `id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `dados` text NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_compras`
--

CREATE TABLE `carrinho_compras` (
  `id` int(11) NOT NULL,
  `cv` varchar(50) NOT NULL,
  `id_etiqueta` varchar(50) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `imagem` text NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_concluido`
--

CREATE TABLE `carrinho_concluido` (
  `id` int(11) NOT NULL,
  `cv` varchar(100) NOT NULL,
  `id_etiqueta` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `data_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `catalogo_musica`
--

CREATE TABLE `catalogo_musica` (
  `id` int(11) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `ip_acesso` varchar(45) NOT NULL,
  `qrcode` varchar(255) NOT NULL,
  `eq_user` varchar(100) NOT NULL,
  `nome_recebedor` varchar(50) NOT NULL,
  `cidade_recebedor` varchar(255) NOT NULL,
  `destino_recebedor` varchar(255) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `documento` varchar(100) NOT NULL,
  `foto_recebedor` varchar(255) DEFAULT NULL,
  `valor_de_aura` int(10) UNSIGNED NOT NULL,
  `data_now` date NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `certificados`
--

CREATE TABLE `certificados` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `link_audio` varchar(255) NOT NULL,
  `hash_audio` varchar(255) NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `certificado_ableton`
--

CREATE TABLE `certificado_ableton` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sala` varchar(100) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `url_certificado` varchar(255) NOT NULL,
  `data_geracao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamadas`
--

CREATE TABLE `chamadas` (
  `id` int(11) NOT NULL,
  `compromisso_id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `participants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`participants`)),
  `offer` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `candidates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`candidates`)),
  `status` varchar(20) DEFAULT 'stopped',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `chats_encerrados`
--

CREATE TABLE `chats_encerrados` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `outro_usuario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_mensagens`
--

CREATE TABLE `chat_mensagens` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `datahora` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades_coords`
--

CREATE TABLE `cidades_coords` (
  `id` int(11) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `compact_fotos`
--

CREATE TABLE `compact_fotos` (
  `id` int(11) NOT NULL,
  `imovel_id` int(11) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `compact_imoveis`
--

CREATE TABLE `compact_imoveis` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `contrato` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dormitorios` int(11) DEFAULT 0,
  `banheiros` int(11) DEFAULT 0,
  `garagem` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `rua` varchar(150) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `complemento` varchar(150) DEFAULT NULL,
  `telefone_dono` varchar(20) NOT NULL,
  `telefone_site` varchar(20) NOT NULL,
  `usuario_cadastro` varchar(100) NOT NULL,
  `link_principal` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'ativo',
  `level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `consorcio_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `entrada` decimal(10,2) NOT NULL,
  `hash_comprovante` varchar(64) NOT NULL,
  `data_compra` datetime NOT NULL,
  `rua` varchar(150) NOT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `nicknamecharacter` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `status_pagamento` enum('pendente','entregue') NOT NULL DEFAULT 'pendente',
  `frete` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `aplicacao` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `compras_aura`
--

CREATE TABLE `compras_aura` (
  `id` int(11) NOT NULL,
  `caixa_postal` varchar(50) DEFAULT NULL,
  `valor_aura` int(11) NOT NULL,
  `valor_reais` decimal(10,2) NOT NULL,
  `wallet` varchar(100) DEFAULT NULL,
  `hash_transacao` varchar(100) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `compras_aurea`
--

CREATE TABLE `compras_aurea` (
  `id` int(11) NOT NULL,
  `caixa_postal` varchar(50) DEFAULT NULL,
  `valor_aurea` int(11) DEFAULT NULL,
  `valor_reais` decimal(10,2) DEFAULT NULL,
  `wallet` varchar(100) DEFAULT NULL,
  `hash_transacao` varchar(100) DEFAULT NULL,
  `data_hora` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comprovantes_aura`
--

CREATE TABLE `comprovantes_aura` (
  `id` int(11) NOT NULL,
  `remetente` varchar(50) DEFAULT NULL,
  `destinatario` varchar(50) DEFAULT NULL,
  `valor` int(10) UNSIGNED NOT NULL,
  `caixa_origem` varchar(100) DEFAULT NULL,
  `caixa_destino` varchar(100) DEFAULT NULL,
  `data_transacao` datetime DEFAULT current_timestamp(),
  `tipo_transacao` enum('CP','IOTA','FAROLQR') DEFAULT 'CP',
  `data_registro` datetime DEFAULT current_timestamp(),
  `assinatura` varchar(64) NOT NULL,
  `transacao_id` bigint(20) NOT NULL,
  `voo_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comprovantes_quitacao`
--

CREATE TABLE `comprovantes_quitacao` (
  `id` int(11) NOT NULL,
  `emprestimo_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `valor_quitado` decimal(10,2) NOT NULL,
  `data_quitacao` datetime NOT NULL,
  `assinatura_contrato` varchar(64) NOT NULL,
  `assinatura_quitacao` varchar(64) NOT NULL,
  `assinatura` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `confirmado_aura`
--

CREATE TABLE `confirmado_aura` (
  `id` int(11) NOT NULL,
  `comprador` varchar(100) NOT NULL,
  `dono_loja` varchar(100) NOT NULL,
  `nome_site` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `caixa_destino` varchar(50) NOT NULL,
  `token_loja` varchar(255) NOT NULL,
  `data_confirmacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consorcio_cadastro`
--

CREATE TABLE `consorcio_cadastro` (
  `id` int(11) NOT NULL,
  `consulta` varchar(250) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `entrada` decimal(10,2) DEFAULT NULL,
  `parcelas` int(11) DEFAULT NULL,
  `valor_parcela` decimal(10,2) DEFAULT NULL,
  `eq_user` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `data_cadastro` date DEFAULT NULL,
  `imagem` varchar(255) NOT NULL,
  `aprovacao` tinyint(1) NOT NULL DEFAULT 0,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `servico` varchar(10) DEFAULT '41106',
  `cep_origem` varchar(9) DEFAULT '78850000',
  `peso` decimal(5,2) DEFAULT 1.00,
  `altura` int(11) DEFAULT 10,
  `largura` int(11) DEFAULT 15,
  `comprimento` int(11) DEFAULT 20,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `lc` varchar(255) NOT NULL,
  `hectare` decimal(10,2) NOT NULL,
  `valor_hectare` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consorcio_cadastro2`
--

CREATE TABLE `consorcio_cadastro2` (
  `id` int(11) NOT NULL,
  `consulta` varchar(255) NOT NULL,
  `valor_total` decimal(12,2) NOT NULL,
  `entrada` decimal(12,2) DEFAULT NULL,
  `eq_user` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `cep_origem` varchar(20) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `comprimento` decimal(10,2) DEFAULT NULL,
  `servico` varchar(100) DEFAULT NULL,
  `lc` varchar(100) DEFAULT NULL,
  `hectare` decimal(12,2) DEFAULT NULL,
  `valor_hectare` decimal(12,2) DEFAULT NULL,
  `aprovacao` tinyint(1) DEFAULT 0,
  `cep` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consorcio_cadastro3`
--

CREATE TABLE `consorcio_cadastro3` (
  `id` int(11) NOT NULL,
  `consulta` varchar(255) NOT NULL,
  `valor_total` decimal(12,2) NOT NULL,
  `entrada` decimal(12,2) DEFAULT NULL,
  `eq_user` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  `cep_origem` varchar(20) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `comprimento` decimal(10,2) DEFAULT NULL,
  `servico` varchar(100) DEFAULT NULL,
  `lc` varchar(100) DEFAULT NULL,
  `hectare` decimal(12,2) DEFAULT NULL,
  `valor_hectare` decimal(12,2) DEFAULT NULL,
  `aprovacao` tinyint(1) DEFAULT 0,
  `cep` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consorcio_contrato`
--

CREATE TABLE `consorcio_contrato` (
  `id` int(11) NOT NULL,
  `consulta` varchar(255) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `entrada` decimal(10,2) NOT NULL,
  `parcelas` int(11) NOT NULL,
  `valor_parcela` decimal(10,2) NOT NULL,
  `eq_user` varchar(100) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `data_cadastro` date NOT NULL,
  `cadastro_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultorio`
--

CREATE TABLE `consultorio` (
  `id` int(11) NOT NULL,
  `consulta` varchar(255) NOT NULL,
  `preco` decimal(20,9) DEFAULT NULL,
  `metamask` varchar(42) NOT NULL,
  `horario` time NOT NULL,
  `eq_user` varchar(255) DEFAULT NULL,
  `horarios` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `consultorio`
--
DELIMITER $$
CREATE TRIGGER `limitar_preco_consultorio_insert` BEFORE INSERT ON `consultorio` FOR EACH ROW BEGIN
  IF NEW.preco > 5000 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'O valor do preço não pode ultrapassar 5000.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `limitar_preco_consultorio_update` BEFORE UPDATE ON `consultorio` FOR EACH ROW BEGIN
  IF NEW.preco > 5000 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'O valor do preço não pode ultrapassar 5000.';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultorios`
--

CREATE TABLE `consultorios` (
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultorio_docs`
--

CREATE TABLE `consultorio_docs` (
  `id` varchar(50) NOT NULL,
  `titulo` text DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `data_upload` date DEFAULT NULL,
  `link_arquivo` text DEFAULT NULL,
  `tipo_arquivo` varchar(20) DEFAULT NULL,
  `ip_upload` varchar(45) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `consultorio_id` varchar(50) DEFAULT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `conteudo_slides` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultorio_odonto2`
--

CREATE TABLE `consultorio_odonto2` (
  `id` int(11) NOT NULL,
  `consulta` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `eq_user` varchar(100) NOT NULL,
  `horarios` text NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `horario` varchar(100) NOT NULL,
  `metamask` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conversas`
--

CREATE TABLE `conversas` (
  `id` int(11) NOT NULL,
  `usuario1` varchar(100) NOT NULL,
  `usuario2` varchar(100) NOT NULL,
  `ativo_usuario1` tinyint(1) DEFAULT 1,
  `ativo_usuario2` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conversoes_tibia`
--

CREATE TABLE `conversoes_tibia` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `aura_convertida` int(11) NOT NULL,
  `tibia_coins` int(11) NOT NULL,
  `nickname_char` varchar(100) NOT NULL,
  `assinatura` varchar(64) NOT NULL,
  `data_conversao` datetime NOT NULL,
  `status_entrega` enum('pendente','entregue','erro') DEFAULT 'pendente',
  `data_entrega` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `corridas`
--

CREATE TABLE `corridas` (
  `id_corrida` int(11) NOT NULL,
  `taxi_id` int(11) NOT NULL,
  `telefone_cliente` varchar(20) NOT NULL,
  `origem` varchar(255) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `data_corrida` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aberto','confirmado','fechado','cancelado') DEFAULT 'pendente',
  `atual_lat` decimal(10,6) DEFAULT NULL,
  `atual_lng` decimal(10,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `crachas_ativos`
--

CREATE TABLE `crachas_ativos` (
  `id` int(11) NOT NULL,
  `idcracha` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `data_entrada` datetime DEFAULT current_timestamp(),
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `datas_voo`
--

CREATE TABLE `datas_voo` (
  `id` int(11) NOT NULL,
  `voo_id` int(11) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `demonstracao_interesse`
--

CREATE TABLE `demonstracao_interesse` (
  `barco_id` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dentistas`
--

CREATE TABLE `dentistas` (
  `consultorio_id` int(11) NOT NULL,
  `numero_dentista` varchar(10) NOT NULL,
  `pago` tinyint(1) DEFAULT 0,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `descargas`
--

CREATE TABLE `descargas` (
  `id` int(11) NOT NULL,
  `data_hora` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_autorais`
--

CREATE TABLE `documentos_autorais` (
  `id` varchar(50) NOT NULL,
  `titulo` text DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `data_upload` date DEFAULT NULL,
  `link_pdf` text DEFAULT NULL,
  `ip_upload` varchar(45) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_ziprar`
--

CREATE TABLE `documentos_ziprar` (
  `id` varchar(50) NOT NULL,
  `titulo` text DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `data_upload` date DEFAULT NULL,
  `link_arquivo` text DEFAULT NULL,
  `tipo_arquivo` varchar(20) DEFAULT NULL,
  `ip_upload` varchar(45) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `taxa` decimal(5,2) NOT NULL,
  `data_contratacao` date NOT NULL,
  `data_quitacao` date DEFAULT NULL,
  `status` enum('aberto','quitado') DEFAULT 'aberto',
  `assinatura` varchar(64) NOT NULL,
  `assinatura_quitacao` varchar(64) DEFAULT NULL,
  `valor_quitado` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco_transacao`
--

CREATE TABLE `endereco_transacao` (
  `id` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `remetente` varchar(100) NOT NULL,
  `destinatario` varchar(100) NOT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `contato` varchar(30) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `data_registro` datetime DEFAULT current_timestamp(),
  `hash_transacao` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entregas`
--

CREATE TABLE `entregas` (
  `id` int(11) NOT NULL,
  `id_mensagem` int(11) NOT NULL,
  `id_etiqueta` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `remetenteinverso` text DEFAULT NULL,
  `hash_transacao` varchar(255) DEFAULT NULL,
  `entregue` tinyint(1) NOT NULL DEFAULT 0,
  `data_modificacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entregas_whatsapp`
--

CREATE TABLE `entregas_whatsapp` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `contrato` varchar(50) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `endereco` text NOT NULL,
  `cep` varchar(10) NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `escolas`
--

CREATE TABLE `escolas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario_id` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `essencias`
--

CREATE TABLE `essencias` (
  `id` int(11) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `valor` decimal(30,12) DEFAULT 0.000000000000,
  `usado` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estacionamento_log`
--

CREATE TABLE `estacionamento_log` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `entrada` datetime DEFAULT NULL,
  `saida` datetime DEFAULT NULL,
  `status` enum('estacionado','liberado','usado','pagamento_ok') DEFAULT 'estacionado',
  `assinatura` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fila_espera`
--

CREATE TABLE `fila_espera` (
  `id` int(11) NOT NULL,
  `id_entidade` varchar(20) DEFAULT NULL,
  `nome_entidade` varchar(100) DEFAULT NULL,
  `prioridade` int(11) DEFAULT 1,
  `status` varchar(20) DEFAULT 'pendente',
  `data_solicitacao` datetime DEFAULT current_timestamp(),
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_recebedor`
--

CREATE TABLE `fotos_recebedor` (
  `id` int(11) NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `caminho_foto` varchar(255) DEFAULT NULL,
  `data_envio` datetime DEFAULT NULL,
  `id_resposta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_tibia`
--

CREATE TABLE `fotos_tibia` (
  `id` int(11) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `data_publicacao` datetime NOT NULL,
  `descricao` text DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_equipamento`
--

CREATE TABLE `historico_equipamento` (
  `id` int(11) NOT NULL,
  `carrinho_id` varchar(50) DEFAULT NULL,
  `contrato` varchar(50) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `eq_user` varchar(50) DEFAULT NULL,
  `acao` enum('equipar','remover') DEFAULT NULL,
  `item_tipo` varchar(50) DEFAULT NULL,
  `numero_etiqueta` varchar(100) DEFAULT NULL,
  `data_acao` datetime DEFAULT current_timestamp(),
  `data_equipado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_eventos`
--

CREATE TABLE `historico_eventos` (
  `id` int(11) NOT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `barco_id` int(11) NOT NULL,
  `tipo_evento` varchar(50) NOT NULL,
  `data_inicio` datetime DEFAULT NULL,
  `data_fim` datetime DEFAULT NULL,
  `numero_contrato_locacao` varchar(100) NOT NULL,
  `numero_contrato_manutencao` varchar(100) NOT NULL,
  `descricao_manutencao` text DEFAULT NULL,
  `real_finalizacao_locacao` datetime DEFAULT NULL,
  `real_finalizacao_manutencao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_movimentos`
--

CREATE TABLE `historico_movimentos` (
  `id` int(11) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `entrada_anterior` datetime NOT NULL,
  `saida_data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_status`
--

CREATE TABLE `historico_status` (
  `id` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `status_novo` tinyint(1) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `alterado_em` timestamp NULL DEFAULT current_timestamp(),
  `usuario` varchar(50) DEFAULT NULL,
  `inicio_gasto` datetime DEFAULT NULL,
  `fim_gasto` datetime DEFAULT NULL,
  `total_gasto` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_tipo`
--

CREATE TABLE `historico_tipo` (
  `id` int(11) NOT NULL,
  `resposta_id` int(11) NOT NULL,
  `cv` varchar(50) DEFAULT NULL,
  `tipo_antigo` varchar(20) DEFAULT NULL,
  `tipo_novo` varchar(20) DEFAULT NULL,
  `alterado_por` varchar(50) DEFAULT NULL,
  `data_alteracao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_trocas_oleo`
--

CREATE TABLE `historico_trocas_oleo` (
  `id` int(11) NOT NULL,
  `barco_id` int(11) NOT NULL,
  `valor_antigo` decimal(10,2) DEFAULT NULL,
  `valor_novo` decimal(10,2) DEFAULT NULL,
  `valor_proxima_troca` decimal(10,2) DEFAULT NULL,
  `data_proxima_troca` date DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `data_troca` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `identificacao`
--

CREATE TABLE `identificacao` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `saldo_total` int(11) DEFAULT 0,
  `assinante` tinyint(1) NOT NULL DEFAULT 0,
  `data_assinante` datetime DEFAULT NULL,
  `assinantenv2` tinyint(1) NOT NULL DEFAULT 0,
  `datanv2` datetime DEFAULT NULL,
  `data_assinatura` datetime DEFAULT NULL,
  `nome_razao` varchar(255) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `data_registro` datetime DEFAULT NULL,
  `assinantenv3` tinyint(1) DEFAULT 0,
  `consultorio_id` int(11) NOT NULL,
  `numero_loja` varchar(50) DEFAULT NULL,
  `orcid` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `identificacao_odonto2`
--

CREATE TABLE `identificacao_odonto2` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `documento` char(11) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `telefone` varchar(20) NOT NULL,
  `orcid` varchar(30) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `saldo_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `assinantenv3` tinyint(1) DEFAULT 0,
  `assinante_compact` tinyint(1) NOT NULL DEFAULT 0,
  `tipo_caixa` enum('PADRAO','CP','IOTA','FAROLQR') DEFAULT 'PADRAO',
  `data_assinatura` datetime DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `saldo_devedor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_emprestimo` date DEFAULT NULL,
  `assinantenv9` int(11) NOT NULL,
  `recorde_moedas` int(11) DEFAULT 0,
  `recorde_moto` int(11) DEFAULT 0,
  `assinante_opentowork` tinyint(1) DEFAULT 0,
  `assinante_bits` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens_barco`
--

CREATE TABLE `imagens_barco` (
  `id` int(11) NOT NULL,
  `barco_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens_bixo`
--

CREATE TABLE `imagens_bixo` (
  `id` int(11) NOT NULL,
  `bixo_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens_produto`
--

CREATE TABLE `imagens_produto` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `imagem` varchar(500) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `data_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens_quarto`
--

CREATE TABLE `imagens_quarto` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `saldoAura` int(11) DEFAULT 0,
  `totalCafe` int(11) DEFAULT 0,
  `totalCha` int(11) DEFAULT 0,
  `totalBolo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_loja_odonto2`
--

CREATE TABLE `itens_loja_odonto2` (
  `id` int(11) NOT NULL,
  `loja_id` int(11) NOT NULL,
  `nome_item` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco_aura` int(11) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogadores`
--

CREATE TABLE `jogadores` (
  `id` varchar(50) NOT NULL,
  `x` float NOT NULL,
  `z` float NOT NULL,
  `yaw` float NOT NULL,
  `roll` float NOT NULL,
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogadores_ativos`
--

CREATE TABLE `jogadores_ativos` (
  `id` varchar(50) NOT NULL,
  `x` float DEFAULT NULL,
  `z` float DEFAULT NULL,
  `yaw` float DEFAULT NULL,
  `roll` float DEFAULT NULL,
  `last_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_espera`
--

CREATE TABLE `lista_espera` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `escola_id` int(11) DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `telefone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `localizacoes`
--

CREATE TABLE `localizacoes` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lojas_odonto2`
--

CREATE TABLE `lojas_odonto2` (
  `id` int(11) NOT NULL,
  `nome_loja` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `maquinas`
--

CREATE TABLE `maquinas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `status_parar` tinyint(1) NOT NULL DEFAULT 0,
  `usuario` varchar(50) DEFAULT NULL,
  `auras_por_hora` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `saldo_aura` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `ultima_atualizacao` datetime DEFAULT NULL,
  `qrcode_url` varchar(255) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `oldid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `maquinas_locacao`
--

CREATE TABLE `maquinas_locacao` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(50) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `data` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens_ocultas`
--

CREATE TABLE `mensagens_ocultas` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `mensagem_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens_privadas`
--

CREATE TABLE `mensagens_privadas` (
  `id` int(11) NOT NULL,
  `remetente` varchar(100) NOT NULL,
  `destinatario` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `metas_aura`
--

CREATE TABLE `metas_aura` (
  `id` int(11) NOT NULL,
  `mes` varchar(7) DEFAULT NULL,
  `meta` int(11) DEFAULT NULL,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `criado_em` date DEFAULT curdate(),
  `descricao` text DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `cidade_antiga` varchar(255) DEFAULT NULL,
  `estado_antigo` varchar(255) DEFAULT NULL,
  `cidade_nova` varchar(255) DEFAULT NULL,
  `estado_nova` varchar(255) DEFAULT NULL,
  `data_movimentacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nome_cars`
--

CREATE TABLE `nome_cars` (
  `numeroid` varchar(50) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nome_cars_itens`
--

CREATE TABLE `nome_cars_itens` (
  `id` int(11) NOT NULL,
  `numeroid` varchar(50) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nome_cars_pagamentos`
--

CREATE TABLE `nome_cars_pagamentos` (
  `id` int(11) NOT NULL,
  `numeroid` varchar(50) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `copia_cola` text DEFAULT NULL,
  `link_pix` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `numeros`
--

CREATE TABLE `numeros` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `status` enum('chamando','pendente','em_chamada','sem_resposta','testando') DEFAULT 'pendente',
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `odonto2_perfis`
--

CREATE TABLE `odonto2_perfis` (
  `id` int(11) NOT NULL,
  `nome_perfil` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `odonto2_users`
--

CREATE TABLE `odonto2_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `maior18` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `odonto2_users_perfil`
--

CREATE TABLE `odonto2_users_perfil` (
  `user_id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `oil_levels`
--

CREATE TABLE `oil_levels` (
  `id` int(11) NOT NULL,
  `boat_id` varchar(255) DEFAULT NULL,
  `oil_level` decimal(10,6) DEFAULT NULL,
  `next_change` date DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT current_timestamp(),
  `next_change_value` decimal(10,2) DEFAULT NULL,
  `whatsapp_number` varchar(15) NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `eq_user` text NOT NULL,
  `payment_status` enum('Pago','Não Pago') DEFAULT 'Não Pago',
  `paymentstatus` text NOT NULL,
  `nv_oleo` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `resposta_id` int(11) NOT NULL,
  `status_pagamento` varchar(50) NOT NULL,
  `data_pagamento` date NOT NULL,
  `valor` decimal(18,8) NOT NULL,
  `hashTransacao` varchar(255) NOT NULL,
  `pago` tinyint(1) NOT NULL DEFAULT 0,
  `tsttt` int(11) NOT NULL,
  `tstt` int(11) NOT NULL,
  `page_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_carrinho_2025`
--

CREATE TABLE `pagamentos_carrinho_2025` (
  `id` int(11) NOT NULL,
  `carrinho` varchar(50) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pendente',
  `criado_em` datetime DEFAULT current_timestamp(),
  `itens` text DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `entregue` tinyint(1) DEFAULT 0,
  `entregue_por` varchar(50) DEFAULT NULL,
  `data_entrega` datetime DEFAULT NULL,
  `metodo_pagamento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_destaque`
--

CREATE TABLE `pagamentos_destaque` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `numeroEtiqueta` int(11) DEFAULT NULL,
  `pix_id` varchar(255) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pendente',
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_mercadopago`
--

CREATE TABLE `pagamentos_mercadopago` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(50) DEFAULT NULL,
  `transaction_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_premium`
--

CREATE TABLE `pagamentos_premium` (
  `id` int(11) NOT NULL,
  `hash_comprovante` varchar(255) NOT NULL,
  `consorcio_id` int(11) NOT NULL,
  `eq_user` varchar(100) NOT NULL,
  `consultorio_id` int(11) NOT NULL,
  `premium_code` varchar(100) NOT NULL,
  `data_pagamento` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','confirmado','cancelado') DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_recebidos`
--

CREATE TABLE `pagamentos_recebidos` (
  `id` int(11) NOT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `caixa_postal` varchar(50) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento_musica`
--

CREATE TABLE `pagamento_musica` (
  `id` int(11) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `id_etiqueta` int(11) NOT NULL,
  `ip_acesso` varchar(45) NOT NULL,
  `agente_usuario` text DEFAULT NULL,
  `assinatura` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `hash_transacao` varchar(255) DEFAULT NULL,
  `data_registro` timestamp NULL DEFAULT current_timestamp(),
  `recebido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `parcelas`
--

CREATE TABLE `parcelas` (
  `id` int(11) NOT NULL,
  `consorcio_id` int(11) DEFAULT NULL,
  `numero_parcela` int(11) DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `valor_parcela` decimal(10,2) DEFAULT NULL,
  `status_pagamento` varchar(255) NOT NULL DEFAULT 'pendente',
  `eq_user` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `hash_comprovante` varchar(255) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `paymentsprestacao`
--

CREATE TABLE `paymentsprestacao` (
  `id` int(11) NOT NULL,
  `eq_user` varchar(255) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `hashTransacao` varchar(255) NOT NULL,
  `payment` varchar(50) DEFAULT NULL,
  `tst` varchar(250) NOT NULL,
  `valorpayment` decimal(11,9) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `data_pagamento` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_livro` varchar(50) DEFAULT NULL,
  `valor_ava` decimal(10,2) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `valor_aura` int(10) UNSIGNED NOT NULL,
  `nome_comprador` varchar(255) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `contato` varchar(50) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `id_cracha` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_assinatura`
--

CREATE TABLE `pedidos_assinatura` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `aura` int(10) UNSIGNED NOT NULL,
  `valor_reais` decimal(10,2) DEFAULT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `copia_cola` text DEFAULT NULL,
  `link_pix` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `id_mercadopago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_aura`
--

CREATE TABLE `pedidos_aura` (
  `id` int(11) NOT NULL,
  `caixa_postal` varchar(100) NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL,
  `valor_reais` decimal(10,2) NOT NULL,
  `metodo` enum('pix','cartao') NOT NULL DEFAULT 'pix',
  `payment_id` varchar(100) DEFAULT NULL,
  `installments` int(11) DEFAULT NULL,
  `last_four_digits` varchar(4) DEFAULT NULL,
  `authorization_code` varchar(50) DEFAULT NULL,
  `pix_id` varchar(100) NOT NULL,
  `copia_cola` text DEFAULT NULL,
  `link_pix` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pendente',
  `ip_cliente` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `unique_id` varchar(50) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `data_pagamento` datetime DEFAULT NULL,
  `metodo_pagamento` varchar(50) DEFAULT 'Pix',
  `observacao` text DEFAULT NULL,
  `hash_verificacao` varchar(64) DEFAULT NULL,
  `payment_method_id` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_cafe`
--

CREATE TABLE `pedidos_cafe` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `item_num` int(11) NOT NULL,
  `item` enum('Café','Chá','Hamburguer de Siri') NOT NULL,
  `tempo_pronto` int(11) NOT NULL,
  `status` enum('pendente','retirado') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_livros`
--

CREATE TABLE `pedidos_livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `nome_comprador` varchar(100) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp(),
  `valor_aura` int(11) NOT NULL DEFAULT 0,
  `estado` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `contato` varchar(30) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `entregue` tinyint(1) DEFAULT 0,
  `eq_user` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_pix`
--

CREATE TABLE `pedidos_pix` (
  `id` int(11) NOT NULL,
  `txid` varchar(64) DEFAULT NULL,
  `caixa_postal` varchar(255) DEFAULT NULL,
  `aura` int(10) UNSIGNED NOT NULL,
  `valor` float DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `performance_log`
--

CREATE TABLE `performance_log` (
  `id` int(11) NOT NULL,
  `fid` decimal(10,3) DEFAULT NULL,
  `lcp` decimal(10,3) DEFAULT NULL,
  `cls` decimal(10,3) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `datahora` timestamp NULL DEFAULT current_timestamp(),
  `url` varchar(255) DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL,
  `origem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pontuacao_godot`
--

CREATE TABLE `pontuacao_godot` (
  `id` int(11) NOT NULL,
  `player` varchar(50) NOT NULL,
  `pontuacao_godot` int(11) NOT NULL,
  `data_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pontuacoes`
--

CREATE TABLE `pontuacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor` int(11) NOT NULL,
  `data_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `portais_usados`
--

CREATE TABLE `portais_usados` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `portal_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `posicoes_player_cafeteria`
--

CREATE TABLE `posicoes_player_cafeteria` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `x` float DEFAULT 0,
  `y` float DEFAULT 0,
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ativo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `produto_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `url_buy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso_usuario`
--

CREATE TABLE `progresso_usuario` (
  `usuario` varchar(255) NOT NULL,
  `id_atual` int(11) NOT NULL,
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quartos`
--

CREATE TABLE `quartos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `metamask` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quartos_farol`
--

CREATE TABLE `quartos_farol` (
  `id` int(11) NOT NULL,
  `nome_quarto` varchar(100) NOT NULL,
  `preco_diaria` decimal(10,2) NOT NULL,
  `estado` varchar(150) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `horarios` varchar(255) DEFAULT '08:00,10:00,14:00,18:00',
  `eq_user` varchar(100) DEFAULT NULL,
  `horarios_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `radioterapia_cobalto_nic`
--

CREATE TABLE `radioterapia_cobalto_nic` (
  `id` int(11) NOT NULL,
  `eletrons` int(11) NOT NULL,
  `protons` int(11) NOT NULL,
  `neutrons` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ranking_hashes`
--

CREATE TABLE `ranking_hashes` (
  `id` int(11) NOT NULL,
  `total_cafe` int(11) NOT NULL,
  `total_cha` int(11) NOT NULL,
  `total_bolo` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `registrointerno`
--

CREATE TABLE `registrointerno` (
  `id` int(11) NOT NULL,
  `tabela_editada` varchar(255) DEFAULT NULL,
  `id_registro_editado` int(11) DEFAULT NULL,
  `coluna_editada` varchar(255) DEFAULT NULL,
  `valor_antigo` varchar(255) DEFAULT NULL,
  `valor_novo` varchar(255) DEFAULT NULL,
  `usuario_que_editou` varchar(255) DEFAULT NULL,
  `data_hora_edicao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `registrointerno2`
--

CREATE TABLE `registrointerno2` (
  `id` int(11) NOT NULL,
  `tabela_editada` varchar(255) NOT NULL,
  `id_registro_editado` int(11) NOT NULL,
  `coluna_editada` varchar(255) NOT NULL,
  `valor_antigo` varchar(255) NOT NULL,
  `valor_novo` varchar(255) NOT NULL,
  `usuario_que_editou` varchar(255) NOT NULL,
  `data_hora_edicao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_solicitacao` datetime NOT NULL,
  `transacao_hash` varchar(255) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `uid_confirmacao` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_barcos`
--

CREATE TABLE `reservas_barcos` (
  `id` int(11) NOT NULL,
  `username` varchar(150) DEFAULT NULL,
  `barco_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `usuario` varchar(150) DEFAULT NULL,
  `pago` tinyint(1) DEFAULT 1,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `fim_reserva` date DEFAULT NULL,
  `id_reserva` varchar(255) NOT NULL,
  `situacao` varchar(20) NOT NULL,
  `token_retirada` varchar(100) DEFAULT NULL,
  `token_devolucao` varchar(255) DEFAULT NULL,
  `data_token_devolucao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_pendentes`
--

CREATE TABLE `reservas_pendentes` (
  `id` int(11) NOT NULL,
  `barco_id` int(11) NOT NULL,
  `nome_barco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `telefone_dono` varchar(20) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `username` varchar(100) NOT NULL,
  `data_pedido` timestamp NULL DEFAULT current_timestamp(),
  `status_usuario` varchar(100) DEFAULT NULL,
  `status_data` datetime DEFAULT NULL,
  `uid_confirmacao` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_pix`
--

CREATE TABLE `reservas_pix` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `quarto_id` int(11) DEFAULT NULL,
  `data_reserva` date DEFAULT NULL,
  `pix_id` varchar(100) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `pago` tinyint(4) DEFAULT 0,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `cracha_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_pix_bixos`
--

CREATE TABLE `reservas_pix_bixos` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `bixo_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `pix_id` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `cracha_id` varchar(100) NOT NULL,
  `pago` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_quarto`
--

CREATE TABLE `reservas_quarto` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL,
  `email_cliente` varchar(150) NOT NULL,
  `data_checkin` date NOT NULL,
  `data_checkout` date NOT NULL,
  `horarios` text DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `eq_user` varchar(100) DEFAULT NULL,
  `page_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas_voo`
--

CREATE TABLE `reservas_voo` (
  `id` int(11) NOT NULL,
  `voo_id` int(11) NOT NULL,
  `numero_assento` varchar(10) DEFAULT NULL,
  `data_reserva` date NOT NULL,
  `transacao_hash` varchar(66) NOT NULL,
  `pago` tinyint(1) DEFAULT 1,
  `trator_id` int(11) NOT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `placa_trator` varchar(20) DEFAULT NULL,
  `renavam` varchar(20) DEFAULT NULL,
  `origem` varchar(100) DEFAULT NULL,
  `destino` varchar(100) DEFAULT NULL,
  `placa_caminhao` varchar(20) DEFAULT NULL,
  `quantidade_assentos` int(11) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `embarcado` tinyint(1) DEFAULT 0,
  `data_embarque` datetime DEFAULT NULL,
  `voo_ok` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reserva_data_barcos`
--

CREATE TABLE `reserva_data_barcos` (
  `id` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `data_reserva` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reserva_dentista`
--

CREATE TABLE `reserva_dentista` (
  `id` int(11) NOT NULL,
  `voo_id` int(11) NOT NULL,
  `assento` varchar(10) NOT NULL,
  `data_reserva` date NOT NULL,
  `transacao_hash` varchar(66) NOT NULL,
  `pago` tinyint(1) DEFAULT 1,
  `numero_dentista` varchar(50) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `embarcado` tinyint(1) DEFAULT 0,
  `data_embarque` datetime DEFAULT NULL,
  `voo_ok` tinyint(1) DEFAULT 0,
  `consultorio_id` int(11) NOT NULL,
  `page_id` varchar(50) DEFAULT NULL,
  `nome_paciente` varchar(255) NOT NULL,
  `saida_voo` datetime DEFAULT NULL,
  `presenca` tinyint(1) DEFAULT 0,
  `aura_creditado` tinyint(1) DEFAULT 0,
  `idcracha` varchar(64) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `respostas`
--

CREATE TABLE `respostas` (
  `tipo` varchar(255) DEFAULT NULL,
  `modelo` text NOT NULL,
  `cv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ano` int(11) NOT NULL,
  `placa` text NOT NULL,
  `estado` text NOT NULL,
  `cidade` text NOT NULL,
  `eq_user` text NOT NULL,
  `telefone` char(20) NOT NULL,
  `id` int(11) NOT NULL,
  `fotos1` text NOT NULL,
  `link` text NOT NULL,
  `preco_total` text NOT NULL,
  `tyus` text NOT NULL,
  `linkiframe` text NOT NULL,
  `linkGIT` text NOT NULL,
  `qrcodelink` text NOT NULL,
  `novo_creditos` text NOT NULL,
  `ultimo_desconto` text NOT NULL,
  `creditos` text NOT NULL,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  `url_buy` text NOT NULL,
  `data` text NOT NULL,
  `locationStatus` text NOT NULL,
  `youtubelink` text NOT NULL,
  `qrcode` varchar(250) NOT NULL,
  `status_pagamento` enum('Pago','Não Pago') DEFAULT 'Não Pago',
  `data_pagamento` date NOT NULL,
  `nova_data_pagamento` date DEFAULT NULL,
  `novo_status_pagamento` varchar(50) DEFAULT NULL,
  `quantidade2` bigint(20) NOT NULL,
  `descricao` decimal(11,9) DEFAULT NULL,
  `oil_level` decimal(10,2) DEFAULT NULL,
  `nome_recebedor` varchar(255) NOT NULL,
  `cidade_recebedor` varchar(255) NOT NULL,
  `caixa` text NOT NULL,
  `mercado_pago_qrcode_url` varchar(255) DEFAULT NULL,
  `metamask` varchar(42) NOT NULL,
  `qrcode_link` text DEFAULT NULL,
  `qrcode_gerado_em` datetime DEFAULT current_timestamp(),
  `ip_acesso` varchar(45) DEFAULT NULL,
  `destino` varchar(150) DEFAULT NULL,
  `destino_recebedor` varchar(150) DEFAULT NULL,
  `caixa_postal` varchar(100) DEFAULT NULL,
  `documento` varchar(100) DEFAULT NULL,
  `tipo_data` datetime NOT NULL DEFAULT current_timestamp(),
  `foto_recebedor` varchar(255) DEFAULT NULL,
  `valor_de_aura` int(10) UNSIGNED NOT NULL,
  `data_now` date DEFAULT curdate(),
  `estrelinhas` int(11) DEFAULT 0,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `peer_id` varchar(100) DEFAULT NULL,
  `id_programa` varchar(50) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `horario` datetime DEFAULT NULL,
  `item_tipo` varchar(50) DEFAULT NULL,
  `num_pecas` int(11) NOT NULL DEFAULT 1,
  `unique_id` varchar(100) NOT NULL,
  `numero_pecas` int(11) NOT NULL DEFAULT 1,
  `validade` int(11) NOT NULL DEFAULT 0,
  `cep` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `respostas2`
--

CREATE TABLE `respostas2` (
  `tipo` char(140) NOT NULL,
  `modelo` text NOT NULL,
  `cv` varchar(100) NOT NULL,
  `ano` int(11) NOT NULL,
  `placa` text NOT NULL,
  `estado` text NOT NULL,
  `cidade` text NOT NULL,
  `eq_user` text NOT NULL,
  `telefone` char(20) NOT NULL,
  `id` int(11) NOT NULL,
  `fotos1` text NOT NULL,
  `link` text NOT NULL,
  `preco_total` text NOT NULL,
  `tyus` text NOT NULL,
  `linkiframe` text NOT NULL,
  `linkGIT` text NOT NULL,
  `qrcodelink` text NOT NULL,
  `novo_creditos` text NOT NULL,
  `ultimo_desconto` text NOT NULL,
  `creditos` text NOT NULL,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  `url_buy` text NOT NULL,
  `data` text NOT NULL,
  `locationStatus` text NOT NULL,
  `youtubelink` text NOT NULL,
  `qrcode` varchar(240) NOT NULL,
  `status_pagamento` enum('Pago','Não Pago') DEFAULT 'Não Pago',
  `data_pagamento` date NOT NULL,
  `nova_data_pagamento` date DEFAULT NULL,
  `novo_status_pagamento` varchar(50) DEFAULT NULL,
  `caixa` varchar(250) NOT NULL,
  `data_hora` datetime DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  `status` varchar(250) NOT NULL,
  `marcado_por` varchar(100) DEFAULT NULL,
  `sangue` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `respostas2_data_encontrada`
--

CREATE TABLE `respostas2_data_encontrada` (
  `id` int(11) NOT NULL,
  `id_resposta` int(11) NOT NULL,
  `data_marcacao` datetime NOT NULL,
  `marcado_por` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `respostas3`
--

CREATE TABLE `respostas3` (
  `tipo` char(140) NOT NULL,
  `modelo` text NOT NULL,
  `cv` varchar(20) NOT NULL,
  `ano` int(11) NOT NULL,
  `placa` text NOT NULL,
  `estado` text NOT NULL,
  `cidade` text NOT NULL,
  `eq_user` text NOT NULL,
  `telefone` char(20) NOT NULL,
  `id` int(11) NOT NULL,
  `fotos1` text NOT NULL,
  `link` text NOT NULL,
  `preco_total` text NOT NULL,
  `tyus` text NOT NULL,
  `linkiframe` text NOT NULL,
  `linkGIT` text NOT NULL,
  `qrcodelink` text NOT NULL,
  `novo_creditos` text NOT NULL,
  `ultimo_desconto` text NOT NULL,
  `creditos` text NOT NULL,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  `url_buy` text NOT NULL,
  `data` text NOT NULL,
  `locationStatus` text NOT NULL,
  `youtubelink` text NOT NULL,
  `qrcode` text NOT NULL,
  `status_pagamento` enum('Pago','Não Pago') DEFAULT 'Não Pago',
  `data_pagamento` date NOT NULL,
  `nova_data_pagamento` date DEFAULT NULL,
  `novo_status_pagamento` varchar(50) DEFAULT NULL,
  `caixa` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `respostas_live`
--

CREATE TABLE `respostas_live` (
  `id` int(11) NOT NULL,
  `id_programa` varchar(255) NOT NULL,
  `peer_id` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `horario` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `retiradas`
--

CREATE TABLE `retiradas` (
  `id` int(11) NOT NULL,
  `cv` varchar(50) NOT NULL,
  `eq_user` varchar(100) DEFAULT NULL,
  `quem_retirou` varchar(100) DEFAULT NULL,
  `data_retirada` datetime DEFAULT current_timestamp(),
  `sig` varchar(255) DEFAULT NULL,
  `registro_id` int(11) NOT NULL,
  `documento_validado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rfid_tags`
--

CREATE TABLE `rfid_tags` (
  `id` int(11) NOT NULL,
  `tag_code` varchar(999) DEFAULT NULL,
  `maquina_id` int(11) DEFAULT NULL,
  `maquina_nome` varchar(250) DEFAULT NULL,
  `ultimo_status` tinyint(4) DEFAULT 0,
  `usuario` varchar(100) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `file` varchar(255) NOT NULL,
  `score` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_entregas_whatsapp`
--

CREATE TABLE `selena_entregas_whatsapp` (
  `id` int(11) NOT NULL,
  `contrato` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `ids_itens` text DEFAULT NULL,
  `enviado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_identificacao`
--

CREATE TABLE `selena_identificacao` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `numero_loja` varchar(50) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_lojas`
--

CREATE TABLE `selena_lojas` (
  `id` int(11) NOT NULL,
  `nome_loja` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_produtos`
--

CREATE TABLE `selena_produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_users`
--

CREATE TABLE `selena_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `loja_etiqueta` varchar(50) DEFAULT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `selena_uso_itens`
--

CREATE TABLE `selena_uso_itens` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `contrato` varchar(50) DEFAULT NULL,
  `usado_por` varchar(50) DEFAULT NULL,
  `usado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sites_parceiros`
--

CREATE TABLE `sites_parceiros` (
  `id` int(11) NOT NULL,
  `nome_site` varchar(100) NOT NULL,
  `token_api` varchar(64) NOT NULL,
  `url_callback` varchar(255) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `username_dono` varchar(100) NOT NULL,
  `url_retorno` varchar(255) NOT NULL,
  `caixa_recebimento` varchar(50) NOT NULL,
  `valor_padrao` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_table`
--

CREATE TABLE `status_table` (
  `id` int(11) NOT NULL,
  `eq_user` varchar(250) NOT NULL,
  `tipo` enum('Entrada','Saída') NOT NULL,
  `data_hora` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `taxis`
--

CREATE TABLE `taxis` (
  `id` int(11) NOT NULL,
  `nome_motorista` varchar(100) NOT NULL,
  `telefone_motorista` varchar(20) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `preco_km` decimal(10,2) NOT NULL,
  `status` enum('disponivel','ocupado') DEFAULT 'disponivel',
  `imagem_carro` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tickets_transporte`
--

CREATE TABLE `tickets_transporte` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `barco_id` int(11) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_reserva` date DEFAULT NULL,
  `hora_reserva` time DEFAULT NULL,
  `voo_ok` tinyint(1) DEFAULT 0,
  `status` enum('pendente','aprovado','cancelado') DEFAULT 'pendente',
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `token_qrcode`
--

CREATE TABLE `token_qrcode` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes_aura`
--

CREATE TABLE `transacoes_aura` (
  `id` int(11) NOT NULL,
  `remetente` varchar(50) DEFAULT NULL,
  `destinatario` varchar(50) DEFAULT NULL,
  `valor` int(10) UNSIGNED NOT NULL,
  `valor_reais` decimal(10,2) DEFAULT NULL,
  `caixa_origem` varchar(100) DEFAULT NULL,
  `caixa_destino` varchar(100) DEFAULT NULL,
  `data_transacao` timestamp NULL DEFAULT current_timestamp(),
  `pix_id` varchar(100) DEFAULT NULL,
  `data_registro` datetime DEFAULT current_timestamp(),
  `tipo_transacao` enum('CP','IOTA','FAROLQR') DEFAULT 'CP',
  `assinatura` varchar(64) NOT NULL,
  `compra_id` varchar(50) DEFAULT NULL,
  `chave_extra` varchar(255) NOT NULL,
  `entregue` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tratores`
--

CREATE TABLE `tratores` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano` int(11) NOT NULL,
  `potencia` varchar(50) NOT NULL,
  `placa_trator` varchar(20) NOT NULL,
  `renavam` varchar(20) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tratores_heycar`
--

CREATE TABLE `tratores_heycar` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano` int(11) NOT NULL,
  `placa` varchar(50) NOT NULL,
  `capacidade` decimal(10,2) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `trator_oleo`
--

CREATE TABLE `trator_oleo` (
  `id` int(11) NOT NULL,
  `boat_id` varchar(255) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `oil_level` varchar(255) NOT NULL,
  `next_change` varchar(255) NOT NULL,
  `next_change_value` varchar(255) NOT NULL,
  `whatsapp_number` varchar(15) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `paymentstatus` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos`
--

CREATE TABLE `treinos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dia` date NOT NULL,
  `musculo` varchar(50) NOT NULL,
  `exercicio` varchar(100) NOT NULL,
  `peso` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos_boxe`
--

CREATE TABLE `treinos_boxe` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dia` date NOT NULL,
  `sequencia` varchar(255) NOT NULL,
  `minutos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos_users`
--

CREATE TABLE `treinos_users` (
  `user_id` int(11) NOT NULL,
  `dia` varchar(20) NOT NULL,
  `exercicio` varchar(100) NOT NULL,
  `peso` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `escola_id` int(11) NOT NULL,
  `usuario_id` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `assinante` tinyint(1) DEFAULT 0,
  `super` tinyint(4) NOT NULL DEFAULT 0,
  `loja_etiqueta` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_cards`
--

CREATE TABLE `user_cards` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `id_cards` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_configs`
--

CREATE TABLE `user_configs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sens_windows` varchar(10) DEFAULT NULL,
  `sens_cs2` varchar(10) DEFAULT NULL,
  `share_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `rfid_uid` varchar(64) NOT NULL,
  `saldo_aura` int(11) NOT NULL DEFAULT 100,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_logados`
--

CREATE TABLE `usuarios_logados` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `pos_x` double DEFAULT NULL,
  `pos_y` double DEFAULT NULL,
  `pos_z` double DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `pontuacao` varchar(250) NOT NULL DEFAULT '0',
  `ultimo_portal_id` int(11) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `last_update` varchar(255) NOT NULL,
  `ultima_data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_fake`
--

CREATE TABLE `usuario_fake` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `deslikes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `voos`
--

CREATE TABLE `voos` (
  `id` int(11) NOT NULL,
  `placa` varchar(20) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `preco` decimal(20,9) DEFAULT NULL,
  `metamask` varchar(42) NOT NULL,
  `horario` time NOT NULL,
  `origem` varchar(100) NOT NULL,
  `datas_permitidas` text NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `caminhao_id` int(11) NOT NULL,
  `quantidade_assentos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `votos`
--

CREATE TABLE `votos` (
  `id` int(11) NOT NULL,
  `id_recebedor` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `voto` enum('positivo','negativo') NOT NULL,
  `data_voto` timestamp NULL DEFAULT current_timestamp(),
  `usuario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `votos_fake`
--

CREATE TABLE `votos_fake` (
  `id` int(11) NOT NULL,
  `eq_user` varchar(255) NOT NULL,
  `id_desafiante` int(11) NOT NULL,
  `id_base` int(11) NOT NULL DEFAULT 201721424,
  `data_voto` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `webrtc_candidates`
--

CREATE TABLE `webrtc_candidates` (
  `id` int(11) NOT NULL,
  `room` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `candidate` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `webrtc_rooms`
--

CREATE TABLE `webrtc_rooms` (
  `room` varchar(255) NOT NULL,
  `compromisso_id` int(11) DEFAULT NULL,
  `offer` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acessos_mensagem`
--
ALTER TABLE `acessos_mensagem`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `acessos_transmissao`
--
ALTER TABLE `acessos_transmissao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `escola_id` (`escola_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `assentos`
--
ALTER TABLE `assentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voo_id` (`voo_id`);

--
-- Índices de tabela `assinantenv3`
--
ALTER TABLE `assinantenv3`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `assinaturas_pdf`
--
ALTER TABLE `assinaturas_pdf`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assinatura_token` (`assinatura_token`);

--
-- Índices de tabela `auditoria_conversao`
--
ALTER TABLE `auditoria_conversao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `auras`
--
ALTER TABLE `auras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transacao_id` (`transacao_id`);

--
-- Índices de tabela `aura_balance`
--
ALTER TABLE `aura_balance`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `aura_compromisso`
--
ALTER TABLE `aura_compromisso`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aura_envios`
--
ALTER TABLE `aura_envios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_confirmacao` (`uid_confirmacao`);

--
-- Índices de tabela `aura_recebida`
--
ALTER TABLE `aura_recebida`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `barcos`
--
ALTER TABLE `barcos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `bixos`
--
ALTER TABLE `bixos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `blocos`
--
ALTER TABLE `blocos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `cadastro_produto`
--
ALTER TABLE `cadastro_produto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caixa_postal`
--
ALTER TABLE `caixa_postal`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caminhoes_heycar`
--
ALTER TABLE `caminhoes_heycar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`,`placa`);

--
-- Índices de tabela `canecas_personalizadas`
--
ALTER TABLE `canecas_personalizadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caronas`
--
ALTER TABLE `caronas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caronas_passageiros`
--
ALTER TABLE `caronas_passageiros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caronas_passageiros_ufsm`
--
ALTER TABLE `caronas_passageiros_ufsm`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `caronas_ufsm`
--
ALTER TABLE `caronas_ufsm`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Índices de tabela `carrinho_compras`
--
ALTER TABLE `carrinho_compras`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `carrinho_concluido`
--
ALTER TABLE `carrinho_concluido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `catalogo_musica`
--
ALTER TABLE `catalogo_musica`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `certificado_ableton`
--
ALTER TABLE `certificado_ableton`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `chamadas`
--
ALTER TABLE `chamadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `chats_encerrados`
--
ALTER TABLE `chats_encerrados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `chat_mensagens`
--
ALTER TABLE `chat_mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cidades_coords`
--
ALTER TABLE `cidades_coords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cidade` (`cidade`,`estado`);

--
-- Índices de tabela `compact_fotos`
--
ALTER TABLE `compact_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imovel_id` (`imovel_id`);

--
-- Índices de tabela `compact_imoveis`
--
ALTER TABLE `compact_imoveis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consorcio_id` (`consorcio_id`);

--
-- Índices de tabela `compras_aura`
--
ALTER TABLE `compras_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `compras_aurea`
--
ALTER TABLE `compras_aurea`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comprovantes_aura`
--
ALTER TABLE `comprovantes_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comprovantes_quitacao`
--
ALTER TABLE `comprovantes_quitacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comprovante_emprestimo` (`emprestimo_id`);

--
-- Índices de tabela `confirmado_aura`
--
ALTER TABLE `confirmado_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consorcio_cadastro`
--
ALTER TABLE `consorcio_cadastro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consorcio_cadastro2`
--
ALTER TABLE `consorcio_cadastro2`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consorcio_cadastro3`
--
ALTER TABLE `consorcio_cadastro3`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consorcio_contrato`
--
ALTER TABLE `consorcio_contrato`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consultorio`
--
ALTER TABLE `consultorio`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consultorio_docs`
--
ALTER TABLE `consultorio_docs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consultorio_odonto2`
--
ALTER TABLE `consultorio_odonto2`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `conversas`
--
ALTER TABLE `conversas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario1` (`usuario1`,`usuario2`);

--
-- Índices de tabela `conversoes_tibia`
--
ALTER TABLE `conversoes_tibia`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `corridas`
--
ALTER TABLE `corridas`
  ADD PRIMARY KEY (`id_corrida`),
  ADD KEY `taxi_id` (`taxi_id`);

--
-- Índices de tabela `crachas_ativos`
--
ALTER TABLE `crachas_ativos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idcracha` (`idcracha`),
  ADD UNIQUE KEY `unique_user_status` (`username`,`status`),
  ADD KEY `username` (`username`);

--
-- Índices de tabela `datas_voo`
--
ALTER TABLE `datas_voo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voo_id` (`voo_id`);

--
-- Índices de tabela `demonstracao_interesse`
--
ALTER TABLE `demonstracao_interesse`
  ADD PRIMARY KEY (`barco_id`);

--
-- Índices de tabela `dentistas`
--
ALTER TABLE `dentistas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `descargas`
--
ALTER TABLE `descargas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos_autorais`
--
ALTER TABLE `documentos_autorais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos_ziprar`
--
ALTER TABLE `documentos_ziprar`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `endereco_transacao`
--
ALTER TABLE `endereco_transacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `entregas_whatsapp`
--
ALTER TABLE `entregas_whatsapp`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `escolas`
--
ALTER TABLE `escolas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `essencias`
--
ALTER TABLE `essencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Índices de tabela `estacionamento_log`
--
ALTER TABLE `estacionamento_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assinatura` (`assinatura`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `fila_espera`
--
ALTER TABLE `fila_espera`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fotos_recebedor`
--
ALTER TABLE `fotos_recebedor`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fotos_tibia`
--
ALTER TABLE `fotos_tibia`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_equipamento`
--
ALTER TABLE `historico_equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_eventos`
--
ALTER TABLE `historico_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_movimentos`
--
ALTER TABLE `historico_movimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_status`
--
ALTER TABLE `historico_status`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_tipo`
--
ALTER TABLE `historico_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_trocas_oleo`
--
ALTER TABLE `historico_trocas_oleo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barco_id` (`barco_id`);

--
-- Índices de tabela `identificacao`
--
ALTER TABLE `identificacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `caixa_postal` (`caixa_postal`),
  ADD UNIQUE KEY `caixa_postal_2` (`caixa_postal`);

--
-- Índices de tabela `identificacao_odonto2`
--
ALTER TABLE `identificacao_odonto2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `caixa_postal` (`caixa_postal`);

--
-- Índices de tabela `imagens_barco`
--
ALTER TABLE `imagens_barco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barco_id` (`barco_id`);

--
-- Índices de tabela `imagens_bixo`
--
ALTER TABLE `imagens_bixo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bixo_id` (`bixo_id`);

--
-- Índices de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `imagens_quarto`
--
ALTER TABLE `imagens_quarto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `itens_loja_odonto2`
--
ALTER TABLE `itens_loja_odonto2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loja_id` (`loja_id`);

--
-- Índices de tabela `jogadores`
--
ALTER TABLE `jogadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `jogadores_ativos`
--
ALTER TABLE `jogadores_ativos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lista_espera`
--
ALTER TABLE `lista_espera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `escola_id` (`escola_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `localizacoes`
--
ALTER TABLE `localizacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lojas_odonto2`
--
ALTER TABLE `lojas_odonto2`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `maquinas`
--
ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `maquinas_locacao`
--
ALTER TABLE `maquinas_locacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `mensagens_ocultas`
--
ALTER TABLE `mensagens_ocultas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`,`mensagem_id`);

--
-- Índices de tabela `mensagens_privadas`
--
ALTER TABLE `mensagens_privadas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `metas_aura`
--
ALTER TABLE `metas_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `nome_cars`
--
ALTER TABLE `nome_cars`
  ADD PRIMARY KEY (`numeroid`);

--
-- Índices de tabela `nome_cars_itens`
--
ALTER TABLE `nome_cars_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `numeroid` (`numeroid`),
  ADD KEY `item_id` (`item_id`);

--
-- Índices de tabela `nome_cars_pagamentos`
--
ALTER TABLE `nome_cars_pagamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `numeros`
--
ALTER TABLE `numeros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `odonto2_perfis`
--
ALTER TABLE `odonto2_perfis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_perfil` (`nome_perfil`);

--
-- Índices de tabela `odonto2_users`
--
ALTER TABLE `odonto2_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `odonto2_users_perfil`
--
ALTER TABLE `odonto2_users_perfil`
  ADD PRIMARY KEY (`user_id`,`perfil_id`),
  ADD KEY `perfil_id` (`perfil_id`);

--
-- Índices de tabela `oil_levels`
--
ALTER TABLE `oil_levels`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hashTransacao` (`hashTransacao`),
  ADD UNIQUE KEY `caixa_id` (`tstt`),
  ADD KEY `resposta_id` (`resposta_id`);

--
-- Índices de tabela `pagamentos_carrinho_2025`
--
ALTER TABLE `pagamentos_carrinho_2025`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carrinho` (`carrinho`);

--
-- Índices de tabela `pagamentos_destaque`
--
ALTER TABLE `pagamentos_destaque`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentos_mercadopago`
--
ALTER TABLE `pagamentos_mercadopago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`);

--
-- Índices de tabela `pagamentos_premium`
--
ALTER TABLE `pagamentos_premium`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash_comprovante` (`hash_comprovante`);

--
-- Índices de tabela `pagamentos_recebidos`
--
ALTER TABLE `pagamentos_recebidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pix_id` (`pix_id`);

--
-- Índices de tabela `pagamento_musica`
--
ALTER TABLE `pagamento_musica`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `parcelas`
--
ALTER TABLE `parcelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consorcio_id` (`consorcio_id`);

--
-- Índices de tabela `paymentsprestacao`
--
ALTER TABLE `paymentsprestacao`
  ADD PRIMARY KEY (`id`,`hashTransacao`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos_assinatura`
--
ALTER TABLE `pedidos_assinatura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos_aura`
--
ALTER TABLE `pedidos_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos_cafe`
--
ALTER TABLE `pedidos_cafe`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos_livros`
--
ALTER TABLE `pedidos_livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos_pix`
--
ALTER TABLE `pedidos_pix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txid` (`txid`);

--
-- Índices de tabela `performance_log`
--
ALTER TABLE `performance_log`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `pontuacao_godot`
--
ALTER TABLE `pontuacao_godot`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pontuacoes`
--
ALTER TABLE `pontuacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`valor`);

--
-- Índices de tabela `portais_usados`
--
ALTER TABLE `portais_usados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `posicoes_player_cafeteria`
--
ALTER TABLE `posicoes_player_cafeteria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`produto_id`);

--
-- Índices de tabela `progresso_usuario`
--
ALTER TABLE `progresso_usuario`
  ADD PRIMARY KEY (`usuario`);

--
-- Índices de tabela `quartos`
--
ALTER TABLE `quartos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `quartos_farol`
--
ALTER TABLE `quartos_farol`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `radioterapia_cobalto_nic`
--
ALTER TABLE `radioterapia_cobalto_nic`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ranking_hashes`
--
ALTER TABLE `ranking_hashes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `registrointerno`
--
ALTER TABLE `registrointerno`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `registrointerno2`
--
ALTER TABLE `registrointerno2`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quarto_id` (`quarto_id`,`data_reserva`),
  ADD UNIQUE KEY `uid_confirmacao` (`uid_confirmacao`);

--
-- Índices de tabela `reservas_barcos`
--
ALTER TABLE `reservas_barcos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barco_id` (`barco_id`);

--
-- Índices de tabela `reservas_pendentes`
--
ALTER TABLE `reservas_pendentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barco_id` (`barco_id`);

--
-- Índices de tabela `reservas_pix`
--
ALTER TABLE `reservas_pix`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas_pix_bixos`
--
ALTER TABLE `reservas_pix_bixos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bixo_id` (`bixo_id`);

--
-- Índices de tabela `reservas_quarto`
--
ALTER TABLE `reservas_quarto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `reservas_voo`
--
ALTER TABLE `reservas_voo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_embarque` (`transacao_hash`,`eq_user`),
  ADD KEY `voo_id` (`voo_id`);

--
-- Índices de tabela `reserva_data_barcos`
--
ALTER TABLE `reserva_data_barcos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reserva_dentista`
--
ALTER TABLE `reserva_dentista`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_id` (`page_id`);

--
-- Índices de tabela `respostas`
--
ALTER TABLE `respostas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `respostas2`
--
ALTER TABLE `respostas2`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `respostas2_data_encontrada`
--
ALTER TABLE `respostas2_data_encontrada`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `respostas3`
--
ALTER TABLE `respostas3`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `respostas_live`
--
ALTER TABLE `respostas_live`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `retiradas`
--
ALTER TABLE `retiradas`
  ADD PRIMARY KEY (`registro_id`);

--
-- Índices de tabela `rfid_tags`
--
ALTER TABLE `rfid_tags`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `selena_entregas_whatsapp`
--
ALTER TABLE `selena_entregas_whatsapp`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `selena_identificacao`
--
ALTER TABLE `selena_identificacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `selena_lojas`
--
ALTER TABLE `selena_lojas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `selena_produtos`
--
ALTER TABLE `selena_produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `selena_users`
--
ALTER TABLE `selena_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `selena_uso_itens`
--
ALTER TABLE `selena_uso_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `sites_parceiros`
--
ALTER TABLE `sites_parceiros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_api` (`token_api`);

--
-- Índices de tabela `status_table`
--
ALTER TABLE `status_table`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `taxis`
--
ALTER TABLE `taxis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tickets_transporte`
--
ALTER TABLE `tickets_transporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barco_id` (`barco_id`);

--
-- Índices de tabela `token_qrcode`
--
ALTER TABLE `token_qrcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Índices de tabela `transacoes_aura`
--
ALTER TABLE `transacoes_aura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tratores`
--
ALTER TABLE `tratores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tratores_heycar`
--
ALTER TABLE `tratores_heycar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`,`placa`);

--
-- Índices de tabela `trator_oleo`
--
ALTER TABLE `trator_oleo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `treinos`
--
ALTER TABLE `treinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `treinos_boxe`
--
ALTER TABLE `treinos_boxe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `treinos_users`
--
ALTER TABLE `treinos_users`
  ADD PRIMARY KEY (`user_id`,`dia`,`exercicio`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loja_etiqueta` (`loja_etiqueta`);

--
-- Índices de tabela `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_card` (`username`,`id_cards`),
  ADD KEY `id_cards` (`id_cards`);

--
-- Índices de tabela `user_configs`
--
ALTER TABLE `user_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `share_token` (`share_token`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfid_uid` (`rfid_uid`);

--
-- Índices de tabela `usuarios_logados`
--
ALTER TABLE `usuarios_logados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario_fake`
--
ALTER TABLE `usuario_fake`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `voos`
--
ALTER TABLE `voos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `votos`
--
ALTER TABLE `votos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `votos_fake`
--
ALTER TABLE `votos_fake`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `webrtc_candidates`
--
ALTER TABLE `webrtc_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`);

--
-- Índices de tabela `webrtc_rooms`
--
ALTER TABLE `webrtc_rooms`
  ADD PRIMARY KEY (`room`),
  ADD KEY `fk_compromisso` (`compromisso_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acessos_mensagem`
--
ALTER TABLE `acessos_mensagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `acessos_transmissao`
--
ALTER TABLE `acessos_transmissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `assentos`
--
ALTER TABLE `assentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `assinantenv3`
--
ALTER TABLE `assinantenv3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `assinaturas_pdf`
--
ALTER TABLE `assinaturas_pdf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `auditoria_conversao`
--
ALTER TABLE `auditoria_conversao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `auras`
--
ALTER TABLE `auras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aura_compromisso`
--
ALTER TABLE `aura_compromisso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aura_envios`
--
ALTER TABLE `aura_envios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aura_recebida`
--
ALTER TABLE `aura_recebida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `barcos`
--
ALTER TABLE `barcos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `bixos`
--
ALTER TABLE `bixos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `blocos`
--
ALTER TABLE `blocos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cadastro_produto`
--
ALTER TABLE `cadastro_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caixa_postal`
--
ALTER TABLE `caixa_postal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caminhoes_heycar`
--
ALTER TABLE `caminhoes_heycar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `canecas_personalizadas`
--
ALTER TABLE `canecas_personalizadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caronas`
--
ALTER TABLE `caronas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caronas_passageiros`
--
ALTER TABLE `caronas_passageiros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caronas_passageiros_ufsm`
--
ALTER TABLE `caronas_passageiros_ufsm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caronas_ufsm`
--
ALTER TABLE `caronas_ufsm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinho_compras`
--
ALTER TABLE `carrinho_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinho_concluido`
--
ALTER TABLE `carrinho_concluido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `catalogo_musica`
--
ALTER TABLE `catalogo_musica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `certificado_ableton`
--
ALTER TABLE `certificado_ableton`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chamadas`
--
ALTER TABLE `chamadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chats_encerrados`
--
ALTER TABLE `chats_encerrados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chat_mensagens`
--
ALTER TABLE `chat_mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidades_coords`
--
ALTER TABLE `cidades_coords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compact_fotos`
--
ALTER TABLE `compact_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compact_imoveis`
--
ALTER TABLE `compact_imoveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compras_aura`
--
ALTER TABLE `compras_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compras_aurea`
--
ALTER TABLE `compras_aurea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `comprovantes_aura`
--
ALTER TABLE `comprovantes_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `comprovantes_quitacao`
--
ALTER TABLE `comprovantes_quitacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `confirmado_aura`
--
ALTER TABLE `confirmado_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consorcio_cadastro`
--
ALTER TABLE `consorcio_cadastro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consorcio_cadastro2`
--
ALTER TABLE `consorcio_cadastro2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consorcio_cadastro3`
--
ALTER TABLE `consorcio_cadastro3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consorcio_contrato`
--
ALTER TABLE `consorcio_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consultorio`
--
ALTER TABLE `consultorio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consultorio_odonto2`
--
ALTER TABLE `consultorio_odonto2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conversas`
--
ALTER TABLE `conversas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conversoes_tibia`
--
ALTER TABLE `conversoes_tibia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `corridas`
--
ALTER TABLE `corridas`
  MODIFY `id_corrida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `crachas_ativos`
--
ALTER TABLE `crachas_ativos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `datas_voo`
--
ALTER TABLE `datas_voo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dentistas`
--
ALTER TABLE `dentistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `descargas`
--
ALTER TABLE `descargas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `endereco_transacao`
--
ALTER TABLE `endereco_transacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entregas_whatsapp`
--
ALTER TABLE `entregas_whatsapp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `escolas`
--
ALTER TABLE `escolas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `essencias`
--
ALTER TABLE `essencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estacionamento_log`
--
ALTER TABLE `estacionamento_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fila_espera`
--
ALTER TABLE `fila_espera`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos_recebedor`
--
ALTER TABLE `fotos_recebedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos_tibia`
--
ALTER TABLE `fotos_tibia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_equipamento`
--
ALTER TABLE `historico_equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_eventos`
--
ALTER TABLE `historico_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_movimentos`
--
ALTER TABLE `historico_movimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_status`
--
ALTER TABLE `historico_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_tipo`
--
ALTER TABLE `historico_tipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_trocas_oleo`
--
ALTER TABLE `historico_trocas_oleo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `identificacao`
--
ALTER TABLE `identificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `identificacao_odonto2`
--
ALTER TABLE `identificacao_odonto2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imagens_barco`
--
ALTER TABLE `imagens_barco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imagens_bixo`
--
ALTER TABLE `imagens_bixo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imagens_quarto`
--
ALTER TABLE `imagens_quarto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_loja_odonto2`
--
ALTER TABLE `itens_loja_odonto2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lista_espera`
--
ALTER TABLE `lista_espera`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `localizacoes`
--
ALTER TABLE `localizacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lojas_odonto2`
--
ALTER TABLE `lojas_odonto2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `maquinas`
--
ALTER TABLE `maquinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `maquinas_locacao`
--
ALTER TABLE `maquinas_locacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens_ocultas`
--
ALTER TABLE `mensagens_ocultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens_privadas`
--
ALTER TABLE `mensagens_privadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `metas_aura`
--
ALTER TABLE `metas_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nome_cars_itens`
--
ALTER TABLE `nome_cars_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nome_cars_pagamentos`
--
ALTER TABLE `nome_cars_pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `numeros`
--
ALTER TABLE `numeros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `odonto2_perfis`
--
ALTER TABLE `odonto2_perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `odonto2_users`
--
ALTER TABLE `odonto2_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `oil_levels`
--
ALTER TABLE `oil_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_carrinho_2025`
--
ALTER TABLE `pagamentos_carrinho_2025`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_destaque`
--
ALTER TABLE `pagamentos_destaque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_mercadopago`
--
ALTER TABLE `pagamentos_mercadopago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_premium`
--
ALTER TABLE `pagamentos_premium`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_recebidos`
--
ALTER TABLE `pagamentos_recebidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamento_musica`
--
ALTER TABLE `pagamento_musica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `parcelas`
--
ALTER TABLE `parcelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_assinatura`
--
ALTER TABLE `pedidos_assinatura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_aura`
--
ALTER TABLE `pedidos_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_cafe`
--
ALTER TABLE `pedidos_cafe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_livros`
--
ALTER TABLE `pedidos_livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_pix`
--
ALTER TABLE `pedidos_pix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `performance_log`
--
ALTER TABLE `performance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pontuacao_godot`
--
ALTER TABLE `pontuacao_godot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pontuacoes`
--
ALTER TABLE `pontuacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portais_usados`
--
ALTER TABLE `portais_usados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `posicoes_player_cafeteria`
--
ALTER TABLE `posicoes_player_cafeteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `produto_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `quartos`
--
ALTER TABLE `quartos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `quartos_farol`
--
ALTER TABLE `quartos_farol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `radioterapia_cobalto_nic`
--
ALTER TABLE `radioterapia_cobalto_nic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ranking_hashes`
--
ALTER TABLE `ranking_hashes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `registrointerno`
--
ALTER TABLE `registrointerno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `registrointerno2`
--
ALTER TABLE `registrointerno2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_barcos`
--
ALTER TABLE `reservas_barcos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_pendentes`
--
ALTER TABLE `reservas_pendentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_pix`
--
ALTER TABLE `reservas_pix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_pix_bixos`
--
ALTER TABLE `reservas_pix_bixos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_quarto`
--
ALTER TABLE `reservas_quarto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas_voo`
--
ALTER TABLE `reservas_voo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reserva_data_barcos`
--
ALTER TABLE `reserva_data_barcos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reserva_dentista`
--
ALTER TABLE `reserva_dentista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `respostas`
--
ALTER TABLE `respostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `respostas2`
--
ALTER TABLE `respostas2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `respostas2_data_encontrada`
--
ALTER TABLE `respostas2_data_encontrada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `respostas3`
--
ALTER TABLE `respostas3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `respostas_live`
--
ALTER TABLE `respostas_live`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `retiradas`
--
ALTER TABLE `retiradas`
  MODIFY `registro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `rfid_tags`
--
ALTER TABLE `rfid_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_entregas_whatsapp`
--
ALTER TABLE `selena_entregas_whatsapp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_identificacao`
--
ALTER TABLE `selena_identificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_lojas`
--
ALTER TABLE `selena_lojas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_produtos`
--
ALTER TABLE `selena_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_users`
--
ALTER TABLE `selena_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `selena_uso_itens`
--
ALTER TABLE `selena_uso_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sites_parceiros`
--
ALTER TABLE `sites_parceiros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_table`
--
ALTER TABLE `status_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `taxis`
--
ALTER TABLE `taxis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tickets_transporte`
--
ALTER TABLE `tickets_transporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `token_qrcode`
--
ALTER TABLE `token_qrcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transacoes_aura`
--
ALTER TABLE `transacoes_aura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tratores`
--
ALTER TABLE `tratores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tratores_heycar`
--
ALTER TABLE `tratores_heycar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `trator_oleo`
--
ALTER TABLE `trator_oleo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `treinos`
--
ALTER TABLE `treinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `treinos_boxe`
--
ALTER TABLE `treinos_boxe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `user_cards`
--
ALTER TABLE `user_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `user_configs`
--
ALTER TABLE `user_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios_logados`
--
ALTER TABLE `usuarios_logados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `voos`
--
ALTER TABLE `voos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `votos`
--
ALTER TABLE `votos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `votos_fake`
--
ALTER TABLE `votos_fake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `webrtc_candidates`
--
ALTER TABLE `webrtc_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`escola_id`) REFERENCES `escolas` (`id`),
  ADD CONSTRAINT `alunos_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `compact_fotos`
--
ALTER TABLE `compact_fotos`
  ADD CONSTRAINT `compact_fotos_ibfk_1` FOREIGN KEY (`imovel_id`) REFERENCES `compact_imoveis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `comprovantes_quitacao`
--
ALTER TABLE `comprovantes_quitacao`
  ADD CONSTRAINT `comprovantes_quitacao_ibfk_1` FOREIGN KEY (`emprestimo_id`) REFERENCES `emprestimos` (`id`),
  ADD CONSTRAINT `fk_comprovante_emprestimo` FOREIGN KEY (`emprestimo_id`) REFERENCES `emprestimos` (`id`);

--
-- Restrições para tabelas `corridas`
--
ALTER TABLE `corridas`
  ADD CONSTRAINT `corridas_ibfk_1` FOREIGN KEY (`taxi_id`) REFERENCES `taxis` (`id`);

--
-- Restrições para tabelas `datas_voo`
--
ALTER TABLE `datas_voo`
  ADD CONSTRAINT `datas_voo_ibfk_1` FOREIGN KEY (`voo_id`) REFERENCES `voos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `imagens_barco`
--
ALTER TABLE `imagens_barco`
  ADD CONSTRAINT `imagens_barco_ibfk_1` FOREIGN KEY (`barco_id`) REFERENCES `barcos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `imagens_bixo`
--
ALTER TABLE `imagens_bixo`
  ADD CONSTRAINT `imagens_bixo_ibfk_1` FOREIGN KEY (`bixo_id`) REFERENCES `bixos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD CONSTRAINT `imagens_produto_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `cadastro_produto` (`id`);

--
-- Restrições para tabelas `imagens_quarto`
--
ALTER TABLE `imagens_quarto`
  ADD CONSTRAINT `imagens_quarto_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `quartos` (`id`);

--
-- Restrições para tabelas `itens_loja_odonto2`
--
ALTER TABLE `itens_loja_odonto2`
  ADD CONSTRAINT `itens_loja_odonto2_ibfk_1` FOREIGN KEY (`loja_id`) REFERENCES `lojas_odonto2` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `lista_espera`
--
ALTER TABLE `lista_espera`
  ADD CONSTRAINT `lista_espera_ibfk_1` FOREIGN KEY (`escola_id`) REFERENCES `escolas` (`id`),
  ADD CONSTRAINT `lista_espera_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `odonto2_users` (`id`);

--
-- Restrições para tabelas `nome_cars_itens`
--
ALTER TABLE `nome_cars_itens`
  ADD CONSTRAINT `nome_cars_itens_ibfk_1` FOREIGN KEY (`numeroid`) REFERENCES `nome_cars` (`numeroid`),
  ADD CONSTRAINT `nome_cars_itens_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `cadastro_produto` (`id`);

--
-- Restrições para tabelas `odonto2_users_perfil`
--
ALTER TABLE `odonto2_users_perfil`
  ADD CONSTRAINT `odonto2_users_perfil_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `odonto2_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `odonto2_users_perfil_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `odonto2_perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pontuacoes`
--
ALTER TABLE `pontuacoes`
  ADD CONSTRAINT `pontuacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios_logados` (`id`);

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `quartos` (`id`);

--
-- Restrições para tabelas `reservas_barcos`
--
ALTER TABLE `reservas_barcos`
  ADD CONSTRAINT `reservas_barcos_ibfk_1` FOREIGN KEY (`barco_id`) REFERENCES `barcos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reservas_pix_bixos`
--
ALTER TABLE `reservas_pix_bixos`
  ADD CONSTRAINT `reservas_pix_bixos_ibfk_1` FOREIGN KEY (`bixo_id`) REFERENCES `bixos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reservas_quarto`
--
ALTER TABLE `reservas_quarto`
  ADD CONSTRAINT `reservas_quarto_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `quartos_farol` (`id`);

--
-- Restrições para tabelas `selena_uso_itens`
--
ALTER TABLE `selena_uso_itens`
  ADD CONSTRAINT `selena_uso_itens_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `selena_produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tickets_transporte`
--
ALTER TABLE `tickets_transporte`
  ADD CONSTRAINT `tickets_transporte_ibfk_1` FOREIGN KEY (`barco_id`) REFERENCES `barcos` (`id`);

--
-- Restrições para tabelas `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `odonto2_users` (`id`);

--
-- Restrições para tabelas `treinos_boxe`
--
ALTER TABLE `treinos_boxe`
  ADD CONSTRAINT `treinos_boxe_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `odonto2_users` (`id`);

--
-- Restrições para tabelas `treinos_users`
--
ALTER TABLE `treinos_users`
  ADD CONSTRAINT `treinos_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `odonto2_users` (`id`);

--
-- Restrições para tabelas `user_cards`
--
ALTER TABLE `user_cards`
  ADD CONSTRAINT `user_cards_ibfk_1` FOREIGN KEY (`id_cards`) REFERENCES `barcos` (`id`);

--
-- Restrições para tabelas `webrtc_candidates`
--
ALTER TABLE `webrtc_candidates`
  ADD CONSTRAINT `webrtc_candidates_ibfk_1` FOREIGN KEY (`room`) REFERENCES `webrtc_rooms` (`room`) ON DELETE CASCADE;

--
-- Restrições para tabelas `webrtc_rooms`
--
ALTER TABLE `webrtc_rooms`
  ADD CONSTRAINT `fk_compromisso` FOREIGN KEY (`compromisso_id`) REFERENCES `consultorio_odonto2` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
