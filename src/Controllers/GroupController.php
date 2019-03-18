<?php

namespace App\Controllers;

use App\Models\CoreCollection;
use App\Models\CoreGroup;

class GroupController extends Controller
{
    public function list($request, $response, $args)
    {
        $this->logger->addInfo("GET groups");
        $baseUrl = $request->getUri()->getBaseUrl();

        $groups = [];
        $groups = CoreGroup::all();

        $scimGroups = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $scimGroups[] = $group->toSCIM(false, $baseUrl);
            }
        }
        $scimGroupCollection = (new CoreCollection($scimGroups))->toSCIM(false);

        $this->logger->addInfo(json_encode($scimGroupCollection, JSON_UNESCAPED_SLASHES));
        return $response->withJson($scimGroupCollection, 200, JSON_UNESCAPED_SLASHES);
    }

    public function create($request, $response, $args)
    {
        $this->logger->addInfo("CREATE group");
        $this->logger->addInfo($request->getBody());

        $baseUrl = $request->getUri()->getBaseUrl();

        try {
            $group = new CoreGroup();
            $group->fromSCIM($request->getParsedBody());
            if ($group->save()) {
                $scimGroup = $group->toSCIM(false, $baseUrl);
                $this->logger->addInfo(json_encode($scimGroup, JSON_UNESCAPED_SLASHES));
                return $response->withJson($scimGroup, 201, JSON_UNESCAPED_SLASHES);
            } else {
                $this->logger->addError("Error creating group");
                return $response->withJson(["Errors" => ["description" => "Error creating group", "code" => 400]], 400);
            }
        } catch (\Exception $e) {
            $this->logger->addError("Error creating group.".$e->getMessage());
            return $response->withJson(["Errors" => ["description" => $e->getMessage(), "code" => 400]], 400);
        }
    }

    public function update($request, $response, $args)
    {
        $this->logger->addInfo("UPDATE group");
        $this->logger->addInfo($request->getBody());

        $baseUrl = $request->getUri()->getBaseUrl();
        $id = $request->getAttribute('id');
        $this->logger->addInfo("ID: ".$id);

        $group = CoreGroup::find($id);
        if (empty($group)) {
            return $response->withStatus(404);
        }

        $groupMembers = array();
        if (!empty($group->members)) {
            $groupMembers = explode(',', $group->members);
        }

        try {
            $data = $request->getParsedBody();
            
            if (empty($data) || !isset($data['members'])) {
                $error = "Error updating group, no members info provided";
                $this->logger->addError($error);
                return $response->withJson(["Errors" => ["description" => $error, "code" => 400]], 400);
            }

            $members = $data['members'];
            $toAdd = [];
            $toRemove = [];
            foreach ($members as $member) {
                if (isset($member['operation']) && $member['operation'] == 'delete') {
                    if (($key = array_search($member['value'], $groupMembers)) !== false) {
                        unset($groupMembers[$key]);
                    }
                } else if (!in_array($member['value'], $groupMembers)) {
                    $groupMembers[] = $member['value'];
                }
            }

            $group->members = implode(',', $groupMembers);
            if ($group->save()) {
                $this->logger->addInfo("Group updated");
                return $response->withStatus(204);
            } else {
                $this->logger->addError("Error updating group");
                return $response->withJson(["Errors" => ["description" => "Error updating group", "code" => 400]], 400);
            }
        } catch (\Exception $e) {
            $this->logger->addError("Error updating group.".$e->getMessage());
            return $response->withJson(["Errors" => ["description" => $e->getMessage(), "code" => 400]], 400);
        }
    }
}
