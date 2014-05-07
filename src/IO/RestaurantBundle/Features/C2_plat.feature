# language: fr
@carte @plat @C2
Fonctionnalité: C2 - Création/modification/suppression des plats


Contexte:
    Soit l'utilisateur "restotest" existe et a le role "ROLE_MANAGER" du restaurant "Restaurant test"
    Soit je suis connecté en tant que "restotest"
    Soit je vide les entités "IORestaurantBundle:CarteItem"
    Soit les items suivants existent:
        | Restaurant      | Name           | Visible | ItemType |
        | Restaurant test | Categorie Test | 1       | CATEGORY |


@C2.1
Scénario: C2.1 -  Création/modification/suppression d'un plat
    # Le plat n'existe pas
    Soit je suis sur la page d'accueil
    Alors je devrais voir "Categorie Test"
    Soit je suis "Categorie Test"
    Alors je ne devrais pas voir "Plat Test"

    # Creation du plat
    Soit je suis "Ajouter un plat"
    Et je presse "Ajouter"
    Alors je devrais voir "Veuillez renseigner un nom"
    Lorsque je remplis "dish[name]" avec "Plat Test"
    Et je presse "Ajouter"
    Alors je devrais voir "Le plat \"Plat Test\" a bien été ajouté"

    # Modification de la categorie
    Soit je suis sur la page d'accueil
    Et je suis "Categorie Test"
    Alors je devrais voir "Plat Test"
    Soit je suis "Modifier Plat Test"
    Lorsque je remplis "dish[name]" avec ""
    Et je presse "Valider"
    Alors je devrais voir "Veuillez renseigner un nom"
    Lorsque je remplis "dish[name]" avec "Nouveau Plat Test"
    Et je presse "Valider"
    Alors je devrais voir "Le plat \"Nouveau Plat Test\" a bien été modifié"

    # Suppression de la categorie
    Soit je suis sur la page d'accueil
    Et je suis "Categorie Test"
    Alors je devrais voir "Nouveau Plat Test"
    Soit je suis "Supprimer Nouveau Plat Test"
    Alors je devrais voir "Le plat \"Nouveau Plat Test\" a bien été supprimé"

    # La categorie n'existe plus
    Soit je suis sur la page d'accueil
    Et je suis "Categorie Test"
    Alors je ne devrais pas voir "Nouveau Plat Test"
