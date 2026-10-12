<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Component;

class ApiTokenManager extends Component
{
    /**
     * List allowed permissions
     *
     * @var string[]
     */
    public $permissions = [
        'create', 'read', 'update', 'delete',
    ];

    /**
     * List default permissions
     *
     * @var string[]
     */
    public $defaultPermissions = [
        'read',
    ];

    /**
     * The create API token form state.
     *
     * @var array
     */
    public $createApiTokenForm = [
        'name' => '',
        'permissions' => [],
    ];

    /**
     * The plain text token value.
     *
     * @var string|null
     */
    public $plainTextToken;

    /**
     * The token that is currently having its permissions managed.
     *
     * @var PersonalAccessToken|null
     */
    public $managingPermissionsFor;

    /**
     * The update API token form state.
     *
     * @var array
     */
    public $updateApiTokenForm = [
        'permissions' => [],
    ];

    /**
     * The ID of the API token being deleted.
     *
     * @var int
     */
    public $apiTokenIdBeingDeleted;

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->createApiTokenForm['permissions'] = $this->defaultPermissions;
    }

    /**
     * Return the permissions in the given list that are actually defined permissions for the application.
     *
     * @return array
     */
    private function validPermissions($permissions)
    {
        return array_values(array_intersect($permissions, $this->permissions));
    }

    /**
     * Create a new API token.
     *
     * @return void
     */
    public function createApiToken()
    {
        $this->resetErrorBag();

        Validator::make([
            'name' => $this->createApiTokenForm['name'],
        ], [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createApiToken');

        $this->displayTokenValue($this->authenticatedUser()->createToken(
            $this->createApiTokenForm['name'],
            $this->validPermissions($this->createApiTokenForm['permissions'])
        ));

        $this->createApiTokenForm['name'] = '';
        $this->createApiTokenForm['permissions'] = $this->defaultPermissions;

        $this->dispatch('created');
    }

    /**
     * Display the token value to the user.
     *
     * @return void
     */
    protected function displayTokenValue(NewAccessToken $token)
    {
        $this->plainTextToken = explode('|', $token->plainTextToken, 2)[1];

        $this->dispatch('open-modal', 'displaying-token');
    }

    /**
     * Allow the given token's permissions to be managed.
     *
     * @return void
     */
    public function manageApiTokenPermissions(int $tokenId)
    {
        $this->managingPermissionsFor = $this->authenticatedUser()->tokens()->where(
            'id', $tokenId
        )->firstOrFail();

        $this->updateApiTokenForm['permissions'] = $this->managingPermissionsFor->abilities;

        $this->dispatch('open-modal', 'managing-api-token-permissions');
    }

    /**
     * Update the API token's permissions.
     *
     * @return void
     */
    public function updateApiToken()
    {
        $this->managingPermissionsFor->forceFill([
            'abilities' => $this->validPermissions($this->updateApiTokenForm['permissions']),
        ])->save();

        $this->dispatch('close');
    }

    /**
     * Confirm that the given API token should be deleted.
     *
     * @return void
     */
    public function confirmApiTokenDeletion(int $tokenId)
    {
        $this->apiTokenIdBeingDeleted = $tokenId;

        $this->dispatch('open-modal', 'confirming-api-token-deletion');
    }

    /**
     * Delete the API token.
     *
     * @return void
     */
    public function deleteApiToken()
    {
        $this->authenticatedUser()->tokens()->where('id', $this->apiTokenIdBeingDeleted)->first()->delete();

        $this->authenticatedUser()->load('tokens');

        $this->managingPermissionsFor = null;

        $this->dispatch('close');
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new AuthenticationException;
        }

        return $user;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.api-token-manager');
    }
}
