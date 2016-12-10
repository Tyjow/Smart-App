# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Hôte: 127.0.0.1 (MySQL 5.7.14-8)
# Base de données: smartapp
# Temps de génération: 2016-12-10 17:18:26 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Affichage de la table activite_horaire
# ------------------------------------------------------------

CREATE TABLE `activite_horaire` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `marche_code` tinyint(3) unsigned zerofill NOT NULL,
  `marche_label` varchar(20) NOT NULL,
  `jour_date` date NOT NULL,
  `jour_libelle` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL COMMENT '	',
  `poid_ca` decimal(5,2) NOT NULL,
  `tickets_nb` tinyint(3) unsigned NOT NULL,
  `panier_moyen` decimal(7,2) NOT NULL,
  `quantites_vendues` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_horaire` (`heure_debut`,`heure_fin`),
  KEY `idx_marche` (`marche_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Affichage de la table entree_jour
# ------------------------------------------------------------

CREATE TABLE `entree_jour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total` int(11) DEFAULT NULL,
  `galerie_sud_percent` decimal(5,2) unsigned DEFAULT NULL,
  `galerie_sud_quantite` int(10) unsigned DEFAULT NULL,
  `porte_parking_percent` decimal(5,2) unsigned DEFAULT NULL,
  `porte_parking_quantite` int(10) unsigned DEFAULT NULL,
  `galerie_nord_percent` decimal(5,2) unsigned DEFAULT NULL,
  `galerie_nord_quantite` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_date_lieu` (`date`,`galerie_sud_quantite`,`porte_parking_quantite`,`galerie_nord_quantite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Affichage de la table evenement_divers
# ------------------------------------------------------------

CREATE TABLE `evenement_divers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `label` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Affichage de la table evenement_meteo
# ------------------------------------------------------------

CREATE TABLE `evenement_meteo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `label` tinyint(1) DEFAULT NULL,
  `pluie` tinyint(1) DEFAULT NULL,
  `neige` tinyint(1) DEFAULT NULL,
  `soleil` tinyint(1) DEFAULT NULL,
  `vent` tinyint(1) DEFAULT NULL,
  `temperature` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
