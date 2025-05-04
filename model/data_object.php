<?php

require_once('model/db.php');

class DataObject
{
    protected string $table;

    public function update($obj, $data)
    {
        $db = app(DB::class);
        $connection = $db->getConnection();
        $update = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = $connection->real_escape_string($value);
                $value = '"' . $value . '"';
            }
            $update[] = $key . '=' . $value;
        }
        $update = implode(',', $update);

        $sql = 'update ' . $this->table . ' set ' . $update . ' where id = ' . $obj->id;
        $connection->query($sql);
    }
}