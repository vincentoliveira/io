# language: fr
@api @apigetorder
Fonctionnalité: API : Get gorder

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "test" a pour restaurant "Restaurant test"
    Soit il n'y a aucune commande
    Soit il y a une commande en cours pour "Restaurant test"

@91.1
Scénario: 91.1 -  Get order
    Lorsque je suis sur "/api/order?order_id=1"
    Alors le code de status de la réponse ne devrait pas être 403

    Lorsque j'appelle "/api/order" authentifié avec "test"
    Alors le json devrait convenir:
        | status | reason                                         |
        | ko     | This order does not exist or you cannot see it |

    Lorsque j'appelle "/api/order?order_id=1" authentifié avec "test"
    Alors le json devrait convenir:
        | status |
        | ok     |
