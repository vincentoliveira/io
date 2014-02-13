# language: fr
@plat
Fonctionnalité: Créer et modifier des plats


Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit l'utilisateur "resto" a pour restaurant "Restaurant test"
    Soit je supprime tous les "IOMenuBundle:Category"
    Soit je crée une catégorie "Entrées" pour le restaurant "Restaurant test"


@6.1
Scénario: 06.1 - Créer un plat : Authorisations
    Soit je suis sur "/plat/new"
    Alors je ne devrais pas voir "Ajouter un plat"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Et l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Et je suis connecté en tant que "cuisto"
    Et je suis sur "/plat/new"
    Alors je ne devrais pas voir "Ajouter un plat"

    Et je suis connecté en tant que "resto"
    Et je suis sur "/plat/new"
    Alors je devrais voir "Ajouter un plat"
 
   
@6.2
Scénario: 06.2 -  Créer un plat
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je ne devrais pas voir "Salade Caesar"

    Soit je suis sur "/plat/new"
    Et je remplis "dish[name]" avec "Salade Caesar"
    Et je remplis "dish[description]" avec "Salade au poulet sauce Caesar"
    Et je remplis "dish[price]" avec "6,50"
    Et je sélectionne "Entrées" depuis "dish[category]"
    Et je presse "Ajouter"
    Alors je devrais voir "Le plat à bien été ajouté"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je devrais voir "Salade Caesar"
    Et je devrais voir "Salade au poulet sauce Caesar"
    Et je devrais voir "6,50 €"


@6.3
Scénario: 06.3 -  Modifier un plat    
    Soit je crée un plat "Salade Nicoise" dans la category "Entrées" du "Restaurant test"
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je devrais voir "Salade Nicoise"
    Et je ne devrais pas voir "Salade Caesar"

    Soit je suis "Modifier Salade Nicoise"
    Et je remplis "dish[name]" avec "Salade Caesar"
    Et je remplis "dish[description]" avec "Salade au poulet sauce Caesar"
    Et je remplis "dish[price]" avec "6,50"
    Et je presse "Modifier"
    Alors je devrais voir "Le plat à bien été modifié"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je devrais voir "Salade Caesar"
    Et je ne devrais pas voir "Salade Nicoise"
    Et je devrais voir "Salade au poulet sauce Caesar"
    Et je devrais voir "6,50 €"


@6.4
Scénario: 06.4 -  Supprimer un plat    
    Soit je crée un plat "Salade Nicoise" dans la category "Entrées" du "Restaurant test"
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je devrais voir "Salade Nicoise"

    Soit je suis "Modifier Salade Nicoise"
    Et je presse "Supprimer"
    Alors je devrais voir "Le plat à bien été supprimé"

    Soit je suis sur "/carte"
    Et je suis "Entrées"
    Alors je ne devrais pas voir "Salade Nicoise"

