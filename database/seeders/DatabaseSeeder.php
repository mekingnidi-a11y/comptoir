<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CategorieDepense;
use App\Models\Depense;
use App\Models\Encaissement;
use App\Models\Gerant;
use App\Models\Journee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $aisha = Gerant::query()->create([
            'nom' => 'Aïsha',
            'telephone' => '06 000 00 01',
            'pourcentage' => 15,
            'actif' => true,
            'notes' => 'Point de vente principal',
        ]);

        $paul = Gerant::query()->create([
            'nom' => 'Paul',
            'telephone' => '06 000 00 02',
            'pourcentage' => 12,
            'actif' => true,
        ]);

        User::query()->create([
            'name' => 'Propriétaire',
            'email' => 'marie@boutique.test',
            'password' => 'Admin123!',
            'role' => User::ROLE_PROPRIETAIRE,
            'actif' => true,
        ]);

        User::query()->create([
            'name' => 'Aïsha',
            'email' => 'aisha@boutique.test',
            'password' => 'Gerant123!',
            'role' => User::ROLE_GERANT,
            'gerant_id' => $aisha->id,
            'actif' => true,
        ]);

        $articles = [];
        foreach ([
            ['Gâteau chocolat', 'gateau', 2500, 'part'],
            ['Gâteau vanille', 'gateau', 2500, 'part'],
            ['Gâteau anniversaire', 'gateau', 15000, 'pièce'],
            ['Glace vanille', 'glace', 1500, 'pot'],
            ['Glace chocolat', 'glace', 1500, 'pot'],
            ['Glace fruit', 'glace', 1500, 'pot'],
            ['Jus naturel', 'boisson', 1000, 'verre'],
            ['Eau 50 cl', 'boisson', 500, 'bouteille'],
            ['Beignet', 'snack', 200, 'pièce'],
        ] as $row) {
            $articles[] = Article::query()->create([
                'nom' => $row[0],
                'categorie' => $row[1],
                'prix_unitaire' => $row[2],
                'unite' => $row[3],
                'actif' => true,
            ]);
        }

        $matieres = CategorieDepense::query()->create(['nom' => 'Matières premières', 'actif' => true]);
        CategorieDepense::query()->create(['nom' => 'Transport', 'actif' => true]);
        $divers = CategorieDepense::query()->create(['nom' => 'Divers', 'actif' => true]);
        CategorieDepense::query()->create(['nom' => 'Emballages', 'actif' => true]);
        CategorieDepense::query()->create(['nom' => 'Énergie / gaz', 'actif' => true]);

        foreach ([
            [now()->subDays(2)->toDateString(), $aisha, true, [0 => 8, 3 => 12, 6 => 6], 3500],
            [now()->subDays(2)->toDateString(), $paul, true, [1 => 5, 4 => 9, 7 => 10], 2000],
            [now()->subDay()->toDateString(), $aisha, true, [0 => 10, 2 => 1, 3 => 15], 4000],
            [now()->toDateString(), $aisha, false, [0 => 4, 4 => 7, 8 => 20], 1500],
        ] as [$date, $gerant, $cloturee, $ventes, $depenseMontant]) {
            $journee = Journee::query()->create([
                'date' => $date,
                'gerant_id' => $gerant->id,
                'statut' => Journee::STATUT_OUVERTE,
                'fond_caisse' => 10000,
                'pourcentage_applique' => $gerant->pourcentage,
                'notes' => $cloturee ? 'Journée d’exemple' : 'En cours',
            ]);

            foreach ($ventes as $index => $qte) {
                Encaissement::query()->create([
                    'journee_id' => $journee->id,
                    'article_id' => $articles[$index]->id,
                    'quantite' => $qte,
                    'prix_unitaire' => $articles[$index]->prix_unitaire,
                ]);
            }

            Depense::query()->create([
                'journee_id' => $journee->id,
                'categorie_depense_id' => $depenseMontant > 2000 ? $matieres->id : $divers->id,
                'libelle' => $depenseMontant > 2000 ? 'Farine / sucre / lait' : 'Petits achats',
                'montant' => $depenseMontant,
            ]);

            $journee->recalculer();
            if ($cloturee) {
                $journee->cloturer();
            }
        }
    }
}
