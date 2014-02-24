# language: fr
@menu
Fonctionnalité: Créer et modifier les menus


@7.1
Scénario: 07.1 - Créer un menus : Authorisations - Non connecté
    Soit je suis sur "/menu/new"
    Alors je ne devrais pas voir "Ajouter un menu"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/menu/new"
    Alors je ne devrais pas voir "Ajouter un menu"
    
    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit l'utilisateur "resto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"
    Soit je suis sur "/menu/new"
    Alors je devrais voir "Ajouter un menu"