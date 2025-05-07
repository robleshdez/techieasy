<?php
// core/ORM.php

abstract class ORM {
    protected static $pdo;
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected $wheres = [];
    protected $orderBy = '';
    protected $limit = null;
    protected $offset = null;
    //protected $timestamps = false;
    protected $casts = [];
    protected $debug = false;
    protected $fillable = [];
    protected $select = '*';
    protected $joins = [];
    protected $groupBy = '';
    protected $havings = []; 
    protected $with = [];



    public function __construct($attributes = []) {
        $this->fill($attributes);
        if (!self::$pdo) self::connect();
    }

    protected static function connect() {
        self::$pdo = getPDOInstance();
    }

    public static function beginTransaction() {
    self::$pdo->beginTransaction();
}
public static function commit() {
    self::$pdo->commit();
}
public static function rollBack() {
    self::$pdo->rollBack();
}


    public function fill(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (empty($this->fillable) || in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    public static function find($id) {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id LIMIT 1";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new static($data) : null;
    }

     
public function where($column, $operator = null, $value = null) {
    if (is_array($column)) {
        foreach ($column as $cond) {
            $this->wheres[] = ["{$cond[0]} {$cond[1]} ?", $cond[2]];
        }
    } else {
        $this->wheres[] = ["$column $operator ?", $value];
    }
    return $this;
}
 


public function orderBy($column, $direction = 'ASC') {
    $this->orderBy = "ORDER BY $column $direction";
    return $this;
}

    public function select(...$columns) {
        if (count($columns) === 1 && $columns[0] === '*') {
        $this->select = '*';
        return $this;
    }
     // Limpieza opcional (por si alguien pone espacios extra)
    $columns = array_map('trim', $columns);

    $this->select = implode(', ', $columns);
        return $this;
    }
public function selectAll() {
    return $this->select('*');
}



    public function paginate($page = 1, $perPage = 10) {
        $this->limit = $perPage;
        $this->offset = ($page - 1) * $perPage;
        return $this->get();
    }
    public function limit($limit) {
    $this->limit = (int) $limit;
    return $this;
}

public function offset($offset) {
    $this->offset = (int) $offset;
    return $this;
}



    public function get() {
    $sql = "SELECT {$this->select} FROM {$this->table}";
    $sql .= ' ' . implode(' ', $this->joins);
    $params = [];

    // WHERE
    if ($this->wheres) {
        $sql .= " WHERE ";
        $whereConditions = [];
        foreach ($this->wheres as [$condition, $value]) {
            $whereConditions[] = $condition;
            $params[] = $value;
        }
        $sql .= implode(' AND ', $whereConditions);
    }

    // GROUP BY
    if ($this->groupBy) {
        $sql .= " " . $this->groupBy;
    }

    // HAVING
    if ($this->havings) {
        $sql .= " HAVING ";
        $havingConditions = [];
        foreach ($this->havings as [$condition, $value]) {
            $havingConditions[] = $condition;
            $params[] = $value;
        }
        $sql .= implode(' AND ', $havingConditions);
    }

    // ORDER BY, LIMIT y OFFSET
    if ($this->orderBy) {
        $sql .= " " . $this->orderBy;
    }

    if ($this->limit !== null) {
        $sql .= " LIMIT {$this->limit}";
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
    }

    if ($this->debug) {
        error_log("[SQL] $sql");
    }

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Relaciones eager loading
    /*if (!empty($this->with)) {
        foreach ($results as &$row) {
            $instance = new static($row);
            foreach ($this->with as $relation) {
                if (!method_exists($instance, $relation)) {
                    throw new Exception("Relación '$relation' no existe en " . static::class);
                }
                $row[$relation] = $instance->$relation();
            }
        }
    }*/

    return $results;
}

    public function first() {
        $this->limit = 1;
        $results = $this->get();
        return isset($results[0]) ? new static($results[0]) : null;
    }

    public static function all(...$columns) {
    $instance = new static();
    if ($columns) $instance->select(...$columns);
    return $instance->get();
}


    public function fromTable($table) {
    $this->table = $table;
    return $this;
}


    public static function queryTable($table) {
        $instance = new static();
        $instance->table = $table;
        $instance->select('*');

    return $instance;
}

    public static function raw($sql, $params = [], $fetchMode = 'all') {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        switch ($fetchMode) {
            case 'one': return $stmt->fetch(PDO::FETCH_ASSOC);
            case 'column': return $stmt->fetchColumn();
            case 'count': return $stmt->rowCount();
            case 'none': return true;
            case 'all':
            default: return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

public function insert() {
    $keys = [];
    $params = [];

    foreach ($this->attributes as $key => $value) {
        if (empty($this->fillable) || in_array($key, $this->fillable)) {
            $keys[] = $key;
            $params[] = $value;
        }
    }

    $placeholders = array_fill(0, count($keys), '?');
    $sql = "INSERT INTO {$this->table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";

    if ($this->debug) error_log("[SQL] $sql");

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);

    $this->attributes[$this->primaryKey] = self::$pdo->lastInsertId();
    return $this->attributes[$this->primaryKey];
}



public function update() {
    if (!isset($this->attributes[$this->primaryKey])) {
        throw new Exception("No se puede actualizar sin clave primaria");
    }

    $columns = [];
    $params = [];

    foreach ($this->attributes as $key => $value) {
        if ($key === $this->primaryKey) continue;
        if (empty($this->fillable) || in_array($key, $this->fillable)) {
            $columns[] = "$key = ?";
            $params[] = $value;
        }
    }

    $params[] = $this->attributes[$this->primaryKey];
    $sql = "UPDATE {$this->table} SET " . implode(', ', $columns) . " WHERE {$this->primaryKey} = ?";

    if ($this->debug) error_log("[SQL] $sql");

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);
    return true;
}

public function bulkInsert(array $rows) {
    if (empty($rows)) return [];

    $columns = array_keys($rows[0]);
    $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
    $allPlaceholders = implode(', ', array_fill(0, count($rows), $placeholders));
    $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES $allPlaceholders";

    $params = [];
    foreach ($rows as $row) {
        foreach ($columns as $col) {
            $params[] = $row[$col];
        }
    }

    if ($this->debug) error_log("[SQL] $sql");

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);

    $firstId = (int) self::$pdo->lastInsertId();
    $insertedCount = $stmt->rowCount();

    $ids = [];
    for ($i = 0; $i < $insertedCount; $i++) {
        $ids[] = $firstId + $i;
    }

    return $ids;
}
public function save() {
    if (isset($this->attributes[$this->primaryKey])) {
        return $this->update();
    } else {
        return $this->insert();
    }
}

   

    public function delete() {
        if (!isset($this->attributes[$this->primaryKey])) return false;
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        if ($this->debug) error_log("[SQL] $sql");
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$this->attributes[$this->primaryKey]]);
    }

    

    public function refresh() {
        if (!isset($this->attributes[$this->primaryKey])) return false;
        $fresh = self::find($this->attributes[$this->primaryKey]);
        if ($fresh) $this->fill($fresh->attributes);
    }

    public function hasMany($relatedClass, $pivotTable, $foreignKey, $relatedKey) {
        $relatedInstance = new $relatedClass();
        $sql = "SELECT r.* FROM {$relatedInstance->table} r
                JOIN {$pivotTable} p ON r.{$relatedKey} = p.{$relatedKey}
                WHERE p.{$foreignKey} = ?";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$this->{$this->primaryKey}]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function belongsTo($relatedClass, $foreignKey, $ownerKey) {
    $relatedInstance = new $relatedClass();
    $sql = "SELECT * FROM {$relatedInstance->table} WHERE {$ownerKey} = ? LIMIT 1";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$this->attributes[$foreignKey]]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // ← aquí el cambio
}


    public function join($table, $leftColumn, $operator, $rightColumn, $type = 'INNER') {
    $this->joins[] = "$type JOIN $table ON $leftColumn $operator $rightColumn";
    return $this;
}

public function leftJoin($table, $leftColumn, $operator, $rightColumn) {
    return $this->join($table, $leftColumn, $operator, $rightColumn, 'LEFT');
}

public function rightJoin($table, $leftColumn, $operator, $rightColumn) {
    return $this->join($table, $leftColumn, $operator, $rightColumn, 'RIGHT');
}

public function hasOne($relatedClass, $foreignKey, $localKey) {
    $relatedInstance = new $relatedClass();
    $sql = "SELECT * FROM {$relatedInstance->table} WHERE {$foreignKey} = ? LIMIT 1";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$this->attributes[$localKey]]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function groupBy($column) {
    $this->groupBy = "GROUP BY $column";
    return $this;
}

public function having($column, $operator, $value) {
    $this->havings[] = ["$column $operator ?", $value];
    return $this;
}

public function reset() {
    $this->wheres = [];
    $this->orderBy = '';
    $this->limit = null;
    $this->offset = null;
    $this->joins = [];
    $this->select = '*';
    $this->groupBy = '';
   //$this->with = [];
    $this->havings = [];

    return $this;
}

public function count($column = '*', $alias = 'total') {
    return $this->aggregate("COUNT($column)", $alias);
}

public function sum($column, $alias = 'sum') {
    return $this->aggregate("SUM($column)", $alias);
}

public function avg($column, $alias = 'avg') {
    return $this->aggregate("AVG($column)", $alias);
}

public function max($column, $alias = 'max') {
    return $this->aggregate("MAX($column)", $alias);
}

public function min($column, $alias = 'min') {
    return $this->aggregate("MIN($column)", $alias);
}

protected function aggregate($expression, $alias) {
    $sql = "SELECT $expression AS $alias FROM {$this->table}";

    //  JOINs
    if (!empty($this->joins)) {
        $sql .= ' ' . implode(' ', $this->joins);
    }

    $params = [];

    //  WHERE
    if ($this->wheres) {
        $sql .= " WHERE ";
        $conditions = [];
        foreach ($this->wheres as [$condition, $value]) {
            $conditions[] = $condition;
            $params[] = $value;
        }
        $sql .= implode(' AND ', $conditions);
    }

    //  GROUP BY
    if (!empty($this->groupBy)) {
        $sql .= " {$this->groupBy}";
    }

    //  HAVING
    if (!empty($this->havings)) {
        $sql .= " HAVING ";
        $havingConditions = [];
        foreach ($this->havings as [$condition, $value]) {
            $havingConditions[] = $condition;
            $params[] = $value;
        }
        $sql .= implode(' AND ', $havingConditions);
    }

    if ($this->debug) error_log("[SQL] $sql");

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result[$alias] ?? null;
}


public function pluck($column,$key = null) {
    $query = $this->newQuery()->select($key ? "$column, $key" : $column);
    $rows = $query->get();

    return $key
        ? array_column($rows, $column, $key)
        : array_column($rows, $column);
}

public function exists() {
    $sql = "SELECT 1 FROM {$this->table}";
    $params = [];

    if ($this->wheres) {
        $sql .= " WHERE ";
        $conditions = [];
        foreach ($this->wheres as [$condition, $value]) {
            $conditions[] = $condition;
            $params[] = $value;
        }
        $sql .= implode(' AND ', $conditions);
    }

    $sql .= " LIMIT 1";

    if ($this->debug) error_log("[SQL] $sql");

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);
    return (bool) $stmt->fetchColumn();
}
public function groupConcat($column, $alias = 'grouped', $separator = ', ') {
    $expression = "GROUP_CONCAT($column SEPARATOR '$separator')";
    return $this->aggregate($expression, $alias);
}
public function newQuery() {
    return clone $this;
}
 
public function with(...$relations) {
    //$this->with = array_merge($this->with, $relations);
    return $this;
}
   
    public function __get($key) {
        $value = $this->attributes[$key] ?? null;
        if (isset($this->casts[$key])) {
            switch ($this->casts[$key]) {
                case 'int': return (int) $value;
                case 'float': return (float) $value;
                case 'bool': return (bool) $value;
                case 'datetime': return new DateTime($value);
                default: return $value;
            }
        }
        return $value;
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
}




