<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAdmin extends AbstractAdmin
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($code, $class, $baseControllerName);
        $this->passwordHasher = $passwordHasher;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('General')
                ->add('email', EmailType::class)
                ->add('plainPassword', PasswordType::class, [
                    'required' => $this->isCurrentRoute('create'),
                    'help' => $this->isCurrentRoute('edit') ? 
                        'Leave empty to keep current password' : 
                        'Enter password'
                ])
            ->end()
            ->with('Management')
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'User' => 'ROLE_USER',
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                    'required' => true,
                ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('email')
            ->add('roles', null, [], ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email')
            ->add('roles', 'array')
            ->add('createdAt')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('General')
                ->add('email')
                ->add('createdAt')
            ->end()
            ->with('Roles')
                ->add('roles', 'array')
            ->end();
    }

    protected function prePersist(object $user): void
    {
        $this->updatePassword($user);
    }

    protected function preUpdate(object $user): void
    {
        $this->updatePassword($user);
    }

    private function updatePassword(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
            );
            $user->eraseCredentials();
        }
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show'); // Удаляем просмотр, если не нужен
    }
}