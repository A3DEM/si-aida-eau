-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  Dim 29 mai 2022 à 12:59
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `si_aida_eau`
--

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

CREATE TABLE `personne` (
  `idPersonne` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `adresse` varchar(100) DEFAULT NULL,
  `identifiant` varchar(50) DEFAULT NULL,
  `motdepasse` varchar(50) DEFAULT NULL,
  `role` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `quantiteeau`
--

CREATE TABLE `quantiteeau` (
  `idConsommation` int(11) NOT NULL,
  `dateConso` date DEFAULT NULL,
  `jour` smallint(6) DEFAULT NULL,
  `heureDebut` time DEFAULT NULL,
  `nbReleve` smallint(6) DEFAULT NULL,
  `heureFin` time DEFAULT NULL,
  `consoInit` int(11) DEFAULT NULL,
  `consoFinale` int(11) DEFAULT NULL,
  `idPersonne` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `personne`
--
ALTER TABLE `personne`
  ADD PRIMARY KEY (`idPersonne`);

--
-- Index pour la table `quantiteeau`
--
ALTER TABLE `quantiteeau`
  ADD PRIMARY KEY (`idConsommation`),
  ADD KEY `idPersonne` (`idPersonne`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `personne`
--
ALTER TABLE `personne`
  MODIFY `idPersonne` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `quantiteeau`
--
ALTER TABLE `quantiteeau`
  MODIFY `idConsommation` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `quantiteeau`
--
ALTER TABLE `quantiteeau`
  ADD CONSTRAINT `quantiteeau_ibfk_1` FOREIGN KEY (`idPersonne`) REFERENCES `personne` (`idPersonne`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
