<?php

namespace App\Validator;

use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object|null       $obj
     * @param Unique|Constraint $constraint
     *
     * @param mixed $obj
     * @param Constraint $constraint
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validate($obj, Constraint $constraint): void
    {
        if (null === $obj) {
            return;
        }

        if (!$constraint instanceof Unique) {
            throw new RuntimeException(sprintf('%s ne peut pas valider des contraintes %s', self::class, get_class($constraint)));
        }

        if (!method_exists($obj, 'getId')) {
            throw new RuntimeException(sprintf('%s ne peut pas être utilisé sur l\'objet %s car il ne possède pas de méthode getId()', self::class, get_class($obj)));
        }

        $accessor = new PropertyAccessor();
        /** @var class-string<\stdClass> $entityClass */
        $entityClass = $constraint->entityClass;

        $value = $accessor->getValue($obj, $constraint->field);
        $repository = $this->em->getRepository($entityClass);

        if ($repository instanceof AbstractRepository) {
            $result = $repository->findOneByCaseInsensitive([
                $constraint->field => $value,
            ]);
        } else {
            $result = $repository->findOneBy([
                $constraint->field => $value,
            ]);
        }

        if (null !== $result &&
            (!method_exists($result, 'getId') || $result->getId() !== $obj->getId())
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->atPath($constraint->field)
                ->addViolation();
        }
    }
}
