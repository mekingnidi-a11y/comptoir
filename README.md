# La Boutique

Application Laravel de gestion commerciale pour un petit commerce (gâteaux, crèmes glacées, etc.).

Le gérant encaisse et dépense au quotidien. Son salaire du jour est un **pourcentage de la recette**, différent pour chaque gérant. La propriétaire voit tout : caisse, bénéfices, reporting.

## Fonctions

- Journées de caisse (encaissements + dépenses)
- Catalogue d’articles à encaisser (plus besoin de tout retaper)
- Motifs de dépense
- Gérants avec % individuel, compte de connexion optionnel
- Clôture de journée (totaux figés)
- Tableau de bord (jour / mois, graphiques, meilleures ventes)
- Reporting par période et par gérant, export CSV
- Bénéfice = recette − dépenses − salaire gérant

## Accès local (XAMPP)

URL : [http://localhost/comptoir/public](http://localhost/comptoir/public)

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Propriétaire | `marie@boutique.test` | `Admin123!` |
| Gérant (Aïsha, 15 %) | `aisha@boutique.test` | `Gerant123!` |

Base MySQL : `comptoir` (utilisateur `root`, mot de passe vide par défaut XAMPP).

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Stack

Laravel 13, Filament 5, MySQL, PHP 8.4+.
