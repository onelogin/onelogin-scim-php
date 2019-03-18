<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'userName', 'givenName',
                           'familyName', 'created_at', 'active',
                           'externalId', 'profileUrl', 'title',
                           'timezone'];
    public $incrementing = false;

    public $schemas = ['urn:scim:schemas:core:1.0'];
    private $baseLocation;

    public function fromArray($data)
    {
        $this->id = isset($data['id'])? $data['id'] : isset($this->id)? $this->id: Helper::gen_uuid();
        $this->userName = isset($data['userName'])? $data['userName'] : null;
        $this->givenName = isset($data['givenName'])? $data['givenName'] : null;
        $this->familyName = isset($data['familyName'])? $data['familyName'] : null;
        $this->created_at = isset($data['created'])? Helper::string2dateTime($data['created']) : isset($this->created_at)? $this->created_at : new \DateTime('NOW');
        $this->active = isset($data['active'])? $data['active'] : true;

        $this->externalId = isset($data['externalId'])? $data['externalId'] : null;
        $this->profileUrl = isset($data['profileUrl'])? $data['profileUrl'] : null;
        $this->title = isset($data['title'])? $data['title'] : null;
        $this->timezone = isset($data['timezone'])? $data['timezone'] : null;
    }

    public function fromSCIM($data)
    {
        $this->id = isset($data['id'])? $data['id'] : isset($this->id)? $this->id: Helper::gen_uuid();
        $this->userName = isset($data['userName'])? $data['userName'] : null;
        $this->givenName = isset($data['name']) && isset($data['name']['givenName'])? $data['name']['givenName'] : null;
        $this->familyName = isset($data['name']) && isset($data['name']['familyName'])? $data['name']['familyName'] : null;
        $this->created_at = isset($data['meta']) && isset($data['meta']['created'])? Helper::string2dateTime($data['meta']['created']) : isset($this->created_at)? $this->created_at : new \DateTime('NOW');
        $this->active = isset($data['active'])? $data['active'] : true;

        $this->externalId = isset($data['externalId'])? $data['externalId'] : null;
        $this->profileUrl = isset($data['profileUrl'])? $data['profileUrl'] : null;
        $this->title = isset($data['title'])? $data['title'] : null;
        $this->timezone = isset($data['timezone'])? $data['timezone'] : null;
    }

    public function toSCIM($encode = true, $baseLocation = 'https://api.scimapp.com/v1')
    {
        $data = [
            'schemas' => $this->schemas,
            'id' => $this->id,
            'externalId' => $this->externalId,
            'meta' => [
                'created' => Helper::dateTime2string($this->created_at),
                'location' => $baseLocation . '/Users/' . $this->id
            ],
            'userName' => $this->userName,
            'nickName' => $this->givenName,
            'name' => [
                'givenName' => $this->givenName,
                'familyName' => $this->familyName
            ],
            'displayName' => $this->givenName . ' ' . $this->familyName,
            'profileUrl' => $this->profileUrl,
            'title' => $this->title,
            'timezone' => $this->timezone,
            'emails' => [],
            'photos' => [],
            'groups' => [],
            'active' => (bool) $this->active
        ];

        if (isset($this->updated_at)) {
            $data['meta']['updated'] = Helper::dateTime2string($this->updated_at);
        }

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}
