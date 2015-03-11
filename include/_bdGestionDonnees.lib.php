<?php
/** 
 * Regroupe les fonctions d'acc�s aux donn�es.
 * @package default
 * @author Arthur Martin
 * @todo Fonctions retournant plusieurs lignes sont � r��crire.
 */

/** 
 * Se connecte au serveur de donn�es MySql.                      
 * Se connecte au serveur de donn�es MySql � partir de valeurs
 * pr�d�finies de connexion (h�te, compte utilisateur et mot de passe). 
 * Retourne l'identifiant de connexion si succ�s obtenu, le bool�en false 
 * si probl�me de connexion.
 * @return resource identifiant de connexion
 */
function connecterServeurBD() {
    $hote = "localhost";
    $login = "root";
    $mdp = "root";
    $dbh = new PDO('mysql:host='.$hote.';dbname=gsb_valide', $login, $mdp);
    return $dbh;
}
/** 
 * Ferme la connexion au serveur de donn�es.
 * Ferme la connexion au serveur de donn�es identifi�e par l'identifiant de 
 * connexion $idCnx.
 * @param resource $idCnx identifiant de connexion
 * @return void  
 */
function deconnecterServeurBD($dbh) {
    $dbh = null;
}


/** 
 * Fournit les informations sur un visiteur demand�. 
 * Retourne les informations du visiteur d'id $unId sous la forme d'un tableau
 * associatif dont les cl�s sont les noms des colonnes(id, nom, prenom).
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array  tableau associatif du visiteur
 */

function obtenirDetailVisiteur($dbh, $unId) {
    $requete = "select id, nom, prenom from visiteur where id='" . $unId . "'";
    $idJeuRes = $dbh->prepare($requete);
    $idJeuRes->execute();
    $ligne = false;
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        $idJeuRes->closeCursor();
    }
    return $ligne ;
}

/** 
 * Fournit les informations d'une fiche de frais. 
 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
 * sous la forme d'un tableau associatif dont les cl�s sont les noms des colonnes
 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return array tableau associatif de la fiche de frais
 */
function obtenirDetailFicheFrais($dbh, $unMois, $unIdVisiteur) {
    $ligne = false;
    $requete="select IFNULL(nbJustificatifs,0) as nbJustificatifs, Etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide 
    from FicheFrais inner join Etat on idEtat = Etat.id 
    where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    $idJeuRes = $dbh->prepare($requete);
    $idJeuRes->execute();
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
    }        
    $idJeuRes->closeCursor();
    
    return $ligne;
}
              
/** 
 * V�rifie si une fiche de frais existe ou non. 
 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur existe, false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return bool�en existence ou non de la fiche de frais
 */
function existeFicheFrais($dbh, $unMois, $unIdVisiteur) {
    $requete = "select idVisiteur from FicheFrais where idVisiteur='" . $unIdVisiteur . 
              "' and mois='" . $unMois . "'";
    $idJeuRes = $dbh->prepare($requete);
    $idJeuRes->execute();
    $ligne = false;
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        $idJeuRes->closeCursor();
    }        
    
    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
    return is_array($ligne);
}

/** 
 * Fournit le mois de la derni�re fiche de frais d'un visiteur.
 * Retourne le mois de la derni�re fiche de frais du visiteur d'id $unIdVisiteur.
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur id visiteur  
 * @return string dernier mois sous la forme AAAAMM
 */
function obtenirDernierMoisSaisi($dbh, $unIdVisiteur) {
	$requete = "select max(mois) as dernierMois from FicheFrais where idVisiteur='" .
            $unIdVisiteur . "'";
	$idJeuRes = $dbh->prepare($requete);
    $idJeuRes->execute();
    $dernierMois = false;
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        $dernierMois = $ligne["dernierMois"];
        $idJeuRes->closeCursor();
    }        
	return $dernierMois;
}

/** 
 * Ajoute une nouvelle fiche de frais et les �l�ments forfaitis�s associ�s, 
 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur, avec les �l�ments forfaitis�s associ�s dont la quantit� initiale
 * est affect�e � 0. Cl�t �ventuellement la fiche de frais pr�c�dente du visiteur. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return void
 */
function ajouterFicheFrais($dbh, $unMois, $unIdVisiteur) {
    // modification de la derni�re fiche de frais du visiteur
    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);
	$laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);
	if ( is_array($laDerniereFiche) && $laDerniereFiche['idEtat']=='CR'){
		modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');
	}
    
    // ajout de la fiche de frais � l'�tat Cr��
    $requete = "insert into FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('" 
              . $unIdVisiteur 
              . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
    $result = $dbh->prepare($requete);
    $result->execute();
    
    // ajout des �l�ments forfaitis�s
    $requete = "select id from FraisForfait";
    $idJeuRes = $dbh->prepare($requete);
    $idJeuRes->execute();
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        while ( is_array($ligne) ) {
            $idFraisForfait = $ligne["id"];
            // insertion d'une ligne frais forfait dans la base
            $requete = "insert into LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite)
                        values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
            $result = $dbh->prepare($requete);
            $result->execute();
            // passage au frais forfait suivant
            $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        }
        $idJeuRes->closeCursor();
    }        
}

/**
 * Retourne le texte de la requ�te select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais. 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqMoisFicheFrais($unIdVisiteur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
            . $unIdVisiteur . "' order by fichefrais.mois desc ";
    return $req;
}  
                  
/**
 * Retourne le texte de la requ�te select concernant les �l�ments forfaitis�s 
 * d'un visiteur pour un mois donn�s. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir l'id, le libell� et la
 * quantit� des �l�ments forfaitis�s de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $requete = "select idFraisForfait, libelle, quantite from LigneFraisForfait
              inner join FraisForfait on FraisForfait.id = LigneFraisForfait.idFraisForfait
              where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Retourne le texte de la requ�te select concernant les �l�ments hors forfait 
 * d'un visiteur pour un mois donn�s. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir l'id, la date, le libell� 
 * et le montant des �l�ments hors forfait de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $requete = "select id, date, libelle, montant from LigneFraisHorsForfait
              where idVisiteur='" . $unIdVisiteur 
              . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Supprime une ligne hors forfait.
 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
 * @param resource $idCnx identifiant de connexion
 * @param string $idLigneHF id de la ligne hors forfait
 * @return void
 */
function supprimerLigneHF($dbh, $unIdLigneHF) {
    $requete = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
    $dbh->query($requete);
}

/**
 * Ajoute une nouvelle ligne hors forfait.
 * Ins�re dans la BD la ligne hors forfait de libell� $unLibelleHF du montant 
 * $unMontantHF ayant eu lieu � la date $uneDateHF pour la fiche de frais du mois
 * $unMois du visiteur d'id $unIdVisiteur
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (AAMMMM)
 * @param string $unIdVisiteur id du visiteur
 * @param string $uneDateHF date du frais hors forfait
 * @param string $unLibelleHF libell� du frais hors forfait 
 * @param double $unMontantHF montant du frais hors forfait
 * @return void
 */
function ajouterLigneHF($dbh, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
    $uneDateHF = convertirDateFrancaisVersAnglais($uneDateHF);
    $requete = "insert into LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant) 
                values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF .")";
    $dbh->query($requete);
}

/**
 * Modifie les quantit�s des �l�ments forfaitis�s d'une fiche de frais. 
 * Met � jour les �l�ments forfaitis�s contenus  
 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
 * le mois $unMois dans la table LigneFraisForfait, apr�s avoir filtr� 
 * (annul� l'effet de certains caract�res consid�r�s comme sp�ciaux par 
 *  MySql) chaque donn�e   
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA) 
 * @param string $unIdVisiteur  id visiteur
 * @param array $desEltsForfait tableau des quantit�s des �l�ments hors forfait
 * avec pour cl�s les identifiants des frais forfaitis�s 
 * @return void  
 */
function modifierEltsForfait($dbh, $unMois, $unIdVisiteur, $desEltsForfait) {
    foreach ($desEltsForfait as $idFraisForfait => $quantite) {
        $requete = "update LigneFraisForfait set quantite = " . $quantite 
                    . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"
                    . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
        $dbh->query($requete);
    }
}

/**
 * Contr�le les informations de connexionn d'un utilisateur.
 * V�rifie si les informations de connexion $unLogin, $unMdp sont ou non valides.
 * Retourne les informations de l'utilisateur sous forme de tableau associatif 
 * dont les cl�s sont les noms des colonnes (id, nom, prenom, login, mdp)
 * si login et mot de passe existent, le bool�en false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unLogin login 
 * @param string $unMdp mot de passe 
 * @return array tableau associatif ou bool�en false 
 */
function verifierInfosConnexion($dbh, $unLogin, $unMdp) {
    // le mot de passe est crypt� dans la base avec la fonction de hachage md5
    $req = "select id, nom, prenom, login, mdp from Visiteur where login='".$unLogin."' and mdp='" . $unMdp . "'";
    $idJeuRes = $dbh->prepare($req);
    $idJeuRes->execute();
    $ligne = false;
    if ( $idJeuRes ) {
        $ligne = $idJeuRes->fetch(PDO::FETCH_ASSOC);
        $idJeuRes->closeCursor();
    }
    return $ligne;
}

/**
 * Modifie l'�tat et la date de modification d'une fiche de frais
 
 * Met � jour l'�tat de la fiche de frais du visiteur $unIdVisiteur pour
 * le mois $unMois � la nouvelle valeur $unEtat et passe la date de modif � 
 * la date d'aujourd'hui
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur 
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function modifierEtatFicheFrais($dbh, $unMois, $unIdVisiteur, $unEtat) {
    $requete = "update FicheFrais set idEtat = '" . $unEtat . 
               "', dateModif = now() where idVisiteur ='" .
               $unIdVisiteur . "' and mois = '". $unMois . "'";
    $result = $dbh->$prepare($requete);
    $result->execute();
}             
?>