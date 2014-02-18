# language: fr
@carte
Fonctionnalité: Voir la carte

Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit je supprime tous les "IOCarteBundle:Category"
    Soit je crée une catégorie "Entrée" pour le restaurant "Restaurant test"
    Soit je crée un plat "Salade" dans la category "Entrée" du "Restaurant test"
    

@4.1
Scénario: 04.1 - Voir la carte : Authorisations - Non connecté
    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "Carte"
    Et je ne devrais pas voir "Entrée"

@4.2
Scénario: 04.2 - Voir la carte : Authorisations - Cuisinier
    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/carte"
    Alors le code de status de la réponse devrait être 403
    Et je ne devrais pas voir "Entrée"

@4.3
Scénario: 04.3 - Voir la carte : Authorisations - Restaurateur
    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit l'utilisateur "resto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"
    Soit je suis sur "/carte"
    Et je devrais voir "Entrée"
    Soit je suis "Entrée"
    Alors je devrais voir "Salade"

@4.4
Scénario: 04.4 - Voir la carte : Authorisations - Admin
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit l'utilisateur "admin" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/carte"
    Alors je devrais voir "Restaurant test"
    Soit je sélectionne "Restaurant test" depuis "select[restaurant]"
    Et je presse "Voir la carte"
    Et je devrais voir "Entrée"
    Soit je suis "Entrée"
    Alors je devrais voir "Salade"

