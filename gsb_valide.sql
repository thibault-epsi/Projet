/*
Navicat MySQL Data Transfer

Source Server         : site
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : gsb_valide

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2015-03-11 16:01:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `etat`
-- ----------------------------
DROP TABLE IF EXISTS `etat`;
CREATE TABLE `etat` (
  `id` char(2) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of etat
-- ----------------------------
INSERT INTO `etat` VALUES ('CL', 'Saisie cl');
INSERT INTO `etat` VALUES ('CR', 'Fiche cr');
INSERT INTO `etat` VALUES ('RB', 'Rembours');
INSERT INTO `etat` VALUES ('VA', 'Valid');

-- ----------------------------
-- Table structure for `fichefrais`
-- ----------------------------
DROP TABLE IF EXISTS `fichefrais`;
CREATE TABLE `fichefrais` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `nbJustificatifs` int(11) DEFAULT NULL,
  `montantValide` decimal(10,2) DEFAULT NULL,
  `dateModif` date DEFAULT NULL,
  `idEtat` char(2) DEFAULT 'CR',
  PRIMARY KEY (`idVisiteur`,`mois`),
  KEY `idEtat` (`idEtat`),
  CONSTRAINT `fichefrais_ibfk_1` FOREIGN KEY (`idEtat`) REFERENCES `etat` (`id`),
  CONSTRAINT `fichefrais_ibfk_2` FOREIGN KEY (`idVisiteur`) REFERENCES `visiteur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fichefrais
-- ----------------------------

-- ----------------------------
-- Table structure for `fraisforfait`
-- ----------------------------
DROP TABLE IF EXISTS `fraisforfait`;
CREATE TABLE `fraisforfait` (
  `id` char(3) NOT NULL,
  `libelle` char(20) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fraisforfait
-- ----------------------------
INSERT INTO `fraisforfait` VALUES ('ETP', 'Forfait Etape', '110.00');
INSERT INTO `fraisforfait` VALUES ('KM', 'Frais Kilom', '0.62');
INSERT INTO `fraisforfait` VALUES ('NUI', 'Nuit', '80.00');
INSERT INTO `fraisforfait` VALUES ('REP', 'Repas Restaurant', '25.00');

-- ----------------------------
-- Table structure for `lignefraisforfait`
-- ----------------------------
DROP TABLE IF EXISTS `lignefraisforfait`;
CREATE TABLE `lignefraisforfait` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `idFraisForfait` char(3) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVisiteur`,`mois`,`idFraisForfait`),
  KEY `idFraisForfait` (`idFraisForfait`),
  CONSTRAINT `lignefraisforfait_ibfk_1` FOREIGN KEY (`idVisiteur`, `mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`),
  CONSTRAINT `lignefraisforfait_ibfk_2` FOREIGN KEY (`idFraisForfait`) REFERENCES `fraisforfait` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lignefraisforfait
-- ----------------------------

-- ----------------------------
-- Table structure for `lignefraishorsforfait`
-- ----------------------------
DROP TABLE IF EXISTS `lignefraishorsforfait`;
CREATE TABLE `lignefraishorsforfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idVisiteur` (`idVisiteur`,`mois`),
  CONSTRAINT `lignefraishorsforfait_ibfk_1` FOREIGN KEY (`idVisiteur`, `mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lignefraishorsforfait
-- ----------------------------

-- ----------------------------
-- Table structure for `visiteur`
-- ----------------------------
DROP TABLE IF EXISTS `visiteur`;
CREATE TABLE `visiteur` (
  `id` char(4) NOT NULL,
  `nom` char(30) DEFAULT NULL,
  `prenom` char(30) DEFAULT NULL,
  `login` char(20) DEFAULT NULL,
  `mdp` char(20) DEFAULT NULL,
  `adresse` char(30) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `ville` char(30) DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of visiteur
-- ----------------------------
INSERT INTO `visiteur` VALUES ('a131', 'Villechalane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', '1');
INSERT INTO `visiteur` VALUES ('a17', 'Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', '0');
INSERT INTO `visiteur` VALUES ('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', '0');
INSERT INTO `visiteur` VALUES ('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', '0');
INSERT INTO `visiteur` VALUES ('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', '11 all', '46512', 'Bessines', '1992-07-09', '0');
INSERT INTO `visiteur` VALUES ('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', '0');
INSERT INTO `visiteur` VALUES ('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', '0');
INSERT INTO `visiteur` VALUES ('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05', '0');
INSERT INTO `visiteur` VALUES ('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12', '0');
INSERT INTO `visiteur` VALUES ('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la R', '75011', 'Paris', '2008-09-23', '0');
INSERT INTO `visiteur` VALUES ('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12', '0');
INSERT INTO `visiteur` VALUES ('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 all', '93230', 'Romainville', '2003-08-11', '0');
INSERT INTO `visiteur` VALUES ('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', '0');
INSERT INTO `visiteur` VALUES ('c14', 'Daburon', 'Fran', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Cr', '2002-02-11', '0');
INSERT INTO `visiteur` VALUES ('c3', 'De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Cr', '2010-12-14', '0');
INSERT INTO `visiteur` VALUES ('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', '0');
INSERT INTO `visiteur` VALUES ('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 all', '44000', 'Nantes', '2000-05-11', '0');
INSERT INTO `visiteur` VALUES ('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', '0');
INSERT INTO `visiteur` VALUES ('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orl', '2005-11-12', '0');
INSERT INTO `visiteur` VALUES ('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des C', '23200', 'Gu', '2001-02-05', '0');
INSERT INTO `visiteur` VALUES ('e39', 'Dudouit', 'Fr', 'fdudouit', '44im8', '18 rue de l', '23120', 'GrandBourg', '2000-08-01', '0');
INSERT INTO `visiteur` VALUES ('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', '0');
INSERT INTO `visiteur` VALUES ('e5', 'Enault-Pascreau', 'C', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01', '0');
INSERT INTO `visiteur` VALUES ('e52', 'Eynde', 'Val', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01', '0');
INSERT INTO `visiteur` VALUES ('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', '0');
INSERT INTO `visiteur` VALUES ('f39', 'Fr', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', '0');
INSERT INTO `visiteur` VALUES ('f4', 'Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', '0');
