<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * \class UtilisateurCASProvider
 *
 * @author  Ivann LARUELLE <ivann.laruelle@gmail.com>
 *
 * \brief Ne sert qu'à faire joli, mais requis par UserInterface
 *
 * \details L'user provider est celui fourni les objets utilisateurs
 *
 * L'authentification classique se fait comme ça : Auth (reçoit les infos de connexions) -> Provider -> User -> Provider -> Auth (vérifie l'user fourni)
 *
 * Chez nous, UtilisateurCASAuthentificator fait déjà l'action de vérifier l'utilisateur fourni (vu que l'auth est externe), son reçoit un appel pour fournir un utilisateur,
 * cela veut dire que l'utilisateur est déjà légitime.
 *
 * @package App\Security
 */
class CASProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /**
     * \brief Nécessaire pour l'appel au constructeur de UtilisateurCAS
     * \details Permet de vérifier les admins
     *
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * UtilisateurCASProvider constructor.
     *
     * @param EntityManagerInterface $entityManager autowiré
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * \brief Renvoie directement un UtilisateurCAS avec l'username / uuid fourni
     *
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @param $username l'uuid
     *
     * @return UserInterface
     *
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        // Load a User object from your data source or throw UsernameNotFoundException.
        // The $username argument may not actually be a username:
        // it is whatever value is being returned by the getUsername()
        // method in your User class.
        //if (phpCAS::isInitialized() && phpCAS::getUser()) {
        if(!$this->entityManager->getRepository(User::class)->findBy(["username"=>$username]))
        {
            $user = new User();
            $user->setUsername($username);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $user;
        }
        else
        {
            return $this->entityManager->getRepository(User::class)->findOneBy(["username"=>$username]);
        }
        //}
        throw new \Exception('fill in loadUserByUsername() inside '.__FILE__);
    }

    /**
     * \brief Refreshes the user after being reloaded from the session.
     * \details On vérifie que l'utilisateur est bien une instance de UtilisateurCAS, si oui on renvoie directement un nouvel UtilistauerCAS avec le même uuid
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if ( !$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        else {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(["username"=>$user->getUsername()]);
            if(is_null($user))
            {
                throw new \Exception('fill in refreshUser() inside '.__FILE__);
            }
            return $user;
        }
        /*$temp = new CasAuthenticator($router,$request);
        //dd(phpCAS::isInitialized());
        if ($temp->getCredentials())
        {
            return new UtilisateurCAS($user->getUsername());
        }*/
        //throw new UsernameNotFoundException();
        // Return a User object after making sure its data is "fresh".
        // Or throw a UsernameNotFoundException if the user no longer exists.
        //
    }

    /**
     * \brief Tells Symfony to use this provider for this User class.
     *
     * @param $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    /**
     * \brief Inutile chez nous
     *
     * \details Upgrades the encoded password of a user, typically for using a better hash algorithm.
     *
     * @param UserInterface $user
     * @param string        $newEncodedPassword
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword) : void
    {
        // when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }
}