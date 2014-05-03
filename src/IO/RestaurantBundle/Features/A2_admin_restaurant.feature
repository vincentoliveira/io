# language: fr
@admin
Fonctionnalité: Administration des restaurants


Contexte:
    Soit je vide les entités "IORestaurantBundle:Restaurant"
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis connecté en tant que "admin"


@A2
Scénario: A2 -  Création d'un restaurant
    # Le restaurant n'existe pas
    Soit je suis sur la page d'accueil
    Et je suis "Administration des restaurants"
    Alors je ne devrais pas voir "Restaurant Test"

    # Creation du restaurant
    Soit je suis "Ajouter un restaurant"
    Et je presse "Ajouter"
    Alors je ne devrais pas voir "Le restaurant \"Restaurant Test\" a bien été ajouté"
    Lorsque je remplis "restaurant[name]" avec "Restaurant Test"
    Et je presse "Ajouter"
    Alors je devrais voir "Le restaurant \"Restaurant Test\" a bien été ajouté"
