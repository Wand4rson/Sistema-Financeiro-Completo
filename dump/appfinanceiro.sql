/*
SQLyog Ultimate v8.5 
MySQL - 5.5.19 : Database - appfinanceiro
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`appfinanceiro` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `tab_categorias` */

DROP TABLE IF EXISTS `tab_categorias`;

CREATE TABLE `tab_categorias` (
  `user_codigo` int(11) NOT NULL DEFAULT '0' COMMENT 'Despesas Vinculadas por Usuarios, Cada um Configura a Sua',
  `tipo_lancamento` char(1) DEFAULT 'D' COMMENT 'D=Despesas, R=Receita',
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_descricao` varchar(100) NOT NULL DEFAULT '',
  `cat_ativo` char(3) NOT NULL DEFAULT 'sim',
  `cat_datacadastro` date DEFAULT NULL,
  `cat_horacadastro` varchar(8) DEFAULT NULL,
  `ip_lancamento` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `tab_categorias` */

insert  into `tab_categorias`(`user_codigo`,`tipo_lancamento`,`cat_id`,`cat_descricao`,`cat_ativo`,`cat_datacadastro`,`cat_horacadastro`,`ip_lancamento`) values (8,'R',4,'Receitas Diversas','sim','2020-02-20','21:08:56','::1');
insert  into `tab_categorias`(`user_codigo`,`tipo_lancamento`,`cat_id`,`cat_descricao`,`cat_ativo`,`cat_datacadastro`,`cat_horacadastro`,`ip_lancamento`) values (8,'D',5,'Despesas Diversas','sim','2020-02-20','21:08:56','::1');

/*Table structure for table `tab_lancamentos` */

DROP TABLE IF EXISTS `tab_lancamentos`;

CREATE TABLE `tab_lancamentos` (
  `user_codigo` int(11) NOT NULL DEFAULT '0' COMMENT 'Despesas por Usuario para Nao Misturar',
  `categoria_id` int(11) NOT NULL DEFAULT '0',
  `lanc_id` int(11) NOT NULL AUTO_INCREMENT,
  `lanc_documento` varchar(30) DEFAULT NULL,
  `lanc_descricao` varchar(100) DEFAULT NULL,
  `lanc_parcela` int(11) NOT NULL DEFAULT '0',
  `lanc_valor_totaldocumento` double DEFAULT NULL COMMENT 'Valor Total Geral do documento para ratear parcelas, caso seja pagamento recorrente.',
  `lanc_valor_porparcela` double DEFAULT NULL,
  `lanc_datalancamento` date DEFAULT NULL,
  `lanc_datavencimento` date DEFAULT NULL,
  `lanc_datapagamento` date DEFAULT NULL,
  `lanc_valor_pagamento` double DEFAULT NULL,
  `lanc_pago` char(3) DEFAULT 'nao',
  `lanc_datacadastro` date DEFAULT NULL COMMENT 'Data de Cadastro da Parcela Automatica pelo Server',
  `lanc_horacadastro` varchar(8) DEFAULT NULL COMMENT 'Hora de Cadastro da Parcela Automatica pelo Server',
  `ip_lancamento` varchar(100) DEFAULT NULL,
  `lanc_mult_qtdeparcelaslancar` int(11) DEFAULT NULL,
  `lanc_mult_intervalotipo` varchar(50) DEFAULT NULL COMMENT 'DiaFixo, QtdeDias',
  `lanc_mult_intervalo_dias_entre_asparcelas` int(11) DEFAULT NULL COMMENT 'Qtde Dias entre parcelas ex: 5, 10 ...',
  `lanc_mult_documento_chave` varchar(50) DEFAULT NULL COMMENT 'Concatena Documento Chave afim de excluir todas parcelas se necessario, Identificador dos documentos lancados como multiplos em comum',
  `lanc_mult_data_primeiro_vencimento` date DEFAULT NULL,
  PRIMARY KEY (`user_codigo`,`lanc_id`),
  UNIQUE KEY `lanc_id` (`lanc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tab_lancamentos` */

/*Table structure for table `tab_usuarios` */

DROP TABLE IF EXISTS `tab_usuarios`;

CREATE TABLE `tab_usuarios` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(150) NOT NULL DEFAULT '',
  `user_senha` varchar(50) NOT NULL DEFAULT '',
  `user_imagemperfil` varchar(150) DEFAULT NULL,
  `user_ativo` char(3) DEFAULT 'sim',
  `user_datacadastro` date DEFAULT NULL,
  `user_horacadastro` varchar(8) DEFAULT NULL,
  `ip_lancamento` varchar(100) DEFAULT NULL,
  `user_ultimoacesso` datetime DEFAULT NULL COMMENT 'Data e Hora do Ultimo Acesso ao sistema',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `tab_usuarios` */

insert  into `tab_usuarios`(`user_id`,`user_email`,`user_senha`,`user_imagemperfil`,`user_ativo`,`user_datacadastro`,`user_horacadastro`,`ip_lancamento`,`user_ultimoacesso`) values (8,'demo@site.com.br','827ccb0eea8a706c4c34a16891f84e7b',NULL,'sim','2020-02-20','21:08:56','::1','2020-02-20 21:11:55');

/*Table structure for table `tab_usuarios_token` */

DROP TABLE IF EXISTS `tab_usuarios_token`;

CREATE TABLE `tab_usuarios_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `token_hash` varchar(100) DEFAULT NULL,
  `token_usado` char(3) DEFAULT 'nao',
  `token_expira_em` date DEFAULT NULL,
  `ip_lancamento` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tab_usuarios_token` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
