<?php

namespace Lib\Actions;


class GetUsersByName extends AbstractAction
{
    public function run(): ?array
    {
        $username = $this->getParam('username');
        if ($username !== null) {
            //Get single name
            if (!is_scalar($username)) {
                throw new \Lib\Exception('Wrong single username input', 400);
            }
            $names = [$username];
        } else {
            //Get multiple names (json array)
            $names = $this->getInput(2);
            if (empty($names)) {
                throw new \Lib\Exception('Wrong multiple names input', 400);
            }
        }

        $keys = [];
        $params = [];
        foreach (array_values($names) as $i => $name) {
            $key = 'name' . $i;
            $keys[] = $key;
            $params[$key] = mb_strtolower($name);
        }
        $rows = \Lib\DB::instance()->qAll('SELECT `id`, `name` FROM `users` WHERE LOWER(`name`) IN (' . implode(',', $keys) . ')', $params);

        return array_map(function($row) {
            return [
                'id' => (new \Lib\UUID($row['id']))->format(),
                'name' => $row['name'],
            ];
        }, $rows);
    }
}
