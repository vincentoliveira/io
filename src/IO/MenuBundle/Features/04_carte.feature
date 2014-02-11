# language: fr
@carte
Fonctionnalité: Voir la carte

Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit je supprime tous les "IOMenuBundle:Category"
    Soit je crée une category "Entrée" pour le restaurant "Restaurant test"
    Soit je crée un plat "Salade" dans la category "Entrée" du "Restaurant test"
    

@4.1
Scénario: 04.1 - Voir la carte : Authorisations - Non connecté
    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "La carte"

@4.2
Scénario: 04.2 - Voir la carte : Authorisations - Cuisinier
    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "cuisto"
    Alors je ne devrais pas voir "Carte"

@4.3
Scénario: 04.3 - Voir la carte : Authorisations - Restaurateur
    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit l'utilisateur "resto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"
    Alors je devrais voir "Carte"
    Soit je suis "Carte"
    Et je devrais voir "Entrée"
    Soit je suis "Entrée"
    Alors je devrais voir "Salade"

@4.4
Scénario: 04.4 - Voir la carte : Authorisations - Admin
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit l'utilisateur "admin" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/carte"
    Soit je suis "Carte"
    Alors je devrais voir "Restaurant test"
    Soit je sélectionne "Restaurant test" depuis "select[restaurant]"
    Et je presse "Voir la carte"
    Et je devrais voir "Entrée"
    Soit je suis "Entrée"
    Alors je devrais voir "Salade"

