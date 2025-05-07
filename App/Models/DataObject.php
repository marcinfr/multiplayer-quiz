<?php

namespace App\Models;

class DataObject
{
    protected string $table;

    protected string $idField = 'id';

    public function save($obj, array $fieldsToUpdate = null)
    {
        $idField = $this->idField;
        if (isset($obj->$idField) && $obj->$idField) {
            $this->update($obj, $fieldsToUpdate);
        } else {
            $this->insert($obj);
        }
    }

    public function insert($obj)
    {
        $idField = $this->idField;
        $values = [];
        $fields = [];

        $data = get_object_vars($obj);

        foreach ($data as $key => $value) {
            $value = $this->prepareValueToSave($value);
            $values[] = $value;
            $fields[] = $key;
        }
        $fields = implode(',', $fields);
        $values = implode(',', $values);
        $sql = 'insert into ' . $this->table . ' (' . $fields . ') values(' . $values . ')';
        $this->getConnection()->query($sql);
        $obj->$idField = $this->getConnection()->insert_id;
    }

    public function update($obj, array $fieldsToUpdate = null)
    {
        $data = get_object_vars($obj);

        if ($fieldsToUpdate !== null) {
            $data = array_intersect_key($data, array_flip($fieldsToUpdate));
        }

        $idField = $this->idField;
        $update = [];
        foreach ($data as $key => $value) {
            $value = $this->prepareValueToSave($value);
            $update[] = $key . '=' . $value;
        }
        $update = implode(',', $update);
        $sql = 'update ' . $this->table . ' set ' . $update . ' where ' . $idField . ' = ' . $obj->id;
        $this->getConnection()->query($sql);
    }

    private function prepareValueToSave($value)
    {
        if (is_string($value)) {
            $value = $this->getConnection()->real_escape_string($value);
            $value = '"' . $value . '"';
        }
        if ($value == null) {
            return 'NULL';
        }
        return $value;
    }

    private function getConnection()
    {
        $db = app(DB::class);
        return $db->getConnection();
    }
}