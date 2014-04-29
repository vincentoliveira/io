# language: fr
@carte @categorie
Fonctionnalité: Création/modification/suppression des categories


Contexte:
    Soit je vide les entités "IORestaurantBundle:CarteItem"
    Soit l'utilisateur "restotest" existe et a le role "ROLE_MANAGER" du restaurant "Restaurant test"
    Soit je suis connecté en tant que "restotest"


@C1
Scénario: C1 -  Création/modification/suppression d'une categorie
    # La categorie n'existe pas
    Soit je suis sur la page d'accueil
    Alors je ne devrais pas voir "Categorie Test"

    # Creation de la categorie
    Soit je suis "Ajouter une categorie"
    Et je presse "Ajouter"
    Alors je ne devrais pas voir "La categorie \"Categorie Test\" a bien été ajoutée"
    Lorsque je remplis "category[name]" avec "Categorie Test"
    Et je presse "Ajouter"
    Alors je devrais voir "La categorie \"Categorie Test\" a bien été ajoutée"

    # Modification de la categorie
    Soit je suis sur la page d'accueil
    Alors je devrais voir "Categorie Test"
    Soit je suis "Categorie Test"
    Et je suis "Modifier Categorie Test"
    Lorsque je remplis "category[name]" avec ""
    Et je presse "Valider"
    Alors je ne devrais pas voir "La categorie \"Categorie Test\" a bien été modifiée"
    Lorsque je remplis "category[name]" avec "Categorie Nouveau Test"
    Et je presse "Valider"
    Alors je devrais voir "La categorie \"Categorie Nouveau Test\" a bien été modifiée"

    # Suppression de la categorie
    Soit je suis sur la page d'accueil
    Alors je devrais voir "Categorie Nouveau Test"
    Soit je suis "Categorie Nouveau Test"
    Soit je suis "Supprimer Categorie Nouveau Test"
    Alors je devrais voir "La categorie \"Categorie Nouveau Test\" a bien été supprimée"

    # La categorie n'existe plus
    Soit je suis sur la page d'accueil
    Alors je ne devrais pas voir "Categorie Test"


@C2
Scénario: C2 -  Création d'une sous-categorie
    # Creation de la categorie
    Soit je suis sur la page d'accueil
    Soit je suis "Ajouter une categorie"
    Lorsque je remplis "category[name]" avec "Categorie Test"
    Et je presse "Ajouter"
    Alors je devrais voir "La categorie \"Categorie Test\" a bien été ajoutée"

    # Creation de la sous-categorie
    Soit je suis sur la page d'accueil
    Alors je devrais voir "Categorie Test"
    Soit je suis "Categorie Test"
    Alors je devrais voir "Ajouter une sous-catégorie"
    Et je ne devrais pas voir "Sous Categorie Test"
    Lorsque je suis "Ajouter une sous-catégorie"
    Et je remplis "category[name]" avec "Sous Categorie Test"
    Et je presse "Ajouter"
    Alors je devrais voir "La categorie \"Sous Categorie Test\" a bien été ajoutée"
    
    # La sous-categorie existe
    Soit je suis sur la page d'accueil
    Et je suis "Categorie Test"
    Alors je devrais voir "Sous Categorie Test"