<?php
namespace Core\Utils;

use Zend\Permissions\Acl\Acl;

final class AclInitiator
{
    /**
     * @var Acl
     */
    private $acl;

    /**
     * @var array
     */
    private $permissions;

    /**
     * AclInitiator constructor.
     *
     * @param  array $permissions
     * @throws \Exception
     */
    public function __construct(array $permissions)
    {
        $this->acl = new \Zend\Permissions\Acl\Acl();
        $this->permissions = $permissions;

        if(isset($permissions['roles'])) {
            $this->mapPermissions($permissions['roles']);
        } else {
            throw new \Exception("No roles defined for permissions");
        }
    }

    /**
     * @param array $roles
     */
    public function mapPermissions(array $roles): void
    {
        // on parcours les rôles déclarés
        foreach ($roles as $role => $permissions) {
            $parents = $permissions['parents'] ?? null;
            $this->acl->addRole($role, $parents);

            if (is_array($parents)) {
                foreach ($parents as $parent) {
                    $roleRoutes = $roles[$role]['routes'] ?? null;
                    $parentRoutes = $roles[$parent]['routes'] ?? null;

                    if (!isset($roleRoutes)) {
                        $roles[$role]['routes'] = $parentRoutes;
                    } else {
                        foreach ($parentRoutes as $key => $parentRoute) {
                            $roleRoutesKey = $roleRoutes[$key] ?? null;

                            if (isset($roleRoutesKey) && isset($parentRoute)) {
                                $roles[$role]['routes'][$key] = array_merge(
                                    $roleRoutesKey,
                                    array_diff($parentRoute, $roleRoutes['deny'] ?? $roleRoutes['allow'] ?? [])
                                );
                            }
                        }
                    }
                }
            }
        }

        foreach ($roles as $role => $permissions) {
            // le rôle n'a accès à rien par défaut
            $this->acl->deny($role, null);

            if (!empty($permissions)) {
                // allow
                if (array_key_exists('allow', $permissions)) {
                    $allow = $permissions['allow'];
                    $this->acl->allow($role, null, $allow);
                }

                // deny
                if (array_key_exists('deny', $permissions)) {
                    $deny = $permissions['deny'];
                    $this->acl->deny($role, null, $deny);
                }

                if (array_key_exists('routes', $permissions) && $permissions['routes'] !== null) {
                    $routesPermissions = $permissions['routes'];

                    foreach ($routesPermissions as $permissionType => $resources) {
                        if (is_array($resources)) {
                            foreach ($resources as $resource) {
                                if ($this->acl->hasResource($resource) === false) {
                                    $this->acl->addResource($resource);
                                }
                            }
                        }

                        if ($permissionType === 'deny') {
                            $this->acl->deny($role, $resources);
                        } elseif ($permissionType === 'allow') {
                            $this->acl->allow($role, $resources);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $roleName
     * @return string
     */
    public static function preprendRoleName(string $roleName): string
    {
        return 'ROLE_' . strtoupper($roleName);
    }

    /**
     * @return Acl
     */
    public function getAcl(): Acl
    {
        return $this->acl;
    }
}
