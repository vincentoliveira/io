# language: fr
@api @apiaddorder
Fonctionnalité: API : Add order

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "test" a pour restaurant "Restaurant test"
    Soit il n'y a aucun plat
    Soit il n'y a aucune commande
    Soit je crée une catégorie "Categorie test" pour le restaurant "Restaurant test"
    Soit je crée un plat "Entrée test" dans la category "Categorie test" du "Restaurant test"
    Soit je crée un plat "Plat test" dans la category "Categorie test" du "Restaurant test"

@112.1
Scénario: 112.1 -  Get order
    Lorsque je suis sur "/api/order/add"
    Alors le code de status de la réponse ne devrait pas être 403

    Lorsque j'appelle "/api/order/add" authentifié avec "test"
    Alors le code de status de la réponse ne devrait pas être 403

    Soit je post sur "/api/order/add" authentifié avec "test" :
        | key           | value         |
        | table_name    | table test    |
    Alors le json devrait convenir:
        | status | reason           |
        | ko     | Nothing to order |

    Soit je post sur "/api/order/add" authentifié avec "test" :
        | key           | value         |
        | table_name    | table test    |
        | items         | dish1         |
        | items         | dish2         |
    Alors le json devrait convenir:
        | status | reason           |
        | ko     | Bad item         |

    Soit je post sur "/api/order/add" authentifié avec "test" :
        | key           | value         |
        | table_name    | table test    |
        | items         | dish:1        |
        | items         | dish:2        |
    Alors le json devrait convenir:
        | status |
        | ok     |