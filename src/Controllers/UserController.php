<?php

namespace App\Controllers;

use App\Models\CoreCollection;
use App\Models\CoreUser;


class UserController extends Controller
{
    public function list($request, $response, $args)
    {
        $this->logger->addInfo("GET Users");
        if (!empty($request->getQueryParam('filter'))) {
            $this->logger->addInfo("Filter --> " .$request->getQueryParam('filter'));
        }
        $baseUrl = $request->getUri()->getBaseUrl();

        $userName = null;
        $users = [];
        if (!empty($request->getQueryParam('filter'))) {
            $userName = \App\Models\Helper::getUserNameFromFilter($request->getQueryParam('filter'));
            if (!empty($userName)) {
                $user = CoreUser::where('userName', $userName)->first();
                if (!empty($user)) {
                    $users[] = $user;
                }
            }
        } else {
            $users = CoreUser::all();
        }

        $scimUsers = [];
        if (!empty($users)) {
            foreach ($users as $user) {
                $scimUsers[] = $user->toSCIM(false, $baseUrl);
            }
        }
        $scimUserCollection = (new CoreCollection($scimUsers))->toSCIM(false);

        $this->logger->addInfo(json_encode($scimUserCollection, JSON_UNESCAPED_SLASHES));
        return $response->withJson($scimUserCollection, 200, JSON_UNESCAPED_SLASHES);
    }

    public function get($request, $response, $args)
    {
        $this->logger->addInfo("GET User");
        $baseUrl = $request->getUri()->getBaseUrl();

        $id = $request->getAttribute('id');
        $this->logger->addInfo("ID: ".$id);
        $user = CoreUser::find($id);
        if (empty($user)) {
            $this->logger->addInfo("Not found");
            return $response->withStatus(404);
        }

        $scimUser = $user->toSCIM(false, $baseUrl);

        $this->logger->addInfo(json_encode($scimUser, JSON_UNESCAPED_SLASHES));

        return $response->withJson($scimUser, 200, JSON_UNESCAPED_SLASHES);
    }

    public function create($request, $response, $args)
    {
        $this->logger->addInfo("CREATE User");
        $this->logger->addInfo($request->getBody());

        $baseUrl = $request->getUri()->getBaseUrl();

        try {
            $user = new CoreUser();
            $user->fromSCIM($request->getParsedBody());

            if ($user->save()) {
                $scimUser = $user->toSCIM(false, $baseUrl);
                $this->logger->addInfo(json_encode($scimUser, JSON_UNESCAPED_SLASHES));
                return $response->withJson($scimUser, 201, JSON_UNESCAPED_SLASHES);
            } else {
                $this->logger->addError("Error updating user");
                return $response->withJson(["Errors" => ["description" => "Error creating user", "code" => 400]], 400);
            }
        } catch (\Exception $e) {
            $this->logger->addError("Error updating user.".$e->getMessage());
            return $response->withJson(["Errors" => ["description" => $e->getMessage(), "code" => 400]], 400);
        }
    }

    public function update($request, $response, $args)
    {
        $this->logger->addInfo("UPDATE User");
        $this->logger->addInfo($request->getBody());
        $baseUrl = $request->getUri()->getBaseUrl();

        $id = $request->getAttribute('id');
        $this->logger->addInfo("ID: ".$id);
        $user = CoreUser::find($id);
        if (empty($user)) {
            $this->logger->addInfo("Not found");
            return $response->withStatus(404);
        }

        try {
            $user->fromSCIM($request->getParsedBody());
            if ($user->save()) {
                $scimUser = $user->toSCIM(false, $baseUrl);
                $this->logger->addInfo(json_encode($scimUser, JSON_UNESCAPED_SLASHES));
                return $response->withJson($scimUser, 201, JSON_UNESCAPED_SLASHES);
            } else {
                $this->logger->addError("Error updating user");
                return $response->withJson(["Errors" => ["description" => "Error updating user", "code" => 400]], 400);
            }
        } catch (\Exception $e) {
            $this->logger->addError("Error updating user.".$e->getMessage());
            return $response->withJson(["Errors" => ["description" => $e->getMessage(), "code" => 400]], 400);
        }
    }

    public function delete($request, $response, $args)
    {
        $this->logger->addInfo("DELETE User");
        $id = $request->getAttribute('id');
        $this->logger->addInfo("ID: ".$id);
        $user = CoreUser::find($id);
        if (empty($user)) {
            $this->logger->addInfo("Not found");
            return $response->withStatus(404);
        }
        $user->delete();
        $this->logger->addInfo("User deleted");

        return $response->withStatus(200);
    }
}
