# language: fr
@api @apigetallcategories
Fonctionnalité: API : Get categories

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "test" a pour restaurant "Restaurant test"

@102.1
Scénario: 102.1 -  Get all categories
    Lorsque je suis sur "/api/categories"
    Alors le code de status de la réponse ne devrait pas être 403

    Lorsque j'appelle "/api/categories" authentifié avec "test"
    Alors le json devrait convenir:
        | status |
        | ok     |
