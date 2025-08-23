<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Friandise;
use App\Entity\User;
use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friandise>
 */
class FriandiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friandise::class);
    }

    /**
     * Retourne les IDs des animaux distincts favoris de l'utilisateur (sans doublons).
     * @return int[]
     */
    public function findDistinctAnimalIdsForUser(User $user): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('DISTINCT IDENTITY(f.animal) AS animal_id')
            ->andWhere('f.envoyeur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getScalarResult(); // ex: [['animal_id' => '8'], ['animal_id' => '7']]

        return array_map('intval', array_column($rows, 'animal_id'));
    }

    /**
     * Vrai si une friandise existe déjà pour (utilisateur, animal).
     */
    public function existsForUserAndAnimal(User $user, Animal $animal): bool
    {
        $id = $this->createQueryBuilder('f')
            ->select('f.id')
            ->andWhere('f.envoyeur = :user')
            ->andWhere('f.animal = :animal')
            ->setParameter('user', $user)
            ->setParameter('animal', $animal)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $id !== null;
    }

    /**
     * Nombre de friandises par animal pour un utilisateur (utile pour afficher un compteur).
     * @return array<int, array{animal_id:int, nb:int}>
     */
    public function countByUserGroupedByAnimal(User $user): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('IDENTITY(f.animal) AS animal_id, COUNT(f.id) AS nb')
            ->andWhere('f.envoyeur = :user')
            ->setParameter('user', $user)
            ->groupBy('animal_id')
            ->orderBy('nb', 'DESC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn(array $r) => [
            'animal_id' => (int) $r['animal_id'],
            'nb'        => (int) $r['nb'],
        ], $rows);
    }

    /**
     * Supprime les doublons pour l'utilisateur : garde la plus ancienne friandise par animal,
     * ne supprime que si nécessaire. Retourne le nombre de lignes supprimées.
     */
    public function cleanDuplicatesForUser(User $user): int
    {
        // Toutes les friandises de l'utilisateur, triées par animal puis par id croissant
        $all = $this->createQueryBuilder('f')
            ->andWhere('f.envoyeur = :user')
            ->setParameter('user', $user)
            ->leftJoin('f.animal', 'a')->addSelect('a')
            ->orderBy('a.id', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();

        $seen = [];
        $toRemove = [];

        foreach ($all as $f) {
            $animalId = $f->getAnimal()->getId();
            if (!isset($seen[$animalId])) {
                $seen[$animalId] = true; // on garde la première (la plus ancienne, id le plus petit)
                continue;
            }
            $toRemove[] = $f; // doublon → suppression
        }

        if (!$toRemove) {
            return 0; // rien à supprimer
        }

        foreach ($toRemove as $f) {
            $this->_em->remove($f);
        }
        $this->_em->flush();

        return count($toRemove);
    }

    // — exemples générés par Symfony (laisse-les si tu veux) —
    // /**
    //  * @return Friandise[] Returns an array of Friandise objects
    //  */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('f')
    //         ->andWhere('f.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('f.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // public function findOneBySomeField($value): ?Friandise
    // {
    //     return $this->createQueryBuilder('f')
    //         ->andWhere('f.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
